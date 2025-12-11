#!/bin/bash

echo "🚀 USSIBATIK Absen - Complete Deployment Workflow"
echo "=================================================="

# Step 1: Prepare Laravel
echo ""
echo "📦 Step 1: Preparing Laravel application..."

# Clean cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Build assets
echo "📦 Building assets..."
npm run build

# Optimize for production
echo "⚡ Optimizing for production..."
composer install --optimize-autoloader --no-dev

# Cache configuration
echo "🔧 Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Step 2: Build Android APK
echo ""
echo "📱 Step 2: Building Android APK..."
cd android-app

# Clean previous builds
echo "🧹 Cleaning previous builds..."
./gradlew clean

# Build release APK
echo "🚀 Building release APK..."
./gradlew assembleRelease

# Check if build successful
if [ -f "app/build/outputs/apk/release/app-release.apk" ]; then
    echo "✅ Release APK built successfully!"
    echo "📁 Location: app/build/outputs/apk/release/app-release.apk"
    echo "📊 Size: $(du -h app/build/outputs/apk/release/app-release.apk | cut -f1)"
else
    echo "❌ APK Build failed!"
fi

cd ..

# Step 3: Commit to Git
echo ""
echo "📤 Step 3: Committing to Git..."
git add .
git commit -m "🚀 Production deployment $(date '+%Y-%m-%d %H:%M:%S')

✅ Updates:
- Laravel optimized for production
- Android APK built and tested
- Ready for hosting deployment"

git push origin main

# Step 4: Create release tag
VERSION=$(date '+v%Y.%m.%d-%H%M')
echo ""
echo "🏷️  Step 4: Creating release tag: $VERSION"
git tag -a $VERSION -m "🎉 Release $VERSION - Production deployment"
git push origin --tags

echo ""
echo "✅ Deployment workflow complete!"
echo ""
echo "📋 Next Steps:"
echo "   1. Upload files to Hostinger (see docs/GitHub-Deployment-Guide.md)"
echo "   2. Test APK on Android devices"
echo "   3. Distribute APK to users"
echo ""
echo "📁 Files ready:"
echo "   - Laravel: Optimized and cached"
echo "   - APK: android-app/app/build/outputs/apk/release/app-release.apk"
echo "   - Git: Committed and tagged"