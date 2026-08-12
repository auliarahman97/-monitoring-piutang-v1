<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<?php
/*
|--------------------------------------------------------------------------
| Dashboard Data
|--------------------------------------------------------------------------
*/

$summary =
    $summary
    ?? [];

$overdue =
    $overdue
    ?? [];

$latestPiutang =
    $latestPiutang
    ?? [];

$latestPembayaran =
    $latestPembayaran
    ?? [];


/*
|--------------------------------------------------------------------------
| Helper
|--------------------------------------------------------------------------
*/

$money = static function (
    float|int|string|null $value
): string {

    return 'Rp '
        . number_format(
            (float) ($value ?? 0),
            0,
            ',',
            '.'
        );
};


$statusBadge = static function (
    string $status
): string {

    return match ($status) {

        'lunas' =>
            '<span class="badge badge-success">
                <i class="fas fa-check-circle mr-1"></i>
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


$paymentBadge = static function (
    string $status
): string {

    return match ($status) {

        'valid' =>
            '<span class="badge badge-success">
                <i class="fas fa-check-circle mr-1"></i>
                Valid
            </span>',

        'dibatalkan' =>
            '<span class="badge badge-danger">
                <i class="fas fa-times-circle mr-1"></i>
                Dibatalkan
            </span>',

        default =>
            '<span class="badge badge-secondary">
                -
            </span>',
    };
};
?>

<style>
    .dashboard-header {
        margin-bottom: 1.25rem;
    }

    .dashboard-title {
        font-size: 1.65rem;
        font-weight: 700;
        margin-bottom: .2rem;
    }

    .dashboard-subtitle {
        color: #6c757d;
        margin-bottom: 0;
    }

    .dashboard-date {
        font-size: .875rem;
        color: #6c757d;
    }

    .dashboard-card {
        border: 0;
        border-radius: .75rem;
        box-shadow: 0 .125rem .5rem rgba(0, 0, 0, .07);
        height: 100%;
    }

    .dashboard-card .card-body {
        padding: 1.15rem;
    }

    .kpi-card {
        position: relative;
        overflow: hidden;
    }

    .kpi-card::after {
        content: "";
        position: absolute;
        width: 90px;
        height: 90px;
        right: -30px;
        top: -30px;
        border-radius: 50%;
        background: rgba(0, 0, 0, .035);
    }

    .kpi-label {
        font-size: .8rem;
        text-transform: uppercase;
        letter-spacing: .03em;
        color: #6c757d;
        margin-bottom: .35rem;
    }

    .kpi-value {
        font-size: 1.35rem;
        font-weight: 700;
        line-height: 1.25;
    }

    .kpi-icon {
        width: 42px;
        height: 42px;
        border-radius: .65rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        margin-bottom: .75rem;
    }

    .section-card {
        border: 0;
        border-radius: .75rem;
        box-shadow: 0 .125rem .5rem rgba(0, 0, 0, .07);
        overflow: hidden;
    }

    .section-card .card-header {
        background: #fff;
        border-bottom: 1px solid #eef0f2;
        padding: .9rem 1.1rem;
    }

    .section-title {
        font-size: 1rem;
        font-weight: 700;
        margin: 0;
    }

    .section-subtitle {
        font-size: .8rem;
        color: #6c757d;
        margin-top: .15rem;
    }

    .status-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: .65rem 0;
        border-bottom: 1px solid #f0f1f2;
    }

    .status-item:last-child {
        border-bottom: 0;
    }

    .status-label {
        color: #495057;
        font-size: .9rem;
    }

    .status-number {
        font-weight: 700;
        font-size: 1rem;
    }

    .table-dashboard {
        margin-bottom: 0;
    }

    .table-dashboard th {
        border-top: 0;
        white-space: nowrap;
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .025em;
        color: #6c757d;
    }

    .table-dashboard td {
        vertical-align: middle;
        font-size: .875rem;
    }

    .customer-name {
        font-weight: 600;
    }

    .table-empty {
        text-align: center;
        padding: 2rem 1rem !important;
        color: #6c757d;
    }

    .table-empty i {
        font-size: 1.75rem;
        display: block;
        margin-bottom: .5rem;
        opacity: .55;
    }

    .dashboard-link {
        font-size: .8rem;
        font-weight: 600;
    }

    .overdue-card .card-header {
        border-left: 4px solid #dc3545;
    }

    .activity-card .card-header {
        border-left: 4px solid #007bff;
    }

    .summary-card .card-header {
        border-left: 4px solid #28a745;
    }

    @media (max-width: 767.98px) {

        .dashboard-title {
            font-size: 1.4rem;
        }

        .kpi-value {
            font-size: 1.15rem;
        }

        .table-dashboard {
            min-width: 700px;
        }
    }
</style>


<!-- ================================================================== -->
<!-- CONTENT HEADER -->
<!-- ================================================================== -->

<section class="content-header">

    <div class="container-fluid">

        <div class="dashboard-header">

            <div class="d-flex justify-content-between align-items-start flex-wrap">

                <div>

                    <h1 class="dashboard-title">

                        <i class="fas fa-tachometer-alt mr-2"></i>

                        Dashboard

                    </h1>

                    <p class="dashboard-subtitle">

                        Monitoring kondisi piutang secara ringkas.

                    </p>

                </div>


                <div class="dashboard-date mt-2 mt-md-0">

                    <i class="far fa-calendar-alt mr-1"></i>

                    Posisi:

                    <?= esc(
                        date(
                            'd-m-Y',
                            strtotime(
                                $tanggalLaporan
                                ?? date('Y-m-d')
                            )
                        )
                    ) ?>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- ================================================================== -->
<!-- CONTENT -->
<!-- ================================================================== -->

<section class="content">

    <div class="container-fluid">


        <!-- ========================================================== -->
        <!-- KPI -->
        <!-- ========================================================== -->

        <div class="row">


            <!-- TOTAL CUSTOMER -->

            <div class="col-lg-4 col-md-6 mb-3">

                <div class="card dashboard-card kpi-card">

                    <div class="card-body">

                        <div class="kpi-icon bg-primary text-white">

                            <i class="fas fa-users"></i>

                        </div>

                        <div class="kpi-label">
                            Total Customer Aktif
                        </div>

                        <div class="kpi-value">

                            <?= number_format(
                                (int) (
                                    $summary[
                                        'total_customer'
                                    ] ?? 0
                                ),
                                0,
                                ',',
                                '.'
                            ) ?>

                        </div>

                    </div>

                </div>

            </div>


            <!-- TOTAL PIUTANG -->

            <div class="col-lg-4 col-md-6 mb-3">

                <div class="card dashboard-card kpi-card">

                    <div class="card-body">

                        <div class="kpi-icon bg-info text-white">

                            <i class="fas fa-hand-holding-usd"></i>

                        </div>

                        <div class="kpi-label">
                            Total Piutang
                        </div>

                        <div class="kpi-value">

                            <?= $money(
                                $summary[
                                    'total_piutang'
                                ] ?? 0
                            ) ?>

                        </div>

                    </div>

                </div>

            </div>


            <!-- TOTAL TAGIHAN -->

            <div class="col-lg-4 col-md-6 mb-3">

                <div class="card dashboard-card kpi-card">

                    <div class="card-body">

                        <div class="kpi-icon bg-warning text-white">

                            <i class="fas fa-file-invoice-dollar"></i>

                        </div>

                        <div class="kpi-label">
                            Total Tagihan
                        </div>

                        <div class="kpi-value">

                            <?= $money(
                                $summary[
                                    'total_tagihan'
                                ] ?? 0
                            ) ?>

                        </div>

                    </div>

                </div>

            </div>


            <!-- TOTAL PEMBAYARAN -->

            <div class="col-lg-4 col-md-6 mb-3">

                <div class="card dashboard-card kpi-card">

                    <div class="card-body">

                        <div class="kpi-icon bg-success text-white">

                            <i class="fas fa-money-bill-wave"></i>

                        </div>

                        <div class="kpi-label">
                            Total Pembayaran
                        </div>

                        <div class="kpi-value">

                            <?= $money(
                                $summary[
                                    'total_pembayaran'
                                ] ?? 0
                            ) ?>

                        </div>

                    </div>

                </div>

            </div>


            <!-- SISA TAGIHAN -->

            <div class="col-lg-4 col-md-6 mb-3">

                <div class="card dashboard-card kpi-card">

                    <div class="card-body">

                        <div class="kpi-icon bg-secondary text-white">

                            <i class="fas fa-wallet"></i>

                        </div>

                        <div class="kpi-label">
                            Sisa Tagihan
                        </div>

                        <div class="kpi-value">

                            <?= $money(
                                $summary[
                                    'sisa_tagihan'
                                ] ?? 0
                            ) ?>

                        </div>

                    </div>

                </div>

            </div>


            <!-- MENUNGGAK -->

            <div class="col-lg-4 col-md-6 mb-3">

                <div class="card dashboard-card kpi-card">

                    <div class="card-body">

                        <div class="kpi-icon bg-danger text-white">

                            <i class="fas fa-exclamation-triangle"></i>

                        </div>

                        <div class="kpi-label">
                            Total Menunggak
                        </div>

                        <div class="kpi-value">

                            <?= $money(
                                $summary[
                                    'total_menunggak'
                                ] ?? 0
                            ) ?>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- ========================================================== -->
        <!-- STATUS -->
        <!-- ========================================================== -->

        <div class="row">


            <!-- STATUS PIUTANG -->

            <div class="col-lg-6 mb-4">

                <div class="card section-card summary-card h-100">

                    <div class="card-header">

                        <h3 class="section-title">

                            <i class="fas fa-chart-pie mr-2"></i>

                            Status Piutang

                        </h3>

                        <div class="section-subtitle">

                            Ringkasan kondisi pembayaran.

                        </div>

                    </div>


                    <div class="card-body">

                        <div class="status-item">

                            <span class="status-label">

                                <i class="fas fa-check-circle text-success mr-2"></i>

                                Lunas

                            </span>

                            <span class="status-number">

                                <?= number_format(
                                    (int) (
                                        $summary[
                                            'jumlah_lunas'
                                        ] ?? 0
                                    ),
                                    0,
                                    ',',
                                    '.'
                                ) ?>

                            </span>

                        </div>


                        <div class="status-item">

                            <span class="status-label">

                                <i class="fas fa-clock text-warning mr-2"></i>

                                Belum Lunas

                            </span>

                            <span class="status-number">

                                <?= number_format(
                                    (int) (
                                        $summary[
                                            'jumlah_belum_lunas'
                                        ] ?? 0
                                    ),
                                    0,
                                    ',',
                                    '.'
                                ) ?>

                            </span>

                        </div>

                    </div>

                </div>

            </div>


            <!-- STATUS JATUH TEMPO -->

            <div class="col-lg-6 mb-4">

                <div class="card section-card summary-card h-100">

                    <div class="card-header">

                        <h3 class="section-title">

                            <i class="fas fa-calendar-check mr-2"></i>

                            Status Jatuh Tempo

                        </h3>

                        <div class="section-subtitle">

                            Posisi jatuh tempo seluruh piutang.

                        </div>

                    </div>


                    <div class="card-body">

                        <div class="status-item">

                            <span class="status-label">

                                <i class="fas fa-calendar text-info mr-2"></i>

                                Belum Jatuh Tempo

                            </span>

                            <span class="status-number">

                                <?= number_format(
                                    (int) (
                                        $summary[
                                            'jumlah_belum_jatuh_tempo'
                                        ] ?? 0
                                    ),
                                    0,
                                    ',',
                                    '.'
                                ) ?>

                            </span>

                        </div>


                        <div class="status-item">

                            <span class="status-label">

                                <i class="fas fa-calendar-day text-warning mr-2"></i>

                                Jatuh Tempo

                            </span>

                            <span class="status-number">

                                <?= number_format(
                                    (int) (
                                        $summary[
                                            'jumlah_jatuh_tempo'
                                        ] ?? 0
                                    ),
                                    0,
                                    ',',
                                    '.'
                                ) ?>

                            </span>

                        </div>


                        <div class="status-item">

                            <span class="status-label">

                                <i class="fas fa-exclamation-circle text-danger mr-2"></i>

                                Menunggak

                            </span>

                            <span class="status-number text-danger">

                                <?= number_format(
                                    (int) (
                                        $summary[
                                            'jumlah_menunggak'
                                        ] ?? 0
                                    ),
                                    0,
                                    ',',
                                    '.'
                                ) ?>

                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- ========================================================== -->
        <!-- OVERDUE -->
        <!-- ========================================================== -->

        <div class="row">

            <div class="col-12 mb-4">

                <div class="card section-card overdue-card">

                    <div class="card-header d-flex justify-content-between align-items-center">

                        <div>

                            <h3 class="section-title">

                                <i class="fas fa-exclamation-triangle text-danger mr-2"></i>

                                Piutang Menunggak

                            </h3>

                            <div class="section-subtitle">

                                Piutang yang perlu mendapat perhatian.

                            </div>

                        </div>


                        <a
                            href="<?= base_url('laporan/piutang') ?>"
                            class="dashboard-link"
                        >
                            Lihat Semua
                            <i class="fas fa-arrow-right ml-1"></i>
                        </a>

                    </div>


                    <div class="card-body p-0">

                        <div class="table-responsive">

                            <table class="table table-dashboard table-hover">

                                <thead>

                                    <tr>

                                        <th>
                                            No. Piutang
                                        </th>

                                        <th>
                                            Customer
                                        </th>

                                        <th>
                                            Jatuh Tempo
                                        </th>

                                        <th class="text-right">
                                            Total Tagihan
                                        </th>

                                        <th class="text-right">
                                            Sisa Tagihan
                                        </th>

                                        <th class="text-center">
                                            Status
                                        </th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <?php if (
                                        empty($overdue)
                                    ) : ?>

                                        <tr>

                                            <td
                                                colspan="6"
                                                class="table-empty"
                                            >

                                                <i class="fas fa-check-circle text-success"></i>

                                                Tidak ada piutang yang menunggak.

                                            </td>

                                        </tr>

                                    <?php else : ?>

                                        <?php foreach (
                                            $overdue as $row
                                        ) : ?>

                                            <tr>

                                                <td class="font-weight-bold">

                                                    <?= esc(
                                                        $row[
                                                            'nomor_piutang'
                                                        ] ?? '-'
                                                    ) ?>

                                                </td>


                                                <td>

                                                    <span class="customer-name">

                                                        <?= esc(
                                                            $row[
                                                                'nama_customer'
                                                            ]
                                                            ?? $row[
                                                                'customer_nama'
                                                            ]
                                                            ?? '-'
                                                        ) ?>

                                                    </span>

                                                </td>


                                                <td>

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

                                                    <?= $money(
                                                        $row[
                                                            'total_tagihan'
                                                        ] ?? 0
                                                    ) ?>

                                                </td>


                                                <td class="text-right font-weight-bold">

                                                    <?= $money(
                                                        $row[
                                                            'sisa_tagihan'
                                                        ] ?? 0
                                                    ) ?>

                                                </td>


                                                <td class="text-center">

                                                    <?= $statusBadge(
                                                        (string) (
                                                            $row[
                                                                'status_jatuh_tempo'
                                                            ]
                                                            ?? ''
                                                        )
                                                    ) ?>

                                                </td>

                                            </tr>

                                        <?php endforeach; ?>

                                    <?php endif; ?>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- ========================================================== -->
        <!-- LATEST ACTIVITY -->
        <!-- ========================================================== -->

        <div class="row">


            <!-- PIUTANG TERBARU -->

            <div class="col-lg-6 mb-4">

                <div class="card section-card activity-card h-100">

                    <div class="card-header d-flex justify-content-between align-items-center">

                        <div>

                            <h3 class="section-title">

                                <i class="fas fa-file-invoice-dollar mr-2"></i>

                                Piutang Terbaru

                            </h3>

                            <div class="section-subtitle">

                                Lima piutang terakhir.

                            </div>

                        </div>


                        <a
                            href="<?= base_url('piutang') ?>"
                            class="dashboard-link"
                        >
                            Lihat Semua
                            <i class="fas fa-arrow-right ml-1"></i>
                        </a>

                    </div>


                    <div class="card-body p-0">

                        <div class="table-responsive">

                            <table class="table table-dashboard table-hover">

                                <thead>

                                    <tr>

                                        <th>
                                            Piutang
                                        </th>

                                        <th>
                                            Customer
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
                                        empty(
                                            $latestPiutang
                                        )
                                    ) : ?>

                                        <tr>

                                            <td
                                                colspan="4"
                                                class="table-empty"
                                            >

                                                <i class="fas fa-inbox"></i>

                                                Belum ada data piutang.

                                            </td>

                                        </tr>

                                    <?php else : ?>

                                        <?php foreach (
                                            $latestPiutang as $row
                                        ) : ?>

                                            <tr>

                                                <td class="font-weight-bold">

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
                                                            'customer_nama'
                                                        ]
                                                        ?? '-'
                                                    ) ?>

                                                </td>


                                                <td class="text-right">

                                                    <?= $money(
                                                        $row[
                                                            'sisa_tagihan'
                                                        ] ?? 0
                                                    ) ?>

                                                </td>


                                                <td class="text-center">

                                                    <?= $statusBadge(
                                                        (string) (
                                                            $row[
                                                                'status_jatuh_tempo'
                                                            ]
                                                            ?? ''
                                                        )
                                                    ) ?>

                                                </td>

                                            </tr>

                                        <?php endforeach; ?>

                                    <?php endif; ?>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>


            <!-- PEMBAYARAN TERBARU -->

            <div class="col-lg-6 mb-4">

                <div class="card section-card activity-card h-100">

                    <div class="card-header d-flex justify-content-between align-items-center">

                        <div>

                            <h3 class="section-title">

                                <i class="fas fa-money-check-alt mr-2"></i>

                                Pembayaran Terbaru

                            </h3>

                            <div class="section-subtitle">

                                Lima transaksi pembayaran terakhir.

                            </div>

                        </div>


                        <a
                            href="<?= base_url('laporan/pembayaran') ?>"
                            class="dashboard-link"
                        >
                            Lihat Semua
                            <i class="fas fa-arrow-right ml-1"></i>
                        </a>

                    </div>


                    <div class="card-body p-0">

                        <div class="table-responsive">

                            <table class="table table-dashboard table-hover">

                                <thead>

                                    <tr>

                                        <th>
                                            Pembayaran
                                        </th>

                                        <th>
                                            Customer
                                        </th>

                                        <th class="text-right">
                                            Nominal
                                        </th>

                                        <th class="text-center">
                                            Status
                                        </th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <?php if (
                                        empty(
                                            $latestPembayaran
                                        )
                                    ) : ?>

                                        <tr>

                                            <td
                                                colspan="4"
                                                class="table-empty"
                                            >

                                                <i class="fas fa-receipt"></i>

                                                Belum ada transaksi pembayaran.

                                            </td>

                                        </tr>

                                    <?php else : ?>

                                        <?php foreach (
                                            $latestPembayaran as $row
                                        ) : ?>

                                            <tr>

                                                <td class="font-weight-bold">

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
                                                        ]
                                                        ?? $row[
                                                            'customer_nama'
                                                        ]
                                                        ?? '-'
                                                    ) ?>

                                                </td>


                                                <td class="text-right">

                                                    <?= $money(
                                                        $row[
                                                            'nominal_pembayaran'
                                                        ] ?? 0
                                                    ) ?>

                                                </td>


                                                <td class="text-center">

                                                    <?= $paymentBadge(
                                                        (string) (
                                                            $row[
                                                                'status'
                                                            ]
                                                            ?? ''
                                                        )
                                                    ) ?>

                                                </td>

                                            </tr>

                                        <?php endforeach; ?>

                                    <?php endif; ?>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>


    </div>

</section>

<?= $this->endSection() ?>