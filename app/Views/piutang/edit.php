<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?>
Edit Piutang
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="mb-4">

    <h3 class="mb-1">

        <i class="fas fa-edit text-warning mr-2"></i>

        Edit Piutang

    </h3>

    <p class="text-muted mb-0">

        Perbarui data transaksi piutang.

    </p>

</div>


<div class="card card-warning card-outline shadow-sm">

    <div class="card-header">

        <h3 class="card-title">

            <i class="fas fa-file-invoice-dollar mr-1"></i>

            Form Piutang

        </h3>

    </div>


    <form
        action="<?= site_url(
            'piutang/update/' . $piutang['id']
        ) ?>"
        method="post"
    >

        <?= csrf_field() ?>


        <div class="card-body">

            <div class="alert alert-info">

                <i class="fas fa-info-circle mr-1"></i>

                Aturan denda tidak dipilih secara manual.
                Sistem menggunakan aturan yang sesuai dengan
                nominal piutang.

            </div>


            <?= $this->include('piutang/_form') ?>

        </div>


        <div class="card-footer">

            <?= view('components/form/form_buttons', [
                'backUrl'    => 'piutang',
                'submitText' => 'Update',
            ]) ?>

        </div>

    </form>

</div>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>

<?= $this->include('piutang/_script') ?>

<?= $this->endSection() ?>