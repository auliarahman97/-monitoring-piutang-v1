<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<?= $this->include('laporan/customer/_style') ?>


<!-- ================================================================
     HEADER
================================================================ -->

<section class="content-header">

    <div class="container-fluid">

        <div class="row mb-2">

            <div class="col-sm-6">

                <h1 class="font-weight-bold">
                    <i class="fas fa-user mr-2"></i>
                    Laporan Customer
                </h1>

            </div>


            <div class="col-sm-6">

                <ol class="breadcrumb float-sm-right">

                    <li class="breadcrumb-item">

                        <a href="<?= base_url() ?>">
                            Dashboard
                        </a>

                    </li>

                    <li class="breadcrumb-item active">
                        Laporan Customer
                    </li>

                </ol>

            </div>

        </div>

    </div>

</section>


<!-- ================================================================
     CONTENT
================================================================ -->

<section class="content">

    <div class="container-fluid">


        <!-- ========================================================
             FILTER
        ========================================================= -->

        <?= $this->include(
            'laporan/customer/_filter'
        ) ?>


        <!-- ========================================================
             ERROR
        ========================================================= -->

        <?php if (! empty($error)) : ?>

            <div class="alert alert-danger">

                <i class="fas fa-exclamation-circle mr-2"></i>

                <?= esc($error) ?>

            </div>

        <?php endif; ?>


        <!-- ========================================================
             EMPTY STATE
        ========================================================= -->

        <?php if (
            ! $customer
            && empty($error)
        ) : ?>

            <div class="card report-section">

                <div class="card-body">

                    <div class="empty-state">

                        <i class="fas fa-user-search d-block"></i>

                        <div class="empty-state-title">
                            Pilih Customer
                        </div>

                        <div>
                            Pilih customer terlebih dahulu
                            untuk menampilkan laporan.
                        </div>

                    </div>

                </div>

            </div>


        <?php endif; ?>


        <!-- ========================================================
             CUSTOMER REPORT
        ========================================================= -->

        <?php if ($customer) : ?>


            <!-- PROFILE -->

            <?= $this->include(
                'laporan/customer/_profile'
            ) ?>


            <!-- SUMMARY -->

            <?= $this->include(
                'laporan/customer/_summary'
            ) ?>


            <!-- PIUTANG -->

            <?= $this->include(
                'laporan/customer/_piutang'
            ) ?>


            <!-- PEMBAYARAN -->

            <?= $this->include(
                'laporan/customer/_pembayaran'
            ) ?>


        <?php endif; ?>


    </div>

</section>


<?= $this->endSection() ?>


<!-- ================================================================
     SCRIPTS
================================================================ -->

<?= $this->section('scripts') ?>

<?= $this->include(
    'laporan/customer/_script'
) ?>

<?= $this->endSection() ?>