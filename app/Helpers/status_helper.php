<?php

if (! function_exists('badgeStatus')) {

    function badgeStatus(string $status): string
    {
        return match ($status) {

            'Menunggu'  => 'warning',

            'Disetujui' => 'primary',

            'Ditolak'   => 'danger',

            'Lunas'     => 'success',

            default     => 'secondary',

        };
    }

}