<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="card shadow-sm">

    <div class="card-header bg-danger text-white">
        <h5 class="mb-0">
            <i class="fas fa-key"></i>
            Reset Password
        </h5>
    </div>

    <form
        method="post"
        action="<?= site_url('pengaturan/user/' . $user->id . '/reset-password') ?>">

        <?= csrf_field() ?>

        <div class="card-body">

            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>

                Password lama akan diganti dengan password baru yang Anda masukkan.
            </div>

            <div class="mb-3">
                <label class="form-label">
                    Username
                </label>

                <input
                    type="text"
                    class="form-control"
                    value="<?= esc($user->username) ?>"
                    readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">
                    Email
                </label>

                <input
                    type="email"
                    class="form-control"
                    value="<?= esc($user->email) ?>"
                    readonly>
            </div>

            <hr>

            <div class="mb-3">
                <label class="form-label">
                    Password Baru
                </label>

                <input
                    type="password"
                    name="password"
                    class="form-control <?= session('errors.password') ? 'is-invalid' : '' ?>">

                <?php if (session('errors.password')) : ?>

                    <div class="invalid-feedback">
                        <?= session('errors.password') ?>
                    </div>

                <?php endif ?>
            </div>

            <div class="mb-3">
                <label class="form-label">
                    Konfirmasi Password
                </label>

                <input
                    type="password"
                    name="password_confirm"
                    class="form-control <?= session('errors.password_confirm') ? 'is-invalid' : '' ?>">

                <?php if (session('errors.password_confirm')) : ?>

                    <div class="invalid-feedback">
                        <?= session('errors.password_confirm') ?>
                    </div>

                <?php endif ?>
            </div>

        </div>

        <div class="card-footer text-end">

            <a
                href="<?= site_url('pengaturan/user') ?>"
                class="btn btn-secondary">

                <i class="fas fa-arrow-left"></i>

                Kembali

            </a>

            <button
                type="submit"
                class="btn btn-danger">

                <i class="fas fa-key"></i>

                Reset Password

            </button>

        </div>

    </form>

</div>

<?= $this->endSection() ?>