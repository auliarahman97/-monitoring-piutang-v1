# Changelog

Semua perubahan penting pada project ini akan didokumentasikan pada file ini.

Format changelog ini mengacu pada prinsip **Keep a Changelog** dan menggunakan **Semantic Versioning (SemVer)**.

---

## [1.0.0] - 2026-07-29

### Added

#### Authentication & Authorization
- Implementasi CodeIgniter Shield.
- Login dan Logout.
- Role Management (Admin, Petugas, Pimpinan).

#### Dashboard
- Dashboard utama aplikasi.
- Ringkasan data koperasi.
- Statistik anggota, simpanan, pinjaman, dan angsuran.

#### Master Data
- Manajemen Anggota.
- Manajemen Jenis Simpanan.

#### Transaction
- Transaksi Simpanan.
- Transaksi Pinjaman.
- Transaksi Angsuran.

#### Reporting
- Laporan Simpanan.
- Laporan Pinjaman.
- Laporan Angsuran.
- Filter laporan.
- Export data.

#### User Management
- Daftar User.
- Tambah User.
- Edit User.
- Reset Password User.
- Manajemen Role User.

---

### Improved

- Struktur Controller lebih konsisten.
- Refactoring UserController.
- Penambahan helper `findUserOrFail()`.
- Konsistensi Flash Message.
- Konsistensi Validation.
- Penambahan PHPDoc.
- Peningkatan Readability.
- Peningkatan Maintainability.

---

### Security

- Password hashing menggunakan CodeIgniter Shield.
- Validasi server-side pada seluruh form.
- Integrasi User Entity dan Identity bawaan Shield.

---

### UI

- AdminLTE 3.
- DataTables.
- SweetAlert2.
- Responsive Layout.

---

## Status

Release pertama aplikasi **Koperasi Simpan Pinjam**.

Status:

**Stable**