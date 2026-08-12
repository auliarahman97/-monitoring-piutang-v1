# Changelog

Semua perubahan penting pada Sistem Monitoring Piutang didokumentasikan di file ini.

Format changelog mengikuti prinsip [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

---

## [Unreleased]

### Planned

Perbaikan dan pengembangan yang sedang dipersiapkan:

* Penguatan automated testing untuk business logic.
* Audit integritas foreign key database.
* Review dan penyempurnaan business rule pembayaran.
* Penyempurnaan monitoring piutang.
* Penyempurnaan laporan dan dashboard.
* Pembersihan legacy code dan dokumentasi secara bertahap.

---

# [1.0.0] — Sistem Monitoring Piutang

**Status:** Baseline

Versi ini menetapkan **Sistem Monitoring Piutang** sebagai identitas dan arah utama project.

Project telah dibersihkan dari komponen legacy aplikasi sebelumnya yang tidak lagi digunakan oleh sistem.

### Added

* Master Customer.
* Modul Piutang.
* Modul Pembayaran.
* Modul Aturan Denda.
* Versioning aturan denda.
* Snapshot parameter aturan denda pada piutang.
* Monitoring piutang.
* Dashboard.
* Laporan.
* User Management.
* Authentication menggunakan CodeIgniter Shield.
* Authorization berbasis group/role.
* Audit trail pada data yang relevan.
* Soft delete pada data yang mendukung histori.
* Mekanisme pembatalan pembayaran.
* Service layer untuk business logic utama.

### Architecture

* Pemisahan Controller, Model, dan Service.
* `PaymentService` untuk business logic pembayaran.
* `PiutangMonitoringService` untuk kebutuhan monitoring piutang.
* `DashboardService` untuk kebutuhan dashboard.
* Reusable view components.
* Migration sebagai sumber utama perubahan schema database.

### Database

Struktur database utama:

```text
customer
aturan_denda
aturan_denda_versi
piutang
pembayaran
```

Migration legacy dari aplikasi sebelumnya telah dikeluarkan dari project.

### Cleanup

Komponen legacy yang tidak lagi digunakan telah dihapus secara bertahap, termasuk:

* Model legacy.
* Controller legacy.
* Helper legacy.
* Asset JavaScript legacy.
* Konfigurasi legacy yang tidak digunakan.
* Migration legacy.

---

# Legacy Project History

Sebelum menjadi Sistem Monitoring Piutang, project ini memiliki sejarah pengembangan sebagai aplikasi Koperasi Simpan Pinjam.

Bagian ini dipertahankan hanya untuk kebutuhan **traceability** dan tidak lagi menjadi bagian dari architecture atau business scope aplikasi saat ini.

Komponen legacy yang sebelumnya pernah digunakan mencakup konsep:

```text
Anggota
Simpanan
Pinjaman
Angsuran
Jenis Simpanan
```

Komponen tersebut tidak lagi menjadi bagian dari sistem aktif.

Migration legacy terkait modul tersebut telah dikeluarkan dari project.

---

# Development History

## Legacy Phase

Project pada awalnya dikembangkan dengan fokus pada kebutuhan aplikasi Koperasi Simpan Pinjam.

Pada fase tersebut terdapat beberapa modul seperti:

```text
Anggota
Simpanan
Pinjaman
Angsuran
```

Seiring perubahan kebutuhan, project diarahkan menjadi sistem yang berfokus pada monitoring piutang.

---

## Migration Phase

Project kemudian mengalami perubahan struktur:

```text
Aplikasi Koperasi
        │
        ▼
Cleanup Legacy
        │
        ▼
Database Redesign
        │
        ▼
Sistem Monitoring Piutang
```

Pada fase ini dilakukan:

* Penghapusan migration legacy.
* Penghapusan model dan controller yang tidak lagi digunakan.
* Penghapusan helper legacy.
* Penghapusan asset legacy.
* Penyesuaian konfigurasi aplikasi.
* Penyesuaian authentication dan authorization.
* Penyusunan ulang dokumentasi.

---

## Monitoring Piutang Phase

Fokus pengembangan kemudian diarahkan pada:

```text
Customer
   ↓
Piutang
   ↓
Monitoring
   ↓
Pembayaran
   ↓
Denda
   ↓
Laporan
```

Business logic pembayaran dipusatkan pada Service Layer.

---

# Versioning Policy

Versi aplikasi menggunakan format:

```text
MAJOR.MINOR.PATCH
```

Contoh:

```text
1.0.0
1.1.0
1.1.1
```

### MAJOR

Digunakan apabila terdapat perubahan besar yang dapat memengaruhi architecture atau compatibility.

Contoh:

```text
2.0.0
```

### MINOR

Digunakan untuk penambahan fitur yang tetap kompatibel dengan sistem yang sudah ada.

Contoh:

```text
1.1.0
```

### PATCH

Digunakan untuk:

* Bug fix.
* Security fix.
* Perbaikan kecil.
* Perbaikan dokumentasi.
* Perbaikan UI yang tidak mengubah business rule.

Contoh:

```text
1.0.1
```

---

# Change Categories

Gunakan kategori berikut dalam setiap release:

### Added

Fitur baru.

### Changed

Perubahan terhadap fitur yang sudah ada.

### Fixed

Perbaikan bug.

### Security

Perbaikan keamanan.

### Removed

Penghapusan fitur atau komponen yang tidak lagi digunakan.

### Deprecated

Fitur yang masih tersedia tetapi direncanakan untuk dihentikan.

### Documentation

Perubahan dokumentasi.

### Refactored

Perubahan struktur kode tanpa mengubah behavior yang diharapkan.

---

# Release Example

Format release berikut dapat digunakan:

```markdown
## [1.1.0] - YYYY-MM-DD

### Added
- Fitur baru.

### Changed
- Perubahan fitur.

### Fixed
- Perbaikan bug.

### Security
- Perbaikan keamanan.

### Documentation
- Pembaruan dokumentasi.
```

---

# Important Rule

Perubahan yang memengaruhi transaksi keuangan atau business logic harus dicatat secara eksplisit.

Contoh:

```text
Changed
- Mengubah aturan alokasi pembayaran.

Changed
- Mengubah formula perhitungan denda.

Fixed
- Memperbaiki perhitungan outstanding.

Fixed
- Memperbaiki status piutang setelah pembayaran.
```

Perubahan tersebut harus disertai pengujian yang sesuai.

---

# Current Baseline

Baseline saat ini:

```text
Project
    Sistem Monitoring Piutang

Core
    Customer
    Piutang
    Pembayaran
    Aturan Denda
    Dashboard
    Monitoring
    Laporan
    User Management

Framework
    CodeIgniter 4

Authentication
    CodeIgniter Shield

Database
    MySQL / MariaDB
```

Project selanjutnya dikembangkan berdasarkan baseline ini dan tidak lagi menggunakan architecture aplikasi Koperasi sebagai acuan.

---

# Changelog Maintenance

Setiap perubahan penting harus dicatat sebelum release.

Urutan yang disarankan:

```text
Development
    ↓
Testing
    ↓
Update Changelog
    ↓
Version
    ↓
Release
```

Changelog tidak perlu mencatat setiap perubahan kecil pada kode. Fokus pada perubahan yang relevan bagi developer, administrator, dan pengguna sistem.