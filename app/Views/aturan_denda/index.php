<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="container-fluid">

    <!-- ================================================================
         HEADER
    ================================================================= -->

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="mb-1">
                <i class="fas fa-percent text-primary mr-1"></i>
                Pengaturan Denda
            </h4>

            <small class="text-muted">
                Kelola versi kebijakan dan rentang aturan denda piutang.
            </small>

        </div>


        <div>

            <a
                href="<?= site_url('pengaturan/aturan-denda/create') ?>"
                class="btn btn-primary"
            >
                <i class="fas fa-plus mr-1"></i>
                Buat Versi Baru
            </a>

        </div>

    </div>


    <!-- ================================================================
         FLASH MESSAGE
    ================================================================= -->

    <?php if (session()->getFlashdata('success')) : ?>

        <div class="alert alert-success alert-dismissible fade show">

            <i class="fas fa-check-circle mr-1"></i>

            <?= esc(session()->getFlashdata('success')) ?>

            <button
                type="button"
                class="close"
                data-dismiss="alert"
                aria-label="Close"
            >
                <span aria-hidden="true">&times;</span>
            </button>

        </div>

    <?php endif; ?>


    <?php if (session()->getFlashdata('error')) : ?>

        <div class="alert alert-danger alert-dismissible fade show">

            <i class="fas fa-exclamation-circle mr-1"></i>

            <?= esc(session()->getFlashdata('error')) ?>

            <button
                type="button"
                class="close"
                data-dismiss="alert"
                aria-label="Close"
            >
                <span aria-hidden="true">&times;</span>
            </button>

        </div>

    <?php endif; ?>


    <!-- ================================================================
         TABLE
    ================================================================= -->

    <div class="card shadow-sm">

        <div class="card-header">

            <h5 class="card-title mb-0">

                <i class="fas fa-history mr-1"></i>

                Versi Kebijakan Denda

            </h5>

        </div>


        <div class="card-body">

            <div class="table-responsive">

                <table
                    id="tableAturanDendaVersi"
                    class="table table-bordered table-hover align-middle"
                    width="100%"
                >

                    <thead class="thead-light">

                        <tr>

                            <th width="5%" class="text-center">
                                #
                            </th>

                            <th width="14%">
                                Kode Versi
                            </th>

                            <th>
                                Nama Versi
                            </th>

                            <th width="15%">
                                Berlaku Mulai
                            </th>

                            <th width="15%">
                                Berlaku Sampai
                            </th>

                            <th width="10%" class="text-center">
                                Rentang
                            </th>

                            <th width="12%" class="text-center">
                                Status
                            </th>

                            <th width="13%" class="text-center">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php if (! empty($versiList)) : ?>

                            <?php foreach ($versiList as $index => $versi) : ?>

                                <?php

                                /*
                                 * ==================================================
                                 * STATUS
                                 * ==================================================
                                 *
                                 * Status berasal langsung dari
                                 * aturan_denda_versi.status.
                                 *
                                 * Business rule:
                                 *
                                 * draft   = masih dapat diedit
                                 * aktif   = sedang berlaku dan terkunci
                                 * selesai = histori dan terkunci
                                 */

                                $status = $versi['status'] ?? '';


                                $statusMeta = match ($status) {

                                    'draft' => [
                                        'label' => 'Draft',
                                        'badge' => 'warning',
                                        'icon'  => 'fa-file-alt',
                                    ],

                                    'aktif' => [
                                        'label' => 'Aktif',
                                        'badge' => 'success',
                                        'icon'  => 'fa-check-circle',
                                    ],

                                    'selesai' => [
                                        'label' => 'Selesai',
                                        'badge' => 'secondary',
                                        'icon'  => 'fa-history',
                                    ],

                                    default => [
                                        'label' => 'Tidak Diketahui',
                                        'badge' => 'dark',
                                        'icon'  => 'fa-question-circle',
                                    ],

                                };


                                /*
                                 * ==================================================
                                 * JUMLAH RENTANG
                                 * ==================================================
                                 */

                                $aturanCount = count(
                                    $versi['aturan'] ?? []
                                );


                                /*
                                 * ==================================================
                                 * TANGGAL MULAI
                                 * ==================================================
                                 */

                                $tanggalMulai = ! empty(
                                    $versi['tanggal_mulai']
                                )
                                    ? date(
                                        'd-m-Y',
                                        strtotime(
                                            $versi['tanggal_mulai']
                                        )
                                    )
                                    : '-';


                                /*
                                 * ==================================================
                                 * TANGGAL SELESAI
                                 * ==================================================
                                 */

                                $tanggalSelesai = ! empty(
                                    $versi['tanggal_selesai']
                                )
                                    ? date(
                                        'd-m-Y',
                                        strtotime(
                                            $versi['tanggal_selesai']
                                        )
                                    )
                                    : null;

                                ?>


                                <tr>

                                    <!-- =================================================
                                         #
                                    ================================================== -->

                                    <td class="text-center">

                                        <?= $index + 1 ?>

                                    </td>


                                    <!-- =================================================
                                         KODE VERSI
                                    ================================================== -->

                                    <td>

                                        <span class="badge badge-primary">

                                            <?= esc(
                                                $versi['kode_versi'] ?? '-'
                                            ) ?>

                                        </span>

                                    </td>


                                    <!-- =================================================
                                         NAMA VERSI
                                    ================================================== -->

                                    <td>

                                        <strong>

                                            <?= esc(
                                                $versi['nama_versi'] ?? '-'
                                            ) ?>

                                        </strong>


                                        <?php if (! empty(
                                            $versi['keterangan']
                                        )) : ?>

                                            <small
                                                class="d-block text-muted mt-1"
                                            >

                                                <?= esc(
                                                    $versi['keterangan']
                                                ) ?>

                                            </small>

                                        <?php endif; ?>

                                    </td>


                                    <!-- =================================================
                                         TANGGAL MULAI
                                    ================================================== -->

                                    <td>

                                        <span class="text-nowrap">

                                            <i
                                                class="far fa-calendar-alt text-muted mr-1"
                                            ></i>

                                            <?= $tanggalMulai ?>

                                        </span>

                                    </td>


                                    <!-- =================================================
                                         TANGGAL SELESAI
                                    ================================================== -->

                                    <td>

                                        <?php if (
                                            $tanggalSelesai === null
                                        ) : ?>

                                            <span class="text-success text-nowrap">

                                                <i
                                                    class="fas fa-infinity mr-1"
                                                ></i>

                                                Sekarang

                                            </span>

                                        <?php else : ?>

                                            <span class="text-nowrap">

                                                <i
                                                    class="far fa-calendar-check text-muted mr-1"
                                                ></i>

                                                <?= $tanggalSelesai ?>

                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <!-- =================================================
                                         JUMLAH RENTANG
                                    ================================================== -->

                                    <td class="text-center">

                                        <span class="badge badge-info">

                                            <?= $aturanCount ?>

                                            <?= $aturanCount === 1
                                                ? 'Rentang'
                                                : 'Rentang' ?>

                                        </span>

                                    </td>


                                    <!-- =================================================
                                         STATUS
                                    ================================================== -->

                                    <td class="text-center">

                                        <span
                                            class="badge badge-<?= esc(
                                                $statusMeta['badge']
                                            ) ?>"
                                        >

                                            <i
                                                class="fas <?= esc(
                                                    $statusMeta['icon']
                                                ) ?> mr-1"
                                            ></i>

                                            <?= esc(
                                                $statusMeta['label']
                                            ) ?>

                                        </span>

                                    </td>


                                    <!-- =================================================
                                         AKSI
                                    ================================================== -->

                                    <td class="text-center">

                                        <div
                                            class="btn-group btn-group-sm"
                                            role="group"
                                            aria-label="Aksi versi aturan denda"
                                        >

                                            <!-- DETAIL -->

                                            <a
                                                href="<?= site_url(
                                                    'pengaturan/aturan-denda/detail/' .
                                                    $versi['id']
                                                ) ?>"
                                                class="btn btn-info"
                                                title="Detail"
                                                data-toggle="tooltip"
                                            >

                                                <i class="fas fa-eye"></i>

                                            </a>


                                            <?php if (
                                                $status === 'draft'
                                            ) : ?>

                                                <!-- EDIT -->

                                                <a
                                                    href="<?= site_url(
                                                        'pengaturan/aturan-denda/edit/' .
                                                        $versi['id']
                                                    ) ?>"
                                                    class="btn btn-warning"
                                                    title="Edit Versi"
                                                    data-toggle="tooltip"
                                                >

                                                    <i class="fas fa-edit"></i>

                                                </a>


                                                <!-- DELETE -->

                                                <button
                                                    type="button"
                                                    class="btn btn-danger btn-delete-versi"
                                                    data-id="<?= esc(
                                                        $versi['id']
                                                    ) ?>"
                                                    data-name="<?= esc(
                                                        $versi['nama_versi'] ?? ''
                                                    ) ?>"
                                                    title="Hapus Versi"
                                                    data-toggle="tooltip"
                                                >

                                                    <i class="fas fa-trash"></i>

                                                </button>

                                            <?php endif; ?>

                                        </div>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>


