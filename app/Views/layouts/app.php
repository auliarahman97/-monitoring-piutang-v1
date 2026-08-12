<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <meta name="description"
          content="Aplikasi Monitoring Piutang">

    <title>
        <?= trim($this->renderSection('title')) ?: 'Monitoring Piutang' ?>
    </title>

    <link rel="icon"
          href="<?= base_url('favicon.ico') ?>">

    <!-- Vendor CSS -->
    <link rel="stylesheet"
          href="<?= base_url('assets/plugins/fontawesome-free/css/all.min.css') ?>">

    <link rel="stylesheet"
          href="<?= base_url('assets/dist/css/adminlte.min.css') ?>">

    <!-- Plugin CSS -->
    <link rel="stylesheet"
          href="<?= base_url('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') ?>">

    <!-- Application CSS -->
    <link rel="stylesheet"
          href="<?= base_url('assets/css/layout/navbar.css') ?>">

    <!-- Page CSS -->
    <?= $this->renderSection('pageStyles') ?>

</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">

<div class="wrapper">

    <!-- Navbar -->
    <?= $this->include('partials/navbar') ?>

    <!-- Sidebar -->
    <?= $this->include('partials/sidebar') ?>

    <!-- Content -->
    <div class="content-wrapper">

        <section class="content">

            <div class="container-fluid py-3">

                <?= $this->include('partials/alert') ?>

                <?= $this->renderSection('content') ?>

            </div>

        </section>

    </div>

    <!-- Footer -->
    <?= $this->include('partials/footer') ?>

</div>

<!-- ================================================================
     Vendor JavaScript
================================================================= -->

<!-- jQuery -->
<script src="<?= base_url(
    'assets/plugins/jquery/jquery.min.js'
) ?>"></script>

<!-- Bootstrap -->
<script src="<?= base_url(
    'assets/plugins/bootstrap/js/bootstrap.bundle.min.js'
) ?>"></script>

<!-- AdminLTE -->
<script src="<?= base_url(
    'assets/dist/js/adminlte.min.js'
) ?>"></script>


<!-- ================================================================
     Plugin JavaScript
================================================================= -->

<!-- DataTables -->
<script src="<?= base_url(
    'assets/plugins/datatables/jquery.dataTables.min.js'
) ?>"></script>

<script src="<?= base_url(
    'assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js'
) ?>"></script>


<!-- SweetAlert2 -->
<script src="<?= base_url(
    'assets/plugins/sweetalert2/sweetalert2.all.min.js'
) ?>"></script>

<!-- Page Specific JavaScript -->
<?= $this->renderSection('scripts') ?>

</body>
</html>