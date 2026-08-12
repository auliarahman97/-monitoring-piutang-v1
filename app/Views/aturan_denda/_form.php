<?php

$isEdit = isset($aturan);

$data = $aturan ?? [];

$old = static function (
    string $field,
    mixed $default = ''
) use ($data): mixed {

    return old(
        $field,
        $data[$field] ?? $default
    );
};

?>

<?php if (session()->getFlashdata('errors')) : ?>

    <div class="alert alert-danger">

        <ul class="mb-0">

            <?php foreach (session()->getFlashdata('errors') as $error) : ?>

                <li><?= esc($error) ?></li>

            <?php endforeach; ?>

        </ul>

    </div>

<?php endif; ?>

<div class="row">


    <!-- Nama Aturan -->

    <div class="col-md-8">

        <div class="form-group">

            <label for="nama_aturan">

                Nama Aturan

                <span class="text-danger">*</span>

            </label>

            <input
                type="text"
                name="nama_aturan"
                id="nama_aturan"
                class="form-control"
                value="<?= esc(
                    (string) $old('nama_aturan')
                ) ?>"
                maxlength="100"
                placeholder="Contoh: Denda 1 - 10 Juta"
                required
            >

            <small class="form-text text-muted">

                Nama untuk memudahkan identifikasi aturan denda.

            </small>

        </div>

    </div>


    <!-- Status -->

    <div class="col-md-4">

        <div class="form-group">

            <label for="status">

                Status

                <span class="text-danger">*</span>

            </label>

            <select
                name="status"
                id="status"
                class="form-control"
                required
            >

                <option value="1"
                    <?= (string) $old('status', 1) === '1'
                        ? 'selected'
                        : '' ?>
                >
                    Aktif
                </option>

                <option value="0"
                    <?= (string) $old('status', 1) === '0'
                        ? 'selected'
                        : '' ?>
                >
                    Tidak Aktif
                </option>

            </select>

            <small class="form-text text-muted">

                Aturan aktif dapat digunakan oleh sistem.

            </small>

        </div>

    </div>


    <!-- Minimum Nominal -->

    <div class="col-md-6">

        <div class="form-group">

            <label for="min_nominal">

                Minimal Nominal Pokok

                <span class="text-danger">*</span>

            </label>

            <div class="input-group">

                <div class="input-group-prepend">

                    <span class="input-group-text">
                        Rp
                    </span>

                </div>

                <input
                    type="number"
                    name="min_nominal"
                    id="min_nominal"
                    class="form-control text-right"
                    value="<?= esc(
                        (string) $old('min_nominal')
                    ) ?>"
                    min="0"
                    step="0.01"
                    required
                >

            </div>

            <small class="form-text text-muted">

                Batas minimal nominal pokok yang dikenakan aturan.

            </small>

        </div>

    </div>


    <!-- Maximum Nominal -->

    <div class="col-md-6">

        <div class="form-group">

            <label for="max_nominal">

                Maksimal Nominal Pokok

            </label>

            <div class="input-group">

                <div class="input-group-prepend">

                    <span class="input-group-text">
                        Rp
                    </span>

                </div>

                <input
                    type="number"
                    name="max_nominal"
                    id="max_nominal"
                    class="form-control text-right"
                    value="<?= esc(
                        (string) $old('max_nominal')
                    ) ?>"
                    min="0"
                    step="0.01"
                    placeholder="Kosongkan jika tidak terbatas"
                >

            </div>

            <small class="form-text text-muted">

                Kosongkan jika rentang nominal tidak memiliki batas atas.

            </small>

        </div>

    </div>


    <!-- Persentase Denda -->

    <div class="col-md-4">

        <div class="form-group">

            <label for="persentase_denda">

                Persentase Denda

                <span class="text-danger">*</span>

            </label>

            <div class="input-group">

                <input
                    type="number"
                    name="persentase_denda"
                    id="persentase_denda"
                    class="form-control text-right"
                    value="<?= esc(
                        (string) $old(
                            'persentase_denda',
                            0
                        )
                    ) ?>"
                    min="0"
                    max="100"
                    step="0.01"
                    required
                >

                <div class="input-group-append">

                    <span class="input-group-text">
                        %
                    </span>

                </div>

            </div>

            <small class="form-text text-muted">

                Persentase denda setiap periode keterlambatan.

            </small>

        </div>

    </div>


    <!-- Periode -->

    <div class="col-md-4">

        <div class="form-group">

            <label for="periode_hari">

                Periode Denda

                <span class="text-danger">*</span>

            </label>

            <div class="input-group">

                <input
                    type="number"
                    name="periode_hari"
                    id="periode_hari"
                    class="form-control text-right"
                    value="<?= esc(
                        (string) $old(
                            'periode_hari',
                            30
                        )
                    ) ?>"
                    min="1"
                    required
                >

                <div class="input-group-append">

                    <span class="input-group-text">
                        Hari
                    </span>

                </div>

            </div>

            <small class="form-text text-muted">

                Default sistem: 30 hari per periode.

            </small>

        </div>

    </div>


    <!-- Maksimum Denda -->

    <div class="col-md-4">

        <div class="form-group">

            <label for="maksimal_denda_persen">

                Maksimal Akumulasi Denda

                <span class="text-danger">*</span>

            </label>

            <div class="input-group">

                <input
                    type="number"
                    name="maksimal_denda_persen"
                    id="maksimal_denda_persen"
                    class="form-control text-right"
                    value="<?= esc(
                        (string) $old(
                            'maksimal_denda_persen',
                            100
                        )
                    ) ?>"
                    min="0"
                    max="100"
                    step="0.01"
                    required
                >

                <div class="input-group-append">

                    <span class="input-group-text">
                        %
                    </span>

                </div>

            </div>

            <small class="form-text text-muted">

                Batas maksimal akumulasi denda terhadap pokok awal.

            </small>

        </div>

    </div>


    <!-- Tanggal Mulai -->

    <div class="col-md-6">

        <div class="form-group">

            <label for="tanggal_mulai">

                Tanggal Mulai Berlaku

                <span class="text-danger">*</span>

            </label>

            <input
                type="date"
                name="tanggal_mulai"
                id="tanggal_mulai"
                class="form-control"
                value="<?= esc(
                    (string) $old(
                        'tanggal_mulai',
                        date('Y-m-d')
                    )
                ) ?>"
                required
            >

            <small class="form-text text-muted">

                Tanggal mulai aturan dapat digunakan oleh sistem.

            </small>

        </div>

    </div>


    <!-- Tanggal Selesai -->

    <div class="col-md-6">

        <div class="form-group">

            <label for="tanggal_selesai">

                Tanggal Selesai Berlaku

            </label>

            <input
                type="date"
                name="tanggal_selesai"
                id="tanggal_selesai"
                class="form-control"
                value="<?= esc(
                    (string) $old('tanggal_selesai')
                ) ?>"
            >

            <small class="form-text text-muted">

                Kosongkan jika aturan tidak memiliki tanggal berakhir.

            </small>

        </div>

    </div>


    <!-- Keterangan -->

    <div class="col-12">

        <div class="form-group">

            <label for="keterangan">

                Keterangan

            </label>

            <textarea
                name="keterangan"
                id="keterangan"
                class="form-control"
                rows="4"
                placeholder="Keterangan tambahan terkait aturan denda..."
            ><?= esc(
                (string) $old('keterangan')
            ) ?></textarea>

            <small class="form-text text-muted">

                Opsional.

            </small>

        </div>

    </div>

</div>


<!-- Informasi Formula -->

<div class="card bg-light border-0 mt-2">

    <div class="card-body">

        <h6 class="font-weight-bold">

            <i class="fas fa-calculator mr-1"></i>

            Mekanisme Denda

        </h6>

        <p class="mb-0 text-muted">

            Denda mulai berlaku setelah melewati jatuh tempo,
            dihitung berdasarkan pokok awal, bertambah setiap periode,
            dan dibatasi oleh maksimal akumulasi denda.

        </p>

    </div>

</div>