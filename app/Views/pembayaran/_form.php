<div class="card card-primary card-outline shadow-sm">

    <div class="card-header">

        <h3 class="card-title">

            <i class="fas fa-file-invoice-dollar mr-1"></i>

            Form Pembayaran

        </h3>

    </div>


    <form
        id="formPembayaran"
        action="<?= site_url('pembayaran/store') ?>"
        method="post"
        autocomplete="off"
    >

        <?= csrf_field() ?>


        <div class="card-body">

            <!-- ======================================================
                 FLASH ERROR
                 ====================================================== -->

            <?php if (
                session()->getFlashdata('error')
            ) : ?>

                <div class="alert alert-danger">

                    <i class="fas fa-exclamation-circle mr-1"></i>

                    <?= esc(
                        session()->getFlashdata('error')
                    ) ?>

                </div>

            <?php endif; ?>


            <?php if (
                session()->getFlashdata('errors')
            ) : ?>

                <div class="alert alert-danger">

                    <strong>
                        Periksa kembali data berikut:
                    </strong>

                    <ul class="mb-0 mt-2">

                        <?php foreach (
                            session()->getFlashdata('errors')
                            as $error
                        ) : ?>

                            <li>
                                <?= esc($error) ?>
                            </li>

                        <?php endforeach; ?>

                    </ul>

                </div>

            <?php endif; ?>


            <!-- ======================================================
                 CUSTOMER
                 ====================================================== -->

            <div class="form-group">

                <label for="customer_id">

                    Customer

                    <span class="text-danger">
                        *
                    </span>

                </label>


                <select
                    name="customer_id"
                    id="customer_id"
                    class="form-control"
                    required
                >

                    <option value="">
                        -- Pilih Customer --
                    </option>


                    <?php foreach (
                        $customers ?? []
                        as $customer
                    ) : ?>

                        <option
                            value="<?= (int) $customer['id'] ?>"
                            <?= old('customer_id')
                                == $customer['id']
                                ? 'selected'
                                : ''
                            ?>
                        >

                            <?= esc(
                                $customer['nama']
                                ?? '-'
                            ) ?>

                            <?php if (
                                ! empty(
                                    $customer[
                                        'kode_customer'
                                    ]
                                )
                            ) : ?>

                                -
                                <?= esc(
                                    $customer[
                                        'kode_customer'
                                    ]
                                ) ?>

                            <?php endif; ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <!-- ======================================================
                 PIUTANG
                 ====================================================== -->

           <div class="form-group">

                <label for="piutang_id">
                    Piutang
                    <span class="text-danger">*</span>
                </label>

                <select
                    name="piutang_id"
                    id="piutang_id"
                    class="form-control"
                    required
                    disabled
                >
                    <option value="">
                        -- Pilih Customer Terlebih Dahulu --
                    </option>
                </select>

                <small class="form-text text-muted">
                    Pilih piutang yang akan dibayar.
                </small>

            </div>


            <!-- ======================================================
                 TANGGAL PEMBAYARAN
                 ====================================================== -->

            <div class="form-group">

                <label for="tanggal_pembayaran">

                    Tanggal Pembayaran

                    <span class="text-danger">
                        *
                    </span>

                </label>


                <input
                    type="date"
                    name="tanggal_pembayaran"
                    id="tanggal_pembayaran"
                    class="form-control"
                    value="<?= esc(
                        old(
                            'tanggal_pembayaran',
                            date('Y-m-d')
                        )
                    ) ?>"
                    required
                >


                <small class="form-text text-muted">

                    Tanggal ini digunakan untuk menentukan
                    apakah denda sudah berlaku.

                </small>

            </div>


            <hr>


            <!-- ======================================================
                 PREVIEW TAGIHAN
                 ====================================================== -->

            <div
                id="tagihanPanel"
                class="d-none"
            >

                <div class="card card-light mb-4">

                    <div class="card-header">

                        <h3 class="card-title">

                            <i
                                class="fas fa-calculator mr-1"
                            ></i>

                            Tagihan Berjalan

                        </h3>

                    </div>


                    <div class="card-body">

                        <div class="row">

                            <!-- Pokok -->
                            <div class="col-md-4">

                                <div
                                    class="small text-muted"
                                >
                                    Sisa Pokok
                                </div>

                                <div
                                    id="previewPokok"
                                    class="font-weight-bold"
                                >
                                    Rp 0
                                </div>

                            </div>


                            <!-- Bunga -->
                            <div class="col-md-4">

                                <div
                                    class="small text-muted"
                                >
                                    Sisa Bunga
                                </div>

                                <div
                                    id="previewBunga"
                                    class="font-weight-bold"
                                >
                                    Rp 0
                                </div>

                            </div>


                            <!-- Denda -->
                            <div class="col-md-4">

                                <div
                                    class="small text-muted"
                                >
                                    Sisa Denda
                                </div>

                                <div
                                    id="previewDenda"
                                    class="font-weight-bold text-danger"
                                >
                                    Rp 0
                                </div>

                            </div>

                        </div>


                        <hr>


                        <div
                            class="d-flex justify-content-between align-items-center"
                        >

                            <span class="font-weight-bold">
                                Total Tagihan
                            </span>

                            <span
                                id="previewTotal"
                                class="h5 mb-0 text-primary font-weight-bold"
                            >
                                Rp 0
                            </span>

                        </div>

                    </div>

                </div>


                <!-- ==================================================
                     NOMINAL PEMBAYARAN
                     ================================================== -->

                <div class="form-group">

                    <label for="nominal_pembayaran">

                        Nominal Pembayaran

                        <span class="text-danger">
                            *
                        </span>

                    </label>


                    <div class="input-group">

                        <div class="input-group-prepend">

                            <span class="input-group-text">
                                Rp
                            </span>

                        </div>


                        <input
                            type="text"
                            name="nominal_pembayaran"
                            id="nominal_pembayaran"
                            class="form-control text-right"
                            placeholder="0"
                            inputmode="decimal"
                            disabled
                            required
                        >

                    </div>


                    <small
                        id="nominalHelp"
                        class="form-text text-muted"
                    >

                        Maksimal sebesar total tagihan.

                    </small>

                </div>


                <!-- ==================================================
                     ALOKASI
                     ================================================== -->

                <div
                    id="alokasiPanel"
                    class="card card-success card-outline d-none"
                >

                    <div class="card-header">

                        <h3 class="card-title">

                            <i
                                class="fas fa-random mr-1"
                            ></i>

                            Alokasi Pembayaran

                        </h3>

                    </div>


                    <div class="card-body">

                        <div
                            class="d-flex justify-content-between mb-2"
                        >

                            <span>

                                <i
                                    class="fas fa-exclamation-circle text-danger mr-1"
                                ></i>

                                Denda

                            </span>

                            <strong
                                id="alokasiDenda"
                                class="text-danger"
                            >
                                Rp 0
                            </strong>

                        </div>


                        <div
                            class="d-flex justify-content-between mb-2"
                        >

                            <span>

                                <i
                                    class="fas fa-percent text-warning mr-1"
                                ></i>

                                Bunga

                            </span>

                            <strong id="alokasiBunga">
                                Rp 0
                            </strong>

                        </div>


                        <div
                            class="d-flex justify-content-between"
                        >

                            <span>

                                <i
                                    class="fas fa-money-bill-wave text-primary mr-1"
                                ></i>

                                Pokok

                            </span>

                            <strong id="alokasiPokok">
                                Rp 0
                            </strong>

                        </div>


                        <hr>


                        <div
                            class="d-flex justify-content-between align-items-center"
                        >

                            <strong>
                                Sisa Tagihan
                            </strong>

                            <strong
                                id="previewSisa"
                                class="text-primary"
                            >
                                Rp 0
                            </strong>

                        </div>

                    </div>

                </div>


                <!-- ==================================================
                     KETERANGAN
                     ================================================== -->

                <div class="form-group">

                    <label for="keterangan">
                        Keterangan
                    </label>


                    <textarea
                        name="keterangan"
                        id="keterangan"
                        rows="3"
                        class="form-control"
                        maxlength="1000"
                        placeholder="Keterangan pembayaran (opsional)"
                    ><?= esc(
                        old('keterangan')
                    ) ?></textarea>

                </div>

            </div>

        </div>


        <!-- ==========================================================
             FOOTER
             ========================================================== -->

        <div class="card-footer d-flex justify-content-between">

            <a
                href="<?= site_url('pembayaran') ?>"
                class="btn btn-secondary"
            >

                <i class="fas fa-times mr-1"></i>

                Batal

            </a>


            <button
                type="submit"
                id="btnSimpan"
                class="btn btn-primary"
                disabled
            >

                <i class="fas fa-save mr-1"></i>

                Simpan Pembayaran

            </button>

        </div>

    </form>

</div>