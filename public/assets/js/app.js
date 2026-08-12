$(function () {
    const DEBUG = false;

    if (DEBUG) {
        console.log("APP JS BERHASIL DIMUAT");
    }

    // ==========================
    // DataTables
    // ==========================
if ($('.datatable').length) {

    $('.datatable').DataTable({

        responsive: true,
        autoWidth: false,
        pageLength: 10,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: true,

        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            zeroRecords: "Data tidak ditemukan",
            paginate: {
                previous: "Sebelumnya",
                next: "Berikutnya"
            }
        }

    });

}

    // ==========================
    // SweetAlert Delete
    // ==========================
    $(document).on('click', '.btn-delete', function (e) {

        e.preventDefault();

        let url = $(this).attr('href');

        Swal.fire({

            title: 'Hapus Data?',

            text: 'Data yang dihapus tidak dapat dikembalikan.',

            icon: 'warning',

            showCancelButton: true,

            confirmButtonColor: '#d33',

            cancelButtonColor: '#3085d6',

            confirmButtonText: 'Ya, Hapus',

            cancelButtonText: 'Batal'

        }).then((result) => {

            if (result.isConfirmed) {

                window.location.href = url;

            }

        });

    });

});