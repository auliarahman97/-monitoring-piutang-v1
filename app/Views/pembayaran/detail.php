<?= $this->extend('layouts/app') ?>


<?= $this->section('title') ?>
Detail Pembayaran
<?= $this->endSection() ?>


<?= $this->section('content') ?>

<?php
$status =
    strtolower(
        trim(
            (string) (
                $pembayaran['status']
                ?? 'valid'
            )
        )
    );


$sisaTagihan =
    (float) (
        $pembayaran[
            'sisa_tagihan'
        ] ?? 0
    );


$isCancelled =
    in_array(
        $status,
        [
            'dibatalkan',
            'batal',
            'cancelled',
        ],
        true
    );


$isLunas =
    ! $isCancelled
    && $sisaTagihan <= 0;


$nomorPembayaran =
    $pembayaran[
        'nomor_pembayaran'
    ] ?? '-';
?>


<!-- ==============================================================
     HEADER
     ============================================================== -->

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h3 class="mb-1">

            <i class="fas fa-receipt text-primary mr-2"></i>

            Detail Pembayaran

        </h3>

        <p class="text-muted mb-0">

            <?= esc(
                $nomorPembayaran
            ) ?>

        </p>

    </div>


    <div>

        <a
            href="<?= site_url('pembayaran') ?>"
            class="btn btn-secondary"
        >

            <i class="fas fa-arrow-left mr-1"></i>

            Kembali

        </a>

    </div>

</div>


<!-- ==============================================================
     STATUS
     ============================================================== -->

<?php if ($isCancelled) : ?>

    <div class="alert alert-danger shadow-sm">

        <h5 class="mb-1">

            <i class="fas fa-ban mr-1"></i>

            Pembayaran Dibatalkan

        </h5>

        <p class="mb-0">

            Transaksi tetap tersimpan sebagai histori,
            tetapi tidak diperhitungkan dalam saldo piutang.

        </p>

    </div>


<?php elseif ($isLunas) : ?>

    <div class="alert alert-success shadow-sm">

        <h5 class="mb-1">

            <i class="fas fa-check-circle mr-1"></i>

            Piutang Lunas

        </h5>

        <p class="mb-0">

            Pembayaran ini telah melunasi seluruh tagihan.

        </p>

    </div>


<?php else : ?>

    <div class="alert alert-primary shadow-sm">

        <h5 class="mb-1">

            <i class="fas fa-check-circle mr-1"></i>

            Pembayaran Valid

        </h5>

        <p class="mb-0">

            Transaksi ini tercatat dan diperhitungkan
            dalam saldo piutang.

        </p>

    </div>

<?php endif; ?>


