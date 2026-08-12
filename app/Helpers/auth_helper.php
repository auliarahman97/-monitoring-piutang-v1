<?php

declare(strict_types=1);

use CodeIgniter\Exceptions\PageForbiddenException;

if (! function_exists('currentUser')) {

    /**
     * Mengembalikan user yang sedang login.
     *
     * @return object|null
     */
    function currentUser()
    {
        return auth()->user();
    }
}

/*
|--------------------------------------------------------------------------
| Role Helper
|--------------------------------------------------------------------------
*/

if (! function_exists('roleList')) {

    /**
     * Daftar role yang digunakan pada aplikasi.
     *
     * Key   : Role internal (Shield Group)
     * Value : Nama role yang ditampilkan ke pengguna.
     *
     * @return array<string, string>
     */
    function roleList(): array
    {
        return [
            'admin'    => 'Administrator',
            'petugas'  => 'Petugas',
            'pimpinan' => 'Pimpinan',
        ];
    }
}

if (! function_exists('currentRole')) {

    /**
     * Mengembalikan role utama user yang sedang login.
     *
     * Catatan:
     * Aplikasi ini menggunakan konsep:
     *
     *      1 User = 1 Role
     *
     * Sehingga hanya role pertama yang digunakan.
     *
     * @return string|null
     */
    function currentRole(): ?string
    {
        $user = currentUser();

        if (! $user) {
            return null;
        }

        return $user->getGroups()[0] ?? null;
    }
}

if (! function_exists('roleName')) {

    /**
     * Mengembalikan nama role yang mudah dibaca.
     *
     * Contoh:
     *
     * roleName();
     * // Administrator
     *
     * roleName('petugas');
     * // Petugas
     *
     * @param string|null $role
     *
     * @return string
     */
    function roleName(?string $role = null): string
    {
        $role ??= currentRole();

        return roleList()[$role] ?? 'Unknown';
    }
}

if (! function_exists('hasRole')) {

    /**
     * Mengecek apakah user memiliki role tertentu.
     *
     * @param string $role
     *
     * @return bool
     */
    function hasRole(string $role): bool
    {
        return currentRole() === $role;
    }
}

/*
|--------------------------------------------------------------------------
| Authorization
|--------------------------------------------------------------------------
*/

if (! function_exists('canAccess')) {

    /**
     * Mengecek apakah role user termasuk dalam daftar role yang diizinkan.
     *
     * Contoh:
     *
     * canAccess(['admin']);
     *
     * canAccess(['admin', 'petugas']);
     *
     * @param array<int, string> $roles
     *
     * @return bool
     */
    function canAccess(array $roles): bool
    {
        return in_array(currentRole(), $roles, true);
    }
}

if (! function_exists('authorize')) {

    /**
     * Menghentikan proses apabila user tidak memiliki hak akses.
     *
     * Contoh:
     *
     * authorize(['admin']);
     *
     * authorize(['admin', 'petugas']);
     *
     * @param array<int, string> $roles
     *
     * @throws PageForbiddenException
     *
     * @return void
     */
    function authorize(array $roles): void
    {
        if (! canAccess($roles)) {
            throw PageForbiddenException::forPageForbidden(
                'Anda tidak memiliki hak akses untuk mengakses halaman ini.'
            );
        }
    }
}

/*
|--------------------------------------------------------------------------
| Shortcut Role
|--------------------------------------------------------------------------
*/

if (! function_exists('isAdmin')) {

    /**
     * Shortcut untuk role Administrator.
     *
     * @return bool
     */
    function isAdmin(): bool
    {
        return hasRole('admin');
    }
}

if (! function_exists('isPetugas')) {

    /**
     * Shortcut untuk role Petugas.
     *
     * @return bool
     */
    function isPetugas(): bool
    {
        return hasRole('petugas');
    }
}

if (! function_exists('isPimpinan')) {

    /**
     * Shortcut untuk role Pimpinan.
     *
     * @return bool
     */
    function isPimpinan(): bool
    {
        return hasRole('pimpinan');
    }
}