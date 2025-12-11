#!/bin/bash

echo "🧪 Testing Android APK..."

# Check if ADB is available
if ! command -v adb &> /dev/null; then
    echo "❌ ADB not found. Please install Android SDK Platform Tools."
    echo "   Download: https://developer.android.com/studio/releases/platform-tools"
    exit 1
fi

# Check connected devices
echo "📱 Checking connected devices..."
DEVICES=$(adb devices | grep -v "List of devices" | grep "device$" | wc -l)

if [ $DEVICES -eq 0 ]; then
    echo "❌ No Android devices connected."
    echo "   1. Enable Developer Options on your Android device"
    echo "   2. Enable USB Debugging"
    echo "   3. Connect device via USB"
    echo "   4. Run 'adb devices' to verify connection"
    exit 1
fi

echo "✅ Found $DEVICES connected device(s)"
adb devices

# Install APK
APK_PATH="android-app/app/build/outputs/apk/release/app-release.apk"

if [ -f "$APK_PATH" ]; then
    echo ""
    echo "📲 Installing APK..."
    adb install -r "$APK_PATH"
    
    if [ $? -eq 0 ]; then
        echo "✅ APK installed successfully!"
        
        echo ""
        echo "🚀 Starting app..."
        adb shell am start -n com.ussibatik.absen/.MainActivity
        
        echo ""
        echo "✅ App started successfully!"
        echo ""
        echo "🧪 Manual Testing Checklist:"
        echo "   - [ ] App opens without crashes"
        echo "   - [ ] Login functionality works"
        echo "   - [ ] Camera permission granted"
        echo "   - [ ] Location permission granted"
        echo "   - [ ] Attendance capture works"
        echo "   - [ ] Navigation works smoothly"
        echo "   - [ ] Back button behavior correct"
        echo "   - [ ] WebView loads properly"
        echo "   - [ ] All features accessible"
        echo ""
        echo "📱 Device Info:"
        adb shell getprop ro.build.version.release
        adb shell getprop ro.product.model
    else
        echo "❌ APK installation failed!"
        echo "   Check device permissions and try again"
    fi
else
    echo "❌ APK not found at: $APK_PATH"
    echo "   Run ./build-apk.sh first"
fi