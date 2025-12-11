#!/bin/bash

echo "📱 Building Android APK for USSIBATIK Absen..."

# Navigate to android directory
cd android-app

# Clean previous builds
echo "🧹 Cleaning previous builds..."
./gradlew clean

# Build debug APK
echo "🔨 Building debug APK..."
./gradlew assembleDebug

# Build release APK
echo "🚀 Building release APK..."
./gradlew assembleRelease

# Show results
echo ""
echo "✅ APK Build Complete!"
echo ""
echo "📁 APK Locations:"
echo "   Debug APK:   app/build/outputs/apk/debug/app-debug.apk"
echo "   Release APK: app/build/outputs/apk/release/app-release.apk"
echo ""
echo "📊 APK Info:"
if [ -f "app/build/outputs/apk/debug/app-debug.apk" ]; then
    DEBUG_SIZE=$(du -h app/build/outputs/apk/debug/app-debug.apk | cut -f1)
    echo "   Debug APK: $DEBUG_SIZE"
else
    echo "   Debug APK: Build failed"
fi

if [ -f "app/build/outputs/apk/release/app-release.apk" ]; then
    RELEASE_SIZE=$(du -h app/build/outputs/apk/release/app-release.apk | cut -f1)
    echo "   Release APK: $RELEASE_SIZE"
else
    echo "   Release APK: Build failed"
fi

echo ""
echo "🔧 Next Steps:"
echo "   1. Test APK on Android device"
echo "   2. Sign APK for Play Store (if needed)"
echo "   3. Distribute APK to users"

cd ..