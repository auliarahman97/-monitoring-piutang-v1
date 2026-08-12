# Sistem Monitoring Piutang

Sistem Monitoring Piutang adalah aplikasi web untuk membantu pengelolaan, pemantauan, dan pelaporan piutang secara terstruktur.

Aplikasi ini mencakup pengelolaan customer, pencatatan piutang, pembayaran, perhitungan denda, monitoring status piutang, dashboard, laporan, serta manajemen pengguna dan hak akses.

---

## 1. Fitur Utama

### Customer

* Pengelolaan data customer
* Informasi identitas dan kontak customer
* Pencarian dan pengelolaan status customer

### Piutang

* Pencatatan piutang
* Nomor piutang
* Nilai piutang
* Tanggal piutang
* Tanggal jatuh tempo
* Status piutang
* Monitoring outstanding
* Informasi keterlambatan

### Pembayaran

* Pencatatan pembayaran piutang
* Perhitungan dan alokasi pembayaran
* Alokasi pembayaran berdasarkan komponen tagihan
* Monitoring sisa piutang
* Pembatalan transaksi pembayaran dengan tetap mempertahankan histori transaksi

### Aturan Denda

* Pengelolaan aturan denda
* Pengelolaan versi aturan denda
* Snapshot aturan denda pada piutang
* Perhitungan denda berdasarkan aturan yang berlaku

> Snapshot aturan denda digunakan agar perubahan aturan di masa depan tidak mengubah perhitungan historis piutang yang sudah tercatat.

### Dashboard

* Ringkasan kondisi piutang
* Informasi customer
* Informasi pembayaran
* Monitoring piutang
* Statistik dan indikator utama sistem

### Laporan

* Laporan piutang
* Laporan pembayaran
* Ringkasan data
* Filter laporan
* Tampilan cetak

### User Management

* Authentication menggunakan CodeIgniter Shield
* Pengelolaan pengguna
* Group/role pengguna
* Pembatasan akses berdasarkan hak akses

---

## 2. Teknologi

Aplikasi dibangun menggunakan:

* **PHP**
* **CodeIgniter 4**
* **CodeIgniter Shield**
* **MySQL / MariaDB**
* **Bootstrap**
* **AdminLTE**
* **jQuery**
* **DataTables**
* **SweetAlert**

Struktur aplikasi mengikuti pola MVC CodeIgniter 4 dengan penggunaan Service untuk business logic yang membutuhkan proses lebih kompleks.

---

## 3. Arsitektur Aplikasi

Secara umum alur aplikasi:

```text
Controller
    │
    ├── Service
    │      │
    │      └── Business Logic
    │
    └── Model
           │
           ▼
        Database
```

Business logic yang kompleks dipisahkan ke dalam Service sehingga Controller tetap berfokus pada pengelolaan request, response, dan alur halaman.

Service utama:

```text
DashboardService
PaymentService
PiutangMonitoringService
```

---

## 4. Modul Utama

Struktur modul aplikasi:

```text
Customer
   │
   ▼
Piutang
   │
   ├── Aturan Denda
   │
   └── Pembayaran
          │
          ▼
      Outstanding
          │
          ▼
      Monitoring
          │
          ├── Dashboard
          └── Laporan
```

---

## 5. Struktur Database

Database utama aplikasi terdiri dari tabel bisnis berikut:

```text
customer
aturan_denda
aturan_denda_versi
piutang
pembayaran
```

Selain tabel bisnis, aplikasi juga menggunakan tabel yang disediakan oleh CodeIgniter Shield untuk kebutuhan authentication dan authorization.

### Relasi utama

```text
customer
    │
    └──< piutang
             │
             └──< pembayaran

aturan_denda_versi
    │
    └──< aturan_denda

piutang
    │
    └── aturan_denda_versi
```

Piutang menyimpan informasi aturan denda yang berlaku pada saat piutang dibuat. Pendekatan ini menjaga konsistensi historis apabila aturan denda berubah di kemudian hari.

---

## 6. Authentication & Authorization

Authentication menggunakan **CodeIgniter Shield**.

Akses aplikasi dibatasi berdasarkan group/role pengguna.

Group yang digunakan aplikasi dapat disesuaikan melalui konfigurasi Shield dan konfigurasi group aplikasi.

Secara konseptual terdapat perbedaan hak akses antara:

```text
Administrator
Petugas
Pimpinan
```

Hak akses setiap group diterapkan melalui route filter dan authorization aplikasi.

---

## 7. Struktur Project

Struktur utama project:

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
├── Views/
└── ...

public/
├── assets/
└── ...

tests/
├── database/
├── session/
├── unit/
└── ...

