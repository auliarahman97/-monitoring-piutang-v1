<div class="card report-section mb-4">

    <div class="card-header">

        <div>

            <h3 class="section-title">

                <i class="fas fa-money-bill-wave text-success mr-2"></i>

                Riwayat Pembayaran

            </h3>


            <div class="section-subtitle">

                Seluruh histori pembayaran,
                termasuk transaksi yang dibatalkan

            </div>

        </div>

    </div>


    <div class="card-body p-0">

        <?php if (
            empty(
                $pembayaranReport
            )
        ) : ?>

            <div class="empty-state">

                <i class="fas fa-receipt d-block"></i>

                <div class="empty-state-title">
                    Belum ada pembayaran
                </div>

                <div>
                    Belum terdapat histori pembayaran
                    untuk customer ini.
                </div>

            </div>

        <?php else : ?>

            <div class="table-responsive">

                <table
                    id="tableCustomerPembayaran"
                    class="table table-hover table-striped mb-0 report-table"
                    width="100%"
                >

                    <thead>

                        <tr>

                            <th class="text-center">
                                #
                            </th>

                            <th>
                                Nomor Pembayaran
                            </th>

                            <th>
                                Nomor Piutang
                            </th>

                            <th class="date">
                                Tanggal
                            </th>

                            <th class="number">
                                Nominal
                            </th>

                            <th class="number">
                                Denda
                            </th>

                            <th class="number">
                                Bunga
                            </th>

                            <th class="number">
                                Pokok
                            </th>

                            <th class="number">
                                Sisa
                            </th>

                            <th class="text-center">
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

                            $nominal =
                                (float) (
                                    $row[
                                        'nominal_pembayaran'
                                    ] ?? 0
                                );

                            $alokasiDenda =
                                (float) (
                                    $row[
                                        'alokasi_denda'
                                    ] ?? 0
                                );

                            $alokasiBunga =
                                (float) (
                                    $row[
                                        'alokasi_bunga'
                                    ] ?? 0
                                );

                            $alokasiPokok =
                                (float) (
                                    $row[
                                        'alokasi_pokok'
                                    ] ?? 0
                                );

                            $sisa =
                                (float) (
                                    $row[
                                        'sisa_tagihan'
                                    ] ?? 0
                                );

                            $status =
                                (string) (
                                    $row[
                                        'status'
                                    ] ?? ''
                                );

                            ?>


                            <tr>

                                <td class="text-center">
                                    <?= $index + 1 ?>
                                </td>


                                <td>

                                    <strong>

                                        <?= esc(
                                            $row[
                                                'nomor_pembayaran'
                                            ]
                                            ?? (
                                                $row['id']
                                                ?? '-'
                                            )
                                        ) ?>

                                    </strong>

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
                                            'tanggal_pembayaran'
                                        ]
                                    )
                                        ? tanggalIndonesia(
                                            $row[
                                                'tanggal_pembayaran'
                                            ]
                                        )
                                        : '-'
                                    ?>

                                </td>


                                <td class="number font-weight-bold">

                                    <?= rupiah(
                                        $nominal
                                    ) ?>

                                </td>


                                <td class="number">

                                    <?= rupiah(
                                        $alokasiDenda
                                    ) ?>

                                </td>


                                <td class="number">

                                    <?= rupiah(
                                        $alokasiBunga
                                    ) ?>

                                </td>


                                <td class="number">

                                    <?= rupiah(
                                        $alokasiPokok
                                    ) ?>

                                </td>


                                <td class="number">

                                    <?= rupiah(
                                        $sisa
                                    ) ?>

                                </td>


                                <td class="text-center">

                                    <?php if (
                                        $status === 'valid'
                                    ) : ?>

                                        <span class="badge badge-success">

                                            <i class="fas fa-check-circle mr-1"></i>

                                            Valid

                                        </span>

                                    <?php elseif (
                                        $status === 'dibatalkan'
                                    ) : ?>

                                        <span class="badge badge-danger">

                                            <i class="fas fa-ban mr-1"></i>

                                            Dibatalkan

                                        </span>

                                    <?php else : ?>

                                        <span class="badge badge-secondary">

                                            <?= esc(
                                                ucwords(
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

            </div>

        <?php endif; ?>

    </div>

</div>


<div class="alert alert-light border">

    <div class="d-flex">

        <i class="fas fa-info-circle text-primary mt-1 mr-2"></i>

        <div class="small text-muted">

            <strong>Catatan:</strong>

            Pembayaran yang dibatalkan tetap ditampilkan
            sebagai histori transaksi, tetapi tidak dianggap
            sebagai pembayaran valid dalam perhitungan saldo.

        </div>

    </div>

</div>