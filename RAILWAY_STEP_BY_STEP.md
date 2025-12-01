# 🚀 PANDUAN LENGKAP: HOSTING DVWA KE RAILWAY

## STEP 1: Persiapan (5 menit)

### ✅ Buat GitHub Repository

1. **Buat akun GitHub** (jika belum punya)
   - Pergi ke https://github.com/signup
   - Daftar dengan email

2. **Buat repository baru**
   - Klik tombol "+" → "New repository"
   - Nama: `dvwalast`
   - Pilih "Public"
   - Jangan initialize dengan README (kita sudah punya)
   - Klik "Create repository"

3. **Copy Git URL**
   - Setelah create, copy URL: `https://github.com/yourusername/dvwalast.git`

---

## STEP 2: Push Code ke GitHub (5 menit)

Buka Terminal/PowerShell di folder `d:\laragon\www\dvwalast`:

```powershell
# 1. Initialize git
git init

# 2. Configure git (set once)
git config --global user.name "Your Name"
git config --global user.email "your.email@example.com"

# 3. Add all files
git add .

# 4. Initial commit
git commit -m "Initial DVWA Lab commit"

# 5. Add remote origin (ganti dengan URL GitHub Anda)
git remote add origin https://github.com/YOURUSERNAME/dvwalast.git

# 6. Rename branch to main (jika perlu)
git branch -M main

# 7. Push ke GitHub
git push -u origin main
```

**Masukkan GitHub credentials saat diminta**

---

## STEP 3: Daftar Railway (2 menit)

1. **Pergi ke https://railway.app**
2. **Klik "Start for free"**
3. **Login dengan GitHub** (recommended)
4. **Authorize Railway** untuk akses repositories

---

## STEP 4: Deploy ke Railway (3 menit)

### Method A: Via Dashboard (Recommended)

1. **Buka Railway Dashboard**
   - https://dashboard.railway.app

2. **Klik "New Project"**

3. **Pilih "Deploy from GitHub repo"**

4. **Cari & pilih `dvwalast` repository**
   - Jika tidak terlihat, klik "Configure GitHub App"
   - Pilih organization & repository
   - Klik "Install"

5. **Railway akan auto-detect `docker-compose.yml`**
   - Status akan berubah ke "Building..."
   - Tunggu 3-5 menit hingga "Live"

6. **Klik "Open App"** untuk akses

### Method B: Via Railway CLI

```powershell
# 1. Login ke Railway
railway login

# 2. Masuk folder project
cd d:\laragon\www\dvwalast

# 3. Initialize Railway project
railway init

# 4. Deploy
railway up

# 5. Cek status
railway status

# 6. Lihat logs
railway logs

# 7. Open di browser
railway open
```

---

## STEP 5: Akses Aplikasi (Selesai! 🎉)

Setelah deploy sukses, Railway akan memberikan **public URL** seperti:
```
https://dvwalast-prod-abc123.up.railway.app
```

### Akses DVWA:

**Indonesian Version:**
```
https://dvwalast-prod-abc123.up.railway.app/login_id.php
```

**English Version:**
```
https://dvwalast-prod-abc123.up.railway.app/login.php
```

### Credentials:
```
Username: admin
Password: admin123
```

---

## STEP 6: Setup Auto-Deploy (Optional)

Railway bisa auto-deploy setiap kali Anda push ke GitHub:

1. **Pergi ke Railway Project Settings**
2. **Toggle "Auto Deploy"** → ON
3. **Sekarang setiap push ke GitHub otomatis deploy**

```powershell
# Contoh: Update code dan auto-deploy
git add .
git commit -m "Fix something"
git push origin main

# Railway akan otomatis build & deploy
```

---

## TROUBLESHOOTING

### ❌ Error: "Port not exposed"
**Solusi:** Railway auto-detect port dari docker-compose.yml
- Pastikan `ports: - "8000:80"` ada di docker-compose.yml ✓

### ❌ Error: "Database connection failed"
**Solusi:**
1. Tunggu 2 menit untuk database siap
2. Check Railway Logs: Project → Deployments → Logs
3. Verifikasi DB credentials di environment variables

### ❌ Error: "502 Bad Gateway"
**Solusi:**
1. Check logs di Railway dashboard
2. Pastikan Dockerfile dan docker-compose.yml valid
3. Restart deployment dari Railway UI

### ❌ Git push rejected
**Solusi:**
```powershell
# Force update jika ada conflict
git pull origin main --rebase
git push origin main -f
```

---

## MONITORING & LOGS

### View Logs di Railway:
1. **Buka Project Dashboard**
2. **Tab "Deployments"**
3. **Pilih deployment terbaru**
4. **Tab "Logs"** untuk real-time logs

### Useful Commands:
```powershell
# View Railway status
railway status

# View logs
railway logs

# Check environment variables
railway variables

# Set environment variable
railway variables set DB_HOST=mysql
```

---

## CUSTOM DOMAIN (Optional)

Railway support custom domain:

1. **Domain sudah dibeli** (GoDaddy, Netlify, dll)

2. **Di Railway Dashboard:**
   - Pilih Project → Settings
   - Klik "Custom Domain"
   - Masukkan domain Anda

3. **Update DNS:**
   - Di provider domain (GoDaddy, Netlify, dll)
   - Buat CNAME record
   - Point ke Railway URL

---

## COST

### Free Tier
- **$5/bulan credit** (cukup untuk development)
- MySQL database included
- PHP + Apache running 24/7

### Estimasi:
- PHP app: ~$1-2/bulan
- MySQL database: ~$1-2/bulan
- **Total: $2-4/bulan** (dalam free tier)

### Jika over quota:
- Pay-as-you-go: $0.50/jam per service
- Bisa set "spending limit" untuk avoid surprise charges

---

## BEST PRACTICES

✅ **Enable Auto-Deploy** untuk continuous deployment
✅ **Monitor Logs** secara regular untuk errors
✅ **Backup Database** menggunakan Railway UI
✅ **Test locally** sebelum push ke production
✅ **Use .gitignore** untuk sensitive files

---

## UPDATE CODE (Setelah Deploy)

Jika Anda ingin update code:

```powershell
# 1. Edit files locally
# ... make changes ...

# 2. Commit & push
git add .
git commit -m "Update XSS lab"
git push origin main

# 3. Railway auto-deploy (jika enabled)
# Atau manual: Railway Dashboard → Redeploy

# 4. Akses di https://yourdomain.up.railway.app
```

---

## ROLLBACK (Kembali ke versi sebelumnya)

Jika ada masalah:

1. **Railway Dashboard**
2. **Deployments tab**
3. **Pilih deployment sebelumnya**
4. **Klik "Redeploy"**

---

## BACKUP DATABASE

Railway auto-backup, tapi Anda bisa manual backup:

```sql
-- Export database
mysqldump -h host -u dvwa -p dvwa > backup.sql

-- Import database
mysql -h host -u dvwa -p dvwa < backup.sql
```

---

## SELESAI! 🎉

DVWA sudah live di internet dan accessible dari mana saja!

### Tips Selanjutnya:
1. **Share URL dengan teman untuk collaborative learning**
2. **Test semua vulnerability labs**
3. **Dokumentasikan exploitation techniques**
4. **Practice defensive coding untuk fix vulnerabilities**

---

**Questions?** Check Railway docs: https://docs.railway.app

**Happy Testing! 🔥**
