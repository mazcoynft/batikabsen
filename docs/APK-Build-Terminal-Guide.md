# 📱 APK Build Terminal Guide - USSIBATIK Absen

## 🚀 **COMPLETE APK BUILD WORKFLOW**

### 🎯 **Overview**
Panduan lengkap untuk generate APK Android via terminal dengan berbagai opsi build dan optimasi.

---

## 🔧 **PREREQUISITES**

### ✅ **Required Tools**
```powershell
# Check Java version (Required: Java 17 for Android 15)
java -version

# Check Android SDK (if using Android Studio)
$env:ANDROID_HOME
$env:JAVA_HOME

# Check Gradle
cd android-app
./gradlew --version
```

### ✅ **Environment Setup**
```powershell
# Set JAVA_HOME (if not set)
$env:JAVA_HOME = "C:\Program Files\Java\jdk-17"

# Set ANDROID_HOME (if using Android Studio)
$env:ANDROID_HOME = "C:\Users\YourName\AppData\Local\Android\Sdk"

# Add to PATH
$env:PATH += ";$env:JAVA_HOME\bin;$env:ANDROID_HOME\tools;$env:ANDROID_HOME\platform-tools"
```

---

## 📱 **APK BUILD COMMANDS**

### ✅ **Basic Build Commands**
```powershell
# Navigate to Android project
cd android-app

# Clean previous builds
./gradlew clean

# Build debug APK (for testing)
./gradlew assembleDebug

# Build release APK (for production)
./gradlew assembleRelease

# Build all variants
./gradlew assemble
```

### ✅ **Advanced Build Options**
```powershell
# Build with specific configuration
./gradlew assembleRelease --info

# Build with parallel execution
./gradlew assembleRelease --parallel

# Build with offline mode (faster if dependencies cached)
./gradlew assembleRelease --offline

# Build with refresh dependencies
./gradlew assembleRelease --refresh-dependencies

# Build with detailed logging
./gradlew assembleRelease --debug
```

---

## 🔍 **BUILD VARIANTS**

### ✅ **Debug Build**
```powershell
# Debug APK (for development/testing)
./gradlew assembleDebug

# Output location:
# android-app/app/build/outputs/apk/debug/app-debug.apk

# Features:
# - Debuggable
# - Not minified
# - Larger file size
# - Faster build time
```

### ✅ **Release Build**
```powershell
# Release APK (for production)
./gradlew assembleRelease

# Output location:
# android-app/app/build/outputs/apk/release/app-release.apk

# Features:
# - Optimized
# - Minified (ProGuard/R8)
# - Smaller file size
# - Production ready
```

---

## 🛠️ **BUILD OPTIMIZATION**

### ✅ **Performance Optimization**
```powershell
# Enable parallel builds
./gradlew assembleRelease --parallel --max-workers=4

# Use build cache
./gradlew assembleRelease --build-cache

# Enable configuration cache
./gradlew assembleRelease --configuration-cache

# Combine all optimizations
./gradlew assembleRelease --parallel --build-cache --configuration-cache
```

### ✅ **Memory Optimization**
```powershell
# Set JVM memory options
$env:GRADLE_OPTS = "-Xmx4g -XX:MaxMetaspaceSize=512m"

# Build with memory optimization
./gradlew assembleRelease -Dorg.gradle.jvmargs="-Xmx4g -XX:MaxMetaspaceSize=512m"
```

---

## 🔐 **SIGNING APK (Production)**

### ✅ **Generate Keystore**
```powershell
# Create keystore for signing
keytool -genkey -v -keystore ussibatik-absen.keystore -alias ussibatik -keyalg RSA -keysize 2048 -validity 10000

# Store keystore securely (don't commit to Git!)
```

### ✅ **Configure Signing**
```gradle
// In android-app/app/build.gradle
android {
    signingConfigs {
        release {
            storeFile file('path/to/ussibatik-absen.keystore')
            storePassword 'your_store_password'
            keyAlias 'ussibatik'
            keyPassword 'your_key_password'
        }
    }
    
    buildTypes {
        release {
            signingConfig signingConfigs.release
            minifyEnabled true
            proguardFiles getDefaultProguardFile('proguard-android-optimize.txt'), 'proguard-rules.pro'
        }
    }
}
```

### ✅ **Build Signed APK**
```powershell
# Build signed release APK
./gradlew assembleRelease

# Verify APK signature
jarsigner -verify -verbose -certs app-release.apk
```

---

## 📊 **BUILD ANALYSIS**

### ✅ **APK Analysis**
```powershell
# Analyze APK size
./gradlew analyzeReleaseApk

# Generate build report
./gradlew assembleRelease --scan

# Check APK contents
unzip -l app-release.apk

# APK size optimization report
./gradlew assembleRelease -Pandroid.enableR8.fullMode=true
```

### ✅ **Build Performance**
```powershell
# Build with profiling
./gradlew assembleRelease --profile

# Build scan (detailed analysis)
./gradlew assembleRelease --scan

# Check build time
Measure-Command { ./gradlew assembleRelease }
```

---

## 🧪 **TESTING APK**

### ✅ **Install APK**
```powershell
# Install via ADB (Android Debug Bridge)
adb install app-release.apk

# Install and replace existing
adb install -r app-release.apk

# Uninstall app
adb uninstall com.ussibatik.absen
```

