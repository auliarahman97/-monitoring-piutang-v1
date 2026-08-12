<?php

$errors = session('errors') ?? [];

// Password hanya ditampilkan saat membuat user
$isCreate = ($mode === 'create');

// Default role untuk halaman Create
$currentGroup = 'admin';

// Jika Edit User, ambil role milik user
if (! $isCreate) {
    $currentGroup = $user->getGroups()[0] ?? 'admin';
}

// old() akan menangani validasi gagal
$selectedGroup = old('group', $currentGroup);

?>

<?php if ($errors) : ?>
    <div class="alert alert-danger">
        <h6>
            <i class="fas fa-exclamation-circle mr-1"></i>
            Terdapat kesalahan pada data yang diinput.
        </h6>

        <ul class="mb-0">
            <?php foreach ($errors as $error) : ?>
                <li><?= esc($error) ?></li>
            <?php endforeach ?>
        </ul>
    </div>
<?php endif ?>

<div class="row">

    <div class="col-md-6">
        <div class="form-group">

            <label>
                Username
                <span class="text-danger">*</span>
            </label>

            <input
                type="text"
                name="username"
                class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>"
                value="<?= esc(old('username', $user?->username ?? '')) ?>"
                placeholder="Masukkan username">

            <?php if (isset($errors['username'])) : ?>
                <div class="invalid-feedback">
                    <?= esc($errors['username']) ?>
                </div>
            <?php endif ?>

        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">

            <label>
                Email
                <span class="text-danger">*</span>
            </label>

            <input
                type="email"
                name="email"
                class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                value="<?= esc(old('email', $user?->email ?? '')) ?>"
                placeholder="Masukkan email">

            <?php if (isset($errors['email'])) : ?>
                <div class="invalid-feedback">
                    <?= esc($errors['email']) ?>
                </div>
            <?php endif ?>

        </div>
    </div>

</div>

<?php if ($isCreate) : ?>

    <div class="row">

        <div class="col-md-6">
            <div class="form-group">

                <label>
                    Password
                    <span class="text-danger">*</span>
                </label>

                <input
                    type="password"
                    name="password"
                    class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                    placeholder="Masukkan password">

                <?php if (isset($errors['password'])) : ?>
                    <div class="invalid-feedback">
                        <?= esc($errors['password']) ?>
                    </div>
                <?php endif ?>

            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">

                <label>
                    Konfirmasi Password
                    <span class="text-danger">*</span>
                </label>

                <input
                    type="password"
                    name="password_confirm"
                    class="form-control <?= isset($errors['password_confirm']) ? 'is-invalid' : '' ?>"
                    placeholder="Ulangi password">

                <?php if (isset($errors['password_confirm'])) : ?>
                    <div class="invalid-feedback">
                        <?= esc($errors['password_confirm']) ?>
                    </div>
                <?php endif ?>

            </div>
        </div>

    </div>

<?php endif ?>

<div class="form-group">

    <label class="mb-2">
        Hak Akses
        <span class="text-danger">*</span>
    </label>

    <div class="custom-control custom-radio">

        <input
            class="custom-control-input"
            type="radio"
            id="admin"
            name="group"
            value="admin"
            <?= $selectedGroup === 'admin' ? 'checked' : '' ?>>

        <label class="custom-control-label" for="admin">
            Admin
        </label>

    </div>

    <div class="custom-control custom-radio">

        <input
            class="custom-control-input"
            type="radio"
            id="petugas"
            name="group"
            value="petugas"
            <?= $selectedGroup === 'petugas' ? 'checked' : '' ?>>

        <label class="custom-control-label" for="petugas">
            Petugas
        </label>

    </div>

    <div class="custom-control custom-radio">

        <input
            class="custom-control-input"
            type="radio"
            id="pimpinan"
            name="group"
            value="pimpinan"
            <?= $selectedGroup === 'pimpinan' ? 'checked' : '' ?>>

        <label class="custom-control-label" for="pimpinan">
            Pimpinan
        </label>

    </div>
    
    <?php if (isset($errors['group'])) : ?>

        <div class="text-danger small mt-2">
            <?= esc($errors['group']) ?>
        </div>

    <?php endif ?>
</div>

<div class="text-right">

    <a
        href="<?= site_url('pengaturan/user') ?>"
        class="btn btn-secondary">

        <i class="fas fa-arrow-left mr-1"></i>
        Kembali

    </a>


    <?php if ($isCreate) : ?>

        <button
            type="submit"
            class="btn btn-success">

            <i class="fas fa-save mr-1"></i>
            Simpan

        </button>

    <?php else : ?>

        <button
            type="submit"
            class="btn btn-primary">

            <i class="fas fa-edit mr-1"></i>
            Update

        </button>

    <?php endif ?>

</div>