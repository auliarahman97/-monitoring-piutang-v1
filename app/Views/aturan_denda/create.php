<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="container-fluid">

    <!-- ================================================================
         HEADER
    ================================================================= -->

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="mb-1">

                <i class="fas fa-layer-group text-primary mr-1"></i>

                Buat Versi Aturan Denda

            </h4>

            <small class="text-muted">

                Buat satu kebijakan denda yang terdiri dari beberapa
                rentang nominal.

            </small>

        </div>


        <a
            href="<?= site_url('pengaturan/aturan-denda') ?>"
            class="btn btn-secondary"
        >

            <i class="fas fa-arrow-left mr-1"></i>

            Kembali

        </a>

    </div>


    <!-- ================================================================
         FLASH ERROR
    ================================================================= -->

    <?php if (session()->getFlashdata('error')) : ?>

        <div class="alert alert-danger alert-dismissible fade show">

            <i class="fas fa-exclamation-circle mr-1"></i>

            <?= esc(session()->getFlashdata('error')) ?>

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
         VALIDATION ERRORS
    ================================================================= -->

    <?php if (session()->getFlashdata('errors')) : ?>

        <?php $errors = session()->getFlashdata('errors'); ?>

        <?php if (is_array($errors)) : ?>

            <div class="alert alert-danger alert-dismissible fade show">

                <strong>
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Terdapat kesalahan:
                </strong>

                <ul class="mb-0 mt-2">

                    <?php foreach ($errors as $error) : ?>

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

    <?php endif; ?>


    <!-- ================================================================
         INFORMATION
    ================================================================= -->

    <div class="alert alert-info">

        <div class="d-flex">

            <div class="mr-3">

                <i class="fas fa-info-circle fa-lg"></i>

            </div>

            <div>

                <strong>Konsep Versi Aturan Denda</strong>

                <div class="mt-1">

                    Satu versi dapat memiliki beberapa rentang nominal.
                    Ketika versi baru mulai berlaku, versi sebelumnya
                    akan otomatis berakhir pada satu hari sebelum tanggal
                    mulai versi baru.

                </div>

            </div>

        </div>

    </div>


    <!-- ================================================================
         FORM
    ================================================================= -->

    <form
        action="<?= site_url(
            'pengaturan/aturan-denda/store'
        ) ?>"
        method="post"
        id="formAturanDenda"
        autocomplete="off"
    >

        <?= csrf_field() ?>


        <!-- ============================================================
             CARD VERSI
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

                                <span class="text-danger">*</span>

                            </label>

                            <input
                                type="text"
                                name="nama_versi"
                                id="nama_versi"
                                class="form-control"
                                value="<?= old('nama_versi') ?>"
                                maxlength="150"
                                placeholder="Contoh: Kebijakan Denda Oktober 2026"
                                required
                            >

                            <small class="form-text text-muted">

                                Nama untuk membedakan kebijakan denda
                                antar periode.

                            </small>

                        </div>

                    </div>


                    <!-- Tanggal Mulai -->

                    <div class="col-md-5">

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
                                value="<?= old('tanggal_mulai') ?>"
                                required
                            >

                            <small class="form-text text-muted">

                                Minimal 30 hari dari hari ini.

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
                                placeholder="Catatan mengenai perubahan kebijakan..."
                            ><?= old('keterangan') ?></textarea>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- ============================================================
             CARD RENTANG
        ============================================================= -->

        <div class="card shadow-sm mb-4">

            <div class="card-header">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <h5 class="card-title mb-0">

                            <i class="fas fa-list-ol text-primary mr-1"></i>

                            Rentang Aturan Denda

                        </h5>

                        <small class="text-muted">

                            Tentukan aturan denda berdasarkan nominal pokok.

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


                <!-- Empty state -->

                <div
                    id="emptyRules"
                    class="text-center text-muted py-5"
                >

                    <i class="fas fa-list fa-2x mb-3"></i>

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

                <div class="d-flex justify-content-between align-items-center">

                    <div class="text-muted">

                        <i class="fas fa-shield-alt mr-1"></i>

                        Pastikan seluruh rentang tidak saling bertabrakan.

                    </div>


                    <div>

                        <a
                            href="<?= site_url(
                                'pengaturan/aturan-denda'
                            ) ?>"
                            class="btn btn-secondary mr-2"
                        >

                            Batal

                        </a>


                        <button
                            type="submit"
                            class="btn btn-primary"
                            id="btnSimpan"
                        >

                            <i class="fas fa-save mr-1"></i>

                            Simpan Versi

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

