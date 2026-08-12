<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="container-fluid">


    <!-- ==========================================================
         PAGE HEADER
         ========================================================== -->

    <div class="d-flex flex-column flex-md-row
                align-items-md-center
                justify-content-between
                mb-4">

        <div>

            <h3 class="mb-1">

                <i class="fas fa-file-invoice-dollar
                          text-primary mr-2"></i>

                Laporan Piutang

            </h3>

            <p class="text-muted mb-0">

                Laporan detail posisi piutang customer
                berdasarkan transaksi dan pembayaran yang valid.

            </p>

        </div>


        <!-- ======================================================
             EXPORT PDF
             ====================================================== -->

        <div class="mt-3 mt-md-0">

            <?php

            $pdfParams = [
                'tanggal_dari' =>
                    $filter['tanggal_dari'] ?? '',

                'tanggal_sampai' =>
                    $filter['tanggal_sampai'] ?? '',

                'customer_id' =>
                    $filter['customer_id'] ?? '',

                'status' =>
                    $filter['status'] ?? 'semua',

                'jatuh_tempo' =>
                    $filter['jatuh_tempo'] ?? 'semua',
            ];


            /*
            * Hapus parameter kosong agar URL tetap bersih.
            */

            $pdfParams = array_filter(
                $pdfParams,
                static function ($value) {
                    return $value !== ''
                        && $value !== null;
                }
            );


            $pdfUrl =
                site_url('laporan/piutang/pdf')
                . (
                    ! empty($pdfParams)
                        ? '?' . http_build_query($pdfParams)
                        : ''
                );

            ?>

            <a
                href="<?= esc($pdfUrl) ?>"
                class="btn btn-danger"
            >
                <i class="fas fa-file-pdf mr-1"></i>
                Export PDF
            </a>

        </div>

    </div>


    <!-- ==========================================================
         INFORMATION
         ========================================================== -->

    <div class="alert alert-light border shadow-sm mb-4">

        <div class="d-flex align-items-start">

            <div class="mr-3">

                <i
                    class="fas fa-info-circle
                           text-primary
                           fa-lg"
                ></i>

            </div>


            <div>

                <strong>
                    Posisi Laporan:
                </strong>

                <?= ! empty(
                    $filter['tanggal_laporan']
                )
                    ? date(
                        'd-m-Y',
                        strtotime(
                            $filter['tanggal_laporan']
                        )
                    )
                    : date('d-m-Y')
                ?>


                <div class="small text-muted mt-1">

                    Denda berjalan dihitung berdasarkan
                    tanggal laporan. Pembayaran yang
                    dibatalkan tidak diperhitungkan.

                </div>

            </div>

        </div>

    </div>


    <!-- ==========================================================
         FILTER
         ========================================================== -->

    <div
        class="card
               card-primary
               card-outline
               shadow-sm
               mb-4"
    >

        <div class="card-header">

            <h3 class="card-title">

                <i class="fas fa-filter mr-1"></i>

                Filter Laporan

            </h3>

        </div>


        <div class="card-body">

            <?= $this->include(
                'laporan/piutang/_form'
            ) ?>

        </div>

    </div>


    <!-- ==========================================================
         REPORT TABLE
         ========================================================== -->

    <div class="card shadow-sm">

        <div class="card-header">

            <h3 class="card-title">

                <i class="fas fa-table mr-1"></i>

                Data Piutang

            </h3>


            <div class="card-tools">

                <span class="badge badge-light">

                    <?= number_format(
                        count($report ?? []),
                        0,
                        ',',
                        '.'
                    ) ?>

                    Data

                </span>

            </div>

        </div>


        <div class="card-body">

            <div class="table-responsive">

                <table
                    id="tableLaporanPiutang"
                    class="table
                           table-bordered
                           table-hover
                           table-striped
                           text-nowrap"
                    style="width:100%;"
                >

                    <thead class="thead-light">

                        <tr>

                            <th class="text-center">
                                No
                            </th>

                            <th>
                                No. Piutang
                            </th>

                            <th>
                                Customer
                            </th>

                            <th>
                                Tanggal Piutang
                            </th>

                            <th>
                                Jatuh Tempo
                            </th>

                            <th class="text-right">
                                Pokok
                            </th>

                            <th class="text-right">
                                Bunga
                            </th>

                            <th class="text-right">
                                Denda
                            </th>

                            <th class="text-right">
                                Total Tagihan
                            </th>

                            <th class="text-right">
                                Pembayaran
                            </th>

                            <th class="text-right">
                                Sisa
                            </th>

                            <th class="text-center">
                                Status
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php if (! empty($report)): ?>


                            <?php foreach (
                                $report as $index => $row
                            ): ?>


                                <?php
                                /*
                                 * ==================================================
                                 * STATUS
                                 * ==================================================
                                 */

                                $status =
                                    $row[
                                        'status_jatuh_tempo'
                                    ] ?? '';


                                switch ($status) {

                                    case 'lunas':

                                        $badgeClass =
                                            'badge-success';

                                        $badgeText =
                                            'Lunas';

                                        break;


                                    case 'belum_jatuh_tempo':

                                        $badgeClass =
                                            'badge-primary';

                                        $badgeText =
                                            'Belum Jatuh Tempo';

                                        break;


                                    case 'jatuh_tempo':

                                        $badgeClass =
                                            'badge-warning';

                                        $badgeText =
                                            'Jatuh Tempo';

                                        break;


                                    case 'menunggak':

                                        $badgeClass =
                                            'badge-danger';

                                        $badgeText =
                                            'Menunggak';

                                        break;


                                    default:

                                        $badgeClass =
                                            'badge-secondary';

                                        $badgeText =
                                            'Tidak Diketahui';

                                        break;
                                }
                                ?>


                                <tr>


                                    <!-- ======================================
                                         NO
                                         ====================================== -->

                                    <td class="text-center">

                                        <?= $index + 1 ?>

                                    </td>


                                    <!-- ======================================
                                         NOMOR PIUTANG
                                         ====================================== -->

                                    <td>

                                        <a
                                            href="<?= site_url(
                                                'piutang/detail/'
                                                . (int) (
                                                    $row['id']
                                                    ?? 0
                                                )
                                            ) ?>"
                                            class="font-weight-bold"
                                        >

                                            <?= esc(
                                                $row[
                                                    'nomor_piutang'
                                                ] ?? '-'
                                            ) ?>

                                        </a>

                                    </td>


                                    <!-- ======================================
                                         CUSTOMER
                                         ====================================== -->

                                    <td>

                                        <?= esc(
                                            $row[
                                                'nama_customer'
                                            ]
                                            ?? $row[
                                                'nama'
                                            ]
                                            ?? '-'
                                        ) ?>

                                    </td>


                                    <!-- ======================================
                                         TANGGAL PIUTANG
                                         ====================================== -->

                                    <td>

                                        <?php if (
                                            ! empty(
                                                $row[
                                                    'tanggal_piutang'
                                                ]
                                            )
                                        ): ?>

                                            <?= date(
                                                'd-m-Y',
                                                strtotime(
                                                    $row[
                                                        'tanggal_piutang'
                                                    ]
                                                )
                                            ) ?>

                                        <?php else: ?>

                                            -

                                        <?php endif; ?>

                                    </td>


                                    <!-- ======================================
                                         JATUH TEMPO
                                         ====================================== -->

                                    <td>

                                        <?php if (
                                            ! empty(
                                                $row[
                                                    'tanggal_jatuh_tempo'
                                                ]
                                            )
                                        ): ?>

                                            <?= date(
                                                'd-m-Y',
                                                strtotime(
                                                    $row[
                                                        'tanggal_jatuh_tempo'
                                                    ]
                                                )
                                            ) ?>

                                        <?php else: ?>

                                            -

                                        <?php endif; ?>

                                    </td>


                                    <!-- ======================================
                                         POKOK
                                         ====================================== -->

                                    <td class="text-right">

                                        Rp
                                        <?= number_format(
                                            (float) (
                                                $row[
                                                    'nominal_pokok'
                                                ] ?? 0
                                            ),
                                            0,
                                            ',',
                                            '.'
                                        ) ?>

                                    </td>


                                    <!-- ======================================
                                         BUNGA
                                         ====================================== -->

                                    <td class="text-right">

                                        Rp
                                        <?= number_format(
                                            (float) (
                                                $row[
                                                    'nominal_bunga'
                                                ] ?? 0
                                            ),
                                            0,
                                            ',',
                                            '.'
                                        ) ?>

                                    </td>


                                    <!-- ======================================
                                         DENDA
                                         ====================================== -->

                                    <td class="text-right">

                                        Rp
                                        <?= number_format(
                                            (float) (
                                                $row[
                                                    'denda_berjalan'
                                                ] ?? 0
                                            ),
                                            0,
                                            ',',
                                            '.'
                                        ) ?>

                                    </td>


                                    <!-- ======================================
                                         TOTAL TAGIHAN
                                         ====================================== -->

                                    <td
                                        class="text-right
                                               font-weight-bold"
                                    >

                                        Rp
                                        <?= number_format(
                                            (float) (
                                                $row[
                                                    'total_tagihan'
                                                ] ?? 0
                                            ),
                                            0,
                                            ',',
                                            '.'
                                        ) ?>

                                    </td>


                                    <!-- ======================================
                                         PEMBAYARAN
                                         ====================================== -->

                                    <td class="text-right">

                                        Rp
                                        <?= number_format(
                                            (float) (
                                                $row[
                                                    'total_pembayaran'
                                                ] ?? 0
                                            ),
                                            0,
                                            ',',
                                            '.'
                                        ) ?>

                                    </td>


                                    <!-- ======================================
                                         SISA
                                         ====================================== -->

                                    <td
                                        class="text-right
                                               font-weight-bold"
                                    >

                                        Rp
                                        <?= number_format(
                                            (float) (
                                                $row[
                                                    'sisa_tagihan'
                                                ] ?? 0
                                            ),
                                            0,
                                            ',',
                                            '.'
                                        ) ?>

                                    </td>


                                    <!-- ======================================
                                         STATUS
                                         ====================================== -->

                                    <td class="text-center">

                                        <span
                                            class="badge
                                                   <?= $badgeClass ?>"
                                        >

                                            <?= $badgeText ?>

                                        </span>

                                    </td>


                                </tr>


                            <?php endforeach; ?>


                        <?php else: ?>


                            <!-- ==============================================
                                 EMPTY DATA
                                 ============================================== -->

                            <tr>

                                <td
                                    colspan="12"
                                    class="text-center py-5"
                                >

                                    <div class="text-muted">

                                        <i
                                            class="fas fa-inbox
                                                   fa-2x
                                                   mb-3"
                                        ></i>


                                        <div class="font-weight-bold">

                                            Belum ada data piutang.

                                        </div>


                                        <div class="small mt-1">

                                            Tidak ada data yang
                                            sesuai dengan filter
                                            yang dipilih.

                                        </div>

                                    </div>

                                </td>

                            </tr>


                        <?php endif; ?>


                    </tbody>

                </table>

            </div>

        </div>

    </div>


</div>


<!-- ==========================================================
     SCRIPT
     ========================================================== -->

<?= $this->include(
    'laporan/piutang/_script'
) ?>


<?= $this->endSection() ?>