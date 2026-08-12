<table class="header-table">

    <tr>

        <td class="text-center">

            <div class="title">
                LAPORAN PIUTANG
            </div>

            <div class="subtitle">

                Laporan detail posisi piutang customer

            </div>

        </td>

    </tr>

</table>


<table class="filter-table">

    <!-- ==========================================================
         PERIODE + CUSTOMER
         ========================================================== -->

    <tr>

        <td class="filter-label">
            Periode Piutang
        </td>

        <td>

            :

            <?= ! empty(
                $filter['tanggal_dari']
            )
                ? date(
                    'd-m-Y',
                    strtotime(
                        $filter['tanggal_dari']
                    )
                )
                : 'Semua'
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
                : 'Semua'
            ?>

        </td>


        <td class="filter-label">
            Customer
        </td>

        <td>

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
         STATUS + JATUH TEMPO
         ========================================================== -->

    <tr>

        <td class="filter-label">
            Status
        </td>

        <td>

            :

            <?php

            $statusLabel = [

                'semua' =>
                    'Semua',

                'lunas' =>
                    'Lunas',

                'belum_lunas' =>
                    'Belum Lunas',

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
            Jatuh Tempo
        </td>

        <td>

            :

            <?php

            $dueLabel = [

                'semua' =>
                    'Semua',

                'belum_jatuh_tempo' =>
                    'Belum Jatuh Tempo',

                'jatuh_tempo' =>
                    'Jatuh Tempo',

                'menunggak' =>
                    'Menunggak',

            ];

            ?>

            <?= esc(
                $dueLabel[
                    $filter['jatuh_tempo']
                    ?? 'semua'
                ]
                ?? 'Semua'
            ) ?>

        </td>

    </tr>

</table>