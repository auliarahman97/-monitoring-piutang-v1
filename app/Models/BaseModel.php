<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * --------------------------------------------------------------------------
 * Base Model
 * --------------------------------------------------------------------------
 *
 * Base model untuk seluruh model pada Sistem Monitoring Piutang.
 *
 * Menyediakan konfigurasi umum:
 * - Timestamp otomatis
 * - Soft delete
 * - Format tanggal
 * - Akses validation rules dan messages
 */
abstract class BaseModel extends Model
{
    /**
     * ----------------------------------------------------------------------
     * Timestamp
     * ----------------------------------------------------------------------
     */

    protected $useTimestamps = true;

    /**
     * ----------------------------------------------------------------------
     * Soft Delete
     * ----------------------------------------------------------------------
     */

    protected $useSoftDeletes = true;

    /**
     * ----------------------------------------------------------------------
     * Date Format
     * ----------------------------------------------------------------------
     */

    protected $dateFormat = 'datetime';

    /**
     * ----------------------------------------------------------------------
     * Validation
     * ----------------------------------------------------------------------
     */

    /**
     * Mengambil validation rules model.
     *
     * @return array<string, mixed>|string
     */
    public function rules(): array|string
    {
        return $this->validationRules;
    }

    /**
     * Mengambil validation messages model.
     *
     * @return array<string, mixed>
     */
    public function messages(): array
    {
        return $this->validationMessages;
    }
}