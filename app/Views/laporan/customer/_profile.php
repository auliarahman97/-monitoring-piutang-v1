<div class="card customer-profile mb-3">

    <div class="card-body">

        <div class="d-flex align-items-center">

            <div class="customer-avatar mr-3">

                <i class="fas fa-user"></i>

            </div>


            <div>

                <div class="customer-name">

                    <?= esc(
                        $customer['nama']
                        ?? '-'
                    ) ?>

                </div>


                <div class="customer-code">

                    <?php if (
                        ! empty(
                            $customer[
                                'kode_customer'
                            ]
                        )
                    ) : ?>

                        <i class="fas fa-id-badge mr-1"></i>

                        <?= esc(
                            $customer[
                                'kode_customer'
                            ]
                        ) ?>

                    <?php endif; ?>

                </div>

            </div>


            <div class="ml-auto text-right">

                <div class="mb-2">

                    <a
                        href="<?= site_url(
                            'laporan/customer/print?customer_id='
                            . (int) $customerId
                        ) ?>"
                        target="_blank"
                        class="btn btn-outline-secondary btn-sm mr-1"
                    >

                        <i class="fas fa-print mr-1"></i>

                        Preview

                    </a>


                    <a
                        href="<?= site_url(
                            'laporan/customer/pdf?customer_id='
                            . (int) $customerId
                        ) ?>"
                        class="btn btn-danger btn-sm"
                    >

                        <i class="fas fa-file-pdf mr-1"></i>

                        Export PDF

                    </a>

                </div>


                <div class="customer-meta">

                    <i class="fas fa-calendar-alt mr-1"></i>

                    Posisi laporan

                </div>


                <strong>

                    <?= tanggalIndonesia(
                        date('Y-m-d')
                    ) ?>

                </strong>

            </div>

        </div>


        <?php if (
            ! empty(
                $customer['no_hp']
            )
            || ! empty(
                $customer['alamat']
            )
        ) : ?>

            <hr>


            <div class="row">

                <?php if (
                    ! empty(
                        $customer['no_hp']
                    )
                ) : ?>

                    <div class="col-md-4">

                        <small class="text-muted d-block">
                            Nomor HP
                        </small>

                        <strong>
                            <?= esc(
                                $customer['no_hp']
                            ) ?>
                        </strong>

                    </div>

                <?php endif; ?>


                <?php if (
                    ! empty(
                        $customer['alamat']
                    )
                ) : ?>

                    <div class="col-md-8">

                        <small class="text-muted d-block">
                            Alamat
                        </small>

                        <strong>
                            <?= esc(
                                $customer['alamat']
                            ) ?>
                        </strong>

                    </div>

                <?php endif; ?>

            </div>

        <?php endif; ?>

    </div>

</div>