<?php

$statusBadge = static function (
    string $status
): string {

    return match ($status) {

        'lunas' =>
            '<span class="badge badge-success px-2 py-1">
                <i class="fas fa-check-circle mr-1"></i>
                Lunas
            </span>',

        'belum_lunas' =>
            '<span class="badge badge-warning px-2 py-1">
                <i class="fas fa-clock mr-1"></i>
                Belum Lunas
            </span>',

        default =>
            '<span class="badge badge-secondary px-2 py-1">'
            . esc(
                ucwords(
                    str_replace(
                        '_',
                        ' ',
                        $status
                    )
                )
            )
            . '</span>',
    };
};


$dueBadge = static function (
    string $status
): string {

    return match ($status) {

        'lunas' =>
            '<span class="badge badge-success">
                Lunas
            </span>',

        'belum_jatuh_tempo' =>
            '<span class="badge badge-info">
                Belum Jatuh Tempo
            </span>',

        'jatuh_tempo' =>
            '<span class="badge badge-warning">
                Jatuh Tempo
            </span>',

        'menunggak' =>
            '<span class="badge badge-danger">
                Menunggak
            </span>',

        default =>
            '<span class="badge badge-secondary">
                -
            </span>',
    };
};

?>

<div class="card report-section mb-4">

    <div class="card-header">

        <div>

            <h3 class="section-title">

                <i class="fas fa-file-invoice-dollar text-primary mr-2"></i>

                Riwayat Piutang

            </h3>


            <div class="section-subtitle">

                Daftar seluruh piutang customer
                beserta kondisi tagihannya

            </div>

        </div>

    </div>


    <div class="card-body p-0">

        <?php if (
            empty($piutangReport)
        ) : ?>

            <div class="empty-state">

                <i class="fas fa-folder-open d-block"></i>

                <div class="empty-state-title">
                    Belum ada piutang
                </div>

                <div>
                    Customer ini belum memiliki
                    data piutang.
                </div>

            </div>

        <?php else : ?>

            <div class="table-responsive">

                <table
                    id="tableCustomerPiutang"
                    class="table table-hover table-striped mb-0 report-table"
                    width="100%"
                >

                    <thead>

                        <tr>

                            <th class="text-center">
                                #
                            </th>

                            <th>
                                Nomor Piutang
                            </th>

                            <th class="date">
                                Tanggal
                            </th>

                            <th class="date">
                                Jatuh Tempo
                            </th>

                            <th class="number">
                                Pokok
                            </th>

                            <th class="number">
                                Bunga
                            </th>

                            <th class="number">
                                Denda
                            </th>

                            <th class="number">
                                Total Tagihan
                            </th>

                            <th class="number">
                                Pembayaran
                            </th>

                            <th class="number">
                                Sisa
                            </th>

                            <th class="text-center">
                                Status
                            </th>

                            <th class="text-center">
                                Jatuh Tempo
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php foreach (
                            $piutangReport
                            as $index => $row
                        ) : ?>

                            <?php

                            $nominalPokok =
                                (float) (
                                    $row[
                                        'nominal_pokok'
                                    ] ?? 0
                                );

                            $nominalBunga =
                                (float) (
                                    $row[
                                        'nominal_bunga'
                                    ] ?? 0
                                );

                            $denda =
                                (float) (
                                    $row[
                                        'denda_berjalan'
                                    ] ?? 0
                                );

                            $tagihan =
                                (float) (
                                    $row[
                                        'total_tagihan'
                                    ] ?? 0
                                );

                            $pembayaran =
                                (float) (
                                    $row[
                                        'total_pembayaran'
                                    ] ?? 0
                                );

                            $sisa =
                                (float) (
                                    $row[
                                        'sisa_tagihan'
                                    ] ?? 0
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
                                                'nomor_piutang'
                                            ] ?? '-'
                                        ) ?>

                                    </strong>

                                </td>


                                <td class="date">

                                    <?= ! empty(
                                        $row[
                                            'tanggal_piutang'
                                        ]
                                    )
                                        ? tanggalIndonesia(
                                            $row[
                                                'tanggal_piutang'
                                            ]
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
                                        ? tanggalIndonesia(
                                            $row[
                                                'tanggal_jatuh_tempo'
                                            ]
                                        )
                                        : '-'
                                    ?>

                                </td>


                                <td class="number">
                                    <?= rupiah(
                                        $nominalPokok
                                    ) ?>
                                </td>


                                <td class="number">
                                    <?= rupiah(
                                        $nominalBunga
                                    ) ?>
                                </td>


                                <td class="number">
                                    <?= rupiah(
                                        $denda
                                    ) ?>
                                </td>


                                <td class="number font-weight-bold">
                                    <?= rupiah(
                                        $tagihan
                                    ) ?>
                                </td>


                                <td class="number text-success">
                                    <?= rupiah(
                                        $pembayaran
                                    ) ?>
                                </td>


                                <td class="number font-weight-bold <?= $sisa > 0
                                    ? 'text-danger'
                                    : 'text-success'
                                ?>">
                                    <?= rupiah(
                                        $sisa
                                    ) ?>
                                </td>


                                <td class="text-center">

                                    <?= $statusBadge(
                                        (string) (
                                            $row[
                                                'status'
                                            ] ?? ''
                                        )
                                    ) ?>

                                </td>


                                <td class="text-center">

                                    <?= $dueBadge(
                                        (string) (
                                            $row[
                                                'status_jatuh_tempo'
                                            ] ?? ''
                                        )
                                    ) ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </div>

</div>