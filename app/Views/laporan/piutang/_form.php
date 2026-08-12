<!-- ==========================================================
     FILTER LAPORAN PIUTANG
     ========================================================== -->

<form
    method="get"
    action="<?= site_url('laporan/piutang') ?>"
    id="formFilterPiutang"
>


    <div class="row">


        <!-- ======================================================
             TANGGAL DARI
             ====================================================== -->

        <div class="col-md-6 col-lg-3">

            <div class="form-group">

                <label for="tanggal_dari">

                    <i class="far fa-calendar-alt mr-1"></i>

                    Tanggal Dari

                </label>

                <input
                    type="date"
                    name="tanggal_dari"
                    id="tanggal_dari"
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

        <div class="col-md-6 col-lg-3">

            <div class="form-group">

                <label for="tanggal_sampai">

                    <i class="far fa-calendar-alt mr-1"></i>

                    Tanggal Sampai

                </label>

                <input
                    type="date"
                    name="tanggal_sampai"
                    id="tanggal_sampai"
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

        <div class="col-md-6 col-lg-3">

            <div class="form-group">

                <label for="customer_id">

                    <i class="fas fa-user mr-1"></i>

                    Customer

                </label>

                <select
                    name="customer_id"
                    id="customer_id"
                    class="form-control"
                >

                    <option value="">
                        Semua Customer
                    </option>


                    <?php foreach (
                        $customers ?? []
                        as $customer
                    ): ?>

                        <?php
                        $customerId =
                            (int) (
                                $customer['id']
                                ?? 0
                            );

                        $selected =
                            (
                                (int) (
                                    $filter['customer_id']
                                    ?? 0
                                )
                                === $customerId
                                && $customerId > 0
                            )
                                ? 'selected'
                                : '';
                        ?>


                        <option
                            value="<?= $customerId ?>"
                            <?= $selected ?>
                        >

                            <?= esc(
                                $customer['nama']
                                ?? $customer['nama_customer']
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

        <div class="col-md-6 col-lg-3">

            <div class="form-group">

                <label for="status">

                    <i class="fas fa-check-circle mr-1"></i>

                    Status Piutang

                </label>

                <select
                    name="status"
                    id="status"
                    class="form-control"
                >

                    <option
                        value="semua"
                        <?= (
                            ($filter['status'] ?? 'semua')
                            === 'semua'
                        )
                            ? 'selected'
                            : ''
                        ?>
                    >
                        Semua
                    </option>


                    <option
                        value="belum_lunas"
                        <?= (
                            ($filter['status'] ?? '')
                            === 'belum_lunas'
                        )
                            ? 'selected'
                            : ''
                        ?>
                    >
                        Belum Lunas
                    </option>


                    <option
                        value="lunas"
                        <?= (
                            ($filter['status'] ?? '')
                            === 'lunas'
                        )
                            ? 'selected'
                            : ''
                        ?>
                    >
                        Lunas
                    </option>

                </select>

            </div>

        </div>

    </div>


    <div class="row">


        <!-- ======================================================
             STATUS JATUH TEMPO
             ====================================================== -->

        <div class="col-md-6 col-lg-3">

            <div class="form-group">

                <label for="jatuh_tempo">

                    <i class="fas fa-clock mr-1"></i>

                    Status Jatuh Tempo

                </label>

                <select
                    name="jatuh_tempo"
                    id="jatuh_tempo"
                    class="form-control"
                >

                    <option
                        value="semua"
                        <?= (
                            ($filter['jatuh_tempo'] ?? 'semua')
                            === 'semua'
                        )
                            ? 'selected'
                            : ''
                        ?>
                    >
                        Semua
                    </option>


                    <option
                        value="belum_jatuh_tempo"
                        <?= (
                            ($filter['jatuh_tempo'] ?? '')
                            === 'belum_jatuh_tempo'
                        )
                            ? 'selected'
                            : ''
                        ?>
                    >
                        Belum Jatuh Tempo
                    </option>


                    <option
                        value="jatuh_tempo"
                        <?= (
                            ($filter['jatuh_tempo'] ?? '')
                            === 'jatuh_tempo'
                        )
                            ? 'selected'
                            : ''
                        ?>
                    >
                        Jatuh Tempo
                    </option>


                    <option
                        value="menunggak"
                        <?= (
                            ($filter['jatuh_tempo'] ?? '')
                            === 'menunggak'
                        )
                            ? 'selected'
                            : ''
                        ?>
                    >
                        Menunggak
                    </option>

                </select>

            </div>

        </div>


        <!-- ======================================================
             ACTION
             ====================================================== -->

        <div class="col-md-6 col-lg-9">

            <div class="form-group">

                <label>
                    &nbsp;
                </label>

                <div>

                    <button
                        type="submit"
                        class="btn btn-primary mr-2"
                    >

                        <i class="fas fa-filter mr-1"></i>

                        Terapkan Filter

                    </button>


                    <a
                        href="<?= site_url('laporan/piutang') ?>"
                        class="btn btn-secondary"
                        id="btnResetFilter"
                    >

                        <i class="fas fa-sync-alt mr-1"></i>

                        Reset

                    </a>

                </div>

            </div>

        </div>

    </div>


</form>