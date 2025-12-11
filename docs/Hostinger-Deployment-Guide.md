# 🌐 Hostinger Deployment Guide - USSIBATIK Absen

## 🚨 **HOSTINGER SHARED HOSTING SOLUTION**

### ⚠️ **Problem Identified**
```bash
npm: command not found
node: command not found
```

**Hostinger shared hosting tidak memiliki Node.js/NPM terinstall!**

---

## ✅ **SOLUTION: BUILD LOCALLY, UPLOAD COMPILED**

### 🔧 **Step 1: Local Build Process**

#### **Build Assets Locally (Windows)**
```powershell
# Navigate to project directory
cd your-project-directory

# Install dependencies locally
npm install

# Build production assets
npm run build

# This creates compiled files in public/build/
```

#### **Optimize Laravel Locally**
```powershell
# Install production dependencies
composer install --optimize-autoloader --no-dev

# Cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Generate optimized autoloader
composer dump-autoload --optimize
```

---

## 📦 **Step 2: Prepare Upload Package**

### ✅ **Create Deployment Package**
```powershell
# Create deployment script
.\prepare-hostinger-upload.ps1
```

<function_calls>
<invoke name="fsWrite">
<parameter name="path">prepare-hostinger-upload.ps1 🌐 Hostinger Upload Preparation Script
# Prepares Laravel project for Hostinger shared hosting

param(
    [string]$OutputDir = "hostinger-upload"
)

$Green = "Green"
$Blue = "Blue"
$Yellow = "Yellow"
$Red = "Red"
$Cyan = "Cyan"

Write-Host "🌐 Preparing USSIBATIK Absen for Hostinger Upload" -ForegroundColor $Green
Write-Host "=================================================" -ForegroundColor $Green

# Check if we're in Laravel project
if (-not (Test-Path "artisan")) {
    Write-Host "❌ Error: Not in Laravel project directory!" -ForegroundColor $Red
    exit 1
}

# Create output directory
if (Test-Path $OutputDir) {
    Write-Host "🗑️  Removing existing upload directory..." -ForegroundColor $Yellow
    Remove-Item -Recurse -Force $OutputDir
}

Write-Host "📁 Creating upload directory: $OutputDir" -ForegroundColor $Blue
New-Item -ItemType Directory -Path $OutputDir | Out-Null

# Build assets locally
Write-Host "🏗️  Building production assets..." -ForegroundColor $Blue
npm install --silent
npm run build

if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Asset build failed!" -ForegroundColor $Red
    exit 1
}

# Install production dependencies
Write-Host "📦 Installing production dependencies..." -ForegroundColor $Blue
composer install --optimize-autoloader --no-dev --quiet

if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Composer install failed!" -ForegroundColor $Red
    exit 1
}

# Cache Laravel configurations
Write-Host "⚡ Caching Laravel configurations..." -ForegroundColor $Blue
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Copy necessary files and directories
Write-Host "📋 Copying files to upload directory..." -ForegroundColor $Blue

$filesToCopy = @(
    "app",
    "bootstrap", 
    "config",
    "database",
    "public",
    "resources",
    "routes",
    "storage",
    "vendor",
    "artisan",
    "composer.json",
    "composer.lock"
)

foreach ($item in $filesToCopy) {
    if (Test-Path $item) {
        Write-Host "  📄 Copying $item..." -ForegroundColor $Cyan
        if (Test-Path $item -PathType Container) {
            Copy-Item -Recurse $item "$OutputDir/$item"
        } else {
            Copy-Item $item "$OutputDir/$item"
        }
    }
}

# Create .env.production template
Write-Host "📝 Creating .env.production template..." -ForegroundColor $Blue
$envTemplate = @"
APP_NAME="USSIBATIK Absen"
APP_ENV=production
APP_KEY=base64:GENERATE_NEW_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_hostinger_db_name
DB_USERNAME=your_hostinger_db_user
DB_PASSWORD=your_hostinger_db_password

