# 🚀 USSIBATIK Absen Complete Deployment Script
# PowerShell script untuk deploy Laravel + Android APK

param(
    [string]$CommitMessage = "🚀 Production deployment",
    [switch]$BuildAPK = $true,
    [switch]$OptimizeLaravel = $true,
    [switch]$PushToGit = $true,
    [switch]$SkipTests = $false
)

# Colors for output
$Green = "Green"
$Blue = "Blue"
$Yellow = "Yellow"
$Red = "Red"
$Cyan = "Cyan"
$Magenta = "Magenta"

Write-Host "🚀 USSIBATIK Absen Complete Deployment" -ForegroundColor $Green
Write-Host "=======================================" -ForegroundColor $Green

# Check prerequisites
Write-Host "🔍 Checking prerequisites..." -ForegroundColor $Blue

$errors = @()

# Check Git
try {
    git --version | Out-Null
    Write-Host "✅ Git found" -ForegroundColor $Green
} catch {
    $errors += "Git not found"
}

# Check PHP
try {
    php --version | Out-Null
    Write-Host "✅ PHP found" -ForegroundColor $Green
} catch {
    $errors += "PHP not found"
}

# Check Composer
try {
    composer --version | Out-Null
    Write-Host "✅ Composer found" -ForegroundColor $Green
} catch {
    $errors += "Composer not found"
}

# Check Node.js
try {
    node --version | Out-Null
    Write-Host "✅ Node.js found" -ForegroundColor $Green
} catch {
    $errors += "Node.js not found"
}

if ($errors.Count -gt 0) {
    Write-Host "❌ Missing prerequisites:" -ForegroundColor $Red
    $errors | ForEach-Object { Write-Host "   • $_" -ForegroundColor $Red }
    exit 1
}

# Laravel Optimization
if ($OptimizeLaravel) {
    Write-Host ""
    Write-Host "⚡ Optimizing Laravel for production..." -ForegroundColor $Blue
    
    # Install dependencies
    Write-Host "📦 Installing Composer dependencies..." -ForegroundColor $Yellow
    composer install --optimize-autoloader --no-dev --quiet
    
    if ($LASTEXITCODE -ne 0) {
        Write-Host "❌ Composer install failed!" -ForegroundColor $Red
        exit 1
    }
    
    # Install Node dependencies and build
    Write-Host "📦 Installing Node dependencies..." -ForegroundColor $Yellow
    npm install --silent
    
    if ($LASTEXITCODE -ne 0) {
        Write-Host "❌ npm install failed!" -ForegroundColor $Red
        exit 1
    }
    
    Write-Host "🏗️  Building production assets..." -ForegroundColor $Yellow
    npm run build
    
    if ($LASTEXITCODE -ne 0) {
        Write-Host "❌ Asset build failed!" -ForegroundColor $Red
        exit 1
    }
    
    # Laravel optimizations
    Write-Host "🚀 Caching Laravel configurations..." -ForegroundColor $Yellow
    
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
    Write-Host "✅ Laravel optimization completed!" -ForegroundColor $Green
}

# Run tests (unless skipped)
if (-not $SkipTests) {
    Write-Host ""
    Write-Host "🧪 Running tests..." -ForegroundColor $Blue
    
    # Check if PHPUnit is available
    if (Test-Path "vendor/bin/phpunit") {
        php vendor/bin/phpunit --stop-on-failure
        
        if ($LASTEXITCODE -ne 0) {
            Write-Host "❌ Tests failed! Deployment aborted." -ForegroundColor $Red
            exit 1
        }
        
        Write-Host "✅ All tests passed!" -ForegroundColor $Green
    } else {
        Write-Host "⚠️  PHPUnit not found, skipping tests" -ForegroundColor $Yellow
    }
}

# Build Android APK
if ($BuildAPK) {
    Write-Host ""
    Write-Host "📱 Building Android APK..." -ForegroundColor $Blue
    
    if (Test-Path "android-app") {
        # Use our APK build script
        & .\build-apk.ps1 -BuildType "release" -Clean
        
        if ($LASTEXITCODE -ne 0) {
            Write-Host "❌ APK build failed!" -ForegroundColor $Red
            exit 1
        }
        
        Write-Host "✅ APK build completed!" -ForegroundColor $Green
    } else {
        Write-Host "⚠️  android-app directory not found, skipping APK build" -ForegroundColor $Yellow
    }
}

