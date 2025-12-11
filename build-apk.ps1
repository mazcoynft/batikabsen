# 📱 USSIBATIK Absen APK Builder
# PowerShell script untuk build APK dengan berbagai opsi

param(
    [string]$BuildType = "release",
    [switch]$Clean = $false,
    [switch]$Install = $false,
    [switch]$Test = $false,
    [switch]$Analyze = $false
)

# Colors for output
$Green = "Green"
$Blue = "Blue"
$Yellow = "Yellow"
$Red = "Red"
$Cyan = "Cyan"

Write-Host "🚀 USSIBATIK Absen APK Builder" -ForegroundColor $Green
Write-Host "=================================" -ForegroundColor $Green

# Check if we're in the right directory
if (-not (Test-Path "android-app")) {
    Write-Host "❌ Error: android-app directory not found!" -ForegroundColor $Red
    Write-Host "Please run this script from the project root directory." -ForegroundColor $Yellow
    exit 1
}

# Navigate to Android project
Write-Host "📁 Navigating to android-app directory..." -ForegroundColor $Blue
Set-Location android-app

# Check Java version
Write-Host "☕ Checking Java version..." -ForegroundColor $Blue
try {
    $javaVersion = java -version 2>&1 | Select-String "version" | Select-Object -First 1
    Write-Host "Java: $javaVersion" -ForegroundColor $Cyan
} catch {
    Write-Host "⚠️  Warning: Java not found in PATH" -ForegroundColor $Yellow
}

# Clean if requested
if ($Clean) {
    Write-Host "🧹 Cleaning previous builds..." -ForegroundColor $Yellow
    ./gradlew clean
    if ($LASTEXITCODE -ne 0) {
        Write-Host "❌ Clean failed!" -ForegroundColor $Red
        Set-Location ..
        exit 1
    }
}

# Build APK
Write-Host "📱 Building $BuildType APK..." -ForegroundColor $Blue
Write-Host "⏱️  This may take a few minutes..." -ForegroundColor $Yellow

$buildCommand = if ($BuildType -eq "debug") { "assembleDebug" } else { "assembleRelease" }
$buildArgs = @($buildCommand, "--parallel", "--build-cache")

if ($Analyze) {
    $buildArgs += "--scan"
}

$startTime = Get-Date
& ./gradlew @buildArgs

if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Build failed!" -ForegroundColor $Red
    Set-Location ..
    exit 1
}

$endTime = Get-Date
$buildTime = $endTime - $startTime

# Determine APK path
$apkPath = if ($BuildType -eq "debug") {
    "app/build/outputs/apk/debug/app-debug.apk"
} else {
    "app/build/outputs/apk/release/app-release.apk"
}

# Check if build successful
if (Test-Path $apkPath) {
    Write-Host "✅ APK built successfully!" -ForegroundColor $Green
    Write-Host "📍 Location: $apkPath" -ForegroundColor $Cyan
    
    # Get APK info
    $apkFile = Get-Item $apkPath
    $size = $apkFile.Length / 1MB
    Write-Host "📦 Size: $([math]::Round($size, 2)) MB" -ForegroundColor $Cyan
    Write-Host "⏱️  Build time: $($buildTime.Minutes)m $($buildTime.Seconds)s" -ForegroundColor $Cyan
    Write-Host "📅 Created: $($apkFile.LastWriteTime)" -ForegroundColor $Cyan
    
    # Copy APK to root directory for easy access
    $rootApkPath = "../ussibatik-absen-$BuildType.apk"
    Copy-Item $apkPath $rootApkPath -Force
    Write-Host "📋 Copied to: ussibatik-absen-$BuildType.apk" -ForegroundColor $Cyan
    
    # Install if requested
    if ($Install) {
        Write-Host "📲 Installing APK..." -ForegroundColor $Yellow
        
        # Check if device connected
        $devices = adb devices 2>$null | Select-String "device$"
        if ($devices.Count -eq 0) {
            Write-Host "⚠️  No Android device connected!" -ForegroundColor $Yellow
            Write-Host "Connect device and enable USB debugging to install." -ForegroundColor $Yellow
        } else {
            Write-Host "📱 Found $($devices.Count) connected device(s)" -ForegroundColor $Cyan
            adb install -r $apkPath
            if ($LASTEXITCODE -eq 0) {
                Write-Host "✅ APK installed successfully!" -ForegroundColor $Green
            } else {
                Write-Host "❌ Installation failed!" -ForegroundColor $Red
            }
        }
    }
    
    # Test if requested
    if ($Test) {
        Write-Host "🧪 Running basic tests..." -ForegroundColor $Yellow
        
        # Check APK signature
        Write-Host "🔐 Checking APK signature..." -ForegroundColor $Blue
        try {
            $signature = jarsigner -verify -verbose $apkPath 2>&1
            if ($signature -match "jar verified") {
                Write-Host "✅ APK signature valid" -ForegroundColor $Green
            } else {
                Write-Host "⚠️  APK signature check failed" -ForegroundColor $Yellow
            }
        } catch {
            Write-Host "⚠️  Could not verify APK signature" -ForegroundColor $Yellow
        }
        
        # Check APK contents
        Write-Host "📦 APK contents summary:" -ForegroundColor $Blue
        try {
            $contents = & "C:\Program Files\7-Zip\7z.exe" l $apkPath 2>$null | Select-String "\.dex|\.so|AndroidManifest"
            $contents | ForEach-Object { Write-Host "  $_" -ForegroundColor $Cyan }
        } catch {
            Write-Host "  (7-Zip not found - cannot analyze APK contents)" -ForegroundColor $Yellow
        }
    }
    
    # Show next steps
    Write-Host ""
    Write-Host "🎉 BUILD COMPLETED SUCCESSFULLY!" -ForegroundColor $Green
    Write-Host "=================================" -ForegroundColor $Green
    Write-Host "📱 APK ready for distribution:" -ForegroundColor $Blue
    Write-Host "   • File: ussibatik-absen-$BuildType.apk" -ForegroundColor $Cyan
    Write-Host "   • Size: $([math]::Round($size, 2)) MB" -ForegroundColor $Cyan
    Write-Host ""
    Write-Host "📋 Next steps:" -ForegroundColor $Blue
    Write-Host "   • Test APK on Android device" -ForegroundColor $Cyan
    Write-Host "   • Upload to GitHub releases" -ForegroundColor $Cyan
    Write-Host "   • Distribute to users" -ForegroundColor $Cyan
    
} else {
    Write-Host "❌ Build failed - APK not found!" -ForegroundColor $Red
    Set-Location ..
    exit 1
}

# Return to root directory
Set-Location ..

Write-Host ""
Write-Host "✨ Script completed successfully!" -ForegroundColor $Green