<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<?php

/*
|--------------------------------------------------------------------------
| Data Versi
|--------------------------------------------------------------------------
*/

$versiData = $versi ?? [];

$aturanData = $aturan ?? [];


/*
|--------------------------------------------------------------------------
| Rules
|--------------------------------------------------------------------------
|
| Jika validasi gagal, gunakan old('rules').
| Jika tidak ada old input, gunakan data dari database.
|
*/

$oldRules = old('rules');

if (! is_array($oldRules) || empty($oldRules)) {
    $oldRules = $aturanData;
}


/*
|--------------------------------------------------------------------------
| Status
|--------------------------------------------------------------------------
*/

$status = $versiData['status'] ?? '';

$statusMeta = match ($status) {

    'aktif' => [
        'label' => 'Aktif',
        'badge' => 'success',
        'icon'  => 'fa-check-circle',
    ],

    'akan_datang' => [
        'label' => 'Akan Datang',
        'badge' => 'warning',
        'icon'  => 'fa-clock',
    ],

    'selesai' => [
        'label' => 'Selesai',
        'badge' => 'secondary',
        'icon'  => 'fa-history',
    ],

    default => [
        'label' => 'Tidak Diketahui',
        'badge' => 'dark',
        'icon'  => 'fa-question-circle',
    ],

};

?>

