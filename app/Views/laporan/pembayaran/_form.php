<form
    id="formFilterPembayaran"
    method="get"
    action="<?= site_url('laporan/pembayaran') ?>"
>

    <div class="row">

        <!-- ======================================================
             TANGGAL DARI
             ====================================================== -->

        <div class="col-md-3">

            <div class="form-group mb-md-0">

                <label for="tanggal_dari">
                    Tanggal Dari
                </label>

                <input
                    type="date"
                    id="tanggal_dari"
                    name="tanggal_dari"
                    class="form-control"
                    value="<?= esc(
                        $filter['tanggal_dari'] ?? ''
                    ) ?>"
                >

            </div>

        </div>


        <!-- ======================================================
             TANGGAL SAMPAI
             ====================================================== -->

        <div class="col-md-3">

            <div class="form-group mb-md-0">

                <label for="tanggal_sampai">
                    Tanggal Sampai
                </label>

                <input
                    type="date"
                    id="tanggal_sampai"
                    name="tanggal_sampai"
                    class="form-control"
                    value="<?= esc(
                        $filter['tanggal_sampai'] ?? ''
                    ) ?>"
                >

            </div>

        </div>


        <!-- ======================================================
             CUSTOMER
             ====================================================== -->

        <div class="col-md-3">

            <div class="form-group mb-md-0">

                <label for="customer_id">
                    Customer
                </label>

                <select
                    id="customer_id"
                    name="customer_id"
                    class="form-control"
                >

                    <option value="">
                        Semua Customer
                    </option>

                    <?php foreach (
                        $customers ?? [] as $customer
                    ): ?>

                        <?php
                        $customerId =
                            (int) (
                                $customer['id'] ?? 0
                            );

                        $selected =
                            (
                                (int) (
                                    $filter['customer_id']
                                    ?? 0
                                ) === $customerId
                                &&
                                ! empty(
                                    $filter['customer_id']
                                )
                            );
                        ?>

                        <option
                            value="<?= $customerId ?>"
                            <?= $selected
                                ? 'selected'
                                : ''
                            ?>
                        >
                            <?= esc(
                                $customer['nama']
                                ?? $customer[
                                    'nama_customer'
                                ]
                                ?? '-'
                            ) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

        </div>


        <!-- ======================================================
             STATUS
             ====================================================== -->

        <div class="col-md-3">

            <div class="form-group mb-md-0">

                <label for="status">
                    Status Pembayaran
                </label>

                <select
                    id="status"
                    name="status"
                    class="form-control"
                >

                    <option
                        value="semua"
                        <?= (
                            ($filter['status']
                                ?? 'semua')
                            === 'semua'
                        )
                            ? 'selected'
                            : ''
                        ?>
                    >
                        Semua
                    </option>


                    <option
                        value="<?= esc(
                            \App\Models\PembayaranModel::STATUS_VALID
                        ) ?>"
                        <?= (
                            ($filter['status'] ?? '')
                            ===
                            \App\Models\PembayaranModel::STATUS_VALID
                        )
                            ? 'selected'
                            : ''
                        ?>
                    >
                        Valid
                    </option>


                    <option
                        value="<?= esc(
                            \App\Models\PembayaranModel::STATUS_DIBATALKAN
                        ) ?>"
                        <?= (
                            ($filter['status'] ?? '')
                            ===
                            \App\Models\PembayaranModel::STATUS_DIBATALKAN
                        )
                            ? 'selected'
                            : ''
                        ?>
                    >
                        Dibatalkan
                    </option>

                </select>

            </div>

        </div>

    </div>


    <!-- ==========================================================
         ACTION
         ========================================================== -->

    <div class="d-flex justify-content-end mt-3">

        <a
            href="<?= site_url(
                'laporan/pembayaran'
            ) ?>"
            class="btn btn-secondary mr-2"
        >

            <i class="fas fa-sync-alt mr-1"></i>

            Reset

        </a>


        <button
            type="submit"
            class="btn btn-primary"
        >

            <i class="fas fa-search mr-1"></i>

            Terapkan Filter

        </button>

    </div>

</form>