BROADCAST_DRIVER=log
CACHE_DRIVER=database
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
SESSION_DRIVER=database
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=your_email@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@yourdomain.com
MAIL_FROM_NAME="`${APP_NAME}"
"@

$envTemplate | Out-File -FilePath "$OutputDir/.env.production" -Encoding UTF8

# Create deployment instructions
Write-Host "📋 Creating deployment instructions..." -ForegroundColor $Blue
$instructions = @"
# 🌐 HOSTINGER DEPLOYMENT INSTRUCTIONS

## 📤 UPLOAD STEPS:

1. **Compress Upload Directory**
   - Zip the entire '$OutputDir' folder
   - Upload ZIP to Hostinger File Manager
   - Extract in public_html/

2. **Configure Environment**
   - Rename .env.production to .env
   - Update database credentials in .env
   - Generate new APP_KEY: php artisan key:generate

3. **Set Document Root**
   - In Hostinger hPanel
   - Go to Advanced → Subdomains
   - Set document root to: public_html/public

4. **Database Setup**
   - Create MySQL database in hPanel
   - Import your database backup
   - Update .env with database credentials

5. **Final Commands** (via SSH or File Manager terminal):
   ```bash
   cd public_html
   php artisan migrate --force
   php artisan storage:link
   chmod -R 755 storage bootstrap/cache
   ```

## 🔧 TROUBLESHOOTING:

- **500 Error**: Check file permissions, .env configuration
- **Database Error**: Verify credentials, database exists
- **Storage Issues**: Run php artisan storage:link
- **Cache Issues**: Delete bootstrap/cache/* files

## ✅ VERIFICATION:

- [ ] Website loads at your domain
- [ ] Login system works
- [ ] Admin panel accessible
- [ ] File uploads work
- [ ] Database operations successful

---
Generated: $(Get-Date)
Project: USSIBATIK Absen
Target: Hostinger Shared Hosting
"@

$instructions | Out-File -FilePath "$OutputDir/DEPLOYMENT_INSTRUCTIONS.txt" -Encoding UTF8

# Create .htaccess for public directory (if not exists)
$htaccessPath = "$OutputDir/public/.htaccess"
if (-not (Test-Path $htaccessPath)) {
    Write-Host "📝 Creating .htaccess file..." -ForegroundColor $Blue
    $htaccess = @"
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
"@
    $htaccess | Out-File -FilePath $htaccessPath -Encoding UTF8
}

# Get directory size
$size = (Get-ChildItem -Recurse $OutputDir | Measure-Object -Property Length -Sum).Sum / 1MB

Write-Host ""
Write-Host "✅ HOSTINGER UPLOAD PACKAGE READY!" -ForegroundColor $Green
Write-Host "=================================" -ForegroundColor $Green
Write-Host "📁 Package location: $OutputDir" -ForegroundColor $Cyan
Write-Host "📦 Package size: $([math]::Round($size, 2)) MB" -ForegroundColor $Cyan
Write-Host ""
Write-Host "📋 Next steps:" -ForegroundColor $Blue
Write-Host "1. Zip the '$OutputDir' folder" -ForegroundColor $Cyan
Write-Host "2. Upload to Hostinger File Manager" -ForegroundColor $Cyan
Write-Host "3. Extract in public_html/" -ForegroundColor $Cyan
Write-Host "4. Follow DEPLOYMENT_INSTRUCTIONS.txt" -ForegroundColor $Cyan
Write-Host ""
Write-Host "📄 Important files created:" -ForegroundColor $Blue
Write-Host "• .env.production (rename to .env)" -ForegroundColor $Cyan
Write-Host "• DEPLOYMENT_INSTRUCTIONS.txt" -ForegroundColor $Cyan
Write-Host "• public/.htaccess" -ForegroundColor $Cyan

Write-Host ""
Write-Host "🎉 Ready for Hostinger deployment!" -ForegroundColor $Green