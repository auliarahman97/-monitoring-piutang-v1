<script>

$(function () {

    /*
     * ================================================================
     * KONFIGURASI
     * ================================================================
     */

    const previewUrl =
        '<?= site_url('pembayaran/preview') ?>';


    let calculation = null;


    /*
     * ================================================================
     * FORMAT RUPIAH
     * ================================================================
     */

    function formatRupiah(value) {

        value = Number(value || 0);

        return 'Rp ' + value.toLocaleString(
            'id-ID',
            {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }
        );
    }

    /*
     * ================================================================
     * FORMAT TANGGAL
     * ================================================================
     */
    function formatTanggal(value) {

        if (! value) {
            return '-';
        }

        const parts =
            String(value).split('-');

        if (parts.length !== 3) {
            return value;
        }

        return (
            parts[2]
            + '-'
            + parts[1]
            + '-'
            + parts[0]
        );
    }


    /*
     * ================================================================
     * PARSE NOMINAL
     * ================================================================
     *
     * Contoh:
     *
     * 1.500.000
     * 1.500.000,50
     * 1500000
     */

    function parseMoney(value) {

        if (
            value === null
            || value === undefined
            || value === ''
        ) {
            return 0;
        }


        value =
            String(value)
                .trim()
                .replace(/\s/g, '');


        /*
         * Format:
         *
         * 1.500.000,50
         */

        if (
            value.includes('.')
            && value.includes(',')
        ) {

            return Number(
                value
                    .replace(/\./g, '')
                    .replace(',', '.')
            ) || 0;
        }


        /*
         * Format:
         *
         * 1.500.000
         */

        if (
            /^\d{1,3}(\.\d{3})+$/.test(value)
        ) {

            return Number(
                value.replace(/\./g, '')
            ) || 0;
        }


        return Number(
            value.replace(',', '.')
        ) || 0;
    }


    /*
     * ================================================================
     * RESET PREVIEW
     * ================================================================
     */

    function resetPreview() {

        calculation = null;


        $('#tagihanPanel')
            .addClass('d-none');


        $('#alokasiPanel')
            .addClass('d-none');


        $('#nominal_pembayaran')
            .val('')
            .prop('disabled', true);


        $('#btnSimpan')
            .prop('disabled', true);

    }


    /*
    * ================================================================
    * LOAD PIUTANG BERDASARKAN CUSTOMER
    * ================================================================
    */

    $('#customer_id').on(
        'change',
        function () {

            const customerId =
                $(this).val();

            const $piutang =
                $('#piutang_id');


            /*
            * ------------------------------------------------------------
            * RESET
            * ------------------------------------------------------------
            */

            resetPreview();

            $piutang
                .empty()
                .append(
                    $('<option>', {
                        value: '',
                        text: customerId
                            ? '-- Memuat Piutang... --'
                            : '-- Pilih Customer Terlebih Dahulu --'
                    })
                )
                .prop('disabled', true);


            /*
            * ------------------------------------------------------------
            * CUSTOMER BELUM DIPILIH
            * ------------------------------------------------------------
            */

            if (! customerId) {
                return;
            }


            /*
            * ------------------------------------------------------------
            * LOAD PIUTANG
            * ------------------------------------------------------------
            */

            $.ajax({

                url:
                    '<?= site_url('pembayaran/piutang') ?>/'
                    + encodeURIComponent(customerId),

                type: 'GET',

                dataType: 'json',

                success: function (response) {

                    $piutang.empty();


                    /*
                    * ----------------------------------------------------
                    * RESPONSE TIDAK VALID
                    * ----------------------------------------------------
                    */

                    if (
                        ! response
                        || response.success !== true
                    ) {

                        $piutang
                            .append(
                                $('<option>', {
                                    value: '',
                                    text:
                                        '-- Gagal mengambil piutang --'
                                })
                            )
                            .prop('disabled', true);

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text:
                                response?.message
                                ||
                                'Gagal mengambil data piutang.'
                        });

                        return;
                    }


                    /*
                    * ----------------------------------------------------
                    * DATA PIUTANG
                    * ----------------------------------------------------
                    */

                    const data =
                        Array.isArray(response.data)
                            ? response.data
                            : [];


                    /*
                    * ----------------------------------------------------
                    * TIDAK ADA PIUTANG
                    * ----------------------------------------------------
                    */

                    if (data.length === 0) {

                        $piutang
                            .append(
                                $('<option>', {
                                    value: '',
                                    text:
                                        '-- Tidak ada piutang --'
                                })
                            )
                            .prop('disabled', true);

                        return;
                    }


                    /*
                    * ----------------------------------------------------
                    * PLACEHOLDER
                    * ----------------------------------------------------
                    */

                    $piutang.append(
                        $('<option>', {
                            value: '',
                            text: '-- Pilih Piutang --'
                        })
                    );


                    /*
                    * ----------------------------------------------------
                    * ISI PIUTANG
                    * ----------------------------------------------------
                    */

                    $.each(
                        data,
                        function (_, piutang) {

                            let label =
                                piutang.nomor_piutang
                                || '-';

                            if (
                                piutang.tanggal_jatuh_tempo
                            ) {

                                label +=
                                    ' — Jatuh tempo '
                                    +
                                    formatTanggal(
                                        piutang.tanggal_jatuh_tempo
                                    );
                            }

                            $piutang.append(
                                $('<option>', {
                                    value:
                                        piutang.id,
                                    text:
                                        label
                                })
                            );

                        }
                    );


                    /*
                    * ----------------------------------------------------
                    * AKTIFKAN DROPDOWN
                    * ----------------------------------------------------
                    */

                    $piutang.prop(
                        'disabled',
                        false
                    );

                },

                error: function (xhr) {

                    $piutang
                        .empty()
                        .append(
                            $('<option>', {
                                value: '',
                                text:
                                    '-- Gagal mengambil piutang --'
                            })
                        )
                        .prop(
                            'disabled',
                            true
                        );


                    let message =
                        'Gagal mengambil data piutang.';


                    if (
                        xhr.responseJSON
                        &&
                        xhr.responseJSON.message
                    ) {

                        message =
                            xhr.responseJSON.message;
                    }


                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal mengambil Piutang',
                        text: message
                    });

                }

            });

        }
    );


    /*
     * ================================================================
     * PIUTANG BERUBAH
     * ================================================================
     */

    $('#piutang_id').on(
        'change',
        function () {

            resetPreview();


            if (! $(this).val()) {
                return;
            }


            loadPreview();

        }
    );


    /*
     * ================================================================
     * TANGGAL BERUBAH
     * ================================================================
     */

    $('#tanggal_pembayaran').on(
        'change',
        function () {

            if (! $('#piutang_id').val()) {
                return;
            }


            loadPreview();

        }
    );


    /*
     * ================================================================
     * NOMINAL BERUBAH
     * ================================================================
     */

    $('#nominal_pembayaran').on(
        'input',
        function () {

            updateAllocation();

        }
    );


    /*
     * ================================================================
     * BLUR NOMINAL
     * ================================================================
     */

    $('#nominal_pembayaran').on(
        'blur',
        function () {

            const value =
                parseMoney(
                    $(this).val()
                );


            if (value > 0) {

                $(this).val(
                    value.toLocaleString(
                        'id-ID'
                    )
                );

            }

        }
    );


    /*
     * ================================================================
     * LOAD PREVIEW
     * ================================================================
     */

    function loadPreview() {

        const piutangId =
            $('#piutang_id').val();


        const tanggal =
            $('#tanggal_pembayaran').val();


        if (
            ! piutangId
            || ! tanggal
        ) {

            return;

        }


        calculation = null;


        $('#tagihanPanel')
            .removeClass('d-none');


        $('#previewPokok')
            .text('Memuat...');


        $('#previewBunga')
            .text('Memuat...');


        $('#previewDenda')
            .text('Memuat...');


        $('#previewTotal')
            .text('Memuat...');


        $('#nominal_pembayaran')
            .val('')
            .prop(
                'disabled',
                true
            );


        $('#alokasiPanel')
            .addClass('d-none');


        $('#btnSimpan')
            .prop(
                'disabled',
                true
            );


        $.ajax({

            url:
                previewUrl
                + '/'
                + encodeURIComponent(
                    piutangId
                ),

            type: 'GET',

            data: {
                tanggal_pembayaran:
                    tanggal
            },

            dataType: 'json',

            success:
                function (response) {

                    if (
                        ! response
                        || ! response.success
                        || ! response.data
                    ) {

                        showError(
                            response
                                ?.message
                            ||
                            'Gagal menghitung tagihan.'
                        );

                        return;

                    }


                    calculation =
                        response.data;


                    renderCalculation(
                        response.data
                    );

                },


            error:
                function (xhr) {

                    let message =
                        'Gagal mengambil data tagihan.';


                    if (
                        xhr.responseJSON
                        && xhr.responseJSON.message
                    ) {

                        message =
                            xhr.responseJSON.message;

                    }


                    showError(message);

                }

        });

    }


    /*
     * ================================================================
     * RENDER CALCULATION
     * ================================================================
     */

    function renderCalculation(data) {

        const sisaPokok =
            Number(
                data.sisa_pokok || 0
            );


        const sisaBunga =
            Number(
                data.sisa_bunga || 0
            );


        const sisaDenda =
            Number(
                data.sisa_denda || 0
            );


        const total =
            Number(
                data.total_tagihan || 0
            );


        $('#previewPokok')
            .text(
                formatRupiah(
                    sisaPokok
                )
            );


        $('#previewBunga')
            .text(
                formatRupiah(
                    sisaBunga
                )
            );


        $('#previewDenda')
            .text(
                formatRupiah(
                    sisaDenda
                )
            );


        $('#previewTotal')
            .text(
                formatRupiah(
                    total
                )
            );


        if (
            data.sudah_lunas
            || total <= 0
        ) {

            $('#nominal_pembayaran')
                .prop(
                    'disabled',
                    true
                );


            $('#btnSimpan')
                .prop(
                    'disabled',
                    true
                );


            $('#nominalHelp')
                .removeClass(
                    'text-muted'
                )
                .addClass(
                    'text-success'
                )
                .text(
                    'Piutang sudah lunas.'
                );


            Swal.fire({
                icon: 'success',
                title: 'Piutang Sudah Lunas',
                text:
                    'Tidak ada lagi tagihan yang perlu dibayar.',
                confirmButtonText: 'Mengerti'
            });


            return;

        }


        $('#nominal_pembayaran')
            .prop(
                'disabled',
                false
            )
            .attr(
                'data-max',
                total
            );


        $('#nominalHelp')
            .removeClass(
                'text-success text-danger'
            )
            .addClass(
                'text-muted'
            )
            .text(
                'Maksimal '
                + formatRupiah(total)
            );


        $('#btnSimpan')
            .prop(
                'disabled',
                false
            );


        updateAllocation();

    }


    /*
     * ================================================================
     * UPDATE ALOKASI
     * ================================================================
     *
     * Urutan:
     *
     * Denda
     *   ↓
     * Bunga
     *   ↓
     * Pokok
     */

    function updateAllocation() {

        if (! calculation) {

            $('#alokasiPanel')
                .addClass('d-none');

            return;

        }


        const nominal =
            parseMoney(
                $('#nominal_pembayaran')
                    .val()
            );


        const total =
            Number(
                calculation.total_tagihan || 0
            );


        if (nominal <= 0) {

            $('#alokasiPanel')
                .addClass('d-none');


            $('#btnSimpan')
                .prop(
                    'disabled',
                    false
                );


            return;

        }


        if (nominal > total) {

            $('#alokasiPanel')
                .removeClass('d-none');


            $('#alokasiDenda')
                .text('-');


            $('#alokasiBunga')
                .text('-');


            $('#alokasiPokok')
                .text('-');


            $('#previewSisa')
                .text(
                    'Melebihi total tagihan'
                )
                .removeClass(
                    'text-primary text-success'
                )
                .addClass(
                    'text-danger'
                );


            $('#nominalHelp')
                .removeClass(
                    'text-muted text-success'
                )
                .addClass(
                    'text-danger'
                )
                .text(
                    'Nominal pembayaran tidak boleh melebihi '
                    + formatRupiah(total)
                );


            $('#btnSimpan')
                .prop(
                    'disabled',
                    true
                );


            return;

        }


        let remaining =
            nominal;


        /*
         * ------------------------------------------------------------
         * Denda
         * ------------------------------------------------------------
         */

        const alokasiDenda =
            Math.min(
                remaining,
                Number(
                    calculation.sisa_denda || 0
                )
            );


        remaining -=
            alokasiDenda;


        /*
         * ------------------------------------------------------------
         * Bunga
         * ------------------------------------------------------------
         */

        const alokasiBunga =
            Math.min(
                remaining,
                Number(
                    calculation.sisa_bunga || 0
                )
            );


        remaining -=
            alokasiBunga;


        /*
         * ------------------------------------------------------------
         * Pokok
         * ------------------------------------------------------------
         */

        const alokasiPokok =
            Math.min(
                remaining,
                Number(
                    calculation.sisa_pokok || 0
                )
            );


        /*
         * ------------------------------------------------------------
         * Sisa
         * ------------------------------------------------------------
         */

        const sisa =
            Math.max(
                0,
                total - nominal
            );


        $('#alokasiPanel')
            .removeClass('d-none');


        $('#alokasiDenda')
            .text(
                formatRupiah(
                    alokasiDenda
                )
            );


        $('#alokasiBunga')
            .text(
                formatRupiah(
                    alokasiBunga
                )
            );


        $('#alokasiPokok')
            .text(
                formatRupiah(
                    alokasiPokok
                )
            );


        $('#previewSisa')
            .text(
                formatRupiah(sisa)
            )
            .removeClass(
                'text-danger text-success text-primary'
            )
            .addClass(
                sisa <= 0
                    ? 'text-success'
                    : 'text-primary'
            );


        $('#nominalHelp')
            .removeClass(
                'text-danger'
            )
            .addClass(
                'text-muted'
            )
            .text(
                'Maksimal '
                + formatRupiah(total)
            );


        $('#btnSimpan')
            .prop(
                'disabled',
                false
            );

    }


    /*
     * ================================================================
     * ERROR PREVIEW
     * ================================================================
     */

    function showError(message) {

        calculation = null;


        $('#tagihanPanel')
            .removeClass('d-none');


        $('#previewPokok')
            .text('-');


        $('#previewBunga')
            .text('-');


        $('#previewDenda')
            .text('-');


        $('#previewTotal')
            .text('-');


        $('#nominal_pembayaran')
            .val('')
            .prop(
                'disabled',
                true
            );


        $('#alokasiPanel')
            .addClass('d-none');


        $('#btnSimpan')
            .prop(
                'disabled',
                true
            );


        Swal.fire({

            icon: 'warning',

            title: 'Tidak Dapat Memproses',

            text: message,

            confirmButtonText: 'Mengerti'

        });

    }


    /*
     * ================================================================
     * SUBMIT PEMBAYARAN
     * ================================================================
     *
     * Frontend hanya membantu.
     *
     * Perhitungan final tetap dilakukan ulang
     * oleh PaymentService di server.
     */

    $('#formPembayaran').on(
        'submit',
        function (event) {

            const nominal =
                parseMoney(
                    $('#nominal_pembayaran')
                        .val()
                );


            if (! calculation) {

                event.preventDefault();


                Swal.fire({

                    icon: 'warning',

                    title: 'Tagihan Belum Dihitung',

                    text:
                        'Silakan pilih piutang dan tunggu perhitungan tagihan.',

                    confirmButtonText:
                        'Mengerti'

                });


                return;

            }


            const total =
                Number(
                    calculation.total_tagihan || 0
                );


            if (
                nominal <= 0
                || nominal > total
            ) {

                event.preventDefault();


                Swal.fire({

                    icon: 'warning',

                    title:
                        'Nominal Tidak Valid',

                    text:
                        'Nominal pembayaran harus lebih dari 0 dan tidak boleh melebihi total tagihan.',

                    confirmButtonText:
                        'Mengerti'

                });


                return;

            }


            /*
             * Ubah format:
             *
             * 1.500.000
             *
             * menjadi:
             *
             * 1500000
             */

            $('#nominal_pembayaran')
                .val(nominal);


        }
    );


    /*
     * ================================================================
     * SWEETALERT PEMBATALAN PEMBAYARAN
     * ================================================================
     */

    $(document).on(
        'click',
        '.btn-cancel-payment',
        function (event) {

            event.preventDefault();


            const button =
                $(this);


            const url =
                button.data('url');


            const number =
                button.data('number')
                || '-';


            Swal.fire({

                title:
                    'Batalkan Pembayaran?',

                html:
                    'Transaksi <strong>'
                    + $('<div>')
                        .text(number)
                        .html()
                    + '</strong> akan dibatalkan.'
                    + '<br><br>'
                    + '<small class="text-muted">'
                    + 'Data tidak akan dihapus dan tetap tersimpan sebagai histori.'
                    + '</small>',

                icon:
                    'warning',

                input:
                    'textarea',

                inputLabel:
                    'Alasan Pembatalan',

                inputPlaceholder:
                    'Masukkan alasan pembatalan...',

                inputAttributes: {
                    maxlength: 1000
                },

                inputValidator:
                    function (value) {

                        if (
                            ! value
                            || ! value.trim()
                        ) {

                            return
                                'Alasan pembatalan wajib diisi.';

                        }


                        return null;

                    },

                showCancelButton:
                    true,

                confirmButtonText:
                    'Ya, Batalkan',

                cancelButtonText:
                    'Kembali',

                confirmButtonColor:
                    '#dc3545',

                reverseButtons:
                    true

            }).then(
                function (result) {

                    if (
                        ! result.isConfirmed
                    ) {

                        return;

                    }


                    const form =
                        $('<form>', {

                            method:
                                'POST',

                            action:
                                url

                        });


                    /*
                     * CSRF
                     */

                    form.append(
                        $('<input>', {

                            type:
                                'hidden',

                            name:
                                '<?= csrf_token() ?>',

                            value:
                                '<?= csrf_hash() ?>'

                        })
                    );


                    /*
                     * Alasan
                     */

                    form.append(
                        $('<input>', {

                            type:
                                'hidden',

                            name:
                                'alasan',

                            value:
                                result.value

                        })
                    );


                    $('body')
                        .append(form);


                    form.submit();

                }
            );

        }
    );


    /*
     * ================================================================
     * DATATABLE PEMBAYARAN
     * ================================================================
     */

    if (
        $('#tablePembayaran').length
    ) {

        $('#tablePembayaran').DataTable({

            responsive:
                true,

            autoWidth:
                false,

            order: [
                [4, 'desc']
            ],

            language: {

                search:
                    'Cari:',

                lengthMenu:
                    'Tampilkan _MENU_ data',

                info:
                    'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',

                infoEmpty:
                    'Menampilkan 0 sampai 0 dari 0 data',

                zeroRecords:
                    'Data tidak ditemukan',

                emptyTable:
                    'Belum ada data pembayaran',

                paginate: {

                    previous:
                        'Sebelumnya',

                    next:
                        'Berikutnya'

                }

            }

        });

    }


    /*
    * ================================================================
    * INIT CUSTOMER
    * ================================================================
    *
    * Jika customer sudah terisi dari old input,
    * otomatis load daftar piutangnya.
    */

    const $customer =
        $('#customer_id');

    const oldCustomer =
        $customer.val();

    const oldPiutang =
        '<?= esc(
            old('piutang_id', '')
        ) ?>';


    /*
    * ----------------------------------------------------------------
    * LOAD PIUTANG UNTUK OLD CUSTOMER
    * ----------------------------------------------------------------
    */

    if (
        $customer.length
        && oldCustomer
    ) {

        /*
        * Trigger hanya satu kali.
        *
        * Ini akan menjalankan AJAX:
        *
        * GET /pembayaran/piutang/{customerId}
        */

        $customer.trigger('change');


        /*
        * Jika form sebelumnya gagal validasi,
        * tunggu sampai option piutang selesai
        * dibuat oleh AJAX.
        */

        if (oldPiutang) {

            const waitForPiutang =
                setInterval(
                    function () {

                        const $option =
                            $('#piutang_id option[value="'
                            + oldPiutang
                            + '"]');


                        if ($option.length) {

                            clearInterval(
                                waitForPiutang
                            );


                            $('#piutang_id')
                                .val(oldPiutang)
                                .trigger('change');

                        }

                    },
                    100
                );


            /*
            * Safety timeout.
            *
            * Jangan biarkan setInterval
            * berjalan tanpa batas.
            */

            setTimeout(
                function () {

                    clearInterval(
                        waitForPiutang
                    );

                },
                10000
            );

        }

    }
});
</script>