<template id="ruleTemplate">

    <div
        class="rule-item border rounded p-3 mb-3"
        data-rule-index="__INDEX__"
    >

        <!-- Header -->

        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>

                <span class="badge badge-primary mr-1">

                    <i class="fas fa-list-ol"></i>

                </span>

                <strong class="rule-title">

                    Rentang #__NUMBER__

                </strong>

            </div>


            <button
                type="button"
                class="btn btn-sm btn-outline-danger btn-remove-rule"
                title="Hapus rentang"
                aria-label="Hapus rentang"
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
                        class="form-control rule-nama"
                        maxlength="100"
                        placeholder="Contoh: Denda 1 - 10 Juta"
                        required
                    >

                </div>

            </div>


            <!-- Persentase -->

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
                            min="0"
                            max="100"
                            step="0.01"
                            placeholder="2"
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
                            min="1"
                            step="1"
                            value="30"
                            required
                        >

                        <div class="input-group-append">

                            <span class="input-group-text">

                                Hari

                            </span>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Min Nominal -->

            <div class="col-md-6">

                <div class="form-group">

                    <label>

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
                            name="rules[__INDEX__][min_nominal]"
                            class="form-control rule-min"
                            min="0.01"
                            step="0.01"
                            placeholder="1000000"
                            required
                        >

                    </div>

                    <small class="form-text text-muted">

                        Contoh: 1000000 untuk Rp1.000.000.

                    </small>

                </div>

            </div>


            <!-- Max Nominal -->

            <div class="col-md-6">

                <div class="form-group">

                    <label>

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
                            name="rules[__INDEX__][max_nominal]"
                            class="form-control rule-max"
                            min="0.01"
                            step="0.01"
                            placeholder="10000000"
                        >

                    </div>

                    <small class="form-text text-muted">

                        Kosongkan jika tidak memiliki batas atas.

                    </small>

                </div>

            </div>


            <!-- Maksimum Denda -->

            <div class="col-md-6">

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
                            min="0"
                            max="100"
                            step="0.01"
                            value="100"
                            required
                        >

                        <div class="input-group-append">

                            <span class="input-group-text">

                                %

                            </span>

                        </div>

                    </div>

                    <small class="form-text text-muted">

                        Contoh 100 berarti denda maksimal 100% dari
                        nominal pokok.

                    </small>

                </div>

            </div>


            <!-- Keterangan -->

            <div class="col-md-6">

                <div class="form-group">

                    <label>

                        Keterangan

                    </label>

                    <input
                        type="text"
                        name="rules[__INDEX__][keterangan]"
                        class="form-control"
                        maxlength="500"
                        placeholder="Catatan untuk rentang ini..."
                    >

                </div>

            </div>

        </div>

    </div>

</template>


<?= $this->endSection() ?>


<?= $this->section('scripts') ?>

<script>