<div class="container-fluid">

    <!-- ================================================================
         HEADER
    ================================================================= -->

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="mb-1">

                <i class="fas fa-edit text-warning mr-1"></i>

                Edit Versi Aturan Denda

            </h4>

            <small class="text-muted">

                Perbarui kebijakan dan rentang aturan
                pada versi yang akan datang.

            </small>

        </div>


        <div>

            <a
                href="<?= site_url(
                    'pengaturan/aturan-denda/detail/' .
                    ($versiData['id'] ?? '')
                ) ?>"
                class="btn btn-secondary"
            >

                <i class="fas fa-arrow-left mr-1"></i>

                Kembali

            </a>

        </div>

    </div>


    <!-- ================================================================
         FLASH MESSAGE
    ================================================================= -->

    <?php if (session()->getFlashdata('error')) : ?>

        <div class="alert alert-danger alert-dismissible fade show">

            <i class="fas fa-exclamation-circle mr-1"></i>

            <?= esc(
                session()->getFlashdata('error')
            ) ?>

            <button
                type="button"
                class="close"
                data-dismiss="alert"
            >

                <span>&times;</span>

            </button>

        </div>

    <?php endif; ?>


    <?php if (session()->getFlashdata('success')) : ?>

        <div class="alert alert-success alert-dismissible fade show">

            <i class="fas fa-check-circle mr-1"></i>

            <?= esc(
                session()->getFlashdata('success')
            ) ?>

            <button
                type="button"
                class="close"
                data-dismiss="alert"
            >

                <span>&times;</span>

            </button>

        </div>

    <?php endif; ?>


    <?php
    $validationErrors =
        session()->getFlashdata('errors');
    ?>

    <?php if (
        is_array($validationErrors)
        && ! empty($validationErrors)
    ) : ?>

        <div class="alert alert-danger alert-dismissible fade show">

            <div class="font-weight-bold mb-2">

                <i class="fas fa-exclamation-triangle mr-1"></i>

                Terdapat kesalahan:

            </div>

            <ul class="mb-0 pl-4">

                <?php foreach (
                    $validationErrors
                    as $error
                ) : ?>

                    <li>
                        <?= esc($error) ?>
                    </li>

                <?php endforeach; ?>

            </ul>

            <button
                type="button"
                class="close"
                data-dismiss="alert"
            >

                <span>&times;</span>

            </button>

        </div>

    <?php endif; ?>


    <!-- ================================================================
         INFO VERSI
    ================================================================= -->

    <div class="alert alert-warning shadow-sm">

        <div class="d-flex align-items-start">

            <div class="mr-3">

                <i
                    class="fas fa-info-circle fa-lg mt-1"
                ></i>

            </div>


            <div>

                <strong>
                    Mengubah Versi Aturan Denda
                </strong>

                <div class="mt-1">

                    Versi

                    <strong>
                        <?= esc(
                            $versiData['kode_versi'] ?? '-'
                        ) ?>
                    </strong>

                    saat ini berstatus

                    <span
                        class="badge badge-<?= esc(
                            $statusMeta['badge']
                        ) ?>"
                    >

                        <i
                            class="fas <?= esc(
                                $statusMeta['icon']
                            ) ?> mr-1"
                        ></i>

                        <?= esc(
                            $statusMeta['label']
                        ) ?>

                    </span>

                    dan masih dapat diperbarui.

                </div>

            </div>

        </div>

    </div>


    <!-- ================================================================
         FORM
    ================================================================= -->

    <form
        action="<?= site_url(
            'pengaturan/aturan-denda/update/' .
            ($versiData['id'] ?? '')
        ) ?>"
        method="post"
        id="formAturanDenda"
        autocomplete="off"
    >

        <?= csrf_field() ?>


        <!-- ============================================================
             INFORMASI VERSI
        ============================================================= -->

        <div class="card shadow-sm mb-4">

            <div class="card-header">

                <h5 class="card-title mb-0">

                    <i class="fas fa-file-alt text-primary mr-1"></i>

                    Informasi Versi

                </h5>

            </div>


            <div class="card-body">

                <div class="row">

                    <!-- Nama Versi -->

                    <div class="col-md-7">

                        <div class="form-group">

                            <label for="nama_versi">

                                Nama Versi

                                <span class="text-danger">
                                    *
                                </span>

                            </label>

                            <input
                                type="text"
                                name="nama_versi"
                                id="nama_versi"
                                class="form-control"
                                value="<?= esc(
                                    old(
                                        'nama_versi',
                                        $versiData['nama_versi']
                                            ?? ''
                                    )
                                ) ?>"
                                maxlength="150"
                                required
                            >

                        </div>

                    </div>


                    <!-- Kode Versi -->

                    <div class="col-md-5">

                        <div class="form-group">

                            <label for="kode_versi">

                                Kode Versi

                            </label>

                            <input
                                type="text"
                                id="kode_versi"
                                class="form-control"
                                value="<?= esc(
                                    $versiData['kode_versi']
                                        ?? '-'
                                ) ?>"
                                readonly
                            >

                            <small class="form-text text-muted">

                                Kode versi merupakan identitas
                                permanen versi aturan.

                            </small>

                        </div>

                    </div>


                    <!-- Tanggal Mulai -->

                    <div class="col-md-6">

                        <div class="form-group">

                            <label for="tanggal_mulai">

                                Tanggal Mulai Berlaku

                                <span class="text-danger">
                                    *
                                </span>

                            </label>

                            <input
                                type="date"
                                name="tanggal_mulai"
                                id="tanggal_mulai"
                                class="form-control"
                                value="<?= esc(
                                    old(
                                        'tanggal_mulai',
                                        $versiData[
                                            'tanggal_mulai'
                                        ] ?? ''
                                    )
                                ) ?>"
                                required
                            >

                            <small class="form-text text-muted">

                                Tanggal mulai menentukan kapan
                                versi ini mulai berlaku.

                            </small>

                        </div>

                    </div>


                    <!-- Status -->

                    <div class="col-md-6">

                        <div class="form-group">

                            <label for="status">

                                Status Periode

                            </label>

                            <div class="input-group">

                                <div class="input-group-prepend">

                                    <span class="input-group-text">

                                        <i
                                            class="fas <?= esc(
                                                $statusMeta['icon']
                                            ) ?>"
                                        ></i>

                                    </span>

                                </div>

                                <input
                                    type="text"
                                    id="status"
                                    class="form-control"
                                    value="<?= esc(
                                        $statusMeta['label']
                                    ) ?>"
                                    readonly
                                >

                            </div>

                            <small class="form-text text-muted">

                                Status ditentukan otomatis
                                berdasarkan tanggal.

                            </small>

                        </div>

                    </div>


                    <!-- Keterangan -->

                    <div class="col-12">

                        <div class="form-group mb-0">

                            <label for="keterangan">

                                Keterangan

                            </label>

                            <textarea
                                name="keterangan"
                                id="keterangan"
                                class="form-control"
                                rows="3"
                                maxlength="1000"
                            ><?= esc(
                                old(
                                    'keterangan',
                                    $versiData[
                                        'keterangan'
                                    ] ?? ''
                                )
                            ) ?></textarea>

                            <small class="form-text text-muted">

                                Tambahkan informasi mengenai
                                kebijakan versi ini jika diperlukan.

                            </small>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- ============================================================
             RENTANG ATURAN
        ============================================================= -->

        <div class="card shadow-sm mb-4">

            <div class="card-header">

                <div
                    class="d-flex justify-content-between align-items-center"
                >

                    <div>

                        <h5 class="card-title mb-0">

                            <i
                                class="fas fa-list-ol text-primary mr-1"
                            ></i>

                            Rentang Aturan Denda

                        </h5>

                        <small class="text-muted">

                            Atur batas nominal dan ketentuan
                            denda untuk versi ini.

                        </small>

                    </div>


                    <button
                        type="button"
                        class="btn btn-sm btn-primary"
                        id="btnTambahRentang"
                    >

                        <i class="fas fa-plus mr-1"></i>

                        Tambah Rentang

                    </button>

                </div>

            </div>


            <div class="card-body">

                <div id="rulesContainer"></div>


                <div
                    id="emptyRules"
                    class="text-center text-muted py-5"
                    style="display: none;"
                >

                    <i
                        class="fas fa-list fa-2x mb-3"
                    ></i>

                    <div>

                        Belum ada rentang aturan.

                    </div>


                    <button
                        type="button"
                        class="btn btn-outline-primary btn-sm mt-3"
                        id="btnTambahRentangEmpty"
                    >

                        <i class="fas fa-plus mr-1"></i>

                        Tambah Rentang Pertama

                    </button>

                </div>

            </div>

        </div>


        <!-- ============================================================
             ACTION
        ============================================================= -->

        <div class="card shadow-sm mb-4">

            <div class="card-body">

                <div
                    class="d-flex justify-content-between align-items-center"
                >

                    <div class="text-muted">

                        <i
                            class="fas fa-shield-alt mr-1"
                        ></i>

                        Pastikan seluruh rentang tidak
                        saling bertabrakan.

                    </div>


                    <div>

                        <a
                            href="<?= site_url(
                                'pengaturan/aturan-denda/detail/' .
                                ($versiData['id'] ?? '')
                            ) ?>"
                            class="btn btn-secondary mr-2"
                        >

                            <i class="fas fa-times mr-1"></i>

                            Batal

                        </a>


                        <button
                            type="submit"
                            class="btn btn-warning"
                            id="btnSimpan"
                        >

                            <i class="fas fa-save mr-1"></i>

                            Simpan Perubahan

                        </button>

                    </div>

                </div>

            </div>

        </div>

    </form>

