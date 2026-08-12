# Release Guide

## Sistem Monitoring Piutang

Dokumen ini menjadi panduan untuk menyiapkan, memeriksa, dan melakukan release Sistem Monitoring Piutang.

---

# 1. Current Release

```text
Application : Sistem Monitoring Piutang
Version     : 1.0.0
Status      : Baseline
Framework   : CodeIgniter 4
Authentication : CodeIgniter Shield
Database    : MySQL / MariaDB
```

Versi `1.0.0` merupakan baseline pertama setelah project diarahkan sepenuhnya menjadi Sistem Monitoring Piutang.

---

# 2. Release Scope

Release `1.0.0` mencakup core functionality:

```text
Customer
Piutang
Pembayaran
Aturan Denda
Monitoring
Dashboard
Laporan
User Management
Authentication
Authorization
```

---

# 3. Core Business Flow

Alur utama aplikasi:

```text
Customer
    │
    ▼
Piutang
    │
    ├── Aturan Denda
    │
    ▼
Monitoring
    │
    ▼
Pembayaran
    │
    ▼
Outstanding
    │
    ├── Dashboard
    └── Laporan
```

---

# 4. Release Architecture

Release menggunakan struktur aplikasi:

```text
app/
├── Config/
├── Controllers/
├── Database/
│   ├── Migrations/
│   └── Seeds/
├── Helpers/
├── Models/
├── Services/
└── Views/
```

Business logic utama berada pada Service Layer:

```text
DashboardService
PaymentService
PiutangMonitoringService
```

---

# 5. Database

Database bisnis utama:

```text
customer
aturan_denda_versi
aturan_denda
piutang
pembayaran
```

Authentication dan authorization menggunakan tabel CodeIgniter Shield.

Migration legacy dari aplikasi sebelumnya tidak termasuk dalam release ini.

---

# 6. Legacy Cleanup

Sebagai bagian dari release baseline, komponen legacy yang tidak lagi digunakan telah dikeluarkan dari project.

Contoh komponen yang telah dibersihkan:

```text
Anggota
Simpanan
Pinjaman
Angsuran
Jenis Simpanan
```

Cleanup dilakukan terhadap:

* Migration
* Model
* Controller
* Helper
* JavaScript asset
* Konfigurasi
* Dokumentasi

Project tidak lagi menggunakan architecture aplikasi Koperasi sebagai bagian dari business logic aktif.

---

# 7. Pre-Release Checklist

Sebelum release, pastikan:

## Application

* [ ] Application name sudah menggunakan Sistem Monitoring Piutang
* [ ] Tidak ada konfigurasi identitas Koperasi pada source aktif
* [ ] `.env` production tidak ikut masuk repository
* [ ] Debug mode sesuai environment
* [ ] Base URL sesuai environment

## Database

* [ ] Backup database tersedia
* [ ] Migration sudah diverifikasi
* [ ] Tidak ada migration legacy yang diperlukan
* [ ] Foreign key sudah diverifikasi
* [ ] Database production sesuai dengan migration
* [ ] Data transaksi sudah diverifikasi

## Authentication

* [ ] Login berhasil
* [ ] Logout berhasil
* [ ] Role/group sesuai
* [ ] Authorization route bekerja
* [ ] User tanpa permission tidak dapat mengakses endpoint terlarang

## Customer

* [ ] Create customer
* [ ] Edit customer
* [ ] View customer
* [ ] Search/filter customer
* [ ] Soft delete bekerja

## Piutang

* [ ] Create piutang
* [ ] Edit piutang
* [ ] Nomor piutang unik
* [ ] Jatuh tempo tersimpan dengan benar
* [ ] Status piutang sesuai
* [ ] Aturan denda tersimpan dengan benar
* [ ] Snapshot aturan denda tersimpan

## Pembayaran

* [ ] Create pembayaran
* [ ] Nomor pembayaran unik
* [ ] Validasi nominal berjalan
* [ ] Alokasi denda berjalan
* [ ] Alokasi bunga berjalan
* [ ] Alokasi pokok berjalan
* [ ] Outstanding dihitung dengan benar
* [ ] Status piutang diperbarui dengan benar
* [ ] Pembatalan pembayaran berjalan
* [ ] Histori pembayaran tetap tersedia setelah pembatalan

## Aturan Denda

* [ ] Create aturan
* [ ] Edit aturan
* [ ] Versioning berjalan
* [ ] Aturan aktif dapat digunakan
* [ ] Piutang menggunakan snapshot aturan
* [ ] Perubahan aturan tidak mengubah histori piutang

## Dashboard

* [ ] Dashboard dapat dibuka
* [ ] Statistik dapat dihitung
* [ ] Nilai dashboard sesuai database
* [ ] Tidak terdapat error JavaScript

## Laporan

* [ ] Laporan piutang dapat dibuka
* [ ] Laporan pembayaran dapat dibuka
* [ ] Filter bekerja
* [ ] Summary sesuai data
* [ ] Print view bekerja

---

# 8. Testing