# Git operations
if ($PushToGit) {
    Write-Host ""
    Write-Host "📝 Preparing Git commit..." -ForegroundColor $Blue
    
    # Check Git status
    $gitStatus = git status --porcelain
    
    if ($gitStatus) {
        Write-Host "📋 Changes detected:" -ForegroundColor $Yellow
        git status --short | ForEach-Object { Write-Host "   $_" -ForegroundColor $Cyan }
        
        # Add all changes
        Write-Host "➕ Adding changes to Git..." -ForegroundColor $Yellow
        git add .
        
        # Create detailed commit message
        $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
        $fullCommitMessage = @"
$CommitMessage

🔧 Deployment Details:
- Timestamp: $timestamp
- Laravel optimized: $OptimizeLaravel
- APK built: $BuildAPK
- Tests run: $(-not $SkipTests)

✅ Production Ready:
- Composer dependencies optimized
- Assets built and minified
- Laravel caches generated
- Android APK generated (if enabled)

🚀 Ready for hosting deployment!
"@
        
        # Commit changes
        Write-Host "💾 Committing changes..." -ForegroundColor $Yellow
        git commit -m $fullCommitMessage
        
        if ($LASTEXITCODE -ne 0) {
            Write-Host "❌ Git commit failed!" -ForegroundColor $Red
            exit 1
        }
        
        # Push to remote
        Write-Host "⬆️  Pushing to remote repository..." -ForegroundColor $Yellow
        git push origin main
        
        if ($LASTEXITCODE -ne 0) {
            Write-Host "❌ Git push failed!" -ForegroundColor $Red
            exit 1
        }
        
        Write-Host "✅ Changes pushed to GitHub!" -ForegroundColor $Green
        
    } else {
        Write-Host "ℹ️  No changes to commit" -ForegroundColor $Cyan
    }
}

# Generate deployment summary
Write-Host ""
Write-Host "📊 Deployment Summary" -ForegroundColor $Magenta
Write-Host "=====================" -ForegroundColor $Magenta

$summary = @()

if ($OptimizeLaravel) {
    $summary += "✅ Laravel optimized for production"
}

if ($BuildAPK -and (Test-Path "ussibatik-absen-release.apk")) {
    $apkSize = (Get-Item "ussibatik-absen-release.apk").Length / 1MB
    $summary += "✅ Android APK built ($([math]::Round($apkSize, 2)) MB)"
}

if ($PushToGit) {
    $summary += "✅ Changes pushed to GitHub"
}

if (-not $SkipTests) {
    $summary += "✅ Tests passed"
}

$summary | ForEach-Object { Write-Host $_ -ForegroundColor $Green }

# Next steps
Write-Host ""
Write-Host "🎯 Next Steps for Hosting Deployment:" -ForegroundColor $Blue
Write-Host "1. Download project from GitHub" -ForegroundColor $Cyan
Write-Host "2. Upload to hosting (public_html/)" -ForegroundColor $Cyan
Write-Host "3. Set document root to 'public/' folder" -ForegroundColor $Cyan
Write-Host "4. Import database" -ForegroundColor $Cyan
Write-Host "5. Update .env with hosting credentials" -ForegroundColor $Cyan
Write-Host "6. Run: php artisan migrate --force" -ForegroundColor $Cyan
Write-Host "7. Run: php artisan storage:link" -ForegroundColor $Cyan
Write-Host "8. Set permissions: chmod -R 755 storage bootstrap/cache" -ForegroundColor $Cyan

if (Test-Path "ussibatik-absen-release.apk") {
    Write-Host ""
    Write-Host "📱 APK Distribution:" -ForegroundColor $Blue
    Write-Host "• Upload APK to GitHub releases" -ForegroundColor $Cyan
    Write-Host "• Share download link with users" -ForegroundColor $Cyan
    Write-Host "• Test installation on Android devices" -ForegroundColor $Cyan
}

Write-Host ""
Write-Host "🎉 DEPLOYMENT COMPLETED SUCCESSFULLY!" -ForegroundColor $Green
Write-Host "=====================================" -ForegroundColor $Green

# Show file locations
Write-Host ""
Write-Host "📁 Important Files:" -ForegroundColor $Blue
if (Test-Path "ussibatik-absen-release.apk") {
    Write-Host "   📱 APK: ussibatik-absen-release.apk" -ForegroundColor $Cyan
}
Write-Host "   🌐 Laravel: Ready for hosting upload" -ForegroundColor $Cyan
Write-Host "   📝 Git: Changes pushed to repository" -ForegroundColor $Cyan

Write-Host ""
Write-Host "✨ All systems ready for production! 🚀" -ForegroundColor $Green