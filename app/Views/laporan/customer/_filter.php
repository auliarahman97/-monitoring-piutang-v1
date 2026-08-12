<div class="card filter-card mb-3">

    <div class="card-body">

        <form
            method="get"
            action="<?= site_url('laporan/customer') ?>"
        >

            <div class="row align-items-end">

                <div class="col-md-9">

                    <label
                        for="customer_id"
                        class="filter-label"
                    >
                        Customer
                    </label>


                    <select
                        name="customer_id"
                        id="customer_id"
                        class="form-control"
                    >

                        <option value="">
                            -- Pilih Customer --
                        </option>


                        <?php foreach (
                            $customers ?? []
                            as $item
                        ) : ?>

                            <option
                                value="<?= (int) $item['id'] ?>"
                                <?= (
                                    (string) $customerId
                                    === (string) $item['id']
                                )
                                    ? 'selected'
                                    : ''
                                ?>
                            >

                                <?= esc(
                                    $item['nama']
                                    ?? '-'
                                ) ?>


                                <?php if (
                                    ! empty(
                                        $item[
                                            'kode_customer'
                                        ]
                                    )
                                ) : ?>

                                    -
                                    <?= esc(
                                        $item[
                                            'kode_customer'
                                        ]
                                    ) ?>

                                <?php endif; ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <div class="col-md-3 mt-3 mt-md-0">

                    <button
                        type="submit"
                        class="btn btn-primary btn-block btn-filter"
                    >

                        <i class="fas fa-search mr-1"></i>

                        Tampilkan Laporan

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>