</div>


<!-- ================================================================
     TEMPLATE RENTANG
================================================================= -->

<script type="text/template" id="ruleTemplate">

    <div
        class="rule-item border rounded p-3 mb-3 bg-light"
        data-index="__INDEX__"
    >

        <div
            class="d-flex justify-content-between align-items-center mb-3"
        >

            <h6 class="mb-0">

                <span class="badge badge-primary mr-1">
                    <span class="rule-number">
                        __NUMBER__
                    </span>
                </span>

                Rentang Aturan

            </h6>


            <button
                type="button"
                class="btn btn-sm btn-outline-danger btn-hapus-rentang"
            >

                <i class="fas fa-trash mr-1"></i>

                Hapus

            </button>

        </div>


        <div class="row">

            <!-- Nama Aturan -->

            <div class="col-md-6">

                <div class="form-group">

                    <label>
                        Nama Aturan
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        name="rules[__INDEX__][nama_aturan]"
                        class="form-control"
                        value="__NAMA_ATURAN__"
                        maxlength="150"
                        required
                    >

                </div>

            </div>


            <!-- Persentase Denda -->

            <div class="col-md-3">

                <div class="form-group">

                    <label>
                        Persentase Denda
                        <span class="text-danger">*</span>
                    </label>

                    <div class="input-group">

                        <input
                            type="number"
                            name="rules[__INDEX__][persentase_denda]"
                            class="form-control"
                            value="__PERSENTASE_DENDA__"
                            min="0.01"
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

                </div>

            </div>


            <!-- Periode -->

            <div class="col-md-3">

                <div class="form-group">

                    <label>
                        Periode Denda
                        <span class="text-danger">*</span>
                    </label>

                    <div class="input-group">

                        <input
                            type="number"
                            name="rules[__INDEX__][periode_hari]"
                            class="form-control"
                            value="__PERIODE_HARI__"
                            min="1"
                            required
                        >

                        <div class="input-group-append">

                            <span class="input-group-text">
                                hari
                            </span>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Minimal Nominal -->

            <div class="col-md-4">

                <div class="form-group">

                    <label>
                        Minimal Nominal
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
                            name="rules[__INDEX__][min_nominal]"
                            class="form-control"
                            value="__MIN_NOMINAL__"
                            min="1"
                            step="0.01"
                            required
                        >

                    </div>

                </div>

            </div>


            <!-- Maksimal Nominal -->

            <div class="col-md-4">

                <div class="form-group">

                    <label>
                        Maksimal Nominal
                    </label>

                    <div class="input-group">

                        <div class="input-group-prepend">

                            <span class="input-group-text">
                                Rp
                            </span>

                        </div>

                        <input
                            type="number"
                            name="rules[__INDEX__][max_nominal]"
                            class="form-control"
                            value="__MAX_NOMINAL__"
                            min="1"
                            step="0.01"
                        >

                    </div>

                    <small class="form-text text-muted">

                        Kosongkan jika tidak memiliki batas atas.

                    </small>

                </div>

            </div>


            <!-- Maksimal Denda -->

            <div class="col-md-4">

                <div class="form-group">

                    <label>
                        Maksimal Akumulasi Denda
                        <span class="text-danger">*</span>
                    </label>

                    <div class="input-group">

                        <input
                            type="number"
                            name="rules[__INDEX__][maksimal_denda_persen]"
                            class="form-control"
                            value="__MAKSIMAL_DENDA_PERSEN__"
                            min="0.01"
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

                </div>

            </div>


            <!-- Keterangan -->

            <div class="col-12">

                <div class="form-group mb-0">

                    <label>
                        Keterangan
                    </label>

                    <textarea
                        name="rules[__INDEX__][keterangan]"
                        class="form-control"
                        rows="2"
                        maxlength="500"
                    >__KETERANGAN__</textarea>

                </div>

            </div>

        </div>

    </div>

