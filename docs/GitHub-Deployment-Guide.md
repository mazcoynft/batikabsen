# 🚀 GitHub Deployment Guide - USSIBATIK Absen

## 📋 **COMPLETE DEPLOYMENT WORKFLOW**

### 🎯 **Overview**
Panduan lengkap untuk upload project ke GitHub dan deploy ke hosting dengan cara yang efisien dan terorganisir.

---

## 🔧 **PREREQUISITES**

### ✅ **Required Tools**
```powershell
# Check if Git is installed
git --version

# Check if Node.js is installed
node --version
npm --version

# Check if Composer is installed
composer --version

# Check if PHP is installed
php --version
```

### ✅ **GitHub Setup**
1. **Create GitHub Account** (jika belum ada)
2. **Create New Repository**:
   - Repository name: `ussibatik-absen`
   - Description: `Sistem Absensi USSIBATIK - Laravel + Android`
   - Set to **Private** (recommended for production)
   - Don't initialize with README (kita sudah punya)

---

## 📁 **PROJECT PREPARATION**

### ✅ **Clean Project Structure**
```powershell
# Remove unnecessary files
Remove-Item -Recurse -Force node_modules -ErrorAction SilentlyContinue
Remove-Item -Recurse -Force vendor -ErrorAction SilentlyContinue
Remove-Item -Recurse -Force storage/logs/* -ErrorAction SilentlyContinue
Remove-Item -Recurse -Force bootstrap/cache/* -ErrorAction SilentlyContinue

# Clean Android build files
Remove-Item -Recurse -Force android-app/app/build -ErrorAction SilentlyContinue
Remove-Item -Recurse -Force android-app/.gradle -ErrorAction SilentlyContinue
```

### ✅ **Environment Files**
```powershell
# Copy .env.example to .env.production
Copy-Item .env.example .env.production

# Edit .env.production for production settings
# Set APP_ENV=production, APP_DEBUG=false, etc.
```

---

## 🔄 **GIT INITIALIZATION & UPLOAD**

### ✅ **Initialize Git Repository**
```powershell
# Initialize git (if not already done)
git init

# Add all files
git add .

# First commit
git commit -m "🚀 Initial commit: USSIBATIK Absen System

✅ Features:
- Laravel backend with Filament admin
- Mobile-responsive frontend
- Android APK ready (API 29-35)
- Complete attendance system
- Leave management
- Overtime & duty management
- Comprehensive reporting

🔧 Tech Stack:
- Laravel 10
- Filament 3
- Android (Kotlin)
- MySQL
- Tailwind CSS

📱 Android Support:
- Min SDK: 29 (Android 10)
- Target SDK: 35 (Android 15)
- WebView optimized
- Modern permissions"
```

### ✅ **Connect to GitHub**
```powershell
# Add GitHub remote (replace with your repository URL)
git remote add origin https://github.com/yourusername/ussibatik-absen.git

# Push to GitHub
git branch -M main
git push -u origin main
```

---

## 📦 **AUTOMATED DEPLOYMENT SCRIPTS**

### ✅ **Create .gitignore (if not exists)**
```gitignore
# Laravel
/node_modules
/public/hot
/public/storage
/storage/*.key
/vendor
.env
.env.backup
.phpunit.result.cache
docker-compose.override.yml
Homestead.json
Homestead.yaml
npm-debug.log
yarn-error.log
/.idea
/.vscode

# Android
android-app/.gradle/
android-app/app/build/
android-app/build/
android-app/local.properties
android-app/.idea/
android-app/*.iml
android-app/captures/
android-app/.externalNativeBuild/
android-app/.cxx/

# OS
.DS_Store
Thumbs.db

# Logs
storage/logs/*
!storage/logs/.gitkeep
bootstrap/cache/*
!bootstrap/cache/.gitkeep
```

---

## 🚀 **DEPLOYMENT WORKFLOW**

### ✅ **Development to Production**
```powershell
# 1. Prepare for production
composer install --optimize-autoloader --no-dev
npm run build

# 2. Cache optimization
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 3. Commit changes
git add .
git commit -m "🔧 Production optimization: cached configs and built assets"

# 4. Push to GitHub
git push origin main
```

### ✅ **Hostinger Deployment Steps**
1. **Download from GitHub**:
   - Go to your repository
   - Click "Code" → "Download ZIP"
   - Extract to your computer

