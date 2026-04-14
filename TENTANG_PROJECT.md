1N# Penjelasan Project "Tether Brew" (Kopi Gerobak Keliling)

Dokumen ini berisi rangkuman mengenai sistem dan fitur utama dari aplikasi web Tether Brew, disajikan dengan bahasa sederhana agar mudah dipahami.

---

## ☕ Apa itu Project Ini?
Project ini adalah sebuah sistem aplikasi web untuk bisnis **Kopi Keliling** yang bernama **"Tether Brew"**. 

Bayangkan aplikasi ini sebagai "otak dan pusat kendali" dari perusahaan kopi keliling. Aplikasi ini bukan hanya sekedar website biasa, tapi juga memiliki fitur canggih untuk memantau gerobak kopi secara *real-time* (pelacakan GPS) dan menjadi mesin kasir (POS) bagi para penjual di jalanan.

---

## 👥 Pengguna Aplikasi (Role)
Sistem ini dirancang untuk digunakan oleh 3 kelompok orang dengan tugas yang berbeda-beda:

### 1. Publik / Pelanggan (Tidak perlu login)
*   Mengakses halaman utama website (*Landing Page*).
*   **Fitur Utama:** Terdapat **Peta (Map)** yang bisa melacak koordinat gerobak-gerobak kopi yang sedang beroperasi secara langsung (*real-time*). Pelanggan tidak perlu repot mencari, cukup buka website, dan datangi gerobak terdekat.

### 2. Rider / Penjual Keliling (Yang berjualan di jalan)
*   Memiliki akun khusus untuk login ke aplikasi lewat ponsel mereka.
*   **Fungsi Kasir (POS):** Layaknya kasir minimarket, mereka dapat mencatat pesanan pembeli (misal: 2 Kopi Susu, 1 Americano) dan mencatat pemasukan.
*   **Update Lokasi (Live Tracking):** Sistem secara otomatis mengirimkan koordinat GPS dari ponsel *Rider*, agar lokasi gerobak tempat *Rider* jualan muncul di peta para pelanggan.
*   **Cek Stok:** Mereka bisa memantau sisa stok barang dan minuman di gerobak mereka masing-masing.

### 3. Owner / Admin (Pemilik Bisnis)
*   Memiliki akses "Panel Kendali" atau *Dashboard* di tingkat perusahaan.
*   Mendaftarkan menu kopi baru dan mengatur harganya (*Products*).
*   Mendaftarkan gerobak dan *Rider* baru ke dalam sistem.
*   Memantau stok semua gerobak (*Inventory*) secara terpusat untuk keperluan pengisian ulang (restock).
*   Melihat rute gerobak di peta khusus Admin.
*   Melihat total keuntungan dan rincian transaksi penjualan kopi (*Transactions*).

---

## 📦 Data yang Disimpan (Database)
Di balik layar, aplikasi ini menyimpan dan mengolah data-data berikut:
*   **User:** Data akun pengguna (email, password, dan wewenangnya sebagai Admin atau Rider).
*   **Cart (Gerobak):** Data unit-unit gerobak kopi.
*   **CartLocation:** Catatan pergerakan titik koordinat peta (Latitude/Longitude) setiap gerobak secara berkala.
*   **Product:** Menu minuman/makanan yang dijual.
*   **Inventory (Stok Barang):** Daftar "berapa sisa kopi X di gerobak Y".
*   **Transaction:** Struk digital yang mencatat kapan terjadi penjualan, pembeli beli apa saja (Transaction Item), dan total uang masuk.

---

## 💻 Teknologi yang Digunakan
Secara teknis, berikut adalah mesin yang menggerakkan project ini:
*   **Platform Utama (Backend):** Aplikasi ini dikembangkan dengan **Laravel 13** (Menggunakan **PHP 8.3**). Laravel adalah framework modern yang handal.
*   **Tampilan Antarmuka (Frontend):** Menggunakan template yang difasilitasi oleh sistem **Vite**, sehingga cepat, halus, dan responsif.
*   **Keamanan:** Sudah terintegrasi dengan sistem otentikasi (Auth) agar data transaksi bisnis aman dan tidak bisa diubah oleh sembarang orang.

> [!NOTE]
> **Kesimpulan:** 
> Project ini adalah fondasi digital yang kuat untuk mengubah bisnis kopi gerobak tradisional menjadi bisnis modern berbasis teknologi pelacakan dan kasir online.
