<?= $this->extend('layouts/app') ?>


<?= $this->section('title') ?>
Pembayaran Baru
<?= $this->endSection() ?>


<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h3 class="mb-1">

            <i class="fas fa-money-check-alt text-primary mr-2"></i>

            Pembayaran Baru

        </h3>

        <p class="text-muted mb-0">

            Catat pembayaran untuk transaksi piutang customer.

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


<div class="row">


    <!-- ==========================================================
         FORM
         ========================================================== -->

    <div class="col-lg-8">

        <?= $this->include(
            'pembayaran/_form'
        ) ?>

    </div>


    <!-- ==========================================================
         INFORMASI
         ========================================================== -->

    <div class="col-lg-4">

        <div
            class="card card-info card-outline shadow-sm"
        >

            <div class="card-header">

                <h3 class="card-title">

                    <i class="fas fa-info-circle mr-1"></i>

                    Informasi Pembayaran

                </h3>

            </div>


            <div class="card-body">


                <div class="callout callout-primary">

                    <h5>
                        Urutan Pembayaran
                    </h5>

                    <p class="mb-2">

                        Setiap pembayaran akan dialokasikan
                        berdasarkan urutan:

                    </p>

                    <ol class="mb-0 pl-4">

                        <li>
                            <strong>Denda</strong>
                        </li>

                        <li>
                            <strong>Bunga</strong>
                        </li>

                        <li>
                            <strong>Pokok</strong>
                        </li>

                    </ol>

                </div>


                <div class="callout callout-warning">

                    <h5>
                        Setelah Jatuh Tempo
                    </h5>

                    <p class="mb-0">

                        Apabila pembayaran dilakukan setelah
                        jatuh tempo, denda akan dihitung
                        otomatis berdasarkan aturan denda
                        yang melekat pada piutang.

                    </p>

                </div>


                <div class="callout callout-success">

                    <h5>
                        Histori Transaksi
                    </h5>

                    <p class="mb-0">

                        Pembayaran yang sudah disimpan tidak
                        dapat diedit atau dihapus.

                    </p>

                </div>


                <div class="callout callout-danger">

                    <h5>
                        Koreksi Pembayaran
                    </h5>

                    <p class="mb-0">

                        Jika terjadi kesalahan, pembayaran
                        dibatalkan dan dibuat transaksi
                        pembayaran baru.

                    </p>

                </div>


            </div>

        </div>

    </div>

</div>

<?= $this->endSection() ?>


<!-- ================================================================
     PAGE SCRIPTS
     ================================================================ -->

<?= $this->section('scripts') ?>

<?= $this->include(
    'pembayaran/_script'
) ?>

<?= $this->endSection() ?>