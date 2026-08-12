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

                <i class="fas fa-money-check-alt
                          text-primary mr-2"></i>

                Laporan Pembayaran

            </h3>

            <p class="text-muted mb-0">

                Histori transaksi pembayaran
                berdasarkan tanggal, customer,
                dan status transaksi.

            </p>

        </div>


        <!-- ======================================================
             EXPORT PDF
             ====================================================== -->

        <?php

        $pdfParams = [

            'tanggal_dari' =>
                $filter['tanggal_dari']
                ?? '',

            'tanggal_sampai' =>
                $filter['tanggal_sampai']
                ?? '',

            'customer_id' =>
                $filter['customer_id']
                ?? '',

            'status' =>
                $filter['status']
                ?? 'semua',

        ];


        /*
         * Hapus parameter kosong agar URL tetap bersih.
         */

        $pdfParams =
            array_filter(
                $pdfParams,
                static function ($value) {

                    return $value !== ''
                        && $value !== null;

                }
            );


        $pdfUrl =
            site_url(
                'laporan/pembayaran/pdf'
            )
            . (
                ! empty($pdfParams)
                    ? '?'
                        . http_build_query(
                            $pdfParams
                        )
                    : ''
            );

        ?>


        <div class="mt-3 mt-md-0">

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

    <div
        class="alert alert-light
               border
               shadow-sm
               mb-4"
    >

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
                    Informasi Laporan:
                </strong>

                Menampilkan histori transaksi
                pembayaran berdasarkan filter
                yang dipilih.

                <div class="small text-muted mt-1">

                    Pembayaran yang dibatalkan tetap
                    ditampilkan sebagai histori transaksi,
                    tetapi tidak dihitung sebagai
                    pembayaran valid.

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
                'laporan/pembayaran/_form'
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

                Data Pembayaran

            </h3>


            <div class="card-tools">

                <span class="badge badge-light">

                    <?= number_format(
                        count($report ?? []),
                        0,
                        ',',
                        '.'
                    ) ?>

                    Transaksi

                </span>

            </div>

        </div>


        <div class="card-body">

            <div class="table-responsive">

                <table
                    id="tableLaporanPembayaran"
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
                                Tanggal
                            </th>

                            <th>
                                No. Pembayaran
                            </th>

                            <th>
                                Customer
                            </th>

                            <th>
                                No. Piutang
                            </th>

                            <th class="text-right">
                                Tagihan
                            </th>

                            <th class="text-right">
                                Pembayaran
                            </th>

                            <th class="text-right">
                                Denda
                            </th>

                            <th class="text-right">
                                Bunga
                            </th>

                            <th class="text-right">
                                Pokok
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

                        <?php if (
                            ! empty($report)
                        ): ?>


                            <?php foreach (
                                $report
                                as $index => $row
                            ): ?>


                                <?php

                                /*
                                 * ==================================================
                                 * STATUS
                                 * ==================================================
                                 */

                                $status =
                                    $row['status']
                                    ?? '';


                                if (
                                    $status
                                    === \App\Models\PembayaranModel::STATUS_VALID
                                ) {

                                    $badgeClass =
                                        'badge-success';

                                    $badgeText =
                                        'Valid';

                                } elseif (
                                    $status
                                    === \App\Models\PembayaranModel::STATUS_DIBATALKAN
                                ) {

                                    $badgeClass =
                                        'badge-danger';

                                    $badgeText =
                                        'Dibatalkan';

                                } else {

                                    $badgeClass =
                                        'badge-secondary';

                                    $badgeText =
                                        ucfirst(
                                            $status
                                            ?: 'Tidak Diketahui'
                                        );
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
                                         TANGGAL
                                         ====================================== -->

                                    <td>

                                        <?php if (
                                            ! empty(
                                                $row[
                                                    'tanggal_pembayaran'
                                                ]
                                            )
                                        ): ?>

                                            <?= date(
                                                'd-m-Y',
                                                strtotime(
                                                    $row[
                                                        'tanggal_pembayaran'
                                                    ]
                                                )
                                            ) ?>

                                        <?php else: ?>

                                            -

                                        <?php endif; ?>

                                    </td>


                                    <!-- ======================================
                                         NOMOR PEMBAYARAN
                                         ====================================== -->

                                    <td>

                                        <strong>

                                            <?= esc(
                                                $row[
                                                    'nomor_pembayaran'
                                                ] ?? '-'
                                            ) ?>

                                        </strong>

                                    </td>


                                    <!-- ======================================
                                         CUSTOMER
                                         ====================================== -->

                                    <td>

                                        <?= esc(
                                            $row[
                                                'nama_customer'
                                            ]
                                            ?? '-'
                                        ) ?>

                                    </td>


                                    <!-- ======================================
                                         NOMOR PIUTANG
                                         ====================================== -->

                                    <td>

                                        <?php if (
                                            ! empty(
                                                $row[
                                                    'piutang_id'
                                                ]
                                            )
                                        ): ?>

                                            <a
                                                href="<?= site_url(
                                                    'piutang/detail/'
                                                    . (int) (
                                                        $row[
                                                            'piutang_id'
                                                        ]
                                                    )
                                                ) ?>"
                                                class="font-weight-bold"
                                            >

                                                <?= esc(
                                                    $row[
                                                        'nomor_piutang'
                                                    ]
                                                    ?? '-'
                                                ) ?>

                                            </a>

                                        <?php else: ?>

                                            -

                                        <?php endif; ?>

                                    </td>


                                    <!-- ======================================
                                         TOTAL TAGIHAN
                                         ====================================== -->

                                    <td class="text-right">

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

                                    <td
                                        class="text-right
                                               font-weight-bold"
                                    >

                                        Rp
                                        <?= number_format(
                                            (float) (
                                                $row[
                                                    'nominal_pembayaran'
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
                                                    'alokasi_denda'
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
                                                    'alokasi_bunga'
                                                ] ?? 0
                                            ),
                                            0,
                                            ',',
                                            '.'
                                        ) ?>

                                    </td>


                                    <!-- ======================================
                                         POKOK
                                         ====================================== -->

                                    <td class="text-right">

                                        Rp
                                        <?= number_format(
                                            (float) (
                                                $row[
                                                    'alokasi_pokok'
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

                                    <td class="text-right">

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

                                            <?= esc(
                                                $badgeText
                                            ) ?>

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
                                            class="fas fa-receipt
                                                   fa-2x
                                                   mb-3"
                                        ></i>


                                        <div
                                            class="font-weight-bold"
                                        >

                                            Belum ada data pembayaran.

                                        </div>


                                        <div
                                            class="small mt-1"
                                        >

                                            Tidak ada transaksi
                                            yang sesuai dengan
                                            filter yang dipilih.

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
    'laporan/pembayaran/_script'
) ?>


<?= $this->endSection() ?>