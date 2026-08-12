<?php

$user = currentUser();
$role = roleName();

/*
 * Avatar Initial
 */
$initial =
    strtoupper(
        substr(
            $user->username,
            0,
            2
        )
    );


/*
 * Tanggal hari ini
 */
$tanggalHariIni =
    date('d-m-Y');

?>

<style>
    /*
    |--------------------------------------------------------------------------
    | NAVBAR
    |--------------------------------------------------------------------------
    */

    .main-header {
        border-bottom: 1px solid #e9ecef;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .04);
    }


    /*
    |--------------------------------------------------------------------------
    | APP BRAND
    |--------------------------------------------------------------------------
    */

    .navbar-app-brand {
        display: flex;
        align-items: center;

        margin-left: .35rem;

        font-size: .9rem;
        font-weight: 600;

        color: #495057;

        white-space: nowrap;
    }

    .navbar-app-brand i {
        font-size: .85rem;
        margin-right: .45rem;

        opacity: .75;
    }


    /*
    |--------------------------------------------------------------------------
    | DATE
    |--------------------------------------------------------------------------
    */

    .navbar-date {
        display: flex;
        align-items: center;

        margin-right: 1rem;

        font-size: .82rem;

        color: #6c757d;

        white-space: nowrap;
    }

    .navbar-date i {
        margin-right: .4rem;
        color: #6c757d;
    }


    /*
    |--------------------------------------------------------------------------
    | USER MENU
    |--------------------------------------------------------------------------
    */

    .user-menu-toggle {
        padding-top: .35rem !important;
        padding-bottom: .35rem !important;
    }


    /*
    |--------------------------------------------------------------------------
    | MOBILE
    |--------------------------------------------------------------------------
    */

    @media (max-width: 767.98px) {

        .navbar-app-brand {
            font-size: .82rem;
        }

        .navbar-date {
            display: none;
        }

    }
</style>


<nav class="main-header navbar navbar-expand navbar-white navbar-light">


    <!-- ================================================================ -->
    <!-- LEFT -->
    <!-- ================================================================ -->

    <ul class="navbar-nav">

        <!-- Sidebar Toggle -->

        <li class="nav-item">

            <a
                href="#"
                class="nav-link"
                data-widget="pushmenu"
                role="button"
                title="Buka/Tutup Sidebar"
            >

                <i class="fas fa-bars"></i>

            </a>

        </li>


        <!-- Application Name -->

        <li class="nav-item d-flex align-items-center">

            <div class="navbar-app-brand">

                <i class="fas fa-chart-line"></i>

                Monitoring Piutang

            </div>

        </li>

    </ul>


    <!-- ================================================================ -->
    <!-- RIGHT -->
    <!-- ================================================================ -->

    <ul class="navbar-nav ml-auto">


        <!-- Date -->

        <li class="nav-item d-flex align-items-center">

            <div class="navbar-date">

                <i class="far fa-calendar-alt"></i>

                <?= esc($tanggalHariIni) ?>

            </div>

        </li>


        <!-- ============================================================ -->
        <!-- USER MENU -->
        <!-- ============================================================ -->

        <li class="nav-item dropdown">

            <a
                href="#"
                class="nav-link dropdown-toggle user-menu-toggle"
                data-toggle="dropdown"
                role="button"
                aria-haspopup="true"
                aria-expanded="false"
            >

                <div class="d-flex align-items-center">


                    <!-- Avatar -->

                    <div class="user-avatar mr-2">

                        <?= esc($initial) ?>

                    </div>


                    <!-- User Info -->

                    <div class="user-info d-none d-md-block">

                        <div class="username">

                            <?= esc(
                                $user->username
                            ) ?>

                        </div>


                        <div class="role">

                            <?= esc(
                                $role
                            ) ?>

                        </div>

                    </div>

                </div>

            </a>


            <!-- ======================================================== -->
            <!-- USER DROPDOWN -->
            <!-- ======================================================== -->

            <div
                class="dropdown-menu dropdown-menu-right user-dropdown"
            >


                <!-- User Header -->

                <div class="user-dropdown-header">

                    <div class="user-avatar">

                        <?= esc($initial) ?>

                    </div>


                    <h6 class="mb-1">

                        <?= esc(
                            $user->username
                        ) ?>

                    </h6>


                    <small>

                        <?= esc(
                            $role
                        ) ?>

                    </small>

                </div>


                <!-- Email -->

                <div
                    class="dropdown-item text-truncate"
                    title="<?= esc($user->email) ?>"
                >

                    <i class="fas fa-envelope text-primary mr-2"></i>

                    <?= esc(
                        $user->email
                    ) ?>

                </div>


                <div class="dropdown-divider"></div>


                <!-- Logout -->

                <div class="dropdown-footer">

                    <a
                        href="<?= url_to('logout') ?>"
                        class="btn btn-outline-danger btn-block"
                    >

                        <i class="fas fa-sign-out-alt mr-1"></i>

                        Logout

                    </a>

                </div>


            </div>

        </li>

    </ul>

</nav>