# Database Documentation

## Sistem Monitoring Piutang

Dokumen ini menjelaskan struktur database utama Sistem Monitoring Piutang, relasi antar tabel, fungsi setiap tabel, serta aturan penting yang berkaitan dengan integritas dan histori data.

Dokumen ini harus diperbarui apabila terdapat perubahan struktur database melalui migration.

---

# 1. Database Overview

Database aplikasi terdiri dari dua kelompok utama:

### Business Tables

```text
customer
aturan_denda_versi
aturan_denda
piutang
pembayaran
```

### Authentication & Authorization

Tabel authentication dan authorization dikelola oleh CodeIgniter Shield.

Struktur tabel Shield tidak dijelaskan secara detail dalam dokumen ini karena merupakan bagian dari framework authentication.

---

# 2. Entity Relationship

Relasi utama database:

```text
                    ┌──────────────────────┐
                    │ aturan_denda_versi   │
                    └──────────┬───────────┘
                               │
                               │
                               ▼
                    ┌──────────────────────┐
                    │    aturan_denda      │
                    └──────────────────────┘


┌──────────────────────┐
│      customer        │
└──────────┬───────────┘
           │
           │ 1 : N
           ▼
┌──────────────────────┐
│       piutang        │
└──────────┬───────────┘
           │
           │ 1 : N
           ▼
┌──────────────────────┐
│      pembayaran     │
└──────────────────────┘

piutang
   │
   └────────── N : 1 ──────────> aturan_denda_versi
```

Secara konseptual:

```text
Customer
   │
   └──< Piutang
           │
           ├──> Aturan Denda Versi
           │
           └──< Pembayaran
```

---

# 3. Tabel `customer`

## Fungsi

Menyimpan master data customer yang memiliki hubungan dengan piutang.

## Struktur

| Kolom               | Tipe         | Null | Keterangan                     |
| ------------------- | ------------ | ---: | ------------------------------ |
| `id`                | INT UNSIGNED |   No | Primary key                    |
| `kode_customer`     | VARCHAR(20)  |   No | Kode unik customer             |
| `nama`              | VARCHAR(100) |   No | Nama customer                  |
| `nik`               | VARCHAR(16)  |   Ya | Nomor identitas                |
| `no_hp`             | VARCHAR(20)  |   Ya | Nomor telepon                  |
| `alamat`            | TEXT         |   Ya | Alamat customer                |
| `tanggal_terdaftar` | DATE         |   No | Tanggal customer terdaftar     |
| `status`            | TINYINT(1)   |   No | `1 = Aktif`, `0 = Tidak Aktif` |
| `created_by`        | INT UNSIGNED |   Ya | User pembuat data              |
| `updated_by`        | INT UNSIGNED |   Ya | User terakhir yang mengubah    |
| `deleted_by`        | INT UNSIGNED |   Ya | User yang melakukan delete     |
| `created_at`        | DATETIME     |   Ya | Waktu pembuatan                |
| `updated_at`        | DATETIME     |   Ya | Waktu perubahan                |
| `deleted_at`        | DATETIME     |   Ya | Waktu soft delete              |

## Index

```text
PRIMARY KEY
    id

UNIQUE
    kode_customer

INDEX
    nama
    status
```

## Relasi

```text
customer.id
    ↓
piutang.customer_id
```

Customer tidak boleh dihapus secara sembarangan apabila masih memiliki histori piutang.

---

# 4. Tabel `aturan_denda_versi`

## Fungsi

Menyimpan versi kebijakan aturan denda.

Versi digunakan untuk menjaga histori perubahan kebijakan denda.

## Struktur

| Kolom             | Tipe         | Null | Keterangan             |
| ----------------- | ------------ | ---: | ---------------------- |
| `id`              | INT UNSIGNED |   No | Primary key            |
| `kode_versi`      | VARCHAR      |   No | Kode unik versi aturan |
| `nama_versi`      | VARCHAR      |   No | Nama versi             |
| `tanggal_mulai`   | DATE         |   No | Awal berlakunya versi  |
| `tanggal_selesai` | DATE         |   Ya | Akhir berlakunya versi |
| `status`          | VARCHAR      |   No | Status versi           |
| `keterangan`      | TEXT         |   Ya | Catatan versi          |
| `created_by`      | INT UNSIGNED |   Ya | User pembuat           |
| `updated_by`      | INT UNSIGNED |   Ya | User pengubah          |
| `deleted_by`      | INT UNSIGNED |   Ya | User penghapus         |
| `created_at`      | DATETIME     |   Ya | Waktu pembuatan        |
| `updated_at`      | DATETIME     |   Ya | Waktu perubahan        |
| `deleted_at`      | DATETIME     |   Ya | Waktu soft delete      |

