<div class="row">

    <!-- ============================================================ -->
    <!-- Nama Customer -->
    <!-- ============================================================ -->

    <div class="col-md-6">

        <div class="form-group">

            <label for="nama">
                Nama Customer <span class="text-danger">*</span>
            </label>

            <input
                type="text"
                name="nama"
                id="nama"
                class="form-control <?= validation_show_error('nama') ? 'is-invalid' : '' ?>"
                value="<?= old('nama', $customer['nama'] ?? '') ?>"
                maxlength="100"
                placeholder="Masukkan nama customer"
                required
            >

            <?php if (validation_show_error('nama')) : ?>

                <div class="invalid-feedback">
                    <?= validation_show_error('nama') ?>
                </div>

            <?php endif; ?>

        </div>

    </div>


    <!-- ============================================================ -->
    <!-- NIK -->
    <!-- ============================================================ -->

    <div class="col-md-6">

        <div class="form-group">

            <label for="nik">
                NIK
            </label>

            <input
                type="text"
                name="nik"
                id="nik"
                class="form-control <?= validation_show_error('nik') ? 'is-invalid' : '' ?>"
                value="<?= old('nik', $customer['nik'] ?? '') ?>"
                maxlength="16"
                inputmode="numeric"
                placeholder="Masukkan NIK"
            >

            <small class="form-text text-muted">
                Opsional. Jika diisi harus 16 digit.
            </small>

            <?php if (validation_show_error('nik')) : ?>

                <div class="invalid-feedback">
                    <?= validation_show_error('nik') ?>
                </div>

            <?php endif; ?>

        </div>

    </div>

</div>


<div class="row">

    <!-- ============================================================ -->
    <!-- No. HP -->
    <!-- ============================================================ -->

    <div class="col-md-6">

        <div class="form-group">

            <label for="no_hp">
                No. HP
            </label>

            <input
                type="text"
                name="no_hp"
                id="no_hp"
                class="form-control <?= validation_show_error('no_hp') ? 'is-invalid' : '' ?>"
                value="<?= old('no_hp', $customer['no_hp'] ?? '') ?>"
                maxlength="15"
                inputmode="numeric"
                placeholder="Masukkan nomor HP"
            >

            <small class="form-text text-muted">
                Opsional. Minimal 10 digit dan maksimal 15 digit.
            </small>

            <?php if (validation_show_error('no_hp')) : ?>

                <div class="invalid-feedback">
                    <?= validation_show_error('no_hp') ?>
                </div>

            <?php endif; ?>

        </div>

    </div>


    <!-- ============================================================ -->
    <!-- Tanggal Terdaftar -->
    <!-- ============================================================ -->

    <div class="col-md-6">

        <div class="form-group">

            <label for="tanggal_terdaftar">
                Tanggal Terdaftar
                <span class="text-danger">*</span>
            </label>

            <input
                type="date"
                name="tanggal_terdaftar"
                id="tanggal_terdaftar"
                class="form-control <?= validation_show_error('tanggal_terdaftar') ? 'is-invalid' : '' ?>"
                value="<?= old('tanggal_terdaftar', $customer['tanggal_terdaftar'] ?? date('Y-m-d')) ?>"
                required
            >

            <small class="form-text text-muted">
                Wajib diisi. Menunjukkan tanggal customer terdaftar dalam sistem.
            </small>

            <?php if (validation_show_error('tanggal_terdaftar')) : ?>

                <div class="invalid-feedback">
                    <?= validation_show_error('tanggal_terdaftar') ?>
                </div>

            <?php endif; ?>

        </div>

    </div>

</div>


<div class="row">

    <!-- ============================================================ -->
    <!-- Status -->
    <!-- ============================================================ -->

    <div class="col-md-6">

        <div class="form-group">

            <label for="status">
                Status
                <span class="text-danger">*</span>
            </label>

            <select
                name="status"
                id="status"
                class="form-control <?= validation_show_error('status') ? 'is-invalid' : '' ?>"
                required
            >

                <option value="">
                    Pilih status
                </option>

                <option
                    value="1"
                    <?= old('status', $customer['status'] ?? '1') === '1' ? 'selected' : '' ?>
                >
                    Aktif
                </option>

                <option
                    value="0"
                    <?= old('status', $customer['status'] ?? '') === '0' ? 'selected' : '' ?>
                >
                    Tidak Aktif
                </option>

            </select>

            <small class="form-text text-muted">
                Wajib dipilih. Menentukan status customer dalam sistem.
            </small>

            <?php if (validation_show_error('status')) : ?>

                <div class="invalid-feedback">
                    <?= validation_show_error('status') ?>
                </div>

            <?php endif; ?>

        </div>

    </div>


    <!-- ============================================================ -->
    <!-- Alamat -->
    <!-- ============================================================ -->

    <div class="col-md-6">

        <div class="form-group">

            <label for="alamat">
                Alamat
            </label>

            <textarea
                name="alamat"
                id="alamat"
                rows="3"
                class="form-control <?= validation_show_error('alamat') ? 'is-invalid' : '' ?>"
                maxlength="255"
                placeholder="Masukkan alamat customer"
            ><?= old('alamat', $customer['alamat'] ?? '') ?></textarea>

            <small class="form-text text-muted">
                Opsional. Diisi jika informasi alamat tersedia.
            </small>

            <?php if (validation_show_error('alamat')) : ?>

                <div class="invalid-feedback">
                    <?= validation_show_error('alamat') ?>
                </div>

            <?php endif; ?>

        </div>

    </div>

</div>