<div class="row">

    <!-- ==========================================================
         INFORMASI TRANSAKSI
         ========================================================== -->

    <div class="col-lg-6">

        <div
            class="card card-primary card-outline shadow-sm"
        >

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-file-invoice mr-1"></i>

                    Informasi Transaksi

                </h3>

            </div>


            <div class="card-body">

                <dl class="row mb-0">

                    <dt class="col-sm-5">
                        Nomor Pembayaran
                    </dt>

                    <dd class="col-sm-7 font-weight-bold">

                        <?= esc(
                            $nomorPembayaran
                        ) ?>

                    </dd>


                    <dt class="col-sm-5">
                        Tanggal Pembayaran
                    </dt>

                    <dd class="col-sm-7">

                        <?= ! empty(
                            $pembayaran[
                                'tanggal_pembayaran'
                            ]
                        )
                            ? date(
                                'd-m-Y',
                                strtotime(
                                    $pembayaran[
                                        'tanggal_pembayaran'
                                    ]
                                )
                            )
                            : '-'
                        ?>

                    </dd>


                    <dt class="col-sm-5">
                        Customer
                    </dt>

                    <dd class="col-sm-7">

                        <div class="font-weight-bold">

                            <?= esc(
                                $pembayaran[
                                    'nama_customer'
                                ]
                                ?? $pembayaran[
                                    'nama'
                                ]
                                ?? '-'
                            ) ?>

                        </div>


                        <?php if (
                            ! empty(
                                $pembayaran[
                                    'kode_customer'
                                ]
                            )
                        ) : ?>

                            <small class="text-muted">

                                <?= esc(
                                    $pembayaran[
                                        'kode_customer'
                                    ]
                                ) ?>

                            </small>

                        <?php endif; ?>

                    </dd>


                    <dt class="col-sm-5">
                        Nomor Piutang
                    </dt>

                    <dd class="col-sm-7">

                        <?php if (
                            ! empty(
                                $pembayaran[
                                    'nomor_piutang'
                                ]
                            )
                        ) : ?>

                            <a
                                href="<?= site_url(
                                    'piutang/detail/'
                                    . (int) (
                                        $pembayaran[
                                            'piutang_id'
                                        ]
                                    )
                                ) ?>"
                                class="font-weight-bold"
                            >

                                <?= esc(
                                    $pembayaran[
                                        'nomor_piutang'
                                    ]
                                ) ?>

                            </a>

                        <?php else : ?>

                            -

                        <?php endif; ?>

                    </dd>


                    <dt class="col-sm-5">
                        Status
                    </dt>

                    <dd class="col-sm-7">

                        <?php if ($isCancelled) : ?>

                            <span
                                class="badge badge-danger"
                            >

                                <i class="fas fa-ban mr-1"></i>

                                Dibatalkan

                            </span>

                        <?php elseif ($isLunas) : ?>

                            <span
                                class="badge badge-success"
                            >

                                <i
                                    class="fas fa-check-circle mr-1"
                                ></i>

                                Lunas

                            </span>

                        <?php else : ?>

                            <span
                                class="badge badge-primary"
                            >

                                <i class="fas fa-check mr-1"></i>

                                Valid

                            </span>

                        <?php endif; ?>

                    </dd>

                </dl>

            </div>

        </div>

    </div>


    <!-- ==========================================================
         RINGKASAN KEUANGAN
         ========================================================== -->

    <div class="col-lg-6">

        <div
            class="card card-success card-outline shadow-sm"
        >

            <div class="card-header">

                <h3 class="card-title">

                    <i
                        class="fas fa-money-bill-wave mr-1"
                    ></i>

                    Ringkasan Keuangan

                </h3>

            </div>


            <div class="card-body">

                <div class="row">

                    <!-- Total -->
                    <div class="col-md-4">

                        <div
                            class="description-block border-right"
                        >

                            <span
                                class="description-header"
                            >

                                <?= rupiah(
                                    (float) (
                                        $pembayaran[
                                            'total_tagihan'
                                        ] ?? 0
                                    )
                                ) ?>

                            </span>

                            <span
                                class="description-text"
                            >
                                TOTAL TAGIHAN
                            </span>

                        </div>

                    </div>


                    <!-- Bayar -->
                    <div class="col-md-4">

                        <div
                            class="description-block border-right"
                        >

                            <span
                                class="description-header text-success"
                            >

                                <?= rupiah(
                                    (float) (
                                        $pembayaran[
                                            'nominal_pembayaran'
                                        ] ?? 0
                                    )
                                ) ?>

                            </span>

                            <span
                                class="description-text"
                            >
                                PEMBAYARAN
                            </span>

                        </div>

                    </div>


                    <!-- Sisa -->
                    <div class="col-md-4">

                        <div
                            class="description-block"
                        >

                            <span
                                class="description-header <?= $sisaTagihan <= 0
                                    ? 'text-success'
                                    : 'text-warning'
                                ?>"
                            >

                                <?= rupiah(
                                    $sisaTagihan
                                ) ?>

                            </span>

                            <span
                                class="description-text"
                            >
                                SISA TAGIHAN
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- ==========================================================
         ALOKASI PEMBAYARAN
         ========================================================== -->

    <div class="col-12">

        <div
            class="card card-info card-outline shadow-sm"
        >

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-random mr-1"></i>

                    Alokasi Pembayaran

                </h3>

            </div>


            <div class="card-body">

                <div class="row">

                    <!-- Denda -->
                    <div class="col-md-4">

                        <div class="description-block">

                            <span
                                class="description-header text-danger"
                            >

                                <?= rupiah(
                                    (float) (
                                        $pembayaran[
                                            'alokasi_denda'
                                        ] ?? 0
                                    )
                                ) ?>

                            </span>

                            <span
                                class="description-text"
                            >
                                DENDA
                            </span>

                        </div>

                    </div>


                    <!-- Bunga -->
                    <div class="col-md-4">

                        <div class="description-block">

                            <span
                                class="description-header text-warning"
                            >

                                <?= rupiah(
                                    (float) (
                                        $pembayaran[
                                            'alokasi_bunga'
                                        ] ?? 0
                                    )
                                ) ?>

                            </span>

                            <span
                                class="description-text"
                            >
                                BUNGA
                            </span>

                        </div>

                    </div>


                    <!-- Pokok -->
                    <div class="col-md-4">

                        <div class="description-block">

                            <span
                                class="description-header text-primary"
                            >

                                <?= rupiah(
                                    (float) (
                                        $pembayaran[
                                            'alokasi_pokok'
                                        ] ?? 0
                                    )
                                ) ?>

                            </span>

                            <span
                                class="description-text"
                            >
                                POKOK
                            </span>

                        </div>

                    </div>

                </div>


                <div class="alert alert-light border mb-0">

                    <i
                        class="fas fa-info-circle mr-1"
                    ></i>

                    Urutan alokasi:

                    <strong>
                        Denda → Bunga → Pokok
                    </strong>

                </div>

            </div>

        </div>

    </div>


    <!-- ==========================================================
         KETERANGAN
         ========================================================== -->

    <?php if (
        ! empty(
            $pembayaran[
                'keterangan'
            ]
        )
    ) : ?>

        <div class="col-12">

            <div
                class="card card-secondary card-outline shadow-sm"
            >

                <div class="card-header">

                    <h3 class="card-title">

                        <i
                            class="fas fa-sticky-note mr-1"
                        ></i>

                        Keterangan

                    </h3>

                </div>


                <div class="card-body">

                    <?= nl2br(
                        esc(
                            $pembayaran[
                                'keterangan'
                            ]
                        )
                    ) ?>

                </div>

            </div>

        </div>

    <?php endif; ?>


    <!-- ==========================================================
         INFORMASI PEMBATALAN
         ========================================================== -->

    <?php if ($isCancelled) : ?>

        <div class="col-12">

            <div
                class="card card-danger card-outline shadow-sm"
            >

                <div class="card-header">

                    <h3 class="card-title">

                        <i class="fas fa-ban mr-1"></i>

                        Informasi Pembatalan

                    </h3>

                </div>


                <div class="card-body">

                    <dl class="row mb-0">

                        <dt class="col-sm-3">
                            Dibatalkan Pada
                        </dt>

                        <dd class="col-sm-9">

                            <?= ! empty(
                                $pembayaran[
                                    'cancelled_at'
                                ]
                            )
                                ? date(
                                    'd-m-Y H:i:s',
                                    strtotime(
                                        $pembayaran[
                                            'cancelled_at'
                                        ]
                                    )
                                )
                                : '-'
                            ?>

                        </dd>


                        <dt class="col-sm-3">
                            Alasan
                        </dt>

                        <dd class="col-sm-9">

                            <?= ! empty(
                                $pembayaran[
                                    'keterangan'
                                ]
                            )
                                ? nl2br(
                                    esc(
                                        $pembayaran[
                                            'alasan_pembatalan'
                                        ]
                                    )
                                )
                                : '-'
                            ?>

                        </dd>

                    </dl>

                </div>

            </div>

        </div>

    <?php endif; ?>


    <!-- ==========================================================
         ACTION
         ========================================================== -->

    <div class="col-12">

        <div
            class="d-flex justify-content-between align-items-center"
        >

            <a
                href="<?= site_url('pembayaran') ?>"
                class="btn btn-secondary"
            >

                <i class="fas fa-arrow-left mr-1"></i>

                Kembali

            </a>


            <?php if (
                ! $isCancelled
                && ! $isLunas
            ) : ?>

                <button
                    type="button"
                    class="btn btn-danger btn-cancel-payment"
                    data-id="<?= (int) (
                        $pembayaran['id']
                    ) ?>"
                    data-url="<?= site_url(
                        'pembayaran/cancel/'
                        . (int) (
                            $pembayaran['id']
                        )
                    ) ?>"
                    data-number="<?= esc(
                        $nomorPembayaran
                    ) ?>"
                >

                    <i class="fas fa-ban mr-1"></i>

                    Batalkan Pembayaran

                </button>

            <?php endif; ?>

        </div>

    </div>

</div>


<?= $this->endSection() ?>


<?= $this->section('scripts') ?>

<?= $this->include(
    'pembayaran/_cancel-script'
) ?>

<?= $this->endSection() ?>