<!-- ================================================================
     DELETE FORM
================================================================= -->

<form
    id="formDeleteVersi"
    method="post"
    style="display: none;"
>

    <?= csrf_field() ?>

</form>


<?= $this->endSection() ?>


<?= $this->section('scripts') ?>

<script>

$(document).ready(function () {

    /*
     * ==============================================================
     * DataTables
     * ==============================================================
     */

    if (! $.fn.DataTable.isDataTable(
        '#tableAturanDendaVersi'
    )) {

        $('#tableAturanDendaVersi').DataTable({

            responsive: true,

            autoWidth: false,

            order: [
                [3, 'desc']
            ],

            language: {

                search: 'Cari:',

                lengthMenu:
                    'Tampilkan _MENU_ data',

                info:
                    'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',

                infoEmpty:
                    'Tidak ada data',

                zeroRecords:
                    'Data tidak ditemukan',

                emptyTable:
                    'Belum ada versi aturan denda',

                paginate: {

                    previous: 'Sebelumnya',

                    next: 'Berikutnya'

                }

            }

        });

    }


    /*
     * ==============================================================
     * Tooltip
     * ==============================================================
     */

    $('[data-toggle="tooltip"]').tooltip();


    /*
     * ==============================================================
     * Delete Versi
     * ==============================================================
     */

    $(document).on(
        'click',
        '.btn-delete-versi',
        function () {

            const id =
                $(this).data('id');

            const name =
                $(this).data('name');


            Swal.fire({

                title: 'Hapus Versi?',

                html:
                    'Versi <strong>' +
                    $('<div>')
                        .text(name)
                        .html() +
                    '</strong> akan dihapus.',

                icon: 'warning',

                showCancelButton: true,

                confirmButtonText:
                    'Ya, Hapus',

                cancelButtonText:
                    'Batal',

                reverseButtons: true,

                focusCancel: true

            }).then(function (result) {

                if (! result.isConfirmed) {

                    return;

                }


                const form =
                    $('#formDeleteVersi');


                form.attr(
                    'action',
                    '<?= site_url(
                        'pengaturan/aturan-denda/delete'
                    ) ?>/' + id
                );


                form.trigger('submit');

            });

        }
    );

});

</script>

<?= $this->endSection() ?>