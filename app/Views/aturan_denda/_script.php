<script>

$(function () {

    /*
     * ---------------------------------------------------------------
     * Konfirmasi Hapus Aturan Denda
     * ---------------------------------------------------------------
     */

    $(document).on('click', '.btn-delete', function (event) {

        event.preventDefault();

        const button = $(this);

        const url = button.data('url');

        const name = button.data('name');


        Swal.fire({

            title: 'Hapus Aturan Denda?',

            html:
                'Aturan <strong>' +
                $('<div>').text(name).html() +
                '</strong> akan dihapus.',

            icon: 'warning',

            showCancelButton: true,

            confirmButtonText: 'Ya, Hapus',

            cancelButtonText: 'Batal',

            reverseButtons: true

        }).then(function (result) {

            if (! result.isConfirmed) {
                return;
            }

            const form = $('<form>', {

                method: 'POST',

                action: url

            });


            form.append(
                $('<input>', {
                    type: 'hidden',
                    name: '<?= csrf_token() ?>',
                    value: '<?= csrf_hash() ?>'
                })
            );


            $('body').append(form);

            form.submit();

        });

    });

});

</script>