Sebelum release, jalankan:

```bash
php spark test
```

Jika tersedia automated test tambahan, seluruh test harus berhasil.

Minimal lakukan manual testing terhadap:

```text
Customer
    ↓
Piutang
    ↓
Pembayaran
    ↓
Outstanding
    ↓
Status
    ↓
Laporan
```

---

# 9. Database Migration

Pada environment baru:

```bash
php spark migrate
```

Jika diperlukan:

```bash
php spark migrate --all
```

Pastikan migration berhasil tanpa error.

Jangan menjalankan migration baru pada production tanpa backup dan review terlebih dahulu.

---

# 10. Environment Configuration

Production menggunakan `.env` sendiri.

Konfigurasi sensitif tidak boleh disimpan di repository.

Contoh konfigurasi:

```ini
CI_ENVIRONMENT = production

database.default.hostname = localhost
database.default.database = monitoring_piutang
database.default.username = <production-user>
database.default.password = <production-password>
database.default.DBDriver = MySQLi
```

Nilai aktual harus disesuaikan dengan environment deployment.

---

# 11. Security Checklist

Sebelum production release:

* [ ] `CI_ENVIRONMENT` production
* [ ] Debugging tidak aktif
* [ ] Credential tidak berada di source code
* [ ] `.env` tidak masuk Git
* [ ] Authorization route aktif
* [ ] Input validation aktif
* [ ] CSRF protection sesuai kebutuhan
* [ ] Database user menggunakan credential yang sesuai
* [ ] Database user production tidak menggunakan privilege berlebihan
* [ ] Error detail tidak ditampilkan kepada pengguna

---

# 12. Git Release

Sebelum release:

```bash
git status
```

Pastikan hanya perubahan yang memang dimaksudkan yang akan di-commit.

Kemudian:

```bash
git add .
git commit -m "release: monitoring piutang 1.0.0"
```

Tag release:

```bash
git tag -a v1.0.0 -m "Sistem Monitoring Piutang 1.0.0"
```

Kemudian:

```bash
git push origin main
git push origin v1.0.0
```

Sesuaikan nama branch dengan repository yang digunakan.

---

# 13. Release Notes

Setiap release harus memiliki ringkasan yang menjelaskan:

```text
Version
Release Date
Added
Changed
Fixed
Security
Migration
Breaking Changes
```

Detail perubahan historis disimpan pada:

```text
docs/changelog.md
```

---

# 14. Versioning

Gunakan Semantic Versioning:

```text
MAJOR.MINOR.PATCH
```

Contoh:

```text
1.0.0
1.1.0
1.1.1
2.0.0
```

### MAJOR

Perubahan besar yang dapat menyebabkan breaking changes.

### MINOR

Penambahan fitur yang tetap kompatibel.

### PATCH

Bug fix atau perubahan kecil yang tidak mengubah API/business contract secara signifikan.

---

# 15. Rollback

Setiap release production harus memiliki strategi rollback.

Minimal:

```text
Application Backup
Database Backup
Previous Release
Migration Plan
```

Untuk perubahan database yang destructive, rollback harus dipertimbangkan sebelum migration dijalankan.

Data transaksi keuangan tidak boleh hilang hanya karena proses rollback aplikasi.

---

# 16. Post-Release Verification

Setelah deployment:

1. Buka halaman login.
2. Login menggunakan user yang sesuai.
3. Buka dashboard.
4. Buka customer.
5. Buka piutang.
6. Buka pembayaran.
7. Buka aturan denda.
8. Buka laporan.
9. Periksa log aplikasi.
10. Lakukan satu transaksi pengujian jika environment memungkinkan.
11. Pastikan database tidak mengalami error.

---

# 17. Release Status

Baseline saat ini:

```text
Version : 1.0.0
Name    : Sistem Monitoring Piutang
```

Core module:

```text
✓ Customer
✓ Piutang
✓ Pembayaran
✓ Aturan Denda
✓ Monitoring
✓ Dashboard
✓ Laporan
✓ User Management
✓ Authentication
✓ Authorization
```

Area yang masih perlu diperkuat:

```text
○ Automated Testing
○ Database Integrity Audit
○ Business Rule Verification
○ Reporting Verification
○ Production Hardening
```

---

# 18. Next Development Priorities

Setelah baseline `1.0.0` stabil, prioritas pengembangan sebaiknya:

```text
1. Business Logic Testing
2. Database Integrity Audit
3. Piutang Monitoring Enhancement
4. Dashboard Enhancement
5. Reporting Enhancement
6. Notification / Reminder
7. Performance Optimization
8. Production Hardening
```

Urutan dapat berubah berdasarkan kebutuhan bisnis.

---

# 19. Release Principle

Release harus memenuhi prinsip:

```text
Stable
    ↓
Tested
    ↓
Documented
    ↓
Backed Up
    ↓
Released
    ↓
Verified
```

Untuk sistem yang menangani transaksi keuangan, **akurasi data dan integritas transaksi lebih penting daripada kecepatan release**.
