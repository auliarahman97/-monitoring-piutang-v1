<script>
$(function () {

    /*
     * --------------------------------------------------------------
     * Delete / Nonaktifkan Customer
     * --------------------------------------------------------------
     */

    $(document).on(
        'click',
        '.btn-delete-customer',
        function (event) {

            event.preventDefault();

            const button = $(this);

            const url =
                button.attr('data-url');

            const nama =
                button.attr('data-nama') || 'customer';


            /*
             * ----------------------------------------------------------
             * Pastikan SweetAlert tersedia
             * ----------------------------------------------------------
             */

            if (typeof Swal === 'undefined') {

                console.error(
                    'SweetAlert2 tidak tersedia.'
                );

                alert(
                    'SweetAlert2 tidak tersedia.'
                );

                return;
            }


            /*
             * ----------------------------------------------------------
             * Konfirmasi
             * ----------------------------------------------------------
             */

            Swal.fire({

                title:
                    'Nonaktifkan Customer?',

                html:
                    'Customer <strong>'
                    + $('<div>')
                        .text(nama)
                        .html()
                    + '</strong> akan dinonaktifkan.'
                    + '<br>'
                    + '<small class="text-muted">'
                    + 'Histori piutang dan pembayaran tetap tersimpan.'
                    + '</small>',

                icon:
                    'warning',

                showCancelButton:
                    true,

                confirmButtonText:
                    'Ya, Nonaktifkan',

                cancelButtonText:
                    'Batal',

                reverseButtons:
                    true,

                focusCancel:
                    true

            }).then(
                function (result) {

                    if (
                        ! result.isConfirmed
                    ) {
                        return;
                    }


                    /*
                     * --------------------------------------------------
                     * Submit POST
                     * --------------------------------------------------
                     */

                    const form =
                        $('<form>', {
                            method: 'POST',
                            action: url
                        });


                    /*
                     * CSRF
                     */

                    form.append(
                        $('<input>', {
                            type: 'hidden',
                            name: '<?= csrf_token() ?>',
                            value: '<?= csrf_hash() ?>'
                        })
                    );


                    $('body').append(form);

                    form.trigger('submit');
                }
            );
        }
    );

});
</script>