## Index

```text
PRIMARY KEY
    id

UNIQUE
    kode_versi

INDEX
    tanggal_mulai
    tanggal_selesai
    status
```

## Konsep Versi

Contoh:

```text
DENDA-V001
Kebijakan Denda Awal
```

Versi berikutnya dapat dibuat apabila terjadi perubahan kebijakan:

```text
DENDA-V002
Kebijakan Denda Periode Berikutnya
```

Perubahan versi tidak boleh mengubah histori piutang yang telah menggunakan versi sebelumnya.

---

# 5. Tabel `aturan_denda`

## Fungsi

Menyimpan detail aturan denda berdasarkan rentang nominal dan periode berlaku.

## Struktur

| Kolom                   | Tipe          | Null | Keterangan                     |
| ----------------------- | ------------- | ---: | ------------------------------ |
| `id`                    | INT UNSIGNED  |   No | Primary key                    |
| `versi_id`              | INT UNSIGNED  |   No | Referensi versi aturan         |
| `nama_aturan`           | VARCHAR(100)  |   No | Nama aturan                    |
| `min_nominal`           | DECIMAL(15,2) |   No | Minimal nominal pokok          |
| `max_nominal`           | DECIMAL(15,2) |   Ya | Maksimal nominal pokok         |
| `persentase_denda`      | DECIMAL(5,2)  |   No | Persentase denda               |
| `periode_hari`          | INT UNSIGNED  |   No | Periode denda                  |
| `maksimal_denda_persen` | DECIMAL(5,2)  |   No | Batas maksimal denda           |
| `tanggal_mulai`         | DATE          |   No | Awal berlaku                   |
| `tanggal_selesai`       | DATE          |   Ya | Akhir berlaku                  |
| `status`                | TINYINT(1)    |   No | `1 = Aktif`, `0 = Tidak Aktif` |
| `keterangan`            | TEXT          |   Ya | Catatan                        |
| `created_by`            | INT UNSIGNED  |   Ya | User pembuat                   |
| `updated_by`            | INT UNSIGNED  |   Ya | User pengubah                  |
| `deleted_by`            | INT UNSIGNED  |   Ya | User penghapus                 |
| `created_at`            | DATETIME      |   Ya | Waktu pembuatan                |
| `updated_at`            | DATETIME      |   Ya | Waktu perubahan                |
| `deleted_at`            | DATETIME      |   Ya | Waktu soft delete              |

## Index

```text
PRIMARY KEY
    id

INDEX
    versi_id
    min_nominal
    max_nominal
    status
    tanggal_mulai
    tanggal_selesai
```

## Relasi

```text
aturan_denda.versi_id
        ↓
aturan_denda_versi.id
```

`versi_id` merupakan relasi wajib setelah migration versi aturan selesai dijalankan.

---

# 6. Tabel `piutang`

## Fungsi

Merupakan tabel utama transaksi piutang.

Tabel ini menghubungkan customer, aturan denda yang digunakan, dan histori pembayaran.

## Struktur

| Kolom                   | Tipe          | Null | Keterangan                    |
| ----------------------- | ------------- | ---: | ----------------------------- |
| `id`                    | INT UNSIGNED  |   No | Primary key                   |
| `customer_id`           | INT UNSIGNED  |   No | Customer pemilik piutang      |
| `nomor_piutang`         | VARCHAR       |   No | Nomor unik piutang            |
| `tanggal_piutang`       | DATE          |   No | Tanggal piutang               |
| `tanggal_jatuh_tempo`   | DATE          |   No | Tanggal jatuh tempo           |
| `nominal_pokok`         | DECIMAL(15,2) |   No | Nilai pokok                   |
| `persentase_bunga`      | DECIMAL(5,2)  |   No | Persentase bunga              |
| `nominal_bunga`         | DECIMAL(15,2) |   No | Nilai bunga                   |
| `denda_versi_id`        | INT UNSIGNED  |   No | Versi aturan denda            |
| `persentase_denda`      | DECIMAL(5,2)  |   No | Snapshot persentase denda     |
| `periode_denda_hari`    | INT           |   No | Snapshot periode denda        |
| `maksimal_denda_persen` | DECIMAL(5,2)  |   No | Snapshot batas maksimal denda |
| `keterangan`            | TEXT          |   Ya | Catatan piutang               |
| `created_by`            | INT UNSIGNED  |   Ya | User pembuat                  |
| `updated_by`            | INT UNSIGNED  |   Ya | User pengubah                 |
| `deleted_by`            | INT UNSIGNED  |   Ya | User penghapus                |
| `created_at`            | DATETIME      |   Ya | Waktu pembuatan               |
| `updated_at`            | DATETIME      |   Ya | Waktu perubahan               |
| `deleted_at`            | DATETIME      |   Ya | Waktu soft delete             |