2. **Upload to Hostinger**:
   - Use File Manager or FTP
   - Upload all files to `public_html/`
   - Set document root to `public/` folder

3. **Database Setup**:
   - Create MySQL database in Hostinger
   - Import your database
   - Update `.env` with Hostinger database credentials

4. **Final Setup**:
   ```bash
   php artisan migrate --force
   php artisan storage:link
   chmod -R 755 storage bootstrap/cache
   ```

---

## 📱 **ANDROID APK DEPLOYMENT**

### ✅ **APK Build Process**
```powershell
# Navigate to Android project
cd android-app

# Clean previous builds
./gradlew clean

# Build release APK
./gradlew assembleRelease

# APK location
# android-app/app/build/outputs/apk/release/app-release.apk
```

### ✅ **APK Distribution**
1. **GitHub Releases**:
   - Go to your repository
   - Click "Releases" → "Create a new release"
   - Upload APK file
   - Add release notes

2. **Direct Distribution**:
   - Upload APK to your hosting
   - Create download link
   - Share with users

---

## 🔐 **SECURITY CONSIDERATIONS**

### ✅ **Sensitive Files**
```powershell
# Never commit these files:
# .env (contains database passwords)
# storage/logs/* (may contain sensitive data)
# vendor/ (can be regenerated)
# node_modules/ (can be regenerated)
```

### ✅ **Production Environment**
```env
# .env.production example
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=your_production_db
DB_USERNAME=your_db_user
DB_PASSWORD=your_secure_password

# Generate new key for production
APP_KEY=base64:your_production_key_here
```

---

## 🔄 **CONTINUOUS DEPLOYMENT**

### ✅ **Update Workflow**
```powershell
# 1. Make changes locally
# 2. Test thoroughly
# 3. Commit changes
git add .
git commit -m "✨ Feature: Description of changes"

# 4. Push to GitHub
git push origin main

# 5. Download and deploy to hosting
# 6. Run migrations if needed
php artisan migrate --force
```

### ✅ **Version Management**
```powershell
# Create version tags
git tag -a v1.0.0 -m "🎉 Release v1.0.0: Initial production release"
git push origin v1.0.0

# List all versions
git tag -l
```

---

## 📊 **MONITORING & MAINTENANCE**

### ✅ **Health Checks**
```powershell
# Check application status
php artisan about

# Check database connection
php artisan migrate:status

# Clear caches if needed
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### ✅ **Backup Strategy**
1. **Database Backup**:
   - Regular MySQL dumps
   - Store in secure location

2. **File Backup**:
   - Backup `storage/app/` folder
   - Backup uploaded files

3. **Code Backup**:
   - GitHub serves as code backup
   - Tag important releases

---

## 🚨 **TROUBLESHOOTING**

### ✅ **Common Issues**
```powershell
# Permission issues
chmod -R 755 storage bootstrap/cache

# Storage link issues
php artisan storage:link

# Cache issues
php artisan cache:clear
php artisan config:clear

# Composer issues
composer dump-autoload
```

### ✅ **Android Build Issues**
```powershell
# Clean Android build
cd android-app
./gradlew clean
./gradlew build --refresh-dependencies

# Check Java version
java -version
# Should be Java 17 for Android 15 support
```

---

## 📞 **SUPPORT & RESOURCES**

### ✅ **Documentation Links**
- [Laravel Deployment](https://laravel.com/docs/deployment)
- [Hostinger Laravel Guide](https://www.hostinger.com/tutorials/how-to-install-laravel-on-hostinger)
- [Android Build Guide](https://developer.android.com/studio/build/building-cmdline)

### ✅ **Quick Commands Reference**
```powershell
# Laravel
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan migrate --force

# Android
cd android-app && ./gradlew assembleRelease

# Git
git add . && git commit -m "Update" && git push origin main
```

---

## 🎉 **DEPLOYMENT CHECKLIST**

### ✅ **Before Upload**
- [ ] Test application locally
- [ ] Update .env.production
- [ ] Build production assets (`npm run build`)
- [ ] Optimize Composer (`--no-dev --optimize-autoloader`)
- [ ] Test Android APK
- [ ] Update documentation

### ✅ **After Upload**
- [ ] Set correct file permissions
- [ ] Run migrations
- [ ] Create storage link
- [ ] Test all functionality
- [ ] Monitor error logs
- [ ] Create database backup

---

**🚀 READY FOR DEPLOYMENT!**

*Follow this guide step by step for smooth deployment to GitHub and hosting.*