<table class="report-table">

    <thead>

        <tr>

            <th width="3%">
                No
            </th>

            <th width="8%">
                No. Piutang
            </th>

            <th width="11%">
                Customer
            </th>

            <th width="7%">
                Tanggal
            </th>

            <th width="7%">
                Jatuh Tempo
            </th>

            <th width="10%">
                Pokok
            </th>

            <th width="10%">
                Bunga
            </th>

            <th width="10%">
                Denda
            </th>

            <th width="10%">
                Total Tagihan
            </th>

            <th width="10%">
                Pembayaran
            </th>

            <th width="10%">
                Sisa
            </th>

            <th width="8%">
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

                $status =
                    $row[
                        'status_jatuh_tempo'
                    ] ?? '';

                switch ($status) {

                    case 'lunas':

                        $statusText =
                            'Lunas';

                        break;


                    case 'belum_jatuh_tempo':

                        $statusText =
                            'Belum Jatuh Tempo';

                        break;


                    case 'jatuh_tempo':

                        $statusText =
                            'Jatuh Tempo';

                        break;


                    case 'menunggak':

                        $statusText =
                            'Menunggak';

                        break;


                    default:

                        $statusText =
                            '-';

                        break;
                }

                ?>


                <tr>

                    <td class="text-center">

                        <?= $index + 1 ?>

                    </td>


                    <td>

                        <?= esc(
                            $row[
                                'nomor_piutang'
                            ] ?? '-'
                        ) ?>

                    </td>


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


                    <td class="text-center">

                        <?= ! empty(
                            $row[
                                'tanggal_piutang'
                            ]
                        )
                            ? date(
                                'd-m-Y',
                                strtotime(
                                    $row[
                                        'tanggal_piutang'
                                    ]
                                )
                            )
                            : '-'
                        ?>

                    </td>


                    <td class="text-center">

                        <?= ! empty(
                            $row[
                                'tanggal_jatuh_tempo'
                            ]
                        )
                            ? date(
                                'd-m-Y',
                                strtotime(
                                    $row[
                                        'tanggal_jatuh_tempo'
                                    ]
                                )
                            )
                            : '-'
                        ?>

                    </td>


                    <td class="text-right">

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


                    <td class="text-right">

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


                    <td class="text-right">

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


                    <td class="text-right">

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


                    <td class="text-right">

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


                    <td class="text-right">

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


                    <td class="text-center">

                        <?= esc(
                            $statusText
                        ) ?>

                    </td>

                </tr>

            <?php endforeach; ?>


        <?php else: ?>

            <tr>

                <td
                    colspan="12"
                    class="text-center"
                >

                    Tidak ada data piutang.

                </td>

            </tr>

        <?php endif; ?>

    </tbody>

</table>