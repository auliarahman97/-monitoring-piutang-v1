<?php

declare(strict_types=1);

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Application extends BaseConfig
{
    /**
     * --------------------------------------------------------------------------
     * Application Identity
     * --------------------------------------------------------------------------
     */
    public object $app;

    /**
     * --------------------------------------------------------------------------
     * Organization Information
     * --------------------------------------------------------------------------
     */
    public object $organization;

    /**
     * --------------------------------------------------------------------------
     * Theme Configuration
     * --------------------------------------------------------------------------
     */
    public object $theme;

    /**
     * --------------------------------------------------------------------------
     * Feature Flags
     * --------------------------------------------------------------------------
     */
    public object $feature;

    public function __construct()
    {
        parent::__construct();

        /*
        |--------------------------------------------------------------------------
        | Application
        |--------------------------------------------------------------------------
        */

        $this->app = (object) [
            'name'      => 'Sistem Monitoring Piutang',
            'shortName' => 'Monitoring Piutang',
            'version'    => '1.0.0',
            'logo'       => 'assets/img/logo.png',
            'favicon'    => 'favicon.ico',
            'locale'     => 'id',
            'timezone'   => 'Asia/Jakarta',
            'currency'   => 'IDR',
            'dateFormat' => 'd F Y',
        ];

        /*
        |--------------------------------------------------------------------------
        | Organization
        |--------------------------------------------------------------------------
        */

        $this->organization = (object) [
            'name'    => 'Monitoring Piutang',
            'address' => '',
            'phone'   => '',
            'email'   => '',
            'website' => '',
        ];

        /*
        |--------------------------------------------------------------------------
        | Theme
        |--------------------------------------------------------------------------
        */

        $this->theme = (object) [
            'primaryColor' => '#007bff',
            'sidebar'      => 'sidebar-dark-primary',
            'navbar'       => 'navbar-white navbar-light',
        ];

        /*
        |--------------------------------------------------------------------------
        | Features
        |--------------------------------------------------------------------------
        */

        $this->feature = (object) [
            'dashboardChart' => true,
            'notification'   => true,
            'exportExcel'    => true,
            'exportPdf'      => true,
            'auditLog'       => false,
        ];
    }
}