## Index

```text
PRIMARY KEY
    id

UNIQUE
    nomor_piutang

INDEX
    customer_id
    denda_versi_id
    tanggal_piutang
    tanggal_jatuh_tempo
```

## Relasi

```text
piutang.customer_id
        ↓
customer.id
```

dan:

```text
piutang.denda_versi_id
        ↓
aturan_denda_versi.id
```

## Prinsip Snapshot

Piutang menyimpan snapshot parameter denda:

```text
persentase_denda
periode_denda_hari
maksimal_denda_persen
```

Tujuannya agar perubahan aturan denda di masa depan tidak mengubah histori piutang lama.

---

# 7. Tabel `pembayaran`

## Fungsi

Menyimpan setiap transaksi pembayaran terhadap piutang.

Pembayaran merupakan histori transaksi dan menjadi dasar penghitungan outstanding.

## Struktur

| Kolom                | Tipe          | Null | Keterangan                        |
| -------------------- | ------------- | ---: | --------------------------------- |
| `id`                 | INT UNSIGNED  |   No | Primary key                       |
| `piutang_id`         | INT UNSIGNED  |   No | Piutang yang dibayar              |
| `nomor_pembayaran`   | VARCHAR(30)   |   No | Nomor unik pembayaran             |
| `tanggal_pembayaran` | DATE          |   No | Tanggal pembayaran                |
| `total_tagihan`      | DECIMAL(15,2) |   No | Snapshot total tagihan            |
| `nominal_pembayaran` | DECIMAL(15,2) |   No | Nominal pembayaran                |
| `alokasi_denda`      | DECIMAL(15,2) |   No | Alokasi ke denda                  |
| `alokasi_bunga`      | DECIMAL(15,2) |   No | Alokasi ke bunga                  |
| `alokasi_pokok`      | DECIMAL(15,2) |   No | Alokasi ke pokok                  |
| `sisa_tagihan`       | DECIMAL(15,2) |   No | Snapshot sisa tagihan             |
| `status`             | VARCHAR(20)   |   No | Status transaksi, default `valid` |
| `keterangan`         | TEXT          |   Ya | Catatan                           |
| `created_by`         | INT UNSIGNED  |   Ya | User pembuat                      |
| `cancelled_by`       | INT UNSIGNED  |   Ya | User yang membatalkan             |
| `cancelled_at`       | DATETIME      |   Ya | Waktu pembatalan                  |
| `created_at`         | DATETIME      |   Ya | Waktu pembuatan                   |
| `updated_at`         | DATETIME      |   Ya | Waktu perubahan                   |

## Index

```text
PRIMARY KEY
    id

UNIQUE
    nomor_pembayaran

INDEX
    piutang_id
    tanggal_pembayaran
    status
```

## Relasi

```text
pembayaran.piutang_id
        ↓
piutang.id
```

Satu piutang dapat memiliki banyak pembayaran:

```text
Piutang
   │
   ├── Pembayaran 1
   ├── Pembayaran 2
   ├── Pembayaran 3
   └── ...
```

---

# 8. Alokasi Pembayaran

Business rule pembayaran menggunakan urutan:

```text
Denda
  ↓
Bunga
  ↓
Pokok
```

Contoh:

```text
Tagihan
├── Denda     Rp 100.000
├── Bunga     Rp 200.000
└── Pokok     Rp 5.000.000

Pembayaran
    Rp 250.000
```

Maka:

```text
Alokasi Denda  = Rp100.000
Alokasi Bunga  = Rp150.000
Alokasi Pokok  = Rp0
```

Sisa komponen tagihan harus dihitung berdasarkan business logic pada `PaymentService`.

---

# 9. Outstanding

Outstanding menggambarkan nilai tagihan yang masih harus diselesaikan.

Secara konseptual:

```text
Total Tagihan
      -
Pembayaran Valid
      =
Outstanding
```

Pembayaran yang telah dibatalkan tidak boleh dianggap sebagai pembayaran valid dalam perhitungan outstanding.

Perhitungan aktual harus mengikuti business logic yang diterapkan oleh `PaymentService` dan `PiutangMonitoringService`.

---

# 10. Status Pembayaran

Status pembayaran disimpan pada kolom:

```text
status
```

Status default:

```text
valid
```

Pembayaran yang dibatalkan memiliki informasi:

```text
cancelled_by
cancelled_at
```

Pembatalan tidak dimaksudkan untuk menghilangkan histori pembayaran secara fisik.

---

# 11. Audit Trail

Tabel berikut memiliki mekanisme audit:

```text
customer
aturan_denda
aturan_denda_versi
piutang
```

Field yang digunakan:

```text
created_by
updated_by
deleted_by

created_at
updated_at
deleted_at
```

Untuk `pembayaran`, mekanisme pembatalan menggunakan:

```text
created_by
cancelled_by
cancelled_at
```

---

# 12. Integritas Data

Prinsip integritas data:

### Customer → Piutang

Piutang harus selalu memiliki customer yang valid.

```text
customer
    │
    └──< piutang
```

### Piutang → Pembayaran

Pembayaran harus selalu memiliki piutang yang valid.

```text
piutang
    │
    └──< pembayaran
```

### Piutang → Versi Denda

Piutang harus menggunakan versi aturan denda yang valid.

```text
aturan_denda_versi
    │
    └──< piutang
```

### Aturan Denda → Versi

Setiap aturan denda aktif harus terkait dengan versi aturan denda.

---

# 13. Migration History

Migration bisnis utama:

```text
2026-08-07
CreateCustomerTable

2026-08-08
CreateAturanDendaTable

2026-08-09
CreateAturanDendaVersiTable
AddVersiIdToAturanDenda
MigrateExistingAturanDendaToVersiPertama
AddForeignKeyAturanDendaVersi
CreatePiutangTableFinal
CreatePembayaranTable
```

Migration tersebut membentuk struktur database Monitoring Piutang saat ini.

Migration legacy dari aplikasi sebelumnya telah dikeluarkan dari project.

---

# 14. Perubahan Schema

Setiap perubahan schema harus dilakukan melalui migration.

Contoh:

```bash
php spark make:migration NamaMigration
```

Setelah migration dibuat:

```bash
php spark migrate
```

Jangan melakukan perubahan schema secara manual tanpa migration yang sesuai.

---

# 15. Prinsip Perubahan Database

Sebelum mengubah database:

1. Tentukan business requirement.
2. Identifikasi tabel terdampak.
3. Periksa foreign key.
4. Periksa model dan service yang menggunakan kolom tersebut.
5. Buat migration.
6. Uji migration.
7. Uji business logic.
8. Dokumentasikan perubahan.

Perubahan database yang menyentuh transaksi keuangan harus dianggap sebagai perubahan berisiko tinggi.

---

# 16. Catatan Teknis

Dokumentasi ini mengikuti migration yang tersedia pada project saat ini.

Beberapa aspek implementasi database perlu mendapatkan review teknis lanjutan, khususnya:

### 16.1 Foreign Key Audit User

Beberapa kolom audit menggunakan foreign key ke tabel `users`.

Perilaku `ON DELETE` perlu dipastikan sesuai dengan kebutuhan audit trail sehingga penghapusan user tidak menyebabkan data transaksi ikut terhapus atau kehilangan integritas histori.

### 16.2 Foreign Key Pembayaran

Relasi:

```text
pembayaran.piutang_id
        ↓
piutang.id
```

saat ini menggunakan konfigurasi foreign key pada migration pembayaran.

Perilaku `ON DELETE` perlu diverifikasi kembali terhadap prinsip bahwa histori transaksi piutang tidak boleh hilang ketika piutang dihapus.

**Catatan ini sengaja tidak mengubah migration sekarang.** Review dan perubahan FK akan dilakukan sebagai pekerjaan database integrity tersendiri setelah cleanup selesai.

---

# 17. Source of Truth

Untuk struktur database, urutan sumber kebenaran adalah:

```text
Migration
    ↓
Database aktual
    ↓
Model
    ↓
Documentation
```

Migration merupakan sumber utama definisi schema pada project.

Dokumentasi tidak boleh menjadi satu-satunya sumber perubahan database.

Apabila dokumentasi berbeda dengan migration, dokumentasi harus diperbarui setelah struktur aktual diverifikasi.

---

# 18. Ringkasan Database

```text
┌────────────────────┐
│      customer      │
└─────────┬──────────┘
          │
          │
          ▼
┌────────────────────┐
│      piutang       │
└──────┬─────────────┘
       │
       ├─────────────────────┐
       │                     │
       ▼                     ▼
┌───────────────┐    ┌──────────────────────┐
│  pembayaran   │    │ aturan_denda_versi  │
└───────────────┘    └──────────┬───────────┘
                                │
                                ▼
                       ┌──────────────────┐
                       │  aturan_denda    │
                       └──────────────────┘
```

Database dirancang untuk mendukung:

* Master customer
* Pencatatan piutang
* Pembayaran bertahap
* Perhitungan denda
* Versioning aturan denda
* Histori transaksi
* Audit trail
* Monitoring outstanding
* Pelaporan
