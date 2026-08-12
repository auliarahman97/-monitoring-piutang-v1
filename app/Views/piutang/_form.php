<?php

$isEdit = isset($piutang);

$data = $piutang ?? [];

$customerList = $customers ?? $customer ?? [];

$old = static function (
    string $field,
    mixed $default = ''
) use ($data): mixed {

    return old(
        $field,
        $data[$field] ?? $default
    );
};

?>

<div class="row">


    <!-- Customer -->

    <div class="col-md-8">

        <div class="form-group">

            <label for="customer_id">

                Customer

                <span class="text-danger">*</span>

            </label>


            <?php if ($isEdit) : ?>

                <div class="input-group">

                    <div class="input-group-prepend">

                        <span class="input-group-text">

                            <i class="fas fa-user"></i>

                        </span>

                    </div>


                    <input
                        type="text"
                        id="customer_display"
                        class="form-control"
                        value="<?= esc(
                            ($data['kode_customer'] ?? '-')
                            . ' - '
                            . ($data['nama_customer'] ?? '-')
                        ) ?>"
                        readonly
                    >

                </div>


                <!--
                    Customer tetap dikirim ke Controller,
                    tetapi tidak dapat diubah oleh user.
                -->

                <input
                    type="hidden"
                    name="customer_id"
                    value="<?= esc(
                        (string) $data['customer_id']
                    ) ?>"
                >


                <small class="form-text text-muted">

                    <i class="fas fa-lock mr-1"></i>

                    Customer tidak dapat diubah setelah
                    piutang dibuat.

                </small>


            <?php else : ?>


                <select
                    name="customer_id"
                    id="customer_id"
                    class="form-control"
                    required
                >

                    <option value="">
                        Pilih Customer
                    </option>


                    <?php foreach ($customerList as $customerItem) : ?>

                        <option
                            value="<?= $customerItem['id'] ?>"
                            <?= (string) $old('customer_id') ===
                                (string) $customerItem['id']
                                ? 'selected'
                                : '' ?>
                        >

                            <?= esc(
                                $customerItem['kode_customer']
                                . ' - '
                                . $customerItem['nama']
                            ) ?>

                        </option>

                    <?php endforeach; ?>

                </select>


                <small class="form-text text-muted">

                    Pilih customer yang memiliki status aktif.

                </small>


            <?php endif; ?>

        </div>

    </div>


    <!-- Nomor Piutang -->

    <div class="col-md-4">

        <div class="form-group">

            <label for="nomor_piutang">

                Nomor Piutang

            </label>

            <input
                type="text"
                name="nomor_piutang"
                id="nomor_piutang"
                class="form-control"
                value="<?= esc(
                    (string) $old(
                        'nomor_piutang',
                        'Otomatis'
                    )
                ) ?>"
                readonly
            >

            <small class="form-text text-muted">

                Nomor piutang tidak dapat diubah.

            </small>

        </div>

    </div>


    <!-- Tanggal Piutang -->

    <div class="col-md-6">

        <div class="form-group">

            <label for="tanggal_piutang">

                Tanggal Piutang

                <span class="text-danger">*</span>

            </label>

            <input
                type="date"
                name="tanggal_piutang"
                id="tanggal_piutang"
                class="form-control"
                value="<?= esc(
                    (string) $old(
                        'tanggal_piutang',
                        date('Y-m-d')
                    )
                ) ?>"
                required
            >

        </div>

    </div>


    <!-- Jatuh Tempo -->

    <div class="col-md-6">

        <div class="form-group">

            <label for="tanggal_jatuh_tempo">

                Tanggal Jatuh Tempo

                <span class="text-danger">*</span>

            </label>

            <input
                type="date"
                name="tanggal_jatuh_tempo"
                id="tanggal_jatuh_tempo"
                class="form-control"
                value="<?= esc(
                    (string) $old(
                        'tanggal_jatuh_tempo'
                    )
                ) ?>"
                required
            >

            <small class="form-text text-muted">

                Denda mulai berlaku setelah tanggal ini terlewati.

            </small>

        </div>

    </div>


    <!-- Nominal Pokok -->

    <div class="col-md-6">

        <div class="form-group">

            <label for="nominal_pokok">

                Nominal Pokok

                <span class="text-danger">*</span>

            </label>

            <div class="input-group">

                <div class="input-group-prepend">

                    <span class="input-group-text">
                        Rp
                    </span>

                </div>

                <input
                    type="number"
                    name="nominal_pokok"
                    id="nominal_pokok"
                    class="form-control text-right"
                    value="<?= esc(
                        (string) $old(
                            'nominal_pokok'
                        )
                    ) ?>"
                    min="1"
                    step="0.01"
                    required
                >

            </div>

            <small class="form-text text-muted">

                Sistem akan menentukan aturan denda berdasarkan
                nominal pokok ini.

            </small>

        </div>

    </div>


    <!-- Persentase Bunga -->

    <div class="col-md-3">

        <div class="form-group">

            <label for="persentase_bunga">

                Bunga

                <span class="text-danger">*</span>

            </label>

            <div class="input-group">

                <input
                    type="number"
                    name="persentase_bunga"
                    id="persentase_bunga"
                    class="form-control text-right"
                    value="<?= esc(
                        (string) $old(
                            'persentase_bunga',
                            0
                        )
                    ) ?>"
                    min="0"
                    max="100"
                    step="0.01"
                    required
                >

                <div class="input-group-append">

                    <span class="input-group-text">
                        %
                    </span>

                </div>

            </div>

        </div>

    </div>


    <!-- Nominal Bunga -->

    <div class="col-md-3">

        <div class="form-group">

            <label for="nominal_bunga">

                Nominal Bunga

            </label>

            <div class="input-group">

                <div class="input-group-prepend">

                    <span class="input-group-text">
                        Rp
                    </span>

                </div>

                <input
                    type="number"
                    id="nominal_bunga"
                    class="form-control text-right"
                    value="<?= esc(
                        (string) $old(
                            'nominal_bunga',
                            0
                        )
                    ) ?>"
                    readonly
                >

            </div>

            <small class="form-text text-muted">

                Dihitung otomatis dari pokok dan persentase bunga.

            </small>

        </div>

    </div>


    <!-- Informasi Aturan Denda -->

    <div class="col-12">

        <div class="card bg-light border-0">

            <div class="card-body">

                <div class="d-flex align-items-start">

                    <div class="mr-3">

                        <i class="fas fa-percentage fa-2x text-warning"></i>

                    </div>


                    <div>

                        <h6 class="font-weight-bold mb-1">

                            Aturan Denda

                        </h6>

                        <p class="text-muted mb-0">

                            Aturan denda akan ditentukan otomatis
                            berdasarkan nominal pokok dan aturan denda
                            yang berlaku.

                        </p>

                        <p class="text-muted mb-0 mt-1">

                            Denda dihitung dari pokok awal,
                            menggunakan periode denda yang telah
                            ditetapkan, dan memiliki batas maksimum.

                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- Keterangan -->

    <div class="col-12">

        <div class="form-group">

            <label for="keterangan">

                Keterangan

            </label>

            <textarea
                name="keterangan"
                id="keterangan"
                class="form-control"
                rows="4"
                placeholder="Keterangan tambahan..."
            ><?= esc(
                (string) $old('keterangan')
            ) ?></textarea>

        </div>

    </div>

</div>