<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?>
Edit Customer
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- ================================================================ -->
<!-- Header -->
<!-- ================================================================ -->

<div class="d-flex justify-content-between align-items-center mb-3">

    <div>

        <h3 class="mb-1">
            <i class="fas fa-user-edit text-warning mr-2"></i>
            Edit Customer
        </h3>

        <p class="text-muted mb-0">
            Perbarui data customer.
        </p>

    </div>

</div>


<!-- ================================================================ -->
<!-- Form Card -->
<!-- ================================================================ -->

<div class="card shadow-sm">

    <div class="card-header">

        <h3 class="card-title">

            <i class="fas fa-id-card mr-1"></i>

            Form Customer

        </h3>

    </div>


    <form
        action="<?= site_url('customer/update/' . $customer['id']) ?>"
        method="post"
    >

        <?= csrf_field() ?>


        <div class="card-body">

            <!-- ==================================================== -->
            <!-- Kode Customer -->
            <!-- ==================================================== -->

            <div class="form-group">

                <label>
                    Kode Customer
                </label>

                <div class="input-group">

                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="fas fa-id-badge"></i>
                        </span>
                    </div>

                    <input
                        type="text"
                        class="form-control"
                        value="<?= esc($customer['kode_customer'] ?? '-') ?>"
                        readonly
                    >

                </div>

                <small class="form-text text-muted">
                    Kode customer dibuat otomatis oleh sistem dan tidak dapat diubah.
                </small>

            </div>


            <!-- ==================================================== -->
            <!-- Customer Form -->
            <!-- ==================================================== -->

            <?= $this->include('customer/_form') ?>

        </div>


        <!-- ======================================================== -->
        <!-- Footer -->
        <!-- ======================================================== -->

        <div class="card-footer">

            <?= view('components/form/form_buttons', [
                'backUrl'    => 'customer',
                'submitText' => 'Update',
            ]) ?>

        </div>

    </form>

</div>

<?= $this->endSection() ?>