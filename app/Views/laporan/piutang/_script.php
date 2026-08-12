<script>

$(function () {


    /* ==========================================================
       DATA TABLES
       ========================================================== */

    const table = $('#tableLaporanPiutang');


    if (table.length) {

        table.DataTable({

            responsive: true,

            autoWidth: false,

            pageLength: 25,

            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, 'Semua']
            ],

            order: [
                [3, 'desc']
            ],

            columnDefs: [

                {
                    targets: [
                        0,
                        11
                    ],

                    className: 'text-center'
                },

                {
                    targets: [
                        5,
                        6,
                        7,
                        8,
                        9,
                        10
                    ],

                    className: 'text-right'
                }

            ],

            language: {

                search: 'Cari:',

                lengthMenu:
                    'Tampilkan _MENU_ data',

                info:
                    'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',

                infoEmpty:
                    'Tidak ada data',

                infoFiltered:
                    '(difilter dari _MAX_ total data)',

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


    /* ==========================================================
       VALIDASI RENTANG TANGGAL
       ========================================================== */

    $('#formFilterPiutang').on(
        'submit',
        function (event) {

            const tanggalDari =
                $('#tanggal_dari').val();

            const tanggalSampai =
                $('#tanggal_sampai').val();


            if (
                tanggalDari
                &&
                tanggalSampai
                &&
                tanggalDari > tanggalSampai
            ) {

                event.preventDefault();


                if (
                    typeof Swal !== 'undefined'
                ) {

                    Swal.fire({

                        icon: 'warning',

                        title: 'Rentang tanggal tidak valid',

                        text:
                            'Tanggal dari tidak boleh lebih besar dari tanggal sampai.',

                        confirmButtonText:
                            'Mengerti'

                    });

                } else {

                    alert(
                        'Tanggal dari tidak boleh lebih besar dari tanggal sampai.'
                    );

                }


                return false;
            }


            return true;

        }
    );


    /* ==========================================================
       CUSTOMER SELECT
       ========================================================== */

    const customerSelect =
        $('#customer_id');


    if (
        customerSelect.length
        &&
        $.fn.select2
    ) {

        customerSelect.select2({

            theme: 'bootstrap4',

            width: '100%',

            placeholder:
                'Semua Customer',

            allowClear: true

        });

    }


    /* ==========================================================
       RESET FILTER
       ========================================================== */

    $('#btnResetFilter').on(
        'click',
        function () {

            /*
             * Tidak perlu AJAX.
             *
             * Cukup kembali ke URL dasar:
             *
             * /laporan/piutang
             */

            return true;

        }
    );


});

</script>