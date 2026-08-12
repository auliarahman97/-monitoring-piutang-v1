<script>

$(function () {


    /*
     * ================================================================
     * DATATABLE — PIUTANG
     * ================================================================
     */

    if (
        $('#tableCustomerPiutang').length
    ) {

        $('#tableCustomerPiutang').DataTable({

            responsive: true,

            autoWidth: false,

            order: [
                [2, 'desc']
            ],

            pageLength: 10,

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
     * ================================================================
     * DATATABLE — PEMBAYARAN
     * ================================================================
     */

    if (
        $('#tableCustomerPembayaran').length
    ) {

        $('#tableCustomerPembayaran').DataTable({

            responsive: true,

            autoWidth: false,

            order: [
                [3, 'desc']
            ],

            pageLength: 10,

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
     * AUTO FOCUS
     * ================================================================
     */

    <?php if (
        ! $customer
        && empty($error)
    ) : ?>

        $('#customer_id').focus();

    <?php endif; ?>


});

</script>