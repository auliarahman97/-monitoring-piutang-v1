<?php

if (! function_exists('rupiah')) {

    function rupiah($angka)
    {
        return 'Rp ' . number_format($angka, 0, ',', '.');
    }

}

if (! function_exists('tanggalIndonesia')) {

    function tanggalIndonesia($tanggal): string
    {
        return date('d-m-Y', strtotime($tanggal));
    }

}

if (! function_exists('tanggalWaktuIndonesia')) {

    /**
     * Mengembalikan tanggal dan waktu Indonesia.
     *
     * Contoh:
     * 15 Juli 2026 14:35:20 WIB
     *
     * @param string|null $datetime
     *
     * @return string
     */
    function tanggalWaktuIndonesia(
        ?string $datetime = null
    ): string {

        $datetime ??= date('Y-m-d H:i:s');

        $timestamp = strtotime($datetime);

        return tanggalIndonesia(
            date('Y-m-d', $timestamp)
        )
            . ' '
            . date('H:i:s', $timestamp)
            . ' WIB';

    }

}

if (! function_exists('aktivitasTerakhir')) {

    /**
     * Mengembalikan informasi aktivitas terakhir.
     *
     * Contoh:
     * [
     *     'tanggal' => 'Hari ini',
     *     'jam' => '19:06 WIB',
     * ]
     *
     * @param \CodeIgniter\I18n\Time|null $waktu
     *
     * @return array{tanggal:string,jam:?string}
     */
    function aktivitasTerakhir(
        ?\CodeIgniter\I18n\Time $waktu = null
    ): array {

        if ($waktu === null) {
            return [
                'tanggal' => 'Belum ada aktivitas',
                'jam'      => null,
                'tooltip'  => null,
            ];
        }

        $hariIni = new \CodeIgniter\I18n\Time('now', 'Asia/Jakarta');
        $kemarin = $hariIni->subDays(1);

        if ($waktu->toDateString() === $hariIni->toDateString()) {
            $tanggal = 'Hari ini';
        } elseif ($waktu->toDateString() === $kemarin->toDateString()) {
            $tanggal = 'Kemarin';
        } else {
            $tanggal = $waktu->format('d M Y');
        }

        return [
            'tanggal' => $tanggal,
            'jam'      => $waktu->format('H:i') . ' WIB',
            'tooltip'  => $waktu->format('d M Y H:i:s') . ' WIB',
        ];
    }
}