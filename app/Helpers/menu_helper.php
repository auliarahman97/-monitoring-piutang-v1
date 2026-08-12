<?php

if (! function_exists('menuActive')) {

    function menuActive($routes): string
    {
        $current = uri_string();

        if (is_array($routes)) {

            foreach ($routes as $route) {

                if (str_starts_with($current, $route)) {
                    return 'active';
                }

            }

            return '';
        }

        return str_starts_with($current, $routes)
            ? 'active'
            : '';
    }

}

if (! function_exists('menuOpen')) {

    function menuOpen(array $routes): string
    {
        $current = uri_string();

        foreach ($routes as $route) {

            if (str_starts_with($current, $route)) {
                return 'menu-open';
            }

        }

        return '';
    }

}