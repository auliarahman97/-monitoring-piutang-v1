<script>

$(function () {


    // ==============================================================
    // DATATABLES
    // ==============================================================

    $('#tableLaporanPembayaran').DataTable({

        responsive: false,

        autoWidth: false,

        pageLength: 10,

        lengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, 'Semua']
        ],

        order: [
            [1, 'desc']
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

            infoFiltered:
                '(difilter dari _MAX_ total data)',

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


    // ==============================================================
    // VALIDASI RENTANG TANGGAL
    // ==============================================================

    $('#formFilterPembayaran').on(
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


                Swal.fire({

                    icon:
                        'warning',

                    title:
                        'Rentang tanggal tidak valid',

                    text:
                        'Tanggal Dari tidak boleh lebih besar dari Tanggal Sampai.',

                    confirmButtonText:
                        'Mengerti'

                });


                return false;
            }


            return true;

        }
    );


    // ==============================================================
    // SELECT2 CUSTOMER
    // ==============================================================

    if (
        $.fn.select2
    ) {

        $('#customer_id').select2({

            theme:
                'bootstrap4',

            width:
                '100%',

            placeholder:
                'Semua Customer',

            allowClear:
                true

        });

    }

});

</script>