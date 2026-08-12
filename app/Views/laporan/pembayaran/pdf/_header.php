<table class="header-table">

    <tr>

        <td class="text-center">

            <div class="title">
                LAPORAN PEMBAYARAN
            </div>

            <div class="subtitle">
                Histori transaksi pembayaran customer
            </div>

        </td>

    </tr>

</table>


<table class="filter-table">

    <!-- ==========================================================
         BARIS 1
         ========================================================== -->

    <tr>

        <td class="filter-label">
            Periode Pembayaran
        </td>

        <td class="filter-value">

            :

            <?php if (
                ! empty(
                    $filter['tanggal_dari']
                )
                ||
                ! empty(
                    $filter['tanggal_sampai']
                )
            ): ?>

                <?= ! empty(
                    $filter['tanggal_dari']
                )
                    ? date(
                        'd-m-Y',
                        strtotime(
                            $filter['tanggal_dari']
                        )
                    )
                    : '-'
                ?>

                s/d

                <?= ! empty(
                    $filter['tanggal_sampai']
                )
                    ? date(
                        'd-m-Y',
                        strtotime(
                            $filter['tanggal_sampai']
                        )
                    )
                    : '-'
                ?>

            <?php else: ?>

                Semua Periode

            <?php endif; ?>

        </td>


        <td class="filter-label">
            Customer
        </td>

        <td class="filter-value">

            :

            <?php

            $customerName =
                'Semua Customer';


            if (
                ! empty(
                    $filter['customer_id']
                )
            ) {

                foreach (
                    $customers ?? []
                    as $customer
                ) {

                    if (
                        (int) (
                            $customer['id']
                            ?? 0
                        )
                        ===
                        (int) (
                            $filter['customer_id']
                        )
                    ) {

                        $customerName =
                            $customer['nama']
                            ?? $customer[
                                'nama_customer'
                            ]
                            ?? '-';

                        break;
                    }
                }
            }

            ?>

            <?= esc(
                $customerName
            ) ?>

        </td>

    </tr>


    <!-- ==========================================================
         BARIS 2
         ========================================================== -->

    <tr>

        <td class="filter-label">
            Status Pembayaran
        </td>

        <td class="filter-value">

            :

            <?php

            $statusLabel = [

                'semua' =>
                    'Semua',

                \App\Models\PembayaranModel::STATUS_VALID =>
                    'Valid',

                \App\Models\PembayaranModel::STATUS_DIBATALKAN =>
                    'Dibatalkan',

            ];

            ?>

            <?= esc(
                $statusLabel[
                    $filter['status']
                    ?? 'semua'
                ]
                ?? 'Semua'
            ) ?>

        </td>


        <td class="filter-label">
            Jenis Laporan
        </td>

        <td class="filter-value">

            :

            Histori Transaksi Pembayaran

        </td>

    </tr>

</table>