### ✅ **APK Testing**
```powershell
# List connected devices
adb devices

# Install and launch
adb install app-release.apk
adb shell am start -n com.ussibatik.absen/.MainActivity

# Check app logs
adb logcat | findstr "UssiBatik"
```

---

## 🔄 **AUTOMATED BUILD SCRIPTS**

### ✅ **PowerShell Build Script**
```powershell
# build-apk.ps1
param(
    [string]$BuildType = "release",
    [switch]$Clean = $false,
    [switch]$Install = $false
)

Write-Host "🚀 Building USSIBATIK Absen APK..." -ForegroundColor Green

# Navigate to Android project
Set-Location android-app

# Clean if requested
if ($Clean) {
    Write-Host "🧹 Cleaning previous builds..." -ForegroundColor Yellow
    ./gradlew clean
}

# Build APK
Write-Host "📱 Building $BuildType APK..." -ForegroundColor Blue
if ($BuildType -eq "debug") {
    ./gradlew assembleDebug --parallel --build-cache
    $apkPath = "app/build/outputs/apk/debug/app-debug.apk"
} else {
    ./gradlew assembleRelease --parallel --build-cache
    $apkPath = "app/build/outputs/apk/release/app-release.apk"
}

# Check if build successful
if (Test-Path $apkPath) {
    Write-Host "✅ APK built successfully!" -ForegroundColor Green
    Write-Host "📍 Location: $apkPath" -ForegroundColor Cyan
    
    # Get APK size
    $size = (Get-Item $apkPath).Length / 1MB
    Write-Host "📦 Size: $([math]::Round($size, 2)) MB" -ForegroundColor Cyan
    
    # Install if requested
    if ($Install) {
        Write-Host "📲 Installing APK..." -ForegroundColor Yellow
        adb install -r $apkPath
    }
} else {
    Write-Host "❌ Build failed!" -ForegroundColor Red
    exit 1
}

# Return to root directory
Set-Location ..
```

### ✅ **Batch Build Script**
```batch
@echo off
echo 🚀 Building USSIBATIK Absen APK...

cd android-app

echo 🧹 Cleaning previous builds...
gradlew.bat clean

echo 📱 Building release APK...
gradlew.bat assembleRelease --parallel --build-cache

if exist "app\build\outputs\apk\release\app-release.apk" (
    echo ✅ APK built successfully!
    echo 📍 Location: app\build\outputs\apk\release\app-release.apk
) else (
    echo ❌ Build failed!
    pause
    exit /b 1
)

cd ..
pause
```

---

## 🚨 **TROUBLESHOOTING**

### ✅ **Common Build Issues**
```powershell
# Java version issues
java -version
# Should show Java 17

# Gradle daemon issues
./gradlew --stop
./gradlew assembleRelease

# Permission issues (Linux/Mac)
chmod +x gradlew

# Memory issues
$env:GRADLE_OPTS = "-Xmx4g"

# Clean and rebuild
./gradlew clean build --refresh-dependencies
```

### ✅ **Build Errors**
```powershell
# Dependency issues
./gradlew assembleRelease --refresh-dependencies

# Cache issues
./gradlew clean
Remove-Item -Recurse -Force .gradle
./gradlew assembleRelease

# SDK issues
# Update Android SDK in Android Studio
# Or set ANDROID_HOME correctly
```

---

## 📋 **BUILD CHECKLIST**

### ✅ **Pre-Build**
- [ ] Java 17 installed and configured
- [ ] Android SDK available
- [ ] Gradle wrapper executable
- [ ] Clean previous builds
- [ ] Update app version in build.gradle

### ✅ **Build Process**
- [ ] Run `./gradlew clean`
- [ ] Run `./gradlew assembleRelease`
- [ ] Verify APK generated
- [ ] Check APK size (should be reasonable)
- [ ] Test APK installation

### ✅ **Post-Build**
- [ ] Test APK on device/emulator
- [ ] Verify all features work
- [ ] Check app permissions
- [ ] Test WebView functionality
- [ ] Backup APK file

---

## 📱 **APK DISTRIBUTION**

### ✅ **Distribution Methods**
1. **Direct Installation**:
   ```powershell
   adb install app-release.apk
   ```

2. **File Sharing**:
   - Upload to cloud storage
   - Share download link
   - Email to users

3. **GitHub Releases**:
   - Create release on GitHub
   - Upload APK as asset
   - Add release notes

4. **Internal Distribution**:
   - Company file server
   - Internal app store
   - QR code for download

---

## 🎯 **QUICK COMMANDS REFERENCE**

```powershell
# Essential commands
cd android-app
./gradlew clean                    # Clean builds
./gradlew assembleDebug           # Debug APK
./gradlew assembleRelease         # Release APK
./gradlew assembleRelease --info  # Detailed output

# Advanced commands
./gradlew assembleRelease --parallel --build-cache  # Optimized build
./gradlew assembleRelease --scan                    # Build analysis
./gradlew analyzeReleaseApk                         # APK analysis

# Testing commands
adb devices                       # List devices
adb install app-release.apk      # Install APK
adb logcat | findstr "UssiBatik" # View logs
```

---

**📱 APK BUILD READY!**

*Use this guide to build professional Android APKs via terminal with optimal performance and security.*