docs/
└── ...
```

### Folder penting

| Folder                    | Fungsi                                  |
| ------------------------- | --------------------------------------- |
| `app/Controllers`         | Menangani request dan response aplikasi |
| `app/Models`              | Akses dan representasi data database    |
| `app/Services`            | Business logic kompleks                 |
| `app/Views`               | Tampilan aplikasi                       |
| `app/Database/Migrations` | Struktur database                       |
| `app/Database/Seeds`      | Data awal                               |
| `public/assets`           | Asset frontend                          |
| `tests`                   | Automated test                          |
| `docs`                    | Dokumentasi project                     |

---

## 8. Instalasi

### Prasyarat

Pastikan environment memiliki:

* PHP sesuai versi yang didukung project
* Composer
* MySQL atau MariaDB
* Extension PHP yang dibutuhkan CodeIgniter 4
* Web server atau PHP development server

### Clone Project

```bash
git clone <repository-url>
cd monitoring-piutang
```

### Install Dependency

```bash
composer install
```

### Konfigurasi Environment

Salin file environment:

```bash
cp env .env
```

Pada Windows, file dapat disalin secara manual sesuai kebutuhan.

Kemudian sesuaikan konfigurasi database pada `.env`.

Contoh:

```ini
database.default.hostname = localhost
database.default.database = monitoring_piutang
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.port = 3306
```

> Sesuaikan konfigurasi database dengan environment masing-masing.

### Jalankan Migration

```bash
php spark migrate
```

Jika aplikasi menggunakan seluruh migration yang tersedia:

```bash
php spark migrate --all
```

### Jalankan Development Server

```bash
php spark serve
```

Kemudian akses aplikasi melalui alamat yang diberikan oleh CodeIgniter.

---

## 9. Konfigurasi Authentication

Aplikasi menggunakan CodeIgniter Shield.

Setelah dependency terpasang, konfigurasi authentication mengikuti konfigurasi Shield yang terdapat di:

```text
app/Config/
```

Pastikan migration Shield telah dijalankan sebelum menggunakan fitur authentication.

---

## 10. Business Flow

Alur utama pengelolaan piutang:

```text
Customer
   │
   ▼
Pencatatan Piutang
   │
   ▼
Penentuan Jatuh Tempo
   │
   ▼
Monitoring Piutang
   │
   ├── Belum Jatuh Tempo
   │
   ├── Jatuh Tempo
   │
   ├── Terlambat
   │
   └── Lunas
   │
   ▼
Pembayaran
   │
   ▼
Perhitungan Outstanding
   │
   ▼
Laporan & Dashboard
```

Pembayaran dapat dialokasikan terhadap komponen tagihan sesuai business rule yang diterapkan oleh `PaymentService`.

---

## 11. Prinsip Pengembangan

Pengembangan aplikasi mengikuti beberapa prinsip:

1. Business logic kompleks ditempatkan pada Service.
2. Controller tidak menjadi tempat utama business calculation.
3. Perubahan struktur database dilakukan melalui migration.
4. Data historis transaksi harus dipertahankan.
5. Perubahan aturan denda tidak boleh mengubah histori piutang yang sudah ada.
6. Akses fitur harus mengikuti authorization.
7. Perubahan terhadap transaksi keuangan harus dapat ditelusuri.
8. Perubahan besar terhadap business logic harus disertai pengujian yang sesuai.

---

## 12. Testing

Automated test ditempatkan pada:

```text
tests/
```

Area yang perlu mendapatkan prioritas pengujian:

* Perhitungan piutang
* Perhitungan denda
* Pembayaran
* Alokasi pembayaran
* Outstanding
* Status piutang
* Pembatalan pembayaran
* Snapshot aturan denda

Untuk menjalankan test:

```bash
php spark test
```

---

## 13. Dokumentasi

Dokumentasi tambahan tersedia di:

```text
docs/
```

Dokumentasi project sebaiknya selalu diperbarui ketika terdapat perubahan besar pada:

* struktur database
* business rule
* modul aplikasi
* authorization
* deployment
* konfigurasi environment

---

## 14. Status Project

Project saat ini memiliki core functionality untuk:

* Customer
* Piutang
* Pembayaran
* Aturan Denda
* Monitoring
* Dashboard
* Laporan
* User Management

Pengembangan berikutnya dapat difokuskan pada peningkatan monitoring, reporting, automation, dan penguatan business rule serta testing.

---

## 15. Catatan Pengembangan

Project ini sebelumnya memiliki sejarah pengembangan dari aplikasi lain. Struktur dan kode legacy yang tidak lagi digunakan telah dibersihkan secara bertahap.

Source code saat ini diarahkan sepenuhnya untuk kebutuhan **Sistem Monitoring Piutang**.

Dokumentasi historis yang masih diperlukan untuk kebutuhan traceability dapat dipertahankan pada changelog atau catatan pengembangan, tetapi tidak menjadi bagian dari business architecture aplikasi saat ini.
