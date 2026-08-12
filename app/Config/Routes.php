<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

/*
|--------------------------------------------------------------------------
| Route Filters
|--------------------------------------------------------------------------
|
| Permission dasar aplikasi:
|
| - auth
|   Semua user yang sudah login.
|
| - group:admin,petugas
|   Modul operasional.
|
| - group:admin
|   Modul khusus administrator.
|
*/

$auth = [
    'filter' => 'auth',
];

$operasional = [
    'filter' => 'group:admin,petugas',
];

$admin = [
    'filter' => 'group:admin',
];


/*
|--------------------------------------------------------------------------
| Router Configuration
|--------------------------------------------------------------------------
*/

$routes->setAutoRoute(false);


/*
|--------------------------------------------------------------------------
| Authentication (Shield)
|--------------------------------------------------------------------------
*/

service('auth')->routes($routes);


/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
|
| Semua pengguna yang telah login dapat mengakses Dashboard.
|
| GET /
| GET /dashboard
|
*/

$routes->get(
    '/',
    'Dashboard::index',
    $auth
);

$routes->get(
    'dashboard',
    'Dashboard::index',
    $auth
);


/*
|--------------------------------------------------------------------------
| Customer
|--------------------------------------------------------------------------
|
| Hak akses:
| - Admin
| - Petugas
|
| Pimpinan tidak dapat mengakses modul ini.
|
*/

$routes->group(
    'customer',
    $operasional,
    static function ($routes) {

        // GET /customer
        $routes->get(
            '/',
            'Customer::index'
        );

        // GET /customer/create
        $routes->get(
            'create',
            'Customer::create'
        );

        // POST /customer/store
        $routes->post(
            'store',
            'Customer::store'
        );

        // GET /customer/edit/{id}
        $routes->get(
            'edit/(:num)',
            'Customer::edit/$1'
        );

        // POST /customer/update/{id}
        $routes->post(
            'update/(:num)',
            'Customer::update/$1'
        );

        // POST /customer/delete/{id}
        $routes->post(
            'delete/(:num)',
            'Customer::delete/$1'
        );
    }
);


/*
|--------------------------------------------------------------------------
| Piutang
|--------------------------------------------------------------------------
|
| Hak akses:
| - Admin
| - Petugas
|
| Pimpinan tidak dapat mengakses modul transaksi piutang.
|
*/

$routes->group(
    'piutang',
    $operasional,
    static function ($routes) {

        // GET /piutang
        $routes->get(
            '/',
            'Piutang::index'
        );

        // GET /piutang/create
        $routes->get(
            'create',
            'Piutang::create'
        );

        // POST /piutang/store
        $routes->post(
            'store',
            'Piutang::store'
        );

        // GET /piutang/detail/{id}
        $routes->get(
            'detail/(:num)',
            'Piutang::detail/$1'
        );

        // GET /piutang/edit/{id}
        $routes->get(
            'edit/(:num)',
            'Piutang::edit/$1'
        );

        // POST /piutang/update/{id}
        $routes->post(
            'update/(:num)',
            'Piutang::update/$1'
        );

        // POST /piutang/delete/{id}
        $routes->post(
            'delete/(:num)',
            'Piutang::delete/$1'
        );

    }
);


/*
|--------------------------------------------------------------------------
| Pembayaran
|--------------------------------------------------------------------------
|
| Hak akses:
| - Admin
| - Petugas
|
| Business Process:
| - Pembayaran dapat dibuat berkali-kali untuk satu piutang.
| - Pembayaran yang sudah tersimpan tidak dapat diedit.
| - Pembayaran tidak dihapus.
| - Pembayaran dapat dibatalkan.
| - Preview digunakan sebelum transaksi disimpan.
|
*/

$routes->group(
    'pembayaran',
    $operasional,
    static function ($routes) {

        // ==============================================================
        // INDEX
        // ==============================================================

        // GET /pembayaran
        $routes->get(
            '/',
            'Pembayaran::index'
        );


        // ==============================================================
        // CREATE
        // ==============================================================

        // GET /pembayaran/create
        $routes->get(
            'create',
            'Pembayaran::create'
        );


        // ==============================================================
        // PIUTANG CUSTOMER
        // ==============================================================

        // GET /pembayaran/piutang/{customerId}
        //
        // AJAX untuk mengambil daftar piutang
        // berdasarkan customer yang dipilih.
        //
        $routes->get(
            'piutang/(:num)',
            'Pembayaran::piutangByCustomer/$1'
        );


        // ==============================================================
        // PREVIEW
        // ==============================================================

        // GET /pembayaran/preview/{piutangId}
        //
        // Menghitung kondisi tagihan berdasarkan:
        // - piutang
        // - tanggal pembayaran
        //
        $routes->get(
            'preview/(:num)',
            'Pembayaran::preview/$1'
        );


        // ==============================================================
        // STORE
        // ==============================================================

        // POST /pembayaran/store
        $routes->post(
            'store',
            'Pembayaran::store'
        );


        // ==============================================================
        // DETAIL
        // ==============================================================

        // GET /pembayaran/detail/{id}
        $routes->get(
            'detail/(:num)',
            'Pembayaran::detail/$1'
        );


        // ==============================================================
        // CANCEL
        // ==============================================================

        // POST /pembayaran/cancel/{id}
        //
        // Pembayaran tidak dihapus.
        // Record tetap tersimpan sebagai histori.
        //
        $routes->post(
            'cancel/(:num)',
            'Pembayaran::cancel/$1'
        );
    }
);


