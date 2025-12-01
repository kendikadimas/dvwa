# Panduan Hosting DVWA ke Railway

## Prasyarat
- GitHub account
- Railway account (daftar gratis di https://railway.app)
- Docker installed (sudah ada)

## Step 1: Push ke GitHub

```bash
# Initialize git repository (jika belum ada)
git init
git add .
git commit -m "Initial DVWA commit"

# Create repository di GitHub, lalu:
git remote add origin https://github.com/yourusername/dvwalast.git
git branch -M main
git push -u origin main
```

## Step 2: Buka Railway Dashboard

1. Pergi ke https://railway.app
2. Login dengan GitHub account
3. Klik "New Project"

## Step 3: Connect GitHub Repository

1. Klik "Deploy from GitHub repo"
2. Pilih repository `dvwalast` Anda
3. Railway akan otomatis detect Docker Compose

## Step 4: Configure Environment Variables

Railway akan automatically detect dari docker-compose.yml:
- `DB_HOST` → mysql
- `DB_USER` → dvwa
- `DB_PASSWORD` → dvwa123
- `DB_NAME` → dvwa

**Manual setup (jika diperlukan):**
1. Pergi ke "Variables" tab
2. Tambahkan environment variables:
   ```
   DB_HOST=mysql
   DB_USER=dvwa
   DB_PASSWORD=dvwa123
   DB_NAME=dvwa
   ```

## Step 5: Deploy

1. Railway akan otomatis build dan deploy
2. Tunggu hingga status "Live" (biasanya 3-5 menit)
3. Klik "Open App" untuk akses aplikasi

## Step 6: Akses DVWA

URL akan menjadi seperti: `https://dvwalast-prod.up.railway.app`

Login dengan:
- **Indonesian:** `/login_id.php`
- **English:** `/login.php`
- Username: `admin`
- Password: `admin123`

---

## Troubleshooting

### Error: Port not exposed
Update docker-compose.yml port yang lebih tinggi:
```yaml
ports:
  - "8000:80"  ← Railway akan auto-detect port
```

### Error: Database connection failed
1. Periksa Railway MySQL status
2. Tunggu 1-2 menit untuk database initialization
3. Check logs: Railway Dashboard → Logs tab

### Error: Permission denied
Update file permissions:
```bash
chmod -R 755 app/
```

---

## Cost

**Free Tier:**
- $5/bulan credit
- MySQL 5GB
- Cukup untuk development/testing

**Jika mau upgrade:**
Railway offer pay-as-you-go mulai dari $0.50/jam

---

## Tips

✅ Enable "Auto Deploy" - otomatis deploy saat push ke main branch
✅ Setup domain custom (optional) - Railway support custom domain
✅ Enable "Persistent Volume" untuk MySQL data

---

## Alternatif: Deploy dengan Railway CLI

```bash
# 1. Install Railway CLI
npm install -g @railway/cli

# 2. Login
railway login

# 3. Create project
railway init

# 4. Deploy
railway up

# 5. View logs
railway logs

# 6. Open in browser
railway open
```

Selesai! 🚀
