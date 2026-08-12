<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?>
Detail Piutang
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h3 class="mb-1">

            <i class="fas fa-file-invoice-dollar text-info mr-2"></i>

            Detail Piutang

        </h3>

        <p class="text-muted mb-0">

            Informasi lengkap transaksi piutang.

        </p>

    </div>


    <a
        href="<?= site_url('piutang') ?>"
        class="btn btn-secondary"
    >

        <i class="fas fa-arrow-left mr-1"></i>

        Kembali

    </a>

</div>


<div class="row">


    <!-- Identitas -->

    <div class="col-md-6">

        <div class="card card-primary card-outline shadow-sm">

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-file-alt mr-1"></i>

                    Informasi Piutang

                </h3>

            </div>


            <div class="card-body">

                <dl class="row mb-0">

                    <dt class="col-sm-5">
                        Nomor Piutang
                    </dt>

                    <dd class="col-sm-7 font-weight-bold">

                        <?= esc(
                            $piutang['nomor_piutang']
                        ) ?>

                    </dd>


                    <dt class="col-sm-5">
                        Customer
                    </dt>

                    <dd class="col-sm-7">

                        <div class="font-weight-bold">

                            <?= esc(
                                $piutang['nama_customer'] ?? '-'
                            ) ?>

                        </div>

                        <?php if (! empty($piutang['kode_customer'])) : ?>

                            <small class="text-muted">

                                <?= esc(
                                    $piutang['kode_customer']
                                ) ?>

                            </small>

                        <?php endif; ?>

                    </dd>


                    <dt class="col-sm-5">
                        Tanggal Piutang
                    </dt>

                    <dd class="col-sm-7">

                        <?= tanggalIndonesia(
                            $piutang['tanggal_piutang']
                        ) ?>

                    </dd>


                    <dt class="col-sm-5">
                        Jatuh Tempo
                    </dt>

                    <dd class="col-sm-7">

                        <?= tanggalIndonesia(
                            $piutang['tanggal_jatuh_tempo']
                        ) ?>

                    </dd>

                </dl>

            </div>

        </div>

    </div>


    <!-- Ringkasan Piutang -->

    <div class="col-md-6">

        <div class="card card-success card-outline shadow-sm">

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-money-bill-wave mr-1"></i>

                    Ringkasan Piutang

                </h3>

            </div>


            <div class="card-body">

                <dl class="row mb-0">


                    <!-- Pokok -->

                    <dt class="col-sm-6">

                        Pokok

                    </dt>

                    <dd class="col-sm-6 text-right font-weight-bold">

                        <?= rupiah(
                            (float) $piutang['nominal_pokok']
                        ) ?>

                    </dd>


                    <!-- Bunga -->

                    <dt class="col-sm-6">

                        Bunga

                        <small class="text-muted d-block">

                            <?= number_format(
                                (float) $piutang['persentase_bunga'],
                                2,
                                ',',
                                '.'
                            ) ?>%

                        </small>

                    </dt>

                    <dd class="col-sm-6 text-right">

                        <?= rupiah(
                            (float) $piutang['nominal_bunga']
                        ) ?>

                    </dd>


                    <!-- Denda -->

                    <dt class="col-sm-6">

                        Denda Berjalan

                        <small class="text-muted d-block">

                            <?php if (
                                $dendaBerjalan > 0
                            ) : ?>

                                Keterlambatan

                            <?php else : ?>

                                Belum ada denda

                            <?php endif; ?>

                        </small>

                    </dt>

                    <dd class="col-sm-6 text-right">

                        <?php if ($dendaBerjalan > 0) : ?>

                            <span class="text-danger font-weight-bold">

                                <?= rupiah($dendaBerjalan) ?>

                            </span>

                        <?php else : ?>

                            <span class="text-muted">

                                <?= rupiah(0) ?>

                            </span>

                        <?php endif; ?>

                    </dd>


                    <div class="col-12">

                        <hr>

                    </div>


                    <!-- Total -->

                    <dt class="col-sm-6">

                        <strong>
                            Total Piutang
                        </strong>

                    </dt>

                    <dd class="col-sm-6 text-right">

                        <strong class="text-primary"
                                style="font-size: 1.25rem;">

                            <?= rupiah($totalPiutang) ?>

                        </strong>

                    </dd>

                </dl>

            </div>

        </div>

    </div>


    <!-- Aturan Denda -->

    <div class="col-12">

        <div class="card card-warning card-outline shadow-sm">

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-percentage mr-1"></i>

                    Snapshot Aturan Denda

                </h3>

            </div>


            <div class="card-body">

                <div class="row">

                    <div class="col-md-3">

                        <div class="description-block">

                            <span class="description-header">

                                <?= number_format(
                                    (float) $piutang['persentase_denda'],
                                    2,
                                    ',',
                                    '.'
                                ) ?>%

                            </span>

                            <span class="description-text">
                                DENDA / PERIODE
                            </span>

                        </div>

                    </div>


                    <div class="col-md-3">

                        <div class="description-block">

                            <span class="description-header">

                                <?= esc(
                                    $piutang['periode_denda_hari']
                                ) ?>

                                Hari

                            </span>

                            <span class="description-text">
                                PERIODE
                            </span>

                        </div>

                    </div>


                    <div class="col-md-3">

                        <div class="description-block">

                            <span class="description-header">

                                <?= number_format(
                                    (float) $piutang['maksimal_denda_persen'],
                                    2,
                                    ',',
                                    '.'
                                ) ?>%

                            </span>

                            <span class="description-text">
                                MAKSIMUM DENDA
                            </span>

                        </div>

                    </div>


                    <div class="col-md-3">

                        <div class="description-block">

                            <span class="description-header">

                                <?= ! empty($piutang['denda_versi_id'])
                                    ? '#' . esc($piutang['denda_versi_id'])
                                    : '-' ?>

                            </span>

                            <span class="description-text">
                                ID VERSI DENDA
                            </span>

                        </div>

                    </div>

                </div>


                <div class="alert alert-info mb-0">

                    <i class="fas fa-info-circle mr-1"></i>

                    Konfigurasi denda di atas merupakan snapshot
                    aturan yang diterapkan ketika piutang dibuat.

                    Perubahan aturan denda di masa depan tidak
                    mengubah snapshot piutang ini.

                </div>

            </div>

        </div>

    </div>


    <!-- Keterangan -->

    <?php if (! empty($piutang['keterangan'])) : ?>

        <div class="col-12">

            <div class="card card-secondary card-outline shadow-sm">

                <div class="card-header">

                    <h3 class="card-title">

                        <i class="fas fa-sticky-note mr-1"></i>

                        Keterangan

                    </h3>

                </div>

                <div class="card-body">

                    <?= nl2br(
                        esc($piutang['keterangan'])
                    ) ?>

                </div>

            </div>

        </div>

    <?php endif; ?>


</div>

<?= $this->endSection() ?>