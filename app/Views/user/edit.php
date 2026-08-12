<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="card shadow-sm">

    <div class="card-header bg-warning text-white">
        <h5 class="mb-0">
            <i class="fas fa-user-edit"></i>
            Edit User
        </h5>
    </div>

    <form
        method="post"
        action="<?= site_url('pengaturan/user/' . $user->id) ?>">

        <?= csrf_field() ?>

        <input
            type="hidden"
            name="_method"
            value="PUT">

        <div class="card-body">

            <?= $this->include('user/_form') ?>

        </div>

    </form>

</div>

<?= $this->endSection() ?>