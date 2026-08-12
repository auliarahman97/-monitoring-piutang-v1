<div class="section">

    <div class="section-title">
        Riwayat Pembayaran
    </div>


    <?php if (
        empty($pembayaranReport)
    ) : ?>

        <div class="empty">

            Tidak ada riwayat pembayaran
            untuk customer ini.

        </div>

    <?php else : ?>


        <table class="report-table">

            <thead>

                <tr>

                    <th width="3%">
                        No
                    </th>

                    <th width="12%">
                        Nomor Pembayaran
                    </th>

                    <th width="11%">
                        Nomor Piutang
                    </th>

                    <th width="9%">
                        Tanggal
                    </th>

                    <th width="13%">
                        Nominal
                    </th>

                    <th width="12%">
                        Alokasi Denda
                    </th>

                    <th width="12%">
                        Alokasi Bunga
                    </th>

                    <th width="12%">
                        Alokasi Pokok
                    </th>

                    <th width="16%">
                        Status
                    </th>

                </tr>

            </thead>


            <tbody>

                <?php foreach (
                    $pembayaranReport
                    as $index => $row
                ) : ?>

                    <?php

                    $status =
                        (string) (
                            $row[
                                'status'
                            ] ?? ''
                        );

                    ?>


                    <tr>

                        <!-- NO -->

                        <td class="text-center">

                            <?= $index + 1 ?>

                        </td>


                        <!-- NOMOR PEMBAYARAN -->

                        <td>

                            <?= esc(
                                $row[
                                    'nomor_pembayaran'
                                ]
                                ?? (
                                    $row['id']
                                    ?? '-'
                                )
                            ) ?>

                        </td>


                        <!-- NOMOR PIUTANG -->

                        <td>

                            <?= esc(
                                $row[
                                    'nomor_piutang'
                                ] ?? '-'
                            ) ?>

                        </td>


                        <!-- TANGGAL -->

                        <td class="date">

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


                        <!-- NOMINAL -->

                        <td class="number">

                            <strong>

                                Rp <?= number_format(
                                    (float) (
                                        $row[
                                            'nominal_pembayaran'
                                        ] ?? 0
                                    ),
                                    0,
                                    ',',
                                    '.'
                                ) ?>

                            </strong>

                        </td>


                        <!-- DENDA -->

                        <td class="number">

                            Rp <?= number_format(
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


                        <!-- BUNGA -->

                        <td class="number">

                            Rp <?= number_format(
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


                        <!-- POKOK -->

                        <td class="number">

                            Rp <?= number_format(
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


                        <!-- STATUS -->

                        <td class="text-center">

                            <?php if (
                                $status === 'valid'
                            ) : ?>

                                <span class="badge badge-success">

                                    VALID

                                </span>

                            <?php elseif (
                                $status === 'dibatalkan'
                            ) : ?>

                                <span class="badge badge-danger">

                                    DIBATALKAN

                                </span>

                            <?php else : ?>

                                <span class="badge">

                                    <?= esc(
                                        strtoupper(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $status
                                            )
                                        )
                                    ) ?>

                                </span>

                            <?php endif; ?>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>


    <?php endif; ?>

</div>