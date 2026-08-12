<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?>
Piutang
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h3 class="mb-1">

            <i class="fas fa-file-invoice-dollar text-primary mr-2"></i>

            Data Piutang

        </h3>

        <p class="text-muted mb-0">

            Kelola seluruh transaksi piutang customer.

        </p>

    </div>


    <div>

        <a
            href="<?= site_url('piutang/create') ?>"
            class="btn btn-primary"
        >

            <i class="fas fa-plus mr-1"></i>

            Tambah Piutang

        </a>

    </div>

</div>


<div class="card card-primary card-outline shadow-sm">

    <div class="card-header">

        <h3 class="card-title">

            <i class="fas fa-list mr-1"></i>

            Daftar Piutang

        </h3>

    </div>


    <div class="card-body">

        <div class="table-responsive">

            <table
                id="tablePiutang"
                class="table table-bordered table-hover table-striped datatable"
            >

                <thead>

                    <tr>

                        <th width="55" class="text-center">
                            No
                        </th>

                        <th width="130">
                            No. Piutang
                        </th>

                        <th>
                            Customer
                        </th>

                        <th width="115" class="text-center">
                            Tanggal
                        </th>

                        <th width="125" class="text-center">
                            Jatuh Tempo
                        </th>

                        <th width="150" class="text-right">
                            Pokok
                        </th>

                        <th width="90" class="text-center">
                            Bunga
                        </th>

                        <th width="90" class="text-center">
                            Denda
                        </th>

                        <th width="110" class="text-center">
                            Status
                        </th>

                        <th width="130" class="text-center">
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody>

                <?php if (! empty($piutang)) : ?>

                    <?php foreach ($piutang as $no => $row) : ?>

                        <tr>

                            <td class="text-center">
                                <?= $no + 1 ?>
                            </td>


                            <td>

                                <strong>
                                    <?= esc($row['nomor_piutang']) ?>
                                </strong>

                            </td>


                            <td>

                                <div class="font-weight-bold">

                                    <?= esc(
                                        $row['nama_customer']
                                        ?? $row['nama']
                                        ?? '-'
                                    ) ?>

                                </div>

                                <?php if (! empty($row['kode_customer'])) : ?>

                                    <small class="text-muted">

                                        <?= esc(
                                            $row['kode_customer']
                                        ) ?>

                                    </small>

                                <?php endif; ?>

                            </td>


                            <td class="text-center">

                                <?= tanggalIndonesia(
                                    $row['tanggal_piutang']
                                ) ?>

                            </td>


                            <td class="text-center">

                                <?= tanggalIndonesia(
                                    $row['tanggal_jatuh_tempo']
                                ) ?>

                            </td>


                            <td class="text-right">

                                <?= rupiah(
                                    (float) $row['nominal_pokok']
                                ) ?>

                            </td>


                            <td class="text-center">

                                <?= number_format(
                                    (float) $row['persentase_bunga'],
                                    2,
                                    ',',
                                    '.'
                                ) ?>%

                            </td>


                            <td class="text-center">

                                <?php if (
                                    (float) $row['persentase_denda'] > 0
                                ) : ?>

                                    <span class="badge badge-warning">

                                        <?= number_format(
                                            (float) $row['persentase_denda'],
                                            2,
                                            ',',
                                            '.'
                                        ) ?>%

                                    </span>

                                <?php else : ?>

                                    <span class="badge badge-secondary">
                                        0%
                                    </span>

                                <?php endif; ?>

                            </td>


                            <td class="text-center">

                                <?php
                                $today = date('Y-m-d');
                                $jatuhTempo = $row['tanggal_jatuh_tempo'];
                                ?>

                                <?php if ($today <= $jatuhTempo) : ?>

                                    <span class="badge badge-success">
                                        Belum Jatuh Tempo
                                    </span>

                                <?php else : ?>

                                    <span class="badge badge-danger">
                                        Jatuh Tempo
                                    </span>

                                <?php endif; ?>

                            </td>


                            <td class="text-center">

                                <a
                                    href="<?= site_url(
                                        'piutang/detail/' . $row['id']
                                    ) ?>"
                                    class="btn btn-info btn-sm"
                                    title="Detail"
                                >

                                    <i class="fas fa-eye"></i>

                                </a>


                                <a
                                    href="<?= site_url(
                                        'piutang/edit/' . $row['id']
                                    ) ?>"
                                    class="btn btn-warning btn-sm"
                                    title="Edit"
                                >

                                    <i class="fas fa-edit"></i>

                                </a>


                                <button
                                    type="button"
                                    class="btn btn-danger btn-sm btn-delete-piutang"
                                    title="Hapus"
                                    data-url="<?= site_url(
                                        'piutang/delete/' . $row['id']
                                    ) ?>"
                                    data-nomor="<?= esc(
                                        $row['nomor_piutang']
                                    ) ?>"
                                >
                                    <i class="fas fa-trash"></i>
                                    <span class="sr-only">
                                        Hapus
                                    </span>
                                </button>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>

<?= $this->include('piutang/_script') ?>

<?= $this->endSection() ?>