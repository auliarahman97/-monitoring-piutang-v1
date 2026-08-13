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
        $this->versiModel = new AturanDendaVersiModel();
        $this->aturanModel = new AturanDendaModel();
    }

    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    public function index(): string
    {
        $versiList = $this->versiModel->getAllVersions();

        foreach ($versiList as $key => $versi) {
            $versiList[$key]['aturan'] =
                $this->aturanModel->getByVersionId(
                    (int) $versi['id']
                );
        }

        return view('aturan_denda/index', [
            'title'     => 'Pengaturan Denda',
            'versiList' => $versiList,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(): string
    {
        return view('aturan_denda/create', [
            'title' => 'Buat Versi Aturan Denda',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function store(): RedirectResponse
    {
        $data = $this->getVersionFormData();

        $rules = $this->request->getPost('rules');

        if (! is_array($rules) || empty($rules)) {
            return $this->backWithInput()
                ->with(
                    'error',
                    'Minimal satu rentang aturan denda harus diisi.'
                );
        }

        /*
         * Semua versi baru selalu dibuat sebagai DRAFT.
         */
        $data['status'] = AturanDendaVersiModel::STATUS_DRAFT;

        /*
         * Validasi dasar model.
         */
        if (! $this->versiModel->validate($data)) {
            return $this->backWithInput();
        }

        /*
         * Validasi periode.
         */
        if (! $this->versiModel->isValidPeriod(
            $data['tanggal_mulai'],
            $data['tanggal_selesai']
        )) {
            return $this->backWithInput()
                ->with(
                    'error',
                    'Tanggal selesai tidak boleh lebih kecil dari tanggal mulai.'
                );
        }

        /*
         * Draft tidak boleh overlap dengan versi
         * AKTIF atau SELESAI.
         */
        $overlap = $this->versiModel->hasPeriodOverlap(
            $data['tanggal_mulai'],
            $data['tanggal_selesai']
        );

        if ($overlap !== null) {
            return $this->backWithInput()
                ->with(
                    'error',
                    $this->buildOverlapMessage($overlap)
                );
        }

        /*
         * Notice period.
         *
         * Versi pertama boleh dibuat kapan saja.
         * Versi berikutnya minimal 30 hari dari hari ini.
         */
        if (! $this->canCreateVersionOnDate(
            $data['tanggal_mulai']
        )) {
            return $this->backWithInput()
                ->with(
                    'error',
                    'Versi baru harus memiliki tanggal mulai minimal 30 hari dari hari ini.'
                );
        }

        /*
         * Normalisasi dan validasi rules.
         */
        $normalizedRules = $this->normalizeRules($rules);

        $ruleError = $this->validateRules($normalizedRules);

        if ($ruleError !== null) {
            return $this->backWithInput()
                ->with(
                    'error',
                    $ruleError
                );
        }

        /*
         * Generate kode versi.
         */
        $data['kode_versi'] = $this->generateVersionCode();

        $data['created_by'] = $this->currentUserId();

        /*
         * Transaction.
         */
        $db = db_connect();

        $db->transStart();

        $versiId = $this->versiModel->insert(
            $data,
            true
        );

        if ($versiId === false) {
            $db->transRollback();

            return $this->backWithInput();
        }

        /*
         * Simpan seluruh rules.
         */
        foreach ($normalizedRules as $rule) {
            $rule['versi_id'] = (int) $versiId;
            $rule['created_by'] = $this->currentUserId();

            if (! $this->aturanModel->insert($rule)) {
                $db->transRollback();

                return $this->backWithInput()
                    ->with(
                        'error',
                        'Salah satu rentang aturan gagal disimpan.'
                    );
            }
        }

        $db->transComplete();

        if (! $db->transStatus()) {
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

    public function detail(int $id): RedirectResponse|string
    {
        $versi = $this->versiModel->findVersion($id);

        if ($versi === null) {
            return $this->redirectError(
                'pengaturan/aturan-denda',
                'Versi aturan denda tidak ditemukan.'
            );
        }

        $aturan = $this->aturanModel->getByVersionId($id);

        return view('aturan_denda/detail', [
            'title'  => 'Detail Versi Aturan Denda',
            'versi'  => $versi,
            'aturan' => $aturan,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    public function edit(int $id): RedirectResponse|string
    {
        $versi = $this->versiModel->findVersion($id);

        if ($versi === null) {
            return $this->redirectError(
                'pengaturan/aturan-denda',
                'Versi aturan denda tidak ditemukan.'
            );
        }

        /*
         * Hanya DRAFT yang boleh diedit.
         */
        if (! $this->versiModel->isDraft($versi)) {
            return $this->redirectError(
                'pengaturan/aturan-denda',
                'Versi aktif dan selesai tidak dapat diedit. Buat versi baru untuk perubahan aturan.'
            );
        }

        $aturan = $this->aturanModel->getByVersionId($id);

        return view('aturan_denda/edit', [
            'title'  => 'Edit Versi Aturan Denda',
            'versi'  => $versi,
            'aturan' => $aturan,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(int $id): RedirectResponse
    {
        $versi = $this->versiModel->findVersion($id);

        if ($versi === null) {
            return $this->redirectError(
                'pengaturan/aturan-denda',
                'Versi aturan denda tidak ditemukan.'
            );
        }

        /*
         * HARD LOCK:
         * hanya DRAFT yang boleh diubah.
         */
        if (! $this->versiModel->isDraft($versi)) {
            return $this->redirectError(
                'pengaturan/aturan-denda',
                'Versi aktif dan selesai tidak dapat diubah.'
            );
        }

        $data = $this->getVersionFormData();

        $rules = $this->request->getPost('rules');

        if (! is_array($rules) || empty($rules)) {
            return $this->backWithInput()
                ->with(
                    'error',
                    'Minimal satu rentang aturan denda harus diisi.'
                );
        }

        /*
         * Status tetap DRAFT.
         *
         * User tidak boleh mengubah status melalui form edit.
         */
        $data['status'] = AturanDendaVersiModel::STATUS_DRAFT;

        /*
         * Validasi model.
         */
        if (! $this->versiModel->validate($data)) {
            return $this->backWithInput();
        }

        /*
         * Validasi periode.
         */
        if (! $this->versiModel->isValidPeriod(
            $data['tanggal_mulai'],
            $data['tanggal_selesai']
        )) {
            return $this->backWithInput()
                ->with(
                    'error',
                    'Tanggal selesai tidak boleh lebih kecil dari tanggal mulai.'
                );
        }

        /*
         * Cek overlap.
         *
         * Versi yang sedang diedit dikecualikan.
         */
        $overlap = $this->versiModel->hasPeriodOverlap(
            $data['tanggal_mulai'],
            $data['tanggal_selesai'],
            $id
        );

        if ($overlap !== null) {
            return $this->backWithInput()
                ->with(
                    'error',
                    $this->buildOverlapMessage($overlap)
                );
        }

        /*
         * Normalisasi dan validasi rules.
         */
        $normalizedRules = $this->normalizeRules($rules);

        $ruleError = $this->validateRules($normalizedRules);

        if ($ruleError !== null) {
            return $this->backWithInput()
                ->with(
                    'error',
                    $ruleError
                );
        }

        /*
         * Audit.
         */
        $data['updated_by'] = $this->currentUserId();

        $db = db_connect();

        $db->transStart();

        /*
         * Update versi.
         */
        if (! $this->versiModel->update($id, $data)) {
            $db->transRollback();

            return $this->backWithInput();
        }

        /*
         * Replace seluruh rules.
         *
         * Karena versi masih DRAFT, seluruh rentang
         * masih boleh diganti.
         */
        if (! $this->aturanModel
            ->where('versi_id', $id)
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
         * Simpan rules baru.
         */
        foreach ($normalizedRules as $rule) {
            $rule['versi_id'] = $id;
            $rule['created_by'] = $this->currentUserId();

            if (! $this->aturanModel->insert($rule)) {
                $db->transRollback();

                return $this->backWithInput()
                    ->with(
                        'error',
                        'Salah satu rentang aturan gagal disimpan.'
                    );
            }
        }

        $db->transComplete();

        if (! $db->transStatus()) {
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

    public function delete(int $id): RedirectResponse
    {
        $versi = $this->versiModel->findVersion($id);

        if ($versi === null) {
            return $this->redirectError(
                'pengaturan/aturan-denda',
                'Versi aturan denda tidak ditemukan.'
            );
        }

        /*
         * Hanya DRAFT yang boleh dihapus.
         */
        if (! $this->versiModel->isDraft($versi)) {
            return $this->redirectError(
                'pengaturan/aturan-denda',
                'Versi aktif dan selesai tidak dapat dihapus.'
            );
        }

        $currentUserId = $this->currentUserId();

        $db = db_connect();

        $db->transStart();

        /*
         * Audit penghapusan seluruh rules.
         */
        $rules = $this->aturanModel->getByVersionId($id);

        foreach ($rules as $rule) {
            if (! $this->aturanModel->update(
                (int) $rule['id'],
                [
                    'deleted_by' => $currentUserId,
                ]
            )) {
                $db->transRollback();

                return $this->redirectError(
                    'pengaturan/aturan-denda',
                    'Audit penghapusan rentang aturan gagal disimpan.'
                );
            }
        }

        /*
         * Soft delete rules.
         */
        if (! $this->aturanModel
            ->where('versi_id', $id)
            ->delete()
        ) {
            $db->transRollback();

            return $this->redirectError(
                'pengaturan/aturan-denda',
                'Rentang aturan gagal dinonaktifkan.'
            );
        }

        /*
         * Audit penghapusan versi.
         */
        if (! $this->versiModel->update(
            $id,
            [
                'deleted_by' => $currentUserId,
            ]
        )) {
            $db->transRollback();

            return $this->redirectError(
                'pengaturan/aturan-denda',
                'Audit penghapusan versi gagal disimpan.'
            );
        }

        /*
         * Soft delete versi.
         */
        if (! $this->versiModel->delete($id)) {
            $db->transRollback();

            return $this->redirectError(
                'pengaturan/aturan-denda',
                'Draft aturan gagal dinonaktifkan.'
            );
        }

        $db->transComplete();

        if (! $db->transStatus()) {
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
            'nama_versi' => trim(
                (string) $this->request->getPost('nama_versi')
            ),

            'tanggal_mulai' => (string) $this->request
                ->getPost('tanggal_mulai'),

            'tanggal_selesai' => $this->request
                ->getPost('tanggal_selesai')
                ?: null,

            'keterangan' => trim(
                (string) $this->request->getPost('keterangan')
            ),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Overlap Message
    |--------------------------------------------------------------------------
    */

    protected function buildOverlapMessage(array $versi): string
    {
        $status = ucfirst(
            (string) ($versi['status'] ?? '')
        );

        $mulai = $versi['tanggal_mulai'] ?? '-';

        $selesai = $versi['tanggal_selesai']
            ?? 'tanpa batas';

        return sprintf(
            'Periode bertabrakan dengan versi %s (%s), periode %s s/d %s.',
            $versi['kode_versi'] ?? '-',
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

    protected function normalizeRules(array $rules): array
    {
        $result = [];

        foreach ($rules as $rule) {
            if (! is_array($rule)) {
                continue;
            }

            $max = $rule['max_nominal'] ?? null;

            $result[] = [
                'nama_aturan' => trim(
                    (string) ($rule['nama_aturan'] ?? '')
                ),

                'min_nominal' => (float) (
                    $rule['min_nominal'] ?? 0
                ),

                'max_nominal' => (
                    $max === ''
                    || $max === null
                )
                    ? null
                    : (float) $max,

                'persentase_denda' => (float) (
                    $rule['persentase_denda'] ?? 0
                ),

                'periode_hari' => (int) (
                    $rule['periode_hari'] ?? 0
                ),

                'maksimal_denda_persen' => (float) (
                    $rule['maksimal_denda_persen'] ?? 0
                ),

                'keterangan' => trim(
                    (string) ($rule['keterangan'] ?? '')
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

    protected function validateRules(array $rules): ?string
    {
        foreach ($rules as $index => $rule) {
            $number = $index + 1;

            if ($rule['nama_aturan'] === '') {
                return "Rentang #{$number}: nama aturan wajib diisi.";
            }

            if ($rule['min_nominal'] <= 0) {
                return "Rentang #{$number}: minimal nominal harus lebih dari 0.";
            }

            if (
                $rule['max_nominal'] !== null
                && $rule['max_nominal'] <= $rule['min_nominal']
            ) {
                return "Rentang #{$number}: maksimal nominal harus lebih besar dari minimal nominal.";
            }

            if (
                $rule['persentase_denda'] <= 0
                || $rule['persentase_denda'] > 100
            ) {
                return "Rentang #{$number}: persentase denda harus antara 0 dan 100%.";
            }

            if ($rule['periode_hari'] <= 0) {
                return "Rentang #{$number}: periode denda harus lebih dari 0 hari.";
            }

            if (
                $rule['maksimal_denda_persen'] <= 0
                || $rule['maksimal_denda_persen'] > 100
            ) {
                return "Rentang #{$number}: maksimal denda harus antara 0 dan 100%.";
            }
        }

        /*
         * Minimal satu rule harus tersedia.
         */
        if (empty($rules)) {
            return 'Minimal satu rentang aturan denda harus diisi.';
        }

        /*
         * Rules diurutkan berdasarkan minimal nominal
         * sebelum pengecekan overlap.
         */
        usort(
            $rules,
            static function (array $a, array $b): int {
                return $a['min_nominal'] <=> $b['min_nominal'];
            }
        );

        $count = count($rules);

        for ($i = 0; $i < $count - 1; $i++) {
            $current = $rules[$i];
            $next = $rules[$i + 1];

            /*
             * Jika current tidak memiliki batas atas,
             * maka semua rule setelahnya pasti overlap.
             */
            if ($current['max_nominal'] === null) {
                return sprintf(
                    'Rentang #%d tidak boleh menjadi rentang terakhir jika masih ada rentang setelahnya.',
                    $i + 1
                );
            }

            if (
                $current['max_nominal']
                >= $next['min_nominal']
            ) {
                return sprintf(
                    'Rentang #%d dan Rentang #%d saling bertabrakan.',
                    $i + 1,
                    $i + 2
                );
            }
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | Generate Version Code
    |--------------------------------------------------------------------------
    */

    protected function generateVersionCode(): string
    {
        $last = $this->versiModel
            ->withDeleted()
            ->orderBy('id', 'DESC')
            ->first();

        $number = 1;

        if ($last !== null) {
            preg_match(
                '/(\d+)$/',
                (string) ($last['kode_versi'] ?? ''),
                $matches
            );

            if (! empty($matches[1])) {
                $number = ((int) $matches[1]) + 1;
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
         * Versi pertama boleh dibuat kapan saja.
         */
        if (
            $this->versiModel
                ->countAllResults() === 0
        ) {
            return true;
        }

        $minimumDate = date(
            'Y-m-d',
            strtotime('+30 days')
        );

        return $tanggalMulai >= $minimumDate;
    }
}