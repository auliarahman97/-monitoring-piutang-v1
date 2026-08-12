<script>
$(function () {

    /*
     * ================================================================
     * SWEETALERT PEMBATALAN PEMBAYARAN
     * ================================================================
     *
     * Digunakan pada:
     *
     * - pembayaran/index
     * - pembayaran/detail
     *
     * Pembatalan tidak menghapus record.
     *
     * User wajib memberikan alasan.
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


            /*
             * ------------------------------------------------------------
             * Validasi URL
             * ------------------------------------------------------------
             */

            if (! url) {

                Swal.fire({
                    icon: 'error',
                    title: 'Tidak dapat memproses',
                    text:
                        'URL pembatalan pembayaran tidak ditemukan.',
                    confirmButtonText: 'Mengerti'
                });

                return;
            }


            /*
             * ------------------------------------------------------------
             * Konfirmasi pembatalan
             * ------------------------------------------------------------
             */

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


                    /*
                     * ----------------------------------------------------
                     * Buat form POST
                     * ----------------------------------------------------
                     */

                    const form =
                        $('<form>', {

                            method:
                                'POST',

                            action:
                                url

                        });


                    /*
                     * ----------------------------------------------------
                     * CSRF
                     * ----------------------------------------------------
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
                     * ----------------------------------------------------
                     * Alasan Pembatalan
                     * ----------------------------------------------------
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


                    /*
                     * ----------------------------------------------------
                     * Submit
                     * ----------------------------------------------------
                     */

                    $('body')
                        .append(form);


                    form.submit();

                }
            );

        }
    );

});
</script>