</script>


<?= $this->section('scripts') ?>

<script>

$(document).ready(function () {

    let ruleIndex = 0;


    /*
    |--------------------------------------------------------------------------
    | Existing Rules
    |--------------------------------------------------------------------------
    */

    const existingRules =
        <?= json_encode(
            $oldRules,
            JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES
        ) ?>;


    /*
    |--------------------------------------------------------------------------
    | Escape HTML
    |--------------------------------------------------------------------------
    */

    function escapeHtml(value) {

        if (
            value === null ||
            value === undefined
        ) {
            return '';
        }

        return $('<div>')
            .text(String(value))
            .html();
    }


    /*
    |--------------------------------------------------------------------------
    | Add Rule
    |--------------------------------------------------------------------------
    */

    function addRule(rule = {}) {

        const index = ruleIndex;

        const number =
            $('#rulesContainer .rule-item').length + 1;


        let template =
            $('#ruleTemplate').html();


        template =
            template
                .replace(
                    /__INDEX__/g,
                    index
                )
                .replace(
                    /__NUMBER__/g,
                    number
                )
                .replace(
                    /__NAMA_ATURAN__/g,
                    escapeHtml(
                        rule.nama_aturan ?? ''
                    )
                )
                .replace(
                    /__PERSENTASE_DENDA__/g,
                    escapeHtml(
                        rule.persentase_denda ?? ''
                    )
                )
                .replace(
                    /__PERIODE_HARI__/g,
                    escapeHtml(
                        rule.periode_hari ?? ''
                    )
                )
                .replace(
                    /__MIN_NOMINAL__/g,
                    escapeHtml(
                        rule.min_nominal ?? ''
                    )
                )
                .replace(
                    /__MAX_NOMINAL__/g,
                    escapeHtml(
                        rule.max_nominal ?? ''
                    )
                )
                .replace(
                    /__MAKSIMAL_DENDA_PERSEN__/g,
                    escapeHtml(
                        rule.maksimal_denda_persen ?? ''
                    )
                )
                .replace(
                    /__KETERANGAN__/g,
                    escapeHtml(
                        rule.keterangan ?? ''
                    )
                );


        $('#rulesContainer').append(
            template
        );


        ruleIndex++;

        refreshRuleNumbers();

        toggleEmptyState();

    }


    /*
    |--------------------------------------------------------------------------
    | Refresh Rule Numbers
    |--------------------------------------------------------------------------
    */

    function refreshRuleNumbers() {

        $('#rulesContainer .rule-item')
            .each(function (index) {

                $(this)
                    .find('.rule-number')
                    .text(index + 1);

            });

    }


    /*
    |--------------------------------------------------------------------------
    | Empty State
    |--------------------------------------------------------------------------
    */

    function toggleEmptyState() {

        const count =
            $('#rulesContainer .rule-item').length;


        if (count === 0) {

            $('#emptyRules').show();

        } else {

            $('#emptyRules').hide();

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Add Button
    |--------------------------------------------------------------------------
    */

    $('#btnTambahRentang').on(
        'click',
        function () {

            addRule();

        }
    );


    $('#btnTambahRentangEmpty').on(
        'click',
        function () {

            addRule();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Delete Rule
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'click',
        '.btn-hapus-rentang',
        function () {

            const item =
                $(this).closest('.rule-item');


            const number =
                item.find('.rule-number').text();


            Swal.fire({

                title: 'Hapus Rentang?',

                html:
                    'Rentang aturan <strong>#' +
                    escapeHtml(number) +
                    '</strong> akan dihapus dari versi ini.',

                icon: 'warning',

                showCancelButton: true,

                confirmButtonText:
                    'Ya, Hapus',

                cancelButtonText:
                    'Batal',

                reverseButtons: true,

                focusCancel: true

            }).then(function (result) {

                if (
                    ! result.isConfirmed
                ) {
                    return;
                }


                item.remove();


                refreshRuleNumbers();

                toggleEmptyState();

            });

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Submit Protection
    |--------------------------------------------------------------------------
    */

    $('#formAturanDenda').on(
        'submit',
        function (event) {

            const count =
                $('#rulesContainer .rule-item').length;


            if (count === 0) {

                event.preventDefault();


                Swal.fire({

                    icon: 'warning',

                    title: 'Rentang Belum Ada',

                    text:
                        'Minimal satu rentang aturan denda harus diisi.',

                    confirmButtonText:
                        'OK'

                });

                return false;
            }


            const button =
                $('#btnSimpan');


            button
                .prop('disabled', true)
                .html(
                    '<i class="fas fa-spinner fa-spin mr-1"></i>' +
                    ' Menyimpan...'
                );

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Load Existing Rules
    |--------------------------------------------------------------------------
    */

    if (
        Array.isArray(existingRules)
        && existingRules.length > 0
    ) {

        existingRules.forEach(
            function (rule) {

                addRule(rule);

            }
        );

    } else {

        addRule();

    }


    toggleEmptyState();

});

</script>

<?= $this->endSection() ?>

<?= $this->endSection() ?>