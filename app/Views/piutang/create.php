<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?>
Tambah Piutang
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="mb-4">

    <h3 class="mb-1">

        <i class="fas fa-plus-circle text-primary mr-2"></i>

        Tambah Piutang

    </h3>

    <p class="text-muted mb-0">

        Tambahkan transaksi piutang baru ke dalam sistem.

    </p>

</div>


<div class="card card-primary card-outline shadow-sm">

    <div class="card-header">

        <h3 class="card-title">

            <i class="fas fa-file-invoice-dollar mr-1"></i>

            Form Piutang

        </h3>

    </div>


    <form
        action="<?= site_url('piutang/store') ?>"
        method="post"
    >

        <?= csrf_field() ?>


        <div class="card-body">

            <?= $this->include('piutang/_form') ?>

        </div>


        <div class="card-footer">

            <?= view('components/form/form_buttons', [
                'backUrl'    => 'piutang',
                'submitText' => 'Simpan',
            ]) ?>

        </div>

    </form>

</div>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>

<?= $this->include('piutang/_script') ?>

<?= $this->endSection() ?>