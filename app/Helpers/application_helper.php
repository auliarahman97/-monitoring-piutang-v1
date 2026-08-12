<?php

declare(strict_types=1);

use Config\Application;

if (! function_exists('application')) {

    function application(): Application
    {
        return config('Application');
    }
}

if (! function_exists('app')) {

    function app(): object
    {
        return application()->app;
    }
}

if (! function_exists('organization')) {

    function organization(): object
    {
        return application()->organization;
    }
}

if (! function_exists('theme')) {

    function theme(): object
    {
        return application()->theme;
    }
}

if (! function_exists('feature')) {

    function feature(): object
    {
        return application()->feature;
    }
}