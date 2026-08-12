<?php

declare(strict_types=1);

if (! function_exists('field_old')) {
    /**
     * Mengembalikan nilai input lama (old input).
     *
     * @param mixed $default Nilai default jika tidak ada old input.
     */
    function field_old(string $field, mixed $default = ''): mixed
    {
        return old($field, $default);
    }
}

if (! function_exists('field_error')) {
    /**
     * Mengembalikan pesan error validasi suatu field.
     *
     * Mendukung:
     * - Validation setelah redirect()->back()->withInput()
     * - Validation pada request yang sama
     */
    function field_error(string $field): string
    {
        $errors = session('_ci_validation_errors');

        if (is_array($errors) && array_key_exists($field, $errors)) {
            return (string) $errors[$field];
        }

        return service('validation')->getError($field);
    }
}

if (! function_exists('field_has_error')) {
    /**
     * Mengecek apakah field memiliki error validasi.
     */
    function field_has_error(string $field): bool
    {
        return field_error($field) !== '';
    }
}

if (! function_exists('field_invalid')) {
    /**
     * Mengembalikan class Bootstrap "is-invalid"
     * jika field memiliki error validasi.
     */
    function field_invalid(string $field): string
    {
        return field_has_error($field)
            ? 'is-invalid'
            : '';
    }
}

if (! function_exists('field_required')) {
    /**
     * Menampilkan tanda (*) untuk field wajib.
     */
    function field_required(bool $required = false): string
    {
        return $required
            ? '<span class="text-danger">*</span>'
            : '';
    }
}