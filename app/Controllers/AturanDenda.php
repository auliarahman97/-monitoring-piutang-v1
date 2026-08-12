<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\AturanDendaModel;
use App\Models\AturanDendaVersiModel;
use CodeIgniter\HTTP\RedirectResponse;

class AturanDenda extends BaseController
{
    protected AturanDendaVersiModel $versiModel;

    protected AturanDendaModel $aturanModel;


    public function __construct()
    {
        $this->versiModel =
            new AturanDendaVersiModel();

        $this->aturanModel =
            new AturanDendaModel();
    }


    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    public function index(): string
    {
        $versiList =
            $this->versiModel->getAllVersions();


        foreach (
            $versiList as $key => $versi
        ) {

            $versiList[$key]['aturan'] =
                $this->aturanModel->getByVersionId(
                    (int) $versi['id']
                );
        }


        return view(
            'aturan_denda/index',
            [
                'title' =>
                    'Pengaturan Denda',

                'versiList' =>
                    $versiList,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(): string
    {
        return view(
            'aturan_denda/create',
            [
                'title' =>
                    'Buat Versi Aturan Denda',
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function store(): RedirectResponse
    {
        $data =
            $this->getVersionFormData();


        $rules =
            $this->request->getPost('rules');


        if (
            ! is_array($rules)
            || empty($rules)
        ) {

            return $this->backWithInput()
                ->with(
                    'error',
                    'Minimal satu rentang aturan denda harus diisi.'
                );
        }


        /*
         * Semua versi baru selalu dibuat sebagai DRAFT.
         */

        $data['status'] =
            AturanDendaVersiModel::STATUS_DRAFT;


        /*
         * Validasi model.
         */

        if (
            ! $this->versiModel->validate(
                $data
            )
        ) {

            return $this->backWithInput();
        }


        /*
         * Validasi periode dasar.
         */

        $periodError =
            $this->versiModel->validatePeriod(
                $data['tanggal_mulai'],
                $data['tanggal_selesai']
            );


        if (
            $periodError !== null
        ) {

            return $this->backWithInput()
                ->with(
                    'error',
                    $periodError
                );
        }


        /*
         * Draft tidak boleh overlap dengan
         * versi AKTIF atau SELESAI.
         */

        $overlap =
            $this->versiModel
                ->findOverlappingVersion(
                    $data['tanggal_mulai'],
                    $data['tanggal_selesai']
                );


        if (
            $overlap !== null
        ) {

            return $this->backWithInput()
                ->with(
                    'error',
                    $this->buildOverlapMessage(
                        $overlap
                    )
                );
        }


        /*
         * Notice period tetap dipertahankan
         * untuk pembuatan versi baru.
         */

        if (
            ! $this->canCreateVersionOnDate(
                $data['tanggal_mulai']
            )
        ) {

            return $this->backWithInput()
                ->with(
                    'error',
                    'Versi baru harus memiliki tanggal mulai minimal 30 hari dari hari ini.'
                );
        }


        /*
         * Validasi rentang.
         */

        $normalizedRules =
            $this->normalizeRules(
                $rules
            );


        $ruleError =
            $this->validateRules(
                $normalizedRules
            );


        if (
            $ruleError !== null
        ) {

            return $this->backWithInput()
                ->with(
                    'error',
                    $ruleError
                );
        }


        /*
         * Generate kode versi.
         */

        $data['kode_versi'] =
            $this->generateVersionCode();


        $data['created_by'] =
            $this->currentUserId();


        /*
         * Transaction.
         */

        $db = db_connect();

        $db->transStart();


        /*
         * Simpan versi.
         */

        $versiId =
            $this->versiModel->insert(
                $data,
                true
            );


        if (
            $versiId === false
        ) {

            $db->transRollback();

            return $this->backWithInput();
        }


        /*
         * PENTING:
         *
         * Tidak ada closePreviousVersion().
         *
         * Kita tidak boleh mengubah periode
         * versi aktif/selesai sebelumnya.
         */


        /*
         * Simpan rentang.
         */

        foreach (
            $normalizedRules as $rule
        ) {

            $rule['versi_id'] =
                (int) $versiId;

            $rule['created_by'] =
                $this->currentUserId();


            if (
                ! $this->aturanModel->insert(
                    $rule
                )
            ) {

                $db->transRollback();

                return $this->backWithInput()
                    ->with(
                        'error',
                        'Salah satu rentang aturan gagal disimpan.'
                    );
            }
        }


        $db->transComplete();


        if (
            ! $db->transStatus()
        ) {

            return $this->redirectError(
                'pengaturan/aturan-denda',
                'Versi aturan denda gagal disimpan.'
            );
        }


        return $this->redirectSuccess(
            'pengaturan/aturan-denda',
            'Draft versi aturan denda berhasil dibuat.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Detail
    |--------------------------------------------------------------------------
    */

    public function detail(
        int $id
    ): RedirectResponse|string {

        $versi =
            $this->versiModel->getById(
                $id
            );


        if (
            $versi === null
        ) {

            return $this->redirectError(
                'pengaturan/aturan-denda',
                'Versi aturan denda tidak ditemukan.'
            );
        }


        $aturan =
            $this->aturanModel->getByVersionId(
                $id
            );


        return view(
            'aturan_denda/detail',
            [
                'title' =>
                    'Detail Versi Aturan Denda',

                'versi' =>
                    $versi,

                'aturan' =>
                    $aturan,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    public function edit(
        int $id
    ): RedirectResponse|string {

        $versi =
            $this->versiModel->getById(
                $id
            );


        if (
            $versi === null
        ) {

            return $this->redirectError(
                'pengaturan/aturan-denda',
                'Versi aturan denda tidak ditemukan.'
            );
        }


        /*
         * Hanya DRAFT yang boleh diedit.
         */

        if (
            ! $this->versiModel->isDraft(
                $versi
            )
        ) {

            return $this->redirectError(
                'pengaturan/aturan-denda',
                'Versi aktif dan selesai tidak dapat diedit. Buat versi baru untuk perubahan aturan.'
            );
        }


        $aturan =
            $this->aturanModel->getByVersionId(
                $id
            );


        return view(
            'aturan_denda/edit',
            [
                'title' =>
                    'Edit Versi Aturan Denda',

                'versi' =>
                    $versi,

                'aturan' =>
                    $aturan,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(
        int $id
    ): RedirectResponse {

        $versi =
            $this->versiModel->getById(
                $id
            );


        if (
            $versi === null
        ) {

            return $this->redirectError(
                'pengaturan/aturan-denda',
                'Versi aturan denda tidak ditemukan.'
            );
        }


        /*
         * ================================================================
         * HARD LOCK
         * ================================================================
         *
         * Hanya DRAFT yang boleh masuk proses update.
         */

        if (
            ! $this->versiModel->isDraft(
                $versi
            )
        ) {

            return $this->redirectError(
                'pengaturan/aturan-denda',
                'Versi aktif dan selesai tidak dapat diubah.'
            );
        }


        $data =
            $this->getVersionFormData();


        $rules =
            $this->request->getPost('rules');


        if (
            ! is_array($rules)
            || empty($rules)
        ) {

            return $this->backWithInput()
                ->with(
                    'error',
                    'Minimal satu rentang aturan denda harus diisi.'
                );
        }


        /*
         * Status tidak boleh diubah melalui form edit.
         *
         * Tetap DRAFT.
         */

        $data['status'] =
            AturanDendaVersiModel::STATUS_DRAFT;


        /*
         * Validasi model.
         */

        if (
            ! $this->versiModel->validate(
                $data
            )
        ) {

            return $this->backWithInput();
        }


        /*
         * Validasi periode.
         */

        $periodError =
            $this->versiModel->validatePeriod(
                $data['tanggal_mulai'],
                $data['tanggal_selesai']
            );


        if (
            $periodError !== null
        ) {

            return $this->backWithInput()
                ->with(
                    'error',
                    $periodError
                );
        }


        /*
         * ================================================================
         * VALIDASI OVERLAP
         * ================================================================
         *
         * Draft boleh digeser ke mana pun selama tidak overlap
         * dengan versi AKTIF atau SELESAI.
         */

        $overlap =
            $this->versiModel
                ->findOverlappingVersion(
                    $data['tanggal_mulai'],
                    $data['tanggal_selesai'],
                    $id
                );


        if (
            $overlap !== null
        ) {

            return $this->backWithInput()
                ->with(
                    'error',
                    $this->buildOverlapMessage(
                        $overlap
                    )
                );
        }


        /*
         * Validasi rentang.
         */

        $normalizedRules =
            $this->normalizeRules(
                $rules
            );


        $ruleError =
            $this->validateRules(
                $normalizedRules
            );


        if (
            $ruleError !== null
        ) {

            return $this->backWithInput()
                ->with(
                    'error',
                    $ruleError
                );
        }


        /*
         * Audit.
         */

        $data['updated_by'] =
            $this->currentUserId();


        $db = db_connect();

        $db->transStart();


        /*
         * Update draft.
         *
         * Tidak menyentuh versi lain.
         */

        if (
            ! $this->versiModel->update(
                $id,
                $data
            )
        ) {

            $db->transRollback();

            return $this->backWithInput();
        }


        /*
         * Hapus rentang lama.
         *
         * Karena status masih DRAFT,
         * perubahan aturan masih diperbolehkan.
         */

        if (
            ! $this->aturanModel
                ->where(
                    'versi_id',
                    $id
                )
                ->delete()
        ) {

            $db->transRollback();

            return $this->backWithInput()
                ->with(
                    'error',
                    'Rentang aturan lama gagal diperbarui.'
                );
        }


        /*
         * Simpan rentang baru.
         */

        foreach (
            $normalizedRules as $rule
        ) {

            $rule['versi_id'] =
                $id;

            $rule['created_by'] =
                $this->currentUserId();


            if (
                ! $this->aturanModel->insert(
                    $rule
                )
            ) {

                $db->transRollback();

                return $this->backWithInput()
                    ->with(
                        'error',
                        'Salah satu rentang aturan gagal disimpan.'
                    );
            }
        }


        $db->transComplete();


        if (
            ! $db->transStatus()
        ) {

            return $this->redirectError(
                'pengaturan/aturan-denda',
                'Versi aturan denda gagal diperbarui.'
            );
        }


        return $this->redirectSuccess(
            'pengaturan/aturan-denda',
            'Draft versi aturan denda berhasil diperbarui.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function delete(
        int $id
    ): RedirectResponse {

        $versi =
            $this->versiModel->getById(
                $id
            );


        if (
            $versi === null
        ) {

            return $this->redirectError(
                'pengaturan/aturan-denda',
                'Versi aturan denda tidak ditemukan.'
            );
        }


        /*
         * Hanya DRAFT yang boleh dihapus.
         */

        if (
            ! $this->versiModel->isDraft(
                $versi
            )
        ) {

            return $this->redirectError(
                'pengaturan/aturan-denda',
                'Versi aktif dan selesai tidak dapat dihapus.'
            );
        }


        $db = db_connect();

        $db->transStart();


        /*
         * Audit deleted_by.
         */

        if (
            ! $this->versiModel->update(
                $id,
                [
                    'deleted_by' =>
                        $this->currentUserId(),
                ]
            )
        ) {

            $db->transRollback();

            return $this->redirectError(
                'pengaturan/aturan-denda',
                'Audit penghapusan versi gagal disimpan.'
            );
        }


        /*
         * Soft delete versi.
         */

        if (
            ! $this->versiModel->delete(
                $id
            )
        ) {

            $db->transRollback();

            return $this->redirectError(
                'pengaturan/aturan-denda',
                'Draft aturan gagal dinonaktifkan.'
            );
        }


        /*
         * Soft delete rentang.
         */

        if (
            ! $this->aturanModel
                ->where(
                    'versi_id',
                    $id
                )
                ->delete()
        ) {

            $db->transRollback();

            return $this->redirectError(
                'pengaturan/aturan-denda',
                'Rentang aturan gagal dinonaktifkan.'
            );
        }


        $db->transComplete();


        if (
            ! $db->transStatus()
        ) {

            return $this->redirectError(
                'pengaturan/aturan-denda',
                'Draft aturan gagal dinonaktifkan.'
            );
        }


        return $this->redirectSuccess(
            'pengaturan/aturan-denda',
            'Draft aturan denda berhasil dinonaktifkan.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Form Data
    |--------------------------------------------------------------------------
    */

    protected function getVersionFormData(): array
    {
        return [
            'nama_versi' =>
                trim(
                    (string) $this->request
                        ->getPost(
                            'nama_versi'
                        )
                ),

            'tanggal_mulai' =>
                $this->request
                    ->getPost(
                        'tanggal_mulai'
                    ),

            'tanggal_selesai' =>
                $this->request
                    ->getPost(
                        'tanggal_selesai'
                    )
                    ?: null,

            'keterangan' =>
                trim(
                    (string) $this->request
                        ->getPost(
                            'keterangan'
                        )
                ),
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Overlap Message
    |--------------------------------------------------------------------------
    */

    protected function buildOverlapMessage(
        array $versi
    ): string {

        $status =
            ucfirst(
                (string) $versi['status']
            );


        $mulai =
            $versi['tanggal_mulai'];


        $selesai =
            $versi['tanggal_selesai']
            ?? 'tanpa batas';


        return sprintf(
            'Periode bertabrakan dengan versi %s (%s), periode %s s/d %s.',
            $versi['kode_versi'],
            $status,
            $mulai,
            $selesai
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Normalize Rules
    |--------------------------------------------------------------------------
    */

    protected function normalizeRules(
        array $rules
    ): array {

        $result = [];


        foreach (
            $rules as $rule
        ) {

            $max =
                $rule['max_nominal']
                ?? null;


            $result[] = [

                'nama_aturan' =>
                    trim(
                        (string) (
                            $rule[
                                'nama_aturan'
                            ] ?? ''
                        )
                    ),

                'min_nominal' =>
                    (float) (
                        $rule[
                            'min_nominal'
                        ] ?? 0
                    ),

                'max_nominal' =>
                    $max === ''
                    || $max === null
                        ? null
                        : (float) $max,

                'persentase_denda' =>
                    (float) (
                        $rule[
                            'persentase_denda'
                        ] ?? 0
                    ),

                'periode_hari' =>
                    (int) (
                        $rule[
                            'periode_hari'
                        ] ?? 0
                    ),

                'maksimal_denda_persen' =>
                    (float) (
                        $rule[
                            'maksimal_denda_persen'
                        ] ?? 0
                    ),

                'keterangan' =>
                    trim(
                        (string) (
                            $rule[
                                'keterangan'
                            ] ?? ''
                        )
                    ),
            ];
        }


        return $result;
    }


    /*
    |--------------------------------------------------------------------------
    | Validate Rules
    |--------------------------------------------------------------------------
    */

    protected function validateRules(
        array $rules
    ): ?string {

        foreach (
            $rules as $index => $rule
        ) {

            $number =
                $index + 1;


            if (
                $rule['min_nominal'] <= 0
            ) {

                return
                    "Rentang #{$number}: minimal nominal harus lebih dari 0.";
            }


            if (
                $rule['max_nominal'] !== null
                && $rule['max_nominal']
                    <= $rule['min_nominal']
            ) {

                return
                    "Rentang #{$number}: maksimal nominal harus lebih besar dari minimal nominal.";
            }


            if (
                $rule['persentase_denda'] <= 0
                || $rule['persentase_denda'] > 100
            ) {

                return
                    "Rentang #{$number}: persentase denda harus antara 0 dan 100%.";
            }


            if (
                $rule['periode_hari'] <= 0
            ) {

                return
                    "Rentang #{$number}: periode denda harus lebih dari 0 hari.";
            }


            if (
                $rule['maksimal_denda_persen'] <= 0
                || $rule['maksimal_denda_persen'] > 100
            ) {

                return
                    "Rentang #{$number}: maksimal denda harus antara 0 dan 100%.";
            }
        }


        /*
         * Cek overlap antar rentang dalam satu versi.
         */

        $count =
            count($rules);


        for (
            $i = 0;
            $i < $count;
            $i++
        ) {

            for (
                $j = $i + 1;
                $j < $count;
                $j++
            ) {

                if (
                    $this->rangesOverlap(
                        $rules[$i]['min_nominal'],
                        $rules[$i]['max_nominal'],
                        $rules[$j]['min_nominal'],
                        $rules[$j]['max_nominal']
                    )
                ) {

                    return sprintf(
                        'Rentang #%d dan Rentang #%d saling bertabrakan.',
                        $i + 1,
                        $j + 1
                    );
                }
            }
        }


        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | Range Overlap
    |--------------------------------------------------------------------------
    */

    protected function rangesOverlap(
        float|int|string $minA,
        float|int|string|null $maxA,
        float|int|string $minB,
        float|int|string|null $maxB
    ): bool {

        $minA =
            (float) $minA;

        $minB =
            (float) $minB;


        $maxA =
            $maxA === null
            || $maxA === ''
                ? null
                : (float) $maxA;


        $maxB =
            $maxB === null
            || $maxB === ''
                ? null
                : (float) $maxB;


        if (
            $maxA === null
        ) {

            return
                $maxB === null
                || $maxB >= $minA;
        }


        if (
            $maxB === null
        ) {

            return
                $maxA >= $minB;
        }


        return
            $minA <= $maxB
            && $maxA >= $minB;
    }


    /*
    |--------------------------------------------------------------------------
    | Generate Version Code
    |--------------------------------------------------------------------------
    */

    protected function generateVersionCode(): string
    {
        $last =
            $this->versiModel
                ->orderBy(
                    'id',
                    'DESC'
                )
                ->first();


        $number = 1;


        if (
            $last !== null
        ) {

            preg_match(
                '/(\d+)$/',
                (string) $last['kode_versi'],
                $matches
            );


            if (
                ! empty($matches[1])
            ) {

                $number =
                    ((int) $matches[1]) + 1;
            }
        }


        return sprintf(
            'DENDA-V%03d',
            $number
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Minimum Notice
    |--------------------------------------------------------------------------
    */

    protected function canCreateVersionOnDate(
        string $tanggalMulai
    ): bool {

        /*
         * Kalau belum ada versi sama sekali,
         * versi pertama boleh dibuat.
         */

        if (
            $this->versiModel
                ->countAllResults() === 0
        ) {

            return true;
        }


        $minimumDate =
            date(
                'Y-m-d',
                strtotime(
                    '+30 days'
                )
            );


        return
            $tanggalMulai >= $minimumDate;
    }
}