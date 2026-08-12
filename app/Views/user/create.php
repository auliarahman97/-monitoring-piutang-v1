<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="card">

    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-user-plus"></i>
            Tambah User
        </h5>
    </div>

    <div class="card-body">

        <form method="post" action="<?= site_url('pengaturan/user') ?>">

            <?= csrf_field() ?>

            <?= $this->include('user/_form', [
                'mode' => $mode,
            ]) ?>

        </form>

    </div>

</div>

<?= $this->endSection() ?>