/*
|--------------------------------------------------------------------------
| Pengaturan
|--------------------------------------------------------------------------
|
| Hak akses:
| - Admin saja
|
*/

$routes->group(
    'pengaturan',
    $admin,
    static function ($routes) {

        /*
        |--------------------------------------------------------------------------
        | User
        |--------------------------------------------------------------------------
        */

        // GET /pengaturan/user
        $routes->get(
            'user',
            'User::index'
        );

        // GET /pengaturan/user/create
        $routes->get(
            'user/create',
            'User::create'
        );

        // POST /pengaturan/user
        $routes->post(
            'user',
            'User::store'
        );

        // GET /pengaturan/user/{id}/edit
        $routes->get(
            'user/(:num)/edit',
            'User::edit/$1'
        );

        // PUT /pengaturan/user/{id}
        $routes->put(
            'user/(:num)',
            'User::update/$1'
        );

        // GET /pengaturan/user/{id}/reset-password
        $routes->get(
            'user/(:num)/reset-password',
            'User::resetPasswordForm/$1'
        );

        // POST /pengaturan/user/{id}/reset-password
        $routes->post(
            'user/(:num)/reset-password',
            'User::resetPassword/$1'
        );


        /*
        |--------------------------------------------------------------------------
        | Aturan Denda
        |--------------------------------------------------------------------------
        */

        // GET /pengaturan/aturan-denda
        $routes->get(
            'aturan-denda',
            'AturanDenda::index'
        );

        // GET /pengaturan/aturan-denda/create
        $routes->get(
            'aturan-denda/create',
            'AturanDenda::create'
        );

        // POST /pengaturan/aturan-denda/store
        $routes->post(
            'aturan-denda/store',
            'AturanDenda::store'
        );

        // GET /pengaturan/aturan-denda/detail/{id}
        $routes->get(
            'aturan-denda/detail/(:num)',
            'AturanDenda::detail/$1'
        );

        // GET /pengaturan/aturan-denda/edit/{id}
        $routes->get(
            'aturan-denda/edit/(:num)',
            'AturanDenda::edit/$1'
        );

        // POST /pengaturan/aturan-denda/update/{id}
        $routes->post(
            'aturan-denda/update/(:num)',
            'AturanDenda::update/$1'
        );

        // POST /pengaturan/aturan-denda/delete/{id}
        $routes->post(
            'aturan-denda/delete/(:num)',
            'AturanDenda::delete/$1'
        );
    }
);


/*
|--------------------------------------------------------------------------
| Laporan
|--------------------------------------------------------------------------
|
| Hak akses:
| - Admin
| - Petugas
| - Pimpinan
|
| Semua laporan hanya membutuhkan autentikasi.
|
*/

$routes->group(
    'laporan',
    $auth,
    static function ($routes) {

        // ==============================================================
        // LAPORAN PIUTANG
        // ==============================================================

        // GET /laporan/piutang
        $routes->get(
            'piutang',
            'Laporan::piutang'
        );

        // GET /laporan/piutang/pdf
        $routes->get(
            'piutang/pdf',
            'Laporan::piutangPdf'
        );


        // ==============================================================
        // LAPORAN PEMBAYARAN
        // ==============================================================

        // GET /laporan/pembayaran
        $routes->get(
            'pembayaran',
            'Laporan::pembayaran'
        );

        // GET /laporan/pembayaran/pdf
        $routes->get(
            'pembayaran/pdf',
            'Laporan::pembayaranPdf'
        );


        // ==============================================================
        // LAPORAN CUSTOMER
        // ==============================================================

        // GET /laporan/customer
        $routes->get(
            'customer',
            'Laporan::customer'
        );

        // GET /laporan/customer/pdf
        $routes->get(
            'customer/pdf',
            'Laporan::customerPdf'
        );

        // GET /laporan/customer/print
        $routes->get(
            'customer/print',
            'Laporan::customerPrint'
        );
    }
);