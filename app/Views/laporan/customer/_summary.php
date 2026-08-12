<?php

$jumlahPiutang =
    (int) (
        $summary[
            'jumlah_piutang'
        ] ?? 0
    );

$jumlahLunas =
    (int) (
        $summary[
            'jumlah_lunas'
        ] ?? 0
    );

$jumlahBelumLunas =
    (int) (
        $summary[
            'jumlah_belum_lunas'
        ] ?? 0
    );

$totalPiutang =
    $summary[
        'total_piutang'
    ] ?? 0;

$totalTagihan =
    $summary[
        'total_tagihan'
    ] ?? 0;

$totalPembayaran =
    $summary[
        'total_pembayaran'
    ] ?? 0;

$sisaTagihan =
    $summary[
        'sisa_tagihan'
    ] ?? 0;

?>


<div class="row">


    <!-- JUMLAH PIUTANG -->

    <div class="col-lg-3 col-md-6">

        <div class="card summary-card">

            <div class="card-body">

                <div class="d-flex">

                    <div>

                        <div class="summary-label">
                            Jumlah Piutang
                        </div>

                        <div class="summary-value">

                            <?= number_format(
                                $jumlahPiutang,
                                0,
                                ',',
                                '.'
                            ) ?>

                        </div>

                    </div>


                    <div class="summary-icon bg-light ml-auto">

                        <i class="fas fa-file-invoice text-primary"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- TOTAL PIUTANG -->

    <div class="col-lg-3 col-md-6">

        <div class="card summary-card">

            <div class="card-body">

                <div class="d-flex">

                    <div>

                        <div class="summary-label">
                            Total Piutang
                        </div>

                        <div class="summary-value text-primary">

                            <?= rupiah(
                                $totalPiutang
                            ) ?>

                        </div>

                    </div>


                    <div class="summary-icon bg-light ml-auto">

                        <i class="fas fa-wallet text-primary"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- TOTAL PEMBAYARAN -->

    <div class="col-lg-3 col-md-6">

        <div class="card summary-card">

            <div class="card-body">

                <div class="d-flex">

                    <div>

                        <div class="summary-label">
                            Total Pembayaran
                        </div>

                        <div class="summary-value text-success">

                            <?= rupiah(
                                $totalPembayaran
                            ) ?>

                        </div>

                    </div>


                    <div class="summary-icon bg-light ml-auto">

                        <i class="fas fa-money-bill-wave text-success"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- SISA -->

    <div class="col-lg-3 col-md-6">

        <div class="card summary-card">

            <div class="card-body">

                <div class="d-flex">

                    <div>

                        <div class="summary-label">
                            Sisa Tagihan
                        </div>

                        <div class="summary-value <?= (
                            (float) $sisaTagihan > 0
                        )
                            ? 'text-danger'
                            : 'text-success'
                        ?>">

                            <?= rupiah(
                                $sisaTagihan
                            ) ?>

                        </div>

                    </div>


                    <div class="summary-icon bg-light ml-auto">

                        <i class="fas fa-exclamation-circle <?= (
                            (float) $sisaTagihan > 0
                        )
                            ? 'text-danger'
                            : 'text-success'
                        ?>"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<!-- STATUS -->

<div class="row">

    <div class="col-md-6">

        <div class="small text-muted mb-1">
            Status Piutang
        </div>

        <div class="mb-3">

            <span class="badge badge-success mr-1">

                <i class="fas fa-check mr-1"></i>

                <?= $jumlahLunas ?>

                Lunas

            </span>


            <span class="badge badge-warning">

                <i class="fas fa-clock mr-1"></i>

                <?= $jumlahBelumLunas ?>

                Belum Lunas

            </span>

        </div>

    </div>


    <div class="col-md-6 text-md-right">

        <div class="small text-muted mb-1">
            Total Tagihan Berjalan
        </div>

        <strong class="h5">

            <?= rupiah(
                $totalTagihan
            ) ?>

        </strong>

    </div>

</div>