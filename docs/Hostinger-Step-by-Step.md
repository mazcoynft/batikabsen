# 🌐 Hostinger Deployment - Step by Step Guide

## 🚨 **MASALAH YANG ANDA ALAMI**

Anda mengalami error `npm: command not found` di Hostinger karena **Node.js tidak tersedia di shared hosting**. Ini normal dan ada solusinya!

---

## ✅ **SOLUSI LENGKAP**

### 🏠 **STEP 1: Persiapan di Local (Windows)**

```powershell
# Jalankan script persiapan Hostinger
.\prepare-hostinger.ps1

# Script ini akan:
# ✅ Build assets di local (npm run build)
# ✅ Install dependencies production
# ✅ Cache Laravel configurations
# ✅ Siapkan folder upload yang ready
```

### 📤 **STEP 2: Upload ke Hostinger**

#### A. **Compress Upload Package**
```powershell
# Zip folder hostinger-upload
Compress-Archive -Path hostinger-upload -DestinationPath ussibatik-absen-hostinger.zip
```

#### B. **Upload via File Manager**
1. Login ke **hPanel Hostinger**
2. Go to **File Manager**
3. Navigate to `public_html/`
4. **Upload** `ussibatik-absen-hostinger.zip`
5. **Extract** the ZIP file
6. **Move** all contents from `hostinger-upload/` to `public_html/`

### 🔧 **STEP 3: Konfigurasi di Hostinger**

#### A. **Set Document Root**
1. Di **hPanel** → **Advanced** → **Subdomains**
2. Edit your domain
3. Set **Document Root** ke: `public_html/public`
4. **Save**

#### B. **Database Setup**
1. **hPanel** → **Databases** → **MySQL Databases**
2. **Create** new database
3. **Create** database user
4. **Assign** user to database
5. **Note** credentials untuk .env

#### C. **Environment Configuration**
1. Di **File Manager**, rename `.env.production` → `.env`
2. **Edit** `.env` file:
```env
APP_NAME="USSIBATIK Absen"
APP_ENV=production
APP_KEY=base64:GENERATE_NEW_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u719590579_ussibatik  # Your actual DB name
DB_USERNAME=u719590579_admin      # Your actual DB user
DB_PASSWORD=your_secure_password  # Your actual DB password
```

### 🗄️ **STEP 4: Database Import**

#### A. **Export dari Local**
```powershell
# Export database dari local
mysqldump -u root -p ussibatik_absen > ussibatik_backup.sql
```

#### B. **Import ke Hostinger**
1. **hPanel** → **Databases** → **phpMyAdmin**
2. Select your database
3. **Import** tab
4. **Choose** `ussibatik_backup.sql`
5. **Go**

### ⚡ **STEP 5: Final Commands**

#### Via SSH (jika tersedia):
```bash
cd domains/demopos.io  # atau domain Anda
php artisan key:generate
php artisan migrate --force
php artisan storage:link
chmod -R 755 storage bootstrap/cache
```

#### Via File Manager Terminal:
```bash
# Generate APP_KEY
php artisan key:generate

# Run migrations
php artisan migrate --force

# Create storage link
php artisan storage:link

# Set permissions (jika diperlukan)
find storage -type f -exec chmod 644 {} \;
find storage -type d -exec chmod 755 {} \;
find bootstrap/cache -type f -exec chmod 644 {} \;
find bootstrap/cache -type d -exec chmod 755 {} \;
```

---

## 🎯 **STRUKTUR FILE DI HOSTINGER**

```
public_html/
├── app/
├── bootstrap/
├── config/
├── database/
├── resources/
├── routes/
├── storage/
├── vendor/
├── public/          ← Document root points here
│   ├── index.php
│   ├── css/         ← Built assets
│   ├── js/          ← Built assets
│   └── images/
├── .env             ← Renamed from .env.production
├── artisan
└── composer.json
```

---

## 🚨 **TROUBLESHOOTING COMMON ISSUES**

### ❌ **500 Internal Server Error**
```bash
# Check error logs
tail -f storage/logs/laravel.log

# Common fixes:
chmod -R 755 storage bootstrap/cache
php artisan config:clear
php artisan cache:clear
```

### ❌ **Database Connection Error**
```bash
# Test database connection
php artisan tinker
# In tinker:
DB::connection()->getPdo();
```

### ❌ **Storage Link Issues**
```bash
# Remove existing link and recreate
rm public/storage
php artisan storage:link
```

### ❌ **Permission Issues**
```bash
# Set correct permissions
find . -type f -name "*.php" -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod -R 755 storage bootstrap/cache
```

---

## ✅ **VERIFICATION CHECKLIST**

### 🌐 **Website Testing**
- [ ] Homepage loads: `https://yourdomain.com`
- [ ] Login page accessible: `https://yourdomain.com/login`
- [ ] Admin panel works: `https://yourdomain.com/admin`
- [ ] No 500 errors in browser console
- [ ] CSS/JS assets loading correctly

### 📱 **Mobile Testing**
- [ ] Responsive design works
- [ ] Touch interactions smooth
- [ ] Camera permission prompts (if applicable)
- [ ] Location services work
- [ ] Forms submit correctly

### 🔧 **Admin Panel Testing**
- [ ] Dashboard widgets load
- [ ] CRUD operations work
- [ ] File uploads successful
- [ ] Reports generate correctly
- [ ] User management functional

---

## 📞 **SUPPORT & NEXT STEPS**

### 🆘 **If You Need Help**
1. **Check Hostinger Knowledge Base**
2. **Contact Hostinger Support** (24/7 chat)
3. **Check Laravel logs**: `storage/logs/laravel.log`
4. **Test database connection** via phpMyAdmin

### 🚀 **After Successful Deployment**
1. **Test all features thoroughly**
2. **Set up SSL certificate** (usually automatic)
3. **Configure email settings** for notifications
4. **Set up regular backups**
5. **Monitor error logs** regularly

### 📱 **APK Distribution**
1. **Upload APK** to your hosting
2. **Create download page**: `https://yourdomain.com/download`
3. **Share link** with users
4. **Test installation** on various devices

---

## 🎉 **SUCCESS INDICATORS**

### ✅ **Deployment Successful When:**
- Website loads without errors
- Login system works
- Admin panel accessible
- Database operations successful
- File uploads work
- Mobile responsive
- SSL certificate active

### 📊 **Performance Optimization**
```bash
# After deployment, optimize:
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

---

## 🔄 **FUTURE UPDATES**

### 📝 **Update Workflow**
1. **Make changes locally**
2. **Test thoroughly**
3. **Run**: `.\prepare-hostinger.ps1`
4. **Upload new package**
5. **Replace files** on server
6. **Run migrations** if needed

### 🔄 **Quick Updates**
```bash
# For small changes, just upload specific files:
# - app/ (for code changes)
# - public/css/ public/js/ (for asset changes)
# - database/migrations/ (for new migrations)
```

---

**🎯 FOLLOW THIS GUIDE STEP BY STEP FOR SUCCESSFUL HOSTINGER DEPLOYMENT!**

*Node.js tidak diperlukan di server karena assets sudah di-build di local.*