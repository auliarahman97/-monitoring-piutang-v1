<script>

$(function () {

    /*
     * ----------------------------------------------------------------------
     * DataTables Piutang
     * ----------------------------------------------------------------------
     */

    if (
        $('#tablePiutang').length &&
        $.fn.DataTable &&
        !$.fn.DataTable.isDataTable('#tablePiutang')
    ) {

        $('#tablePiutang').DataTable({

            responsive: true,

            autoWidth: false,

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
                    'Belum ada data piutang',

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
     * ----------------------------------------------------------------------
     * SweetAlert Delete Piutang
     * ----------------------------------------------------------------------
     */

    $(document).on(
        'click',
        '.btn-delete-piutang',
        function (event) {

            event.preventDefault();

            event.stopPropagation();


            const button =
                $(this);

            const url =
                button.data('url');

            const nomor =
                button.data('nomor') || '-';


            /*
             * --------------------------------------------------------------
             * Validasi URL
             * --------------------------------------------------------------
             */

            if (! url) {

                console.error(
                    'URL delete piutang tidak ditemukan.'
                );

                return;
            }


            /*
             * --------------------------------------------------------------
             * Pastikan SweetAlert tersedia
             * --------------------------------------------------------------
             */

            if (
                typeof Swal === 'undefined'
            ) {

                console.error(
                    'SweetAlert2 tidak tersedia. Pastikan sweetalert2.min.js sudah dimuat.'
                );

                return;
            }


            /*
             * --------------------------------------------------------------
             * Konfirmasi
             * --------------------------------------------------------------
             */

            Swal.fire({

                title:
                    'Hapus Piutang?',

                html:
                    'Piutang <strong>'
                    + $('<div>')
                        .text(nomor)
                        .html()
                    + '</strong> akan dihapus.',

                icon:
                    'warning',

                showCancelButton:
                    true,

                confirmButtonText:
                    'Ya, Hapus',

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
                     * ------------------------------------------------------
                     * POST DELETE
                     * ------------------------------------------------------
                     *
                     * Jangan menggunakan:
                     *
                     * window.location.href = url
                     *
                     * karena itu menghasilkan GET.
                     *
                     * Route delete Piutang menggunakan POST.
                     */

                    const form =
                        $('<form>', {

                            method:
                                'POST',

                            action:
                                url
                        });


                    /*
                     * ------------------------------------------------------
                     * CSRF
                     * ------------------------------------------------------
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
                     * ------------------------------------------------------
                     * Submit
                     * ------------------------------------------------------
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