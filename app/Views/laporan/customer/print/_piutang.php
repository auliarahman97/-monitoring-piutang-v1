<div class="section">

    <div class="section-title">
        Riwayat Piutang
    </div>


    <?php if (
        empty($piutangReport)
    ) : ?>

        <div class="empty">

            Tidak ada riwayat piutang
            untuk customer ini.

        </div>

    <?php else : ?>


        <table class="report-table">

            <thead>

                <tr>

                    <th width="3%">
                        No
                    </th>

                    <th width="11%">
                        Nomor Piutang
                    </th>

                    <th width="8%">
                        Tanggal
                    </th>

                    <th width="9%">
                        Jatuh Tempo
                    </th>

                    <th width="10%">
                        Pokok
                    </th>

                    <th width="9%">
                        Bunga
                    </th>

                    <th width="9%">
                        Denda
                    </th>

                    <th width="11%">
                        Total Tagihan
                    </th>

                    <th width="11%">
                        Pembayaran
                    </th>

                    <th width="11%">
                        Sisa Tagihan
                    </th>

                    <th width="8%">
                        Status
                    </th>

                </tr>

            </thead>


            <tbody>

                <?php foreach (
                    $piutangReport
                    as $index => $row
                ) : ?>

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


                        <td class="date">

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


                        <td class="date">

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


                        <td class="number">

                            Rp <?= number_format(
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


                        <td class="number">

                            Rp <?= number_format(
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


                        <td class="number">

                            Rp <?= number_format(
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


                        <td class="number">

                            <strong>

                                Rp <?= number_format(
                                    (float) (
                                        $row[
                                            'total_tagihan'
                                        ] ?? 0
                                    ),
                                    0,
                                    ',',
                                    '.'
                                ) ?>

                            </strong>

                        </td>


                        <td class="number">

                            Rp <?= number_format(
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


                        <td class="number">

                            <strong>

                                Rp <?= number_format(
                                    (float) (
                                        $row[
                                            'sisa_tagihan'
                                        ] ?? 0
                                    ),
                                    0,
                                    ',',
                                    '.'
                                ) ?>

                            </strong>

                        </td>


                        <td class="text-center">

                            <?= (
                                $row[
                                    'status'
                                ] ?? ''
                            ) === 'lunas'

                                ? 'LUNAS'

                                : 'BELUM LUNAS'
                            ?>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>


    <?php endif; ?>

</div>