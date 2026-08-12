<?= $this->section('scripts') ?>

<script>
$(function () {

    $('#tableUser').DataTable({
        responsive: true,
        autoWidth: false,
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

});
</script>

<?= $this->endSection() ?>