$(document).ready(function () {

    let ruleIndex = 0;


    /*
     * ==============================================================
     * Helper
     * ==============================================================
     */

    function escapeHtml(value) {

        return $('<div>')
            .text(value ?? '')
            .html();

    }


    /*
     * ==============================================================
     * Tambah Rule
     * ==============================================================
     */

    function addRule(data = null) {

        const template =
            $('#ruleTemplate')
                .html()
                .replaceAll(
                    '__INDEX__',
                    ruleIndex
                )
                .replaceAll(
                    '__NUMBER__',
                    ruleIndex + 1
                );


        $('#rulesContainer')
            .append(template);


        const $rule =
            $('#rulesContainer .rule-item').last();


        /*
         * Isi data jika berasal dari old input.
         */

        if (data) {

            $rule.find('.rule-nama')
                .val(data.nama_aturan ?? '');

            $rule.find('.rule-min')
                .val(data.min_nominal ?? '');

            $rule.find('.rule-max')
                .val(data.max_nominal ?? '');

            $rule.find(
                '[name$="[persentase_denda]"]'
            ).val(
                data.persentase_denda ?? ''
            );

            $rule.find(
                '[name$="[periode_hari]"]'
            ).val(
                data.periode_hari ?? 30
            );

            $rule.find(
                '[name$="[maksimal_denda_persen]"]'
            ).val(
                data.maksimal_denda_persen ?? 100
            );

            $rule.find(
                '[name$="[keterangan]"]'
            ).val(
                data.keterangan ?? ''
            );

        }


        ruleIndex++;

        updateEmptyState();

        updateRuleTitles();

    }


    /*
    * ==============================================================
    * Hapus Rentang
    * ==============================================================
    */

    $(document).on(
        'click',
        '#rulesContainer .btn-remove-rule',
        function (event) {

            event.preventDefault();
            event.stopPropagation();

            const $button = $(this);
            const $rule   = $button.closest('.rule-item');
            const $rules  = $('#rulesContainer .rule-item');

            if ($rule.length === 0) {
                return;
            }


            /*
            * ----------------------------------------------------------
            * Minimal 1 rentang
            * ----------------------------------------------------------
            */

            if ($rules.length <= 1) {

                Swal.fire({

                    icon: 'info',

                    title: 'Tidak dapat dihapus',

                    html: `
                        <div class="text-muted">
                            Satu versi aturan denda minimal harus
                            memiliki <strong>1 rentang</strong>.
                        </div>
                    `,

                    confirmButtonText: 'Mengerti',

                    confirmButtonColor: '#007bff',

                    buttonsStyling: true

                });

                return;
            }


            /*
            * ----------------------------------------------------------
            * Konfirmasi hapus
            * ----------------------------------------------------------
            */

            Swal.fire({

                icon: 'warning',

                title: 'Hapus rentang ini?',

                html: `
                    <div class="text-muted">
                        Rentang ini akan dihapus dari form.
                    </div>

                    <div class="mt-2">
                        <strong>
                            Data belum disimpan ke database.
                        </strong>
                    </div>
                `,

                showCancelButton: true,

                confirmButtonText:
                    '<i class="fas fa-trash mr-1"></i> Ya, Hapus',

                cancelButtonText:
                    '<i class="fas fa-times mr-1"></i> Batal',

                reverseButtons: true,

                focusCancel: true,

                buttonsStyling: true

            }).then(function (result) {

                if (! result.isConfirmed) {
                    return;
                }


                /*
                * ------------------------------------------------------
                * Hapus dengan animasi
                * ------------------------------------------------------
                */

                $rule.fadeOut(
                    180,
                    function () {

                        $(this).remove();

                        updateRuleTitles();

                        updateEmptyState();

                        /*
                        * Toast sukses
                        */
                        Swal.fire({

                            icon: 'success',

                            title: 'Rentang dihapus',

                            text:
                                'Rentang telah dihapus dari form.',

                            toast: true,

                            position: 'top-end',

                            showConfirmButton: false,

                            timer: 1800,

                            timerProgressBar: true

                        });

                    }
                );

            });

        }
    );


    /*
     * ==============================================================
     * Tambah Button
     * ==============================================================
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
     * ==============================================================
     * Update Title
     * ==============================================================
     */

    function updateRuleTitles() {

        $('#rulesContainer .rule-item')
            .each(function (index) {

                $(this)
                    .attr(
                        'data-rule-index',
                        index
                    );

                $(this)
                    .find('.rule-title')
                    .text(
                        'Rentang #' + (index + 1)
                    );

            });

    }


    /*
     * ==============================================================
     * Empty State
     * ==============================================================
     */

    function updateEmptyState() {

        const count =
            $('#rulesContainer .rule-item').length;


        if (count === 0) {

            $('#emptyRules')
                .removeClass('d-none');

        } else {

            $('#emptyRules')
                .addClass('d-none');

        }

    }


    /*
     * ==============================================================
     * Validasi sederhana sebelum submit
     * ==============================================================
     */

    $('#formAturanDenda').on(
        'submit',
        function (event) {

            const $rules =
                $('#rulesContainer .rule-item');


            if ($rules.length === 0) {

                event.preventDefault();

                Swal.fire({

                    icon: 'warning',

                    title: 'Rentang belum ada',

                    text:
                        'Tambahkan minimal satu rentang aturan denda.',

                    confirmButtonText: 'Mengerti'

                });

                return;

            }


            let invalidRange = false;


            $rules.each(function () {

                const min = parseFloat(
                    $(this)
                        .find('.rule-min')
                        .val()
                );

                const maxValue =
                    $(this)
                        .find('.rule-max')
                        .val();

                const max =
                    maxValue === ''
                        ? null
                        : parseFloat(maxValue);


                if (
                    Number.isNaN(min)
                    || (
                        max !== null
                        && max <= min
                    )
                ) {

                    invalidRange = true;

                    $(this)
                        .find('.rule-max')
                        .addClass('is-invalid');

                } else {

                    $(this)
                        .find('.rule-max')
                        .removeClass('is-invalid');

                }

            });


            if (invalidRange) {

                event.preventDefault();

                Swal.fire({

                    icon: 'warning',

                    title: 'Rentang tidak valid',

                    text:
                        'Maksimal nominal harus lebih besar dari minimal nominal.',

                    confirmButtonText: 'Periksa Lagi'

                });

                return;

            }


            /*
             * Cegah double submit.
             */

            $('#btnSimpan')
                .prop('disabled', true)
                .html(
                    '<i class="fas fa-spinner fa-spin mr-1"></i>' +
                    ' Menyimpan...'
                );

        }
    );


    /*
     * ==============================================================
     * Old Input
     * ==============================================================
     */

    const oldRules = <?= json_encode(
        old('rules') ?? [],
        JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES
    ) ?>;


    if (
        Array.isArray(oldRules)
        && oldRules.length > 0
    ) {

        oldRules.forEach(function (rule) {

            addRule(rule);

        });

    } else {

        /*
         * Form baru selalu dimulai dengan satu rentang.
         */

        addRule();

    }


    /*
     * ==============================================================
     * Default tanggal
     * ==============================================================
     */

    const dateInput =
        $('#tanggal_mulai');


    if (
        dateInput.length
        && ! dateInput.val()
    ) {

        const date =
            new Date();

        date.setDate(
            date.getDate() + 30
        );


        const year =
            date.getFullYear();

        const month =
            String(
                date.getMonth() + 1
            ).padStart(2, '0');

        const day =
            String(
                date.getDate()
            ).padStart(2, '0');


        dateInput.val(
            year + '-' +
            month + '-' +
            day
        );

    }

});

</script>

<?= $this->endSection() ?>