<?php

if (! function_exists('nomorUrut')) {

    function nomorUrut(int &$no): int
    {
        return $no++;
    }

}