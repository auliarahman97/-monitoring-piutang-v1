<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?>
Tambah Customer
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-3">

    <div>

        <h3 class="mb-1">
            <i class="fas fa-user-plus text-primary mr-2"></i>
            Tambah Customer
        </h3>

        <p class="text-muted mb-0">
            Tambahkan data customer baru ke dalam sistem monitoring piutang.
        </p>

    </div>

</div>

<!-- Card -->
<div class="card shadow-sm">

    <div class="card-header">

        <h3 class="card-title">

            <i class="fas fa-id-card mr-1"></i>

            Form Customer

        </h3>

    </div>

    <form action="<?= base_url('customer/store') ?>" method="post">

        <?= csrf_field() ?>

        <div class="card-body">

            <?= $this->include('customer/_form') ?>

        </div>

        <div class="card-footer">

            <div class="d-flex justify-content-between">

                <a href="<?= base_url('customer') ?>"
                   class="btn btn-secondary">

                    <i class="fas fa-arrow-left mr-1"></i>

                    Kembali

                </a>

                <button type="submit"
                        class="btn btn-primary">

                    <i class="fas fa-save mr-1"></i>

                    Simpan

                </button>

            </div>

        </div>

    </form>

</div>

<?= $this->endSection() ?>