# ⚡ Quick Commands - USSIBATIK Absen

## 🚀 **ONE-CLICK DEPLOYMENT**

### ✅ **Complete Deployment (Recommended)**
```powershell
# Deploy everything: Laravel + APK + Git push
.\deploy-all.ps1

# Custom deployment with message
.\deploy-all.ps1 -CommitMessage "✨ New feature: Enhanced mobile UI"

# Deploy without tests (faster)
.\deploy-all.ps1 -SkipTests

# Deploy only Laravel (no APK)
.\deploy-all.ps1 -BuildAPK:$false
```

### ✅ **APK Only**
```powershell
# Build release APK
.\build-apk.ps1

# Build and install on device
.\build-apk.ps1 -Install

# Build with analysis
.\build-apk.ps1 -Analyze

# Clean build
.\build-apk.ps1 -Clean
```

---

## 📱 **ANDROID COMMANDS**

### ✅ **Manual APK Build**
```powershell
cd android-app
./gradlew clean
./gradlew assembleRelease
cd ..
```

### ✅ **APK Testing**
```powershell
# Install APK
adb install ussibatik-absen-release.apk

# Check connected devices
adb devices

# View app logs
adb logcat | findstr "UssiBatik"
```

---

## 🌐 **LARAVEL COMMANDS**

### ✅ **Production Optimization**
```powershell
# Install dependencies
composer install --optimize-autoloader --no-dev

# Build assets
npm run build

# Cache everything
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### ✅ **Development**
```powershell
# Install dev dependencies
composer install
npm install

# Start development
php artisan serve
npm run dev
```

### ✅ **Database**
```powershell
# Run migrations
php artisan migrate

# Fresh migration with seeders
php artisan migrate:fresh --seed

# Create backup
php artisan backup:run
```

---

## 📝 **GIT COMMANDS**

### ✅ **Quick Git Operations**
```powershell
# Quick commit and push
git add .
git commit -m "🚀 Update"
git push origin main

# Create release tag
git tag -a v1.0.0 -m "🎉 Release v1.0.0"
git push origin v1.0.0

# Check status
git status
git log --oneline -10
```

### ✅ **Branch Management**
```powershell
# Create feature branch
git checkout -b feature/new-feature
git push -u origin feature/new-feature

# Merge to main
git checkout main
git merge feature/new-feature
git push origin main
```

---

## 🧪 **TESTING COMMANDS**

### ✅ **Laravel Tests**
```powershell
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=AttendanceTest

# Run with coverage
php artisan test --coverage
```

### ✅ **Android Tests**
```powershell
cd android-app
./gradlew test
./gradlew connectedAndroidTest
cd ..
```

---

## 🔧 **MAINTENANCE COMMANDS**

### ✅ **Clear Caches**
```powershell
# Laravel caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Composer cache
composer clear-cache

# NPM cache
npm cache clean --force
```

### ✅ **File Permissions (Linux/Mac)**
```bash
# Set correct permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### ✅ **File Permissions (Windows)**
```powershell
# Usually not needed on Windows, but if required:
icacls storage /grant Everyone:F /T
icacls bootstrap\cache /grant Everyone:F /T
```

---

## 📊 **MONITORING COMMANDS**

### ✅ **System Health**
```powershell
# Check Laravel
php artisan about
php artisan route:list
php artisan migrate:status

# Check Android build
cd android-app
./gradlew tasks
./gradlew dependencies
cd ..
```

### ✅ **Performance**
```powershell
# Laravel performance
php artisan optimize
php artisan config:cache
php artisan route:cache

# Check APK size
Get-Item ussibatik-absen-release.apk | Select-Object Name, @{Name="Size(MB)";Expression={[math]::Round($_.Length/1MB,2)}}
```

---

## 🚨 **EMERGENCY COMMANDS**

### ✅ **Quick Fixes**
```powershell
# Reset everything
git reset --hard HEAD
composer install
npm install
php artisan migrate:fresh --seed

# Emergency deployment
.\deploy-all.ps1 -SkipTests -CommitMessage "🚨 Emergency fix"

# Rollback migration
php artisan migrate:rollback

# Restore from backup
php artisan backup:restore
```

### ✅ **Debug Mode**
```powershell
# Enable debug (development only!)
# In .env: APP_DEBUG=true

# View logs
Get-Content storage/logs/laravel.log -Tail 50

# Clear all caches
php artisan optimize:clear
```

---

## 📋 **CHECKLISTS**

### ✅ **Before Deployment**
```powershell
# Run this checklist
php artisan test                    # ✅ Tests pass
.\build-apk.ps1 -Test              # ✅ APK builds and tests
git status                         # ✅ All changes committed
php artisan about                  # ✅ System healthy
```

### ✅ **After Deployment**
```powershell
# Verify deployment
curl https://yourdomain.com        # ✅ Site accessible
php artisan migrate:status         # ✅ Database up to date
php artisan queue:work --once      # ✅ Queues working
```

---

## 🎯 **MOST USED COMMANDS**

### ✅ **Daily Development**
```powershell
# Start development
php artisan serve & npm run dev

# Quick test
php artisan test --filter=FeatureTest

# Quick commit
git add . && git commit -m "Update" && git push
```

### ✅ **Weekly Deployment**
```powershell
# Full deployment
.\deploy-all.ps1 -CommitMessage "🚀 Weekly deployment"

# Create release
git tag -a v1.$(Get-Date -Format "MMdd") -m "Weekly release"
git push origin --tags
```

### ✅ **Monthly Maintenance**
```powershell
# Update dependencies
composer update
npm update

# Clean everything
php artisan optimize:clear
composer clear-cache
npm cache clean --force

# Full test
php artisan test
.\build-apk.ps1 -Test -Analyze
```

---

## 💡 **PRO TIPS**

### ✅ **Aliases (PowerShell Profile)**
```powershell
# Add to $PROFILE
function Deploy { .\deploy-all.ps1 @args }
function BuildAPK { .\build-apk.ps1 @args }
function LaravelTest { php artisan test @args }
function GitQuick { git add .; git commit -m $args[0]; git push }

# Usage:
Deploy
BuildAPK -Install
LaravelTest --filter=UserTest
GitQuick "Quick fix"
```

### ✅ **Batch Operations**
```powershell
# Multiple commands in sequence
php artisan optimize:clear; composer install; npm run build; php artisan test

# Conditional execution
php artisan test && .\build-apk.ps1 && git push
```

---

**⚡ SAVE TIME WITH THESE QUICK COMMANDS!**

*Bookmark this file for instant access to all essential commands.*