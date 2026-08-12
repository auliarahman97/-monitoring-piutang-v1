<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?>
Customer
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- ================================================================ -->
<!-- Header -->
<!-- ================================================================ -->

<div class="d-flex justify-content-between align-items-center mb-3">

    <div>

        <h3 class="mb-1">
            <i class="fas fa-users text-primary mr-2"></i>
            Data Customer
        </h3>

        <p class="text-muted mb-0">
            Kelola seluruh data customer.
        </p>

    </div>

    <div>

        <a
            href="<?= base_url('customer/create') ?>"
            class="btn btn-primary"
        >

            <i class="fas fa-plus mr-1"></i>

            Tambah Customer

        </a>

    </div>

</div>


<!-- ================================================================ -->
<!-- Card -->
<!-- ================================================================ -->

<div class="card shadow-sm">

    <div class="card-header">

        <h3 class="card-title">

            <i class="fas fa-list mr-1"></i>

            Daftar Customer

        </h3>

    </div>


    <div class="card-body">

        <div class="table-responsive">

            <table
                id="tableCustomer"
                class="table table-bordered table-hover table-striped"
                width="100%"
            >

                <thead>

                    <tr>

                        <th
                            width="60"
                            class="text-center"
                        >
                            No
                        </th>

                        <th width="150">
                            Kode Customer
                        </th>

                        <th>
                            Nama
                        </th>

                        <th width="120">
                            No. HP
                        </th>

                        <th width="120" class="text-center">
                            Status
                        </th>

                        <th width="150" class="text-center">
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody>

                <?php $no = 1; ?>

                <?php foreach ($customer as $row) : ?>

                    <tr>

                        <td class="text-center">
                            <?= $no++ ?>
                        </td>


                        <td>

                            <strong>
                                <?= esc($row['kode_customer'] ?? '-') ?>
                            </strong>

                        </td>


                        <td>
                            <?= esc($row['nama'] ?? '-') ?>
                        </td>


                        <td>
                            <?= esc($row['no_hp'] ?? '-') ?>
                        </td>


                        <td class="text-center">

                            <?php if (! empty($row['deleted_at'])) : ?>

                                <span class="badge badge-secondary">
                                    <i class="fas fa-user-slash mr-1"></i>
                                    Tidak Aktif
                                </span>

                                <div class="small text-muted mt-1">
                                    Tidak aktif sejak
                                    <?= date(
                                        'd F Y',
                                        strtotime($row['deleted_at'])
                                    ) ?>
                                </div>

                            <?php else : ?>

                                <span class="badge badge-success">
                                    <i class="fas fa-user-check mr-1"></i>
                                    Aktif
                                </span>

                            <?php endif; ?>

                        </td>


                        <td class="text-center">

                            <?php if (empty($row['deleted_at'])) : ?>

                                <!-- Edit -->
                                <a
                                    href="<?= site_url(
                                        'customer/edit/' . $row['id']
                                    ) ?>"
                                    class="btn btn-warning btn-sm"
                                    title="Edit"
                                >
                                    <i class="fas fa-edit"></i>
                                </a>

                                <!-- Nonaktifkan -->
                                <button
                                    type="button"
                                    class="btn btn-danger btn-sm btn-delete-customer"
                                    data-url="<?= site_url(
                                        'customer/delete/' . $row['id']
                                    ) ?>"
                                    data-nama="<?= esc(
                                        $row['nama']
                                    ) ?>"
                                    title="Nonaktifkan"
                                >
                                    <i class="fas fa-user-slash"></i>
                                </button>

                            <?php else : ?>

                                <!-- Customer nonaktif -->
                                <span
                                    class="text-muted"
                                    title="Customer sudah tidak aktif"
                                >
                                    <i class="fas fa-lock"></i>
                                </span>

                            <?php endif; ?>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<!-- ================================================================
     Customer - DataTables
================================================================= -->

<script>
$(document).ready(function () {

    const table =
        $('#tableCustomer').DataTable({

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
                    'Belum ada data customer',

                paginate: {

                    previous:
                        'Sebelumnya',

                    next:
                        'Berikutnya'
                }
            },

            columnDefs: [

                {
                    orderable: false,
                    searchable: false,
                    targets: [0, 5]
                }

            ],

            order: []
        });


    /*
     * Nomor urut mengikuti posisi baris setelah
     * sorting / filtering / pagination.
     */

    table.on(
        'order.dt search.dt draw.dt',
        function () {

            table
                .column(0, {
                    search: 'applied',
                    order: 'applied'
                })
                .nodes()
                .each(
                    function (cell, i) {

                        cell.innerHTML =
                            i + 1;
                    }
                );

        }
    ).draw();

});
</script>


<!-- ================================================================
     Customer - Nonaktifkan
================================================================= -->

<?= $this->include('customer/_script') ?>


<?= $this->endSection() ?>