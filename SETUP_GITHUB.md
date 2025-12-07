# 🚀 PANDUAN UNTUK TEMAN YANG CLONE DARI GITHUB

## ✅ Langkah-langkah Setup (Super Mudah!)

### 1️⃣ Clone Repository
```bash
git clone https://github.com/kendikadimas/dvwa.git
cd dvwa
```

### 2️⃣ Start Docker
Pastikan Docker Desktop sudah running, lalu:
```bash
docker compose up -d
```

Tunggu sampai selesai (30-60 detik).

### 3️⃣ Buka Browser
```
http://localhost:8000
```

Atau versi Indonesia:
```
http://localhost:8000/login_id.php
```

### 4️⃣ Setup Database (Pertama Kali Saja)

Anda akan melihat halaman seperti ini:

```
┌─────────────────────────────────────────┐
│           DVWA - Login                  │
├─────────────────────────────────────────┤
│                                         │
│  ⚠️ Database tidak ditemukan            │
│                                         │
│  🚀 First Time Setup Required           │
│                                         │
│  Klik tombol di bawah untuk create      │
│  database otomatis                      │
│                                         │
│  ┌───────────────────────────────┐     │
│  │   📦 Setup Database           │     │
│  └───────────────────────────────┘     │
│                                         │
└─────────────────────────────────────────┘
```

**Klik tombol "Setup Database"** itu. Nanti akan:
- Buat database `dvwa`
- Buat tabel `users` dan `comments`
- Insert user admin dan sample data
- Semua otomatis!

### 5️⃣ Login
Setelah setup selesai, akan muncul tombol "Go to Login Page". Klik, lalu login:

```
Username: admin
Password: admin123
```

## 🎉 Selesai!

Sekarang kamu bisa:
- Test SQL Injection di "Pencarian Surat"
- Test XSS Stored di "Komentar Surat"
- Test XSS Reflected di "Feedback Sistem"
- Test CSRF di "Ganti Password"

## 🔧 Troubleshooting

### ❌ Docker tidak bisa start
```bash
# Cek apakah Docker Desktop running
docker version

# Kalau error, buka Docker Desktop dulu
# Tunggu sampai icon Docker hijau
```

### ❌ Port 8000 sudah dipakai
Edit file `docker-compose.yml`, cari baris:
```yaml
ports:
  - "8000:80"
```

Ganti jadi:
```yaml
ports:
  - "8080:80"  # atau port lain yang kosong
```

Lalu restart:
```bash
docker compose down
docker compose up -d
```

### ❌ Halaman tidak muncul
```bash
# Cek status container
docker ps

# Harus muncul 2 container:
# - dvwa_web (Apache + PHP)
# - dvwa_db (MariaDB)

# Kalau tidak ada, coba restart
docker compose down
docker compose up -d
```

### ❌ Setup Database gagal
Kemungkinan database container belum ready. Tunggu 30 detik lagi, lalu:
1. Klik tombol "🔄 Retry Connection"
2. Atau refresh halaman (F5)
3. Lalu klik "Setup Database" lagi

## 📚 Dokumentasi Lengkap

Setelah berhasil login, baca dokumentasi ini:

1. **PAYLOADS.md** - Semua payload untuk SQLi, XSS, CSRF
2. **EVASION_PAYLOADS.md** - 40+ teknik bypass IDS
3. **LAPORAN_IDS_EVASION.md** - Laporan lengkap (BAB I-V)
4. **TABEL_REKAP_TESTING.md** - Hasil testing IDS

## 🎯 Quick Test

Setelah login, coba payload ini di **Pencarian Surat**:

### Test 1: Show All Records
```sql
1' OR '1'='1' #
```
Hasil: Semua data surat muncul

### Test 2: Extract Users
```sql
1' UNION SELECT username,password,3,created_at FROM users #
```
Hasil: Username dan password hash muncul

### Test 3: XSS
Di form **Komentar Surat**, isi:
```html
<script>alert('XSS Works!')</script>
```
Hasil: Alert box muncul

## 🌐 Akses dari Kali VM

Jika mau test dari Kali VM di network yang sama:

1. **Cek IP Windows:**
   ```powershell
   ipconfig | findstr IPv4
   ```
   Contoh output: `192.168.1.100`

2. **Di Kali, buka:**
   ```
   http://192.168.1.100:8000
   ```

3. **Setup IDS di Kali:**
   - Install Suricata atau Snort3
   - Monitor traffic ke IP tersebut
   - Load custom rules (lihat LAPORAN_IDS_EVASION.md)

## 💡 Tips

- Default credentials: `admin` / `admin123`
- Ada 4 user sample: john, jane, bob (password: `{username}123`)
- Setiap kali restart Docker, database akan reset otomatis
- Gunakan tombol "Reset Database" di XSS Stored untuk clear comments

## ❓ Butuh Bantuan?

1. Baca [README.md](README.md) - Panduan lengkap
2. Baca [LAPORAN_IDS_EVASION.md](LAPORAN_IDS_EVASION.md) - Troubleshooting detail
3. Check GitHub Issues

---

**Happy Hacking! 🎓🔐**

*Project ini untuk educational purposes only. Jangan deploy ke production!*
