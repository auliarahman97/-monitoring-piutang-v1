<aside class="main-sidebar sidebar-dark-primary elevation-4">

    <!-- Brand -->
    <a href="<?= base_url('dashboard') ?>" class="brand-link text-center">

        <img
            src="<?= base_url('assets/img/logo.jpg') ?>"
            alt="Logo"
            class="brand-image img-circle elevation-2"
        >

        <span class="brand-text font-weight-light">
            Monitoring Piutang
        </span>

    </a>

    <div class="sidebar">

        <nav class="mt-2">

            <ul
                class="nav nav-pills nav-sidebar flex-column"
                data-widget="treeview"
                role="menu"
                data-accordion="false"
            >

                <!-- ===================================================== -->
                <!-- Dashboard -->
                <!-- ===================================================== -->

                <li class="nav-item">

                    <a
                        href="<?= base_url('dashboard') ?>"
                        class="nav-link <?= menuActive('dashboard') ?>"
                    >

                        <i class="nav-icon fas fa-home"></i>

                        <p>
                            Dashboard
                        </p>

                    </a>

                </li>


                <!-- ===================================================== -->
                <!-- Master Data -->
                <!-- Admin & Petugas -->
                <!-- ===================================================== -->

                <?php if (canAccess(['admin', 'petugas'])) : ?>

                    <li class="nav-header">
                        MASTER DATA
                    </li>

                    <li class="nav-item">

                        <a
                            href="<?= base_url('customer') ?>"
                            class="nav-link <?= menuActive('customer') ?>"
                        >

                            <i class="nav-icon fas fa-users"></i>

                            <p>
                                Customer
                            </p>

                        </a>

                    </li>

                <?php endif; ?>


                <!-- ===================================================== -->
                <!-- Transaksi -->
                <!-- Admin & Petugas -->
                <!-- ===================================================== -->

                <?php if (canAccess(['admin', 'petugas'])) : ?>

                    <li class="nav-header">
                        TRANSAKSI
                    </li>


                    <!-- Piutang -->

                    <li class="nav-item">

                        <a
                            href="<?= base_url('piutang') ?>"
                            class="nav-link <?= menuActive('piutang') ?>"
                        >

                            <i class="nav-icon fas fa-file-invoice-dollar"></i>

                            <p>
                                Piutang
                            </p>

                        </a>

                    </li>


                    <!-- Pembayaran -->

                    <li class="nav-item">

                        <a
                            href="<?= base_url('pembayaran') ?>"
                            class="nav-link <?= menuActive('pembayaran') ?>"
                        >

                            <i class="nav-icon fas fa-money-bill-wave"></i>

                            <p>
                                Pembayaran
                            </p>

                        </a>

                    </li>

                <?php endif; ?>


                <!-- ===================================================== -->
                <!-- Laporan -->
                <!-- Admin, Petugas & Pimpinan -->
                <!-- ===================================================== -->

                <?php if (canAccess(['admin', 'petugas', 'pimpinan'])) : ?>

                    <li class="nav-header">
                        LAPORAN
                    </li>


                    <!-- =================================================
                        Laporan Piutang
                        ================================================= -->

                    <li class="nav-item">

                        <a
                            href="<?= base_url('laporan/piutang') ?>"
                            class="nav-link <?= menuActive('laporan/piutang') ?>"
                        >

                            <i class="nav-icon fas fa-file-invoice-dollar"></i>

                            <p>
                                Laporan Piutang
                            </p>

                        </a>

                    </li>


                    <!-- =================================================
                        Laporan Pembayaran
                        ================================================= -->

                    <li class="nav-item">

                        <a
                            href="<?= base_url('laporan/pembayaran') ?>"
                            class="nav-link <?= menuActive('laporan/pembayaran') ?>"
                        >

                            <i class="nav-icon fas fa-money-check-alt"></i>

                            <p>
                                Laporan Pembayaran
                            </p>

                        </a>

                    </li>


                    <!-- =================================================
                        Laporan Customer
                        ================================================= -->

                    <li class="nav-item">

                        <a
                            href="<?= base_url('laporan/customer') ?>"
                            class="nav-link <?= menuActive('laporan/customer') ?>"
                        >

                            <i class="nav-icon fas fa-users"></i>

                            <p>
                                Laporan Customer
                            </p>

                        </a>

                    </li>


                <?php endif; ?>


                <!-- ===================================================== -->
                <!-- Pengaturan -->
                <!-- Administrator -->
                <!-- ===================================================== -->

                <?php if (isAdmin()) : ?>

                    <li class="nav-header">
                        PENGATURAN
                    </li>


                    <li class="nav-item">

                        <a
                            href="<?= base_url('pengaturan/aturan-denda') ?>"
                            class="nav-link <?= menuActive('pengaturan/aturan-denda') ?>"
                        >

                            <i class="nav-icon fas fa-percentage"></i>

                            <p>
                                Aturan Denda
                            </p>

                        </a>

                    </li>


                    <li class="nav-item">

                        <a
                            href="<?= base_url('pengaturan/user') ?>"
                            class="nav-link <?= menuActive('pengaturan/user') ?>"
                        >

                            <i class="nav-icon fas fa-user-cog"></i>

                            <p>
                                User
                            </p>

                        </a>

                    </li>

                <?php endif; ?>

            </ul>

        </nav>

    </div>

</aside>