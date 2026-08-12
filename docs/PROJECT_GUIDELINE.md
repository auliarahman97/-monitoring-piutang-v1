# Project Guideline

## Sistem Monitoring Piutang

Dokumen ini menjadi pedoman pengembangan dan pemeliharaan Sistem Monitoring Piutang.

Tujuan utama guideline ini adalah menjaga agar pengembangan aplikasi tetap konsisten, mudah dipelihara, aman, serta tidak merusak business logic yang sudah berjalan.

---

# 1. Tujuan Sistem

Sistem Monitoring Piutang digunakan untuk mengelola dan memonitor siklus piutang mulai dari pencatatan customer, pembentukan piutang, monitoring jatuh tempo, pembayaran, perhitungan denda, hingga pelaporan.

Sistem dirancang untuk menjaga:

* Konsistensi data transaksi
* Akurasi perhitungan piutang
* Histori transaksi
* Auditability
* Keamanan akses
* Kemudahan monitoring
* Kemudahan pengembangan

---

# 2. Ruang Lingkup

Modul utama sistem:

```text
Customer
Piutang
Pembayaran
Aturan Denda
Dashboard
Monitoring
Laporan
User Management
```

Modul yang belum tersedia tidak boleh dianggap sebagai bagian dari business logic aktif sampai benar-benar diimplementasikan.

---

# 3. Arsitektur Aplikasi

Aplikasi menggunakan CodeIgniter 4 dengan pola MVC dan Service Layer.

Secara umum:

```text
Request
   │
   ▼
Controller
   │
   ├── Validation
   │
   ├── Authorization
   │
   └── Service
          │
          ├── Business Logic
          └── Transaction
                 │
                 ▼
               Model
                 │
                 ▼
              Database
```

## 3.1 Controller

Controller bertanggung jawab terhadap:

* menerima request
* melakukan validasi input dasar
* memeriksa authorization
* memanggil service/model
* menentukan response
* menampilkan view

Controller tidak sebaiknya menjadi tempat utama untuk:

* perhitungan keuangan kompleks
* alokasi pembayaran
* perhitungan denda
* perubahan beberapa tabel dalam satu business transaction

Business logic tersebut ditempatkan pada Service.

---

# 4. Service Layer

Service digunakan untuk business logic yang membutuhkan proses lebih dari sekadar CRUD.

Service utama:

```text
app/Services/
├── DashboardService.php
├── PaymentService.php
└── PiutangMonitoringService.php
```

## 4.1 PaymentService

`PaymentService` bertanggung jawab terhadap proses pembayaran piutang.

Secara konseptual:

```text
Pembayaran
    │
    ▼
Validasi
    │
    ▼
Hitung Tagihan
    │
    ▼
Hitung Denda/Bunga
    │
    ▼
Alokasi Pembayaran
    │
    ├── Denda
    ├── Bunga
    └── Pokok
    │
    ▼
Hitung Outstanding
    │
    ▼
Update Status Piutang
```

Perubahan terhadap alur pembayaran harus dilakukan dengan hati-hati karena dapat memengaruhi saldo dan histori transaksi.

---

# 5. Piutang

Piutang merupakan entitas utama dalam sistem.

Informasi utama piutang meliputi:

```text
Customer
Nomor Piutang
Nominal
Tanggal Piutang
Tanggal Jatuh Tempo
Status
Aturan Denda
Outstanding
```

Setiap piutang harus memiliki identitas transaksi yang unik.

Perubahan terhadap piutang harus mempertimbangkan histori pembayaran yang telah terjadi.

---

# 6. Pembayaran

Pembayaran merupakan transaksi yang mengurangi kewajiban piutang.

Prinsip utama:

1. Pembayaran harus memiliki identitas transaksi yang unik.
2. Pembayaran harus terhubung dengan piutang.
3. Nilai pembayaran harus divalidasi.
4. Alokasi pembayaran harus mengikuti business rule.
5. Outstanding harus dihitung berdasarkan transaksi yang valid.
6. Pembatalan pembayaran harus mempertahankan histori transaksi.
7. Transaksi yang sudah tercatat tidak boleh diubah secara sembarangan.

---

# 7. Alokasi Pembayaran

Apabila business rule menggunakan urutan:

```text
Denda
  ↓
Bunga
  ↓
Pokok
```

maka perubahan urutan alokasi harus dianggap sebagai perubahan business rule.

Contoh:

```text
Total Pembayaran
       │
       ▼
   Bayar Denda
       │
       ▼
   Bayar Bunga
       │
       ▼
   Bayar Pokok
       │
       ▼
Outstanding
```

Implementasi detail alokasi berada pada `PaymentService`.

---

# 8. Aturan Denda

Sistem menggunakan konsep aturan denda dan versi aturan.

Struktur konseptual:

```text
Aturan Denda
      │
      └── Versi Aturan
              │
              └── Digunakan oleh Piutang
```

## 8.1 Snapshot Aturan

Piutang menyimpan informasi aturan yang berlaku ketika piutang dibuat.

Informasi tersebut dapat meliputi:

```text
denda_versi_id
persentase_denda
periode_denda_hari
maksimal_denda_persen
```

Tujuannya adalah menjaga histori.

Contoh:

```text
2026
Aturan Denda A = 1%

2027
Aturan Denda A berubah menjadi 1.5%
```

Piutang tahun 2026 tetap menggunakan aturan 1% sesuai histori ketika piutang tersebut dibuat.

Perubahan aturan denda tidak boleh secara otomatis mengubah histori piutang lama.

---

# 9. Status Piutang

Status piutang harus menggambarkan kondisi aktual piutang.

Secara konseptual status dapat mencakup:

```text
Belum Jatuh Tempo
Jatuh Tempo
Terlambat
Lunas
```

Status harus ditentukan berdasarkan business rule yang berlaku.

Jangan membuat status baru hanya untuk kebutuhan tampilan tanpa mempertimbangkan:

* database
* model
* service
* dashboard
* laporan
* filter
* query monitoring

---

# 10. Outstanding

Outstanding merupakan nilai kewajiban yang masih harus dibayar.

Secara sederhana:

```text
Outstanding
=
Total Tagihan
-
Total Pembayaran Valid
```

Apabila terdapat komponen:

```text
Pokok
Bunga
Denda
```

maka perhitungan outstanding harus mengikuti aturan alokasi pembayaran yang berlaku.

Perhitungan outstanding tidak boleh dilakukan dengan formula berbeda-beda pada setiap controller.

Sumber business logic harus dipusatkan pada Service atau mekanisme domain yang telah ditetapkan.

---

# 11. Database

Struktur database bisnis utama:

```text
customer
aturan_denda
aturan_denda_versi
piutang
pembayaran
```

Authentication dan authorization menggunakan tabel yang disediakan CodeIgniter Shield.

## 11.1 Migration

Setiap perubahan struktur database harus dibuat melalui migration.

Jangan melakukan perubahan schema database secara manual pada project tanpa migration yang sesuai.

Contoh:

```bash
php spark make:migration NamaMigration
```

Kemudian jalankan:

```bash
php spark migrate
```

---

# 12. Foreign Key

Relasi antar tabel harus menggunakan foreign key apabila memang diperlukan oleh business relationship.

Contoh:

```text
customer
   │
   └── piutang

piutang
   │
   └── pembayaran
```

Foreign key membantu menjaga integritas data.

Penghapusan data parent harus mempertimbangkan data transaksi yang bergantung kepadanya.

---

# 13. Soft Delete dan Histori

Data transaksi keuangan tidak boleh dihapus sembarangan.

Untuk data yang menggunakan soft delete:

```text
deleted_at
deleted_by
```

penghapusan hanya menandai data sebagai tidak aktif tanpa menghilangkan histori fisik dari database.

Untuk transaksi pembayaran, pembatalan transaksi lebih diutamakan daripada penghapusan fisik.

---

# 14. Audit Trail

Data penting sebaiknya dapat ditelusuri:

```text
created_by
created_at
updated_by
updated_at
deleted_by
deleted_at
```

Tujuannya untuk mengetahui:

* siapa yang membuat data
* siapa yang mengubah data
* siapa yang membatalkan/menghapus data
* kapan perubahan dilakukan

---

# 15. Authentication & Authorization

Authentication menggunakan CodeIgniter Shield.

Authorization dilakukan berdasarkan group/role.

Akses terhadap route harus menggunakan filter authorization yang sesuai.

Jangan mengandalkan:

```text
hidden button
hidden menu
```

sebagai satu-satunya mekanisme keamanan.

Pembatasan harus tetap diterapkan pada sisi server.

---

# 16. Validation

Semua input pengguna harus divalidasi.

Validasi harus mempertimbangkan:

* tipe data
* format
* required field
* range nilai
* relasi data
* business rule

Khusus nilai keuangan:

```text
nominal
pembayaran
denda
bunga
```

harus divalidasi agar tidak menerima nilai yang tidak masuk akal.

---

# 17. Database Transaction

Operasi yang memengaruhi beberapa tabel sekaligus harus menggunakan database transaction.

Contoh pembayaran:

```text
BEGIN TRANSACTION
      │
      ├── Simpan pembayaran
      ├── Alokasi pembayaran
      ├── Update outstanding/status
      └── Audit
      │
COMMIT
```

Jika salah satu proses gagal:

```text
ROLLBACK
```

Tujuannya agar database tidak berada dalam kondisi setengah berhasil.

---

# 18. Controller Convention

Controller sebaiknya memiliki tanggung jawab yang jelas.

Contoh:

```text
Customer
    → CustomerController

Piutang
    → PiutangController

Pembayaran
    → PembayaranController

Aturan Denda
    → AturanDendaController

Dashboard
    → DashboardController

Laporan
    → LaporanController
```

Jika controller mulai mengandung business calculation yang panjang, pertimbangkan pemindahan logic tersebut ke Service.

---

# 19. Model Convention

Model bertanggung jawab terhadap:

* struktur data
* table mapping
* allowed fields
* relationship/query yang sesuai
* konstanta domain sederhana

Model tidak sebaiknya menjadi tempat seluruh business workflow.

Business workflow kompleks ditempatkan pada Service.

---

# 20. View Convention

View bertanggung jawab terhadap presentation.

View tidak sebaiknya melakukan:

* query database
* business calculation kompleks
* perubahan data
* transaksi database

Gunakan:

```text
Component
Partial
Helper
```

untuk bagian UI yang digunakan berulang.

---

# 21. Reusable Components

Komponen UI yang digunakan berulang sebaiknya ditempatkan pada:

```text
app/Views/components/
```

Contoh:

```text
form/
table/
layout/
widget/
```

Tujuannya:

* mengurangi duplikasi HTML
* menjaga konsistensi UI
* memudahkan perubahan global

---

# 22. JavaScript

JavaScript untuk modul tertentu sebaiknya ditempatkan pada asset yang sesuai.

Hindari menempatkan script besar secara berulang di banyak view.

Untuk komponen umum, gunakan script yang dapat digunakan kembali.

---

# 23. Laporan

Laporan harus menggunakan sumber data dan business rule yang sama dengan sistem transaksi.

Jangan membuat perhitungan laporan yang berbeda dari perhitungan transaksi.

Contoh:

```text
PaymentService
      │
      ▼
Business Rule Pembayaran
      │
      ├── Dashboard
      ├── Monitoring
      └── Laporan
```

Dengan demikian angka yang muncul pada dashboard dan laporan tetap konsisten dengan transaksi.

---

# 24. Testing

Testing harus diprioritaskan pada business logic yang memiliki risiko finansial.

Prioritas:

```text
1. Pembayaran
2. Alokasi pembayaran
3. Denda
4. Outstanding
5. Status piutang
6. Pembatalan pembayaran
7. Snapshot aturan denda
```

Contoh skenario minimum:

```text
Piutang Rp10.000.000
Pembayaran Rp2.000.000
Outstanding Rp8.000.000
```

Skenario tambahan:

```text
Belum jatuh tempo
Jatuh tempo
Terlambat
Terkena denda
Partial payment
Lunas
Pembayaran dibatalkan
Perubahan aturan denda
```

---

# 25. Perubahan Business Rule

Perubahan terhadap business rule harus diperlakukan sebagai perubahan penting.

Sebelum melakukan perubahan:

1. Identifikasi rule yang berubah.
2. Identifikasi Service yang terdampak.
3. Identifikasi Model yang terdampak.
4. Identifikasi Dashboard/Laporan yang terdampak.
5. Tambahkan atau perbarui test.
6. Uji transaksi lama.
7. Uji transaksi baru.

Jangan mengubah formula keuangan hanya untuk memperbaiki tampilan.

---

# 26. Refactoring

Refactoring harus dilakukan secara bertahap.

Prinsip:

```text
Working Code
    ↓
Test
    ↓
Refactor
    ↓
Test
    ↓
Commit
```

Jangan melakukan refactoring besar sekaligus tanpa test.

Controller atau Service yang panjang tidak otomatis harus dipecah jika business logic-nya masih jelas dan stabil.

Prioritaskan refactoring apabila:

* terdapat duplikasi logic
* sulit diuji
* sulit dipahami
* memiliki dependency berlebihan
* sering menyebabkan bug
* business rule bercampur dengan presentation logic

---

# 27. Git Workflow

Setiap perubahan yang memiliki tujuan jelas sebaiknya dibuat dalam commit terpisah.

Contoh:

```text
cleanup: remove legacy cooperative files
cleanup: update application identity
docs: rewrite project README
docs: update project guideline
refactor: extract payment calculation
test: add payment service tests
```

Hindari commit seperti:

```text
update
fix
perbaikan
coba
final
final2
final-banget
```

Commit message harus menjelaskan tujuan perubahan.

---

# 28. Prinsip Cleanup Legacy

Project memiliki sejarah pengembangan dari aplikasi sebelumnya.

Legacy code harus ditangani dengan prinsip:

```text
Search
   ↓
Verify Dependency
   ↓
Understand Purpose
   ↓
Remove / Refactor
   ↓
Test
```

Jangan melakukan penghapusan berdasarkan nama file saja.

Sebelum menghapus file:

1. Cari seluruh referensinya.
2. Pastikan tidak digunakan.
3. Hapus.
4. Jalankan aplikasi.
5. Jalankan test yang relevan.

---

# 29. Prinsip Pengembangan Utama

Setiap pengembangan baru harus mengikuti prinsip berikut:

### 1. Jangan merusak transaksi lama

Data historis harus tetap konsisten.

### 2. Jangan menduplikasi business logic

Satu business rule harus memiliki sumber yang jelas.

### 3. Jangan memasukkan business logic ke View

View hanya untuk presentation.

### 4. Jangan memasukkan business workflow kompleks ke Controller

Gunakan Service.

### 5. Jangan mengubah database tanpa migration

Semua perubahan schema harus terdokumentasi.

### 6. Jangan menghapus histori transaksi secara fisik tanpa alasan kuat

Gunakan mekanisme pembatalan atau soft delete yang sesuai.

### 7. Test sebelum refactor besar

Terutama untuk perhitungan keuangan.

### 8. Dokumentasikan perubahan penting

Dokumentasi harus mengikuti kondisi sistem aktual.

---

# 30. Development Checklist

Sebelum sebuah fitur dianggap selesai:

* [ ] Business rule sudah jelas
* [ ] Database sudah sesuai
* [ ] Migration dibuat jika diperlukan
* [ ] Model sudah sesuai
* [ ] Service digunakan jika terdapat business logic kompleks
* [ ] Controller tetap sederhana
* [ ] Validation diterapkan
* [ ] Authorization diterapkan
* [ ] View sudah konsisten
* [ ] Error handling sudah diperiksa
* [ ] Testing sudah dilakukan
* [ ] Dokumentasi diperbarui jika diperlukan

---

# 31. Prinsip Akhir

Sistem Monitoring Piutang adalah aplikasi yang menangani data dan transaksi yang memiliki konsekuensi finansial.

Karena itu prioritas pengembangan adalah:

```text
Accuracy
   ↓
Data Integrity
   ↓
Auditability
   ↓
Security
   ↓
Maintainability
   ↓
User Experience
```

Fitur baru sebaiknya tidak ditambahkan dengan mengorbankan integritas data atau business rule yang sudah berjalan.

Setiap perubahan besar harus mempertimbangkan dampaknya terhadap:

```text
Customer
Piutang
Pembayaran
Denda
Outstanding
Dashboard
Laporan
Audit Trail
Authorization
```

Pedoman ini menjadi acuan utama dalam pengembangan dan pemeliharaan Sistem Monitoring Piutang.
