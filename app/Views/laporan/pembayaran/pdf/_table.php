<table class="report-table">

    <thead>

        <tr>

            <th width="3%">
                No
            </th>

            <th width="7%">
                Tanggal
            </th>

            <th width="9%">
                No. Pembayaran
            </th>

            <th width="12%">
                Customer
            </th>

            <th width="9%">
                No. Piutang
            </th>

            <th width="11%">
                Tagihan
            </th>

            <th width="11%">
                Pembayaran
            </th>

            <th width="9%">
                Denda
            </th>

            <th width="9%">
                Bunga
            </th>

            <th width="10%">
                Pokok
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

        <?php if (
            ! empty($report)
        ): ?>

            <?php foreach (
                $report as $index => $row
            ): ?>

                <?php

                $status =
                    $row['status']
                    ?? '';

                $isValid =
                    $status
                    ===
                    \App\Models\PembayaranModel::STATUS_VALID;

                $statusText =
                    $isValid
                        ? 'Valid'
                        : (
                            $status
                            ===
                            \App\Models\PembayaranModel::STATUS_DIBATALKAN
                                ? 'Dibatalkan'
                                : ucfirst(
                                    $status
                                    ?: '-'
                                )
                        );

                ?>

                <tr>

                    <td class="text-center">
                        <?= $index + 1 ?>
                    </td>


                    <td class="text-center">

                        <?= ! empty(
                            $row[
                                'tanggal_pembayaran'
                            ]
                        )
                            ? date(
                                'd-m-Y',
                                strtotime(
                                    $row[
                                        'tanggal_pembayaran'
                                    ]
                                )
                            )
                            : '-'
                        ?>

                    </td>


                    <td>

                        <?= esc(
                            $row[
                                'nomor_pembayaran'
                            ] ?? '-'
                        ) ?>

                    </td>


                    <td>

                        <?= esc(
                            $row[
                                'nama_customer'
                            ] ?? '-'
                        ) ?>

                    </td>


                    <td>

                        <?= esc(
                            $row[
                                'nomor_piutang'
                            ] ?? '-'
                        ) ?>

                    </td>


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


                    <td class="text-right">

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


                    <td class="text-center">

                        <?php if (
                            $isValid
                        ): ?>

                            <strong>
                                Valid
                            </strong>

                        <?php elseif (
                            $status
                            ===
                            \App\Models\PembayaranModel::STATUS_DIBATALKAN
                        ): ?>

                            <strong>
                                Dibatalkan
                            </strong>

                        <?php else: ?>

                            <?= esc(
                                $statusText
                            ) ?>

                        <?php endif; ?>

                    </td>

                </tr>

            <?php endforeach; ?>


        <?php else: ?>

            <tr>

                <td
                    colspan="12"
                    class="text-center"
                >

                    Tidak ada transaksi
                    pembayaran yang sesuai
                    dengan filter.

                </td>

            </tr>

        <?php endif; ?>

    </tbody>

</table>