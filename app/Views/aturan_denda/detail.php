<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<?php

/*
|--------------------------------------------------------------------------
| DATA VERSI
|--------------------------------------------------------------------------
*/

$versiData = $versi ?? [];

$aturanData = $aturan ?? [];


/*
|--------------------------------------------------------------------------
| STATUS
|--------------------------------------------------------------------------
|
| Status ditentukan oleh periode berlaku versi:
|
| - akan_datang : belum mulai
| - aktif       : sedang berlaku
| - selesai     : sudah berakhir
|
*/

$status = $versiData['status'] ?? '';

$statusMeta = match ($status) {

    'aktif' => [
        'label' => 'Aktif',
        'badge' => 'success',
        'icon'  => 'fa-check-circle',
        'text'  => 'Versi ini sedang berlaku.',
    ],

    'akan_datang' => [
        'label' => 'Akan Datang',
        'badge' => 'warning',
        'icon'  => 'fa-clock',
        'text'  => 'Versi ini belum mulai berlaku.',
    ],

    'selesai' => [
        'label' => 'Selesai',
        'badge' => 'secondary',
        'icon'  => 'fa-history',
        'text'  => 'Masa berlaku versi ini telah selesai.',
    ],

    default => [
        'label' => 'Tidak Diketahui',
        'badge' => 'dark',
        'icon'  => 'fa-question-circle',
        'text'  => 'Status versi tidak dapat ditentukan.',
    ],

};


/*
|--------------------------------------------------------------------------
| TANGGAL
|--------------------------------------------------------------------------
*/

$tanggalMulai = ! empty(
    $versiData['tanggal_mulai']
)
    ? date(
        'd-m-Y',
        strtotime(
            $versiData['tanggal_mulai']
        )
    )
    : '-';


$tanggalSelesai = ! empty(
    $versiData['tanggal_selesai']
)
    ? date(
        'd-m-Y',
        strtotime(
            $versiData['tanggal_selesai']
        )
    )
    : null;


/*
|--------------------------------------------------------------------------
| JUMLAH RENTANG
|--------------------------------------------------------------------------
*/

$jumlahAturan = count($aturanData);


/*
|--------------------------------------------------------------------------
| FORMAT NOMINAL
|--------------------------------------------------------------------------
*/

$formatNominal = static function ($value): string {

    if (
        $value === null ||
        $value === ''
    ) {
        return 'Tanpa Batas';
    }

    return 'Rp ' . number_format(
        (float) $value,
        0,
        ',',
        '.'
    );
};


/*
|--------------------------------------------------------------------------
| FORMAT PERSENTASE
|--------------------------------------------------------------------------
*/

$formatPersentase = static function ($value): string {

    return number_format(
        (float) ($value ?? 0),
        2,
        ',',
        '.'
    ) . '%';

};

?>

<div class="container-fluid">

    <!-- ================================================================
         HEADER
    ================================================================= -->

    <div
        class="d-flex justify-content-between align-items-center mb-4"
    >

        <div>

            <div
                class="d-flex align-items-center flex-wrap mb-1"
            >

                <h4 class="mb-0 mr-2">

                    <i
                        class="fas fa-layer-group text-primary mr-1"
                    ></i>

                    Detail Versi Aturan Denda

                </h4>


                <span
                    class="badge badge-<?= esc(
                        $statusMeta['badge']
                    ) ?>"
                >

                    <i
                        class="fas <?= esc(
                            $statusMeta['icon']
                        ) ?> mr-1"
                    ></i>

                    <?= esc(
                        $statusMeta['label']
                    ) ?>

                </span>

            </div>


            <small class="text-muted">

                Detail kebijakan denda beserta seluruh
                rentang nominal yang berlaku pada versi ini.

            </small>

        </div>


        <div>

            <a
                href="<?= site_url(
                    'pengaturan/aturan-denda'
                ) ?>"
                class="btn btn-secondary"
            >

                <i class="fas fa-arrow-left mr-1"></i>

                Kembali

            </a>

        </div>

    </div>


    <!-- ================================================================
         STATUS INFORMATION
    ================================================================= -->

    <div
        class="alert alert-<?= esc(
            $statusMeta['badge'] === 'secondary'
                ? 'secondary'
                : $statusMeta['badge']
        ) ?> shadow-sm"
    >

        <div class="d-flex align-items-start">

            <div class="mr-3">

                <i
                    class="fas <?= esc(
                        $statusMeta['icon']
                    ) ?> fa-lg mt-1"
                ></i>

            </div>


            <div>

                <strong>
                    Status: <?= esc(
                        $statusMeta['label']
                    ) ?>
                </strong>

                <div class="mt-1">

                    <?= esc(
                        $statusMeta['text']
                    ) ?>

                </div>

            </div>

        </div>

    </div>


    <!-- ================================================================
         INFORMASI VERSI
    ================================================================= -->

    <div class="card shadow-sm mb-4">

        <div class="card-header">

            <h5 class="card-title mb-0">

                <i
                    class="fas fa-file-alt text-primary mr-1"
                ></i>

                Informasi Versi

            </h5>

        </div>


        <div class="card-body">

            <div class="row">

                <!-- ====================================================
                     KODE VERSI
                ===================================================== -->

                <div class="col-md-3 mb-4">

                    <small
                        class="text-muted d-block mb-1"
                    >
                        Kode Versi
                    </small>

                    <span
                        class="badge badge-primary"
                        style="font-size: .9rem;"
                    >

                        <i
                            class="fas fa-code-branch mr-1"
                        ></i>

                        <?= esc(
                            $versiData['kode_versi'] ?? '-'
                        ) ?>

                    </span>

                </div>


                <!-- ====================================================
                     NAMA VERSI
                ===================================================== -->

                <div class="col-md-5 mb-4">

                    <small
                        class="text-muted d-block mb-1"
                    >
                        Nama Versi
                    </small>

                    <strong
                        class="d-block"
                        style="font-size: 1rem;"
                    >

                        <?= esc(
                            $versiData['nama_versi'] ?? '-'
                        ) ?>

                    </strong>

                </div>


                <!-- ====================================================
                     JUMLAH RENTANG
                ===================================================== -->

                <div class="col-md-4 mb-4">

                    <small
                        class="text-muted d-block mb-1"
                    >
                        Jumlah Rentang
                    </small>

                    <span class="badge badge-info">

                        <i
                            class="fas fa-list-ol mr-1"
                        ></i>

                        <?= $jumlahAturan ?>

                        Rentang

                    </span>

                </div>


                <!-- ====================================================
                     TANGGAL MULAI
                ===================================================== -->

                <div class="col-md-4">

                    <small
                        class="text-muted d-block mb-1"
                    >
                        Berlaku Mulai
                    </small>

                    <div>

                        <i
                            class="far fa-calendar-alt text-primary mr-1"
                        ></i>

                        <strong>
                            <?= esc(
                                $tanggalMulai
                            ) ?>
                        </strong>

                    </div>

                </div>


                <!-- ====================================================
                     TANGGAL SELESAI
                ===================================================== -->

                <div class="col-md-4">

                    <small
                        class="text-muted d-block mb-1"
                    >
                        Berlaku Sampai
                    </small>

                    <?php if (
                        $tanggalSelesai === null
                    ) : ?>

                        <?php if (
                            $status === 'akan_datang'
                        ) : ?>

                            <div class="text-warning">

                                <i
                                    class="far fa-clock mr-1"
                                ></i>

                                <strong>
                                    Belum dimulai
                                </strong>

                            </div>

                        <?php else : ?>

                            <div class="text-success">

                                <i
                                    class="fas fa-infinity mr-1"
                                ></i>

                                <strong>
                                    Sekarang
                                </strong>

                            </div>

                        <?php endif; ?>

                    <?php else : ?>

                        <div>

                            <i
                                class="far fa-calendar-check text-muted mr-1"
                            ></i>

                            <strong>
                                <?= esc(
                                    $tanggalSelesai
                                ) ?>
                            </strong>

                        </div>

                    <?php endif; ?>

                </div>


                <!-- ====================================================
                     STATUS
                ===================================================== -->

                <div class="col-md-4">

                    <small
                        class="text-muted d-block mb-1"
                    >
                        Status Periode
                    </small>

                    <span
                        class="badge badge-<?= esc(
                            $statusMeta['badge']
                        ) ?>"
                    >

                        <i
                            class="fas <?= esc(
                                $statusMeta['icon']
                            ) ?> mr-1"
                        ></i>

                        <?= esc(
                            $statusMeta['label']
                        ) ?>

                    </span>

                </div>


                <!-- ====================================================
                     KETERANGAN
                ===================================================== -->

                <div class="col-12 mt-3">

                    <div
                        class="border-top pt-3"
                    >

                        <small
                            class="text-muted d-block mb-2"
                        >
                            Keterangan Versi
                        </small>


                        <?php if (
                            ! empty(
                                $versiData['keterangan']
                            )
                        ) : ?>

                            <div>

                                <?= nl2br(
                                    esc(
                                        $versiData[
                                            'keterangan'
                                        ]
                                    )
                                ) ?>

                            </div>

                        <?php else : ?>

                            <span class="text-muted">

                                Tidak ada keterangan.

                            </span>

                        <?php endif; ?>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- ================================================================
         RENTANG ATURAN
    ================================================================= -->

    <div class="card shadow-sm mb-4">

        <div class="card-header">

            <div
                class="d-flex justify-content-between align-items-center"
            >

                <div>

                    <h5 class="card-title mb-0">

                        <i
                            class="fas fa-list-ol text-primary mr-1"
                        ></i>

                        Rentang Aturan Denda

                    </h5>

                    <small class="text-muted">

                        Seluruh rentang nominal yang menjadi
                        bagian dari versi

                        <strong>
                            <?= esc(
                                $versiData[
                                    'kode_versi'
                                ] ?? '-'
                            ) ?>
                        </strong>.

                    </small>

                </div>


                <span class="badge badge-info">

                    <?= $jumlahAturan ?>

                    Rentang

                </span>

            </div>

        </div>


        <div class="card-body">

            <?php if (
                ! empty($aturanData)
            ) : ?>

                <div class="table-responsive">

                    <table
                        id="tableDetailAturanDenda"
                        class="table table-bordered table-hover mb-0"
                        width="100%"
                    >

                        <thead class="thead-light">

                            <tr>

                                <th
                                    width="5%"
                                    class="text-center"
                                >
                                    #
                                </th>

                                <th width="18%">
                                    Nama Aturan
                                </th>

                                <th width="20%">
                                    Rentang Nominal
                                </th>

                                <th
                                    width="12%"
                                    class="text-center"
                                >
                                    Denda
                                </th>

                                <th
                                    width="12%"
                                    class="text-center"
                                >
                                    Periode
                                </th>

                                <th
                                    width="14%"
                                    class="text-center"
                                >
                                    Maksimal Denda
                                </th>

                                <th>
                                    Keterangan
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <?php foreach (
                                $aturanData
                                as $index => $row
                            ) : ?>

                                <tr>

                                    <!-- =================================
                                         NOMOR
                                    ================================== -->

                                    <td
                                        class="text-center align-middle"
                                    >

                                        <?= $index + 1 ?>

                                    </td>


                                    <!-- =================================
                                         NAMA ATURAN
                                    ================================== -->

                                    <td
                                        class="align-middle"
                                    >

                                        <strong>

                                            <?= esc(
                                                $row[
                                                    'nama_aturan'
                                                ] ?? '-'
                                            ) ?>

                                        </strong>

                                    </td>


                                    <!-- =================================
                                         RENTANG NOMINAL
                                    ================================== -->

                                    <td
                                        class="align-middle"
                                    >

                                        <div
                                            class="font-weight-bold"
                                        >

                                            <?= $formatNominal(
                                                $row[
                                                    'min_nominal'
                                                ] ?? null
                                            ) ?>

                                        </div>


                                        <?php
                                        $maxNominal =
                                            $row[
                                                'max_nominal'
                                            ] ?? null;
                                        ?>


                                        <?php if (
                                            $maxNominal !== null
                                            && $maxNominal !== ''
                                        ) : ?>

                                            <small
                                                class="text-muted"
                                            >

                                                s/d

                                                <?= $formatNominal(
                                                    $maxNominal
                                                ) ?>

                                            </small>

                                        <?php else : ?>

                                            <small
                                                class="text-success"
                                            >

                                                <i
                                                    class="fas fa-infinity mr-1"
                                                ></i>

                                                Tanpa batas atas

                                            </small>

                                        <?php endif; ?>

                                    </td>


                                    <!-- =================================
                                         DENDA
                                    ================================== -->

                                    <td
                                        class="text-center align-middle"
                                    >

                                        <span
                                            class="badge badge-warning"
                                        >

                                            <?= $formatPersentase(
                                                $row[
                                                    'persentase_denda'
                                                ] ?? 0
                                            ) ?>

                                        </span>

                                    </td>


                                    <!-- =================================
                                         PERIODE
                                    ================================== -->

                                    <td
                                        class="text-center align-middle"
                                    >

                                        <span
                                            class="text-nowrap"
                                        >

                                            <i
                                                class="far fa-clock text-muted mr-1"
                                            ></i>

                                            <?= number_format(
                                                (int) (
                                                    $row[
                                                        'periode_hari'
                                                    ] ?? 0
                                                ),
                                                0,
                                                ',',
                                                '.'
                                            ) ?>

                                            hari

                                        </span>

                                    </td>


                                    <!-- =================================
                                         MAKSIMAL DENDA
                                    ================================== -->

                                    <td
                                        class="text-center align-middle"
                                    >

                                        <strong>

                                            <?= $formatPersentase(
                                                $row[
                                                    'maksimal_denda_persen'
                                                ] ?? 0
                                            ) ?>

                                        </strong>

                                    </td>


                                    <!-- =================================
                                         KETERANGAN
                                    ================================== -->

                                    <td
                                        class="align-middle"
                                    >

                                        <?php if (
                                            ! empty(
                                                $row[
                                                    'keterangan'
                                                ]
                                            )
                                        ) : ?>

                                            <span
                                                data-toggle="tooltip"
                                                title="<?= esc(
                                                    $row[
                                                        'keterangan'
                                                    ]
                                                ) ?>"
                                            >

                                                <?= esc(
                                                    $row[
                                                        'keterangan'
                                                    ]
                                                ) ?>

                                            </span>

                                        <?php else : ?>

                                            <span
                                                class="text-muted"
                                            >
                                                -
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php else : ?>

                <div
                    class="text-center py-5 text-muted"
                >

                    <i
                        class="fas fa-inbox fa-3x mb-3"
                    ></i>

                    <h6>
                        Belum Ada Rentang Aturan
                    </h6>

                    <p class="mb-0">

                        Versi ini belum memiliki
                        detail aturan denda.

                    </p>

                </div>

            <?php endif; ?>

        </div>

    </div>


    <!-- ================================================================
         CATATAN VERSI
    ================================================================= -->

    <div class="alert alert-light border shadow-sm">

        <div
            class="d-flex align-items-start"
        >

            <div class="mr-3">

                <i
                    class="fas fa-shield-alt text-primary fa-lg"
                ></i>

            </div>


            <div>

                <strong>
                    Catatan Versi Aturan
                </strong>


                <p class="mb-0 mt-1 text-muted">

                    Versi aturan denda merupakan satu kesatuan
                    kebijakan. Ketika versi baru mulai berlaku,
                    versi sebelumnya menjadi histori dan tidak
                    mengubah aturan yang pernah digunakan.

                </p>

            </div>

        </div>

    </div>


    <!-- ================================================================
         ACTION
    ================================================================= -->

    <div
        class="d-flex justify-content-between align-items-center mb-4"
    >

        <a
            href="<?= site_url(
                'pengaturan/aturan-denda'
            ) ?>"
            class="btn btn-secondary"
        >

            <i class="fas fa-arrow-left mr-1"></i>

            Kembali ke Daftar

        </a>


        <?php if (
            $status === 'akan_datang'
        ) : ?>

            <a
                href="<?= site_url(
                    'pengaturan/aturan-denda/edit/' .
                    $versiData['id']
                ) ?>"
                class="btn btn-warning"
            >

                <i class="fas fa-edit mr-1"></i>

                Edit Versi

            </a>

        <?php endif; ?>

    </div>

</div>


<?= $this->endSection() ?>


<?= $this->section('scripts') ?>

<script>

$(document).ready(function () {

    /*
    |--------------------------------------------------------------------------
    | DataTables
    |--------------------------------------------------------------------------
    */

    if (
        $('#tableDetailAturanDenda').length
        &&
        !$.fn.DataTable.isDataTable(
            '#tableDetailAturanDenda'
        )
    ) {

        $('#tableDetailAturanDenda').DataTable({

            responsive: true,

            autoWidth: false,

            paging: false,

            searching: false,

            info: false,

            ordering: false,

            language: {

                zeroRecords:
                    'Data tidak ditemukan',

                emptyTable:
                    'Belum ada rentang aturan'

            }

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Tooltip
    |--------------------------------------------------------------------------
    */

    $('[data-toggle="tooltip"]').tooltip();

});

</script>

<?= $this->endSection() ?>