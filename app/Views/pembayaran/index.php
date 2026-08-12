<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?>
Pembayaran
<?= $this->endSection() ?>


<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h3 class="mb-1">

            <i class="fas fa-money-check-alt text-primary mr-2"></i>

            Data Pembayaran

        </h3>

        <p class="text-muted mb-0">
            Kelola histori pembayaran piutang customer.
        </p>

    </div>


    <div>

        <a
            href="<?= site_url('pembayaran/create') ?>"
            class="btn btn-primary"
        >

            <i class="fas fa-plus mr-1"></i>

            Pembayaran Baru

        </a>

    </div>

</div>


<div class="card card-primary card-outline shadow-sm">

    <div class="card-header">

        <h3 class="card-title">

            <i class="fas fa-list mr-1"></i>

            Daftar Pembayaran

        </h3>

    </div>


    <div class="card-body">

        <div class="table-responsive">

            <table
                id="tablePembayaran"
                class="table table-bordered table-hover table-striped datatable"
            >

                <thead>

                    <tr>

                        <th
                            width="55"
                            class="text-center"
                        >
                            No
                        </th>

                        <th>
                            No. Pembayaran
                        </th>

                        <th>
                            Customer
                        </th>

                        <th>
                            No. Piutang
                        </th>

                        <th
                            width="120"
                            class="text-center"
                        >
                            Tanggal
                        </th>

                        <th
                            width="160"
                            class="text-right"
                        >
                            Total Tagihan
                        </th>

                        <th
                            width="160"
                            class="text-right"
                        >
                            Pembayaran
                        </th>

                        <th
                            width="160"
                            class="text-right"
                        >
                            Sisa
                        </th>

                        <th
                            width="110"
                            class="text-center"
                        >
                            Status
                        </th>

                        <th
                            width="85"
                            class="text-center"
                        >
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody>

                    <?php if (! empty($pembayaran)) : ?>

                        <?php foreach (
                            $pembayaran
                            as $no => $row
                        ) : ?>

                            <?php
                            $status =
                                strtolower(
                                    trim(
                                        (string) (
                                            $row['status']
                                            ?? 'valid'
                                        )
                                    )
                                );

                            $sisaTagihan =
                                (float) (
                                    $row['sisa_tagihan']
                                    ?? 0
                                );

                            $dibatalkan =
                                in_array(
                                    $status,
                                    [
                                        'dibatalkan',
                                        'batal',
                                        'cancelled',
                                    ],
                                    true
                                );
                            ?>

                            <tr>

                                <!-- No -->
                                <td class="text-center">

                                    <?= $no + 1 ?>

                                </td>


                                <!-- Nomor Pembayaran -->
                                <td>

                                    <a
                                        href="<?= site_url(
                                            'pembayaran/detail/'
                                            . (int) $row['id']
                                        ) ?>"
                                        class="font-weight-bold"
                                    >

                                        <?= esc(
                                            $row[
                                                'nomor_pembayaran'
                                            ] ?? '-'
                                        ) ?>

                                    </a>

                                </td>


                                <!-- Customer -->
                                <td>

                                    <div class="font-weight-bold">

                                        <?= esc(
                                            $row[
                                                'nama_customer'
                                            ]
                                            ?? $row['nama']
                                            ?? '-'
                                        ) ?>

                                    </div>

                                    <?php if (
                                        ! empty(
                                            $row[
                                                'kode_customer'
                                            ]
                                        )
                                    ) : ?>

                                        <small class="text-muted">

                                            <?= esc(
                                                $row[
                                                    'kode_customer'
                                                ]
                                            ) ?>

                                        </small>

                                    <?php endif; ?>

                                </td>


                                <!-- Piutang -->
                                <td>

                                    <?php if (
                                        ! empty(
                                            $row[
                                                'nomor_piutang'
                                            ]
                                        )
                                    ) : ?>

                                        <a
                                            href="<?= site_url(
                                                'piutang/detail/'
                                                . (int) (
                                                    $row[
                                                        'piutang_id'
                                                    ]
                                                )
                                            ) ?>"
                                        >

                                            <?= esc(
                                                $row[
                                                    'nomor_piutang'
                                                ]
                                            ) ?>

                                        </a>

                                    <?php else : ?>

                                        -

                                    <?php endif; ?>

                                </td>


                                <!-- Tanggal -->
                                <td class="text-center">

                                    <?= ! empty(
                                        $row[
                                            'tanggal_pembayaran'
                                        ]
                                    )
                                        ? date(
                                            'd-m-Y',
                                            strtotime(
                                                $row[
                                                    'tanggal_pembayaran'
                                                ]
                                            )
                                        )
                                        : '-'
                                    ?>

                                </td>


                                <!-- Total Tagihan -->
                                <td class="text-right">

                                    <?= rupiah(
                                        (float) (
                                            $row[
                                                'total_tagihan'
                                            ] ?? 0
                                        )
                                    ) ?>

                                </td>


                                <!-- Pembayaran -->
                                <td class="text-right font-weight-bold">

                                    <?= rupiah(
                                        (float) (
                                            $row[
                                                'nominal_pembayaran'
                                            ] ?? 0
                                        )
                                    ) ?>

                                </td>


                                <!-- Sisa -->
                                <td class="text-right">

                                    <span
                                        class="<?= $sisaTagihan <= 0
                                            ? 'text-success font-weight-bold'
                                            : 'text-warning font-weight-bold'
                                        ?>"
                                    >

                                        <?= rupiah(
                                            $sisaTagihan
                                        ) ?>

                                    </span>

                                </td>


                                <!-- Status -->
                                <td class="text-center">

                                    <?php if ($dibatalkan) : ?>

                                        <span
                                            class="badge badge-danger"
                                        >

                                            <i
                                                class="fas fa-ban mr-1"
                                            ></i>

                                            Dibatalkan

                                        </span>

                                    <?php elseif (
                                        $sisaTagihan <= 0
                                    ) : ?>

                                        <span
                                            class="badge badge-success"
                                        >

                                            <i
                                                class="fas fa-check-circle mr-1"
                                            ></i>

                                            Lunas

                                        </span>

                                    <?php else : ?>

                                        <span
                                            class="badge badge-primary"
                                        >

                                            <i
                                                class="fas fa-check mr-1"
                                            ></i>

                                            Valid

                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- Aksi -->
                                <td class="text-center">

                                    <a
                                        href="<?= site_url(
                                            'pembayaran/detail/'
                                            . (int) $row['id']
                                        ) ?>"
                                        class="btn btn-info btn-sm"
                                        title="Detail"
                                    >

                                        <i class="fas fa-eye"></i>

                                    </a>

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