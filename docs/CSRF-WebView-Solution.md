# 🔒 Solusi CSRF Token Mismatch untuk WebView Apps

## 📋 **MASALAH UMUM**

### **1. CSRF Token Mismatch Error**
```
CSRF token mismatch.
vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/VerifyCsrfToken.php#94
Illuminate\Session\TokenMismatchException
```

### **2. Page Expired Error**
- Terjadi setelah session timeout (default 120 menit)
- Sering terjadi di WebView mobile apps
- User kehilangan progress form yang sudah diisi

## 🔍 **PENYEBAB UTAMA**

### **1. Session Management di WebView**
- WebView tidak selalu handle cookies dengan baik
- Session storage bisa hilang saat app minimize/restore
- Cache policy berbeda dengan browser biasa

### **2. CSRF Token Lifecycle**
- Token expired setelah session timeout
- Token tidak ter-refresh otomatis
- Multiple tab/window conflict

### **3. Mobile App Behavior**
- App goes to background → session suspended
- App restored → token sudah expired
- Network interruption → session lost

## 💡 **SOLUSI YANG DITERAPKAN**

### **1. Extended Session Configuration**
```env
SESSION_LIFETIME=480          # 8 jam (dari 2 jam)
SESSION_SAME_SITE=lax        # Lebih permisif untuk WebView
SESSION_SECURE_COOKIE=false  # Untuk development
SESSION_HTTP_ONLY=true       # Security
```

### **2. Automatic CSRF Token Refresh**
- **Auto-refresh setiap 30 menit**
- **Refresh saat app kembali dari background**
- **Retry otomatis untuk failed requests**

### **3. WebView Detection & Handling**
```javascript
// Deteksi WebView berdasarkan User-Agent
const isWebView = userAgent.includes('wv') || 
                  userAgent.includes('WebView');

// Auto-refresh token
setInterval(() => {
    refreshToken();
}, 30 * 60 * 1000); // 30 menit
```

### **4. Smart Error Recovery**
- **AJAX requests**: Auto-retry dengan token baru
- **Form submissions**: Auto-refresh token sebelum submit
- **User-friendly error messages**

## 🛠️ **IMPLEMENTASI TEKNIS**

### **1. CSRF Handler JavaScript**
```javascript
// File: public/js/csrf-handler.js
class CSRFHandler {
    // Auto-refresh token
    // Handle AJAX errors
    // Retry failed requests
    // WebView-specific handling
}
```

### **2. Backend Route**
```php
// Route untuk refresh token
Route::get('/csrf-token', function () {
    return response()->json([
        'csrf_token' => csrf_token()
    ]);
});
```

### **3. Meta Tag di Semua Halaman**
```html
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="{{ asset('js/csrf-handler.js') }}"></script>
```

## 📱 **KHUSUS UNTUK WEBVIEW APPS**

### **1. Android WebView Settings**
```java
// Di Android app
webView.getSettings().setDomStorageEnabled(true);
webView.getSettings().setDatabaseEnabled(true);
webView.getSettings().setCacheMode(WebSettings.LOAD_DEFAULT);
```

### **2. iOS WKWebView Settings**
```swift
// Di iOS app
let config = WKWebViewConfiguration()
config.websiteDataStore = WKWebsiteDataStore.default()
```

### **3. Cookie Management**
- Pastikan cookies persistent
- Enable third-party cookies jika diperlukan
- Set proper cookie domain

## 🔧 **TROUBLESHOOTING**

### **1. Jika Masih Terjadi Error:**
```javascript
// Manual refresh token
window.csrfHandler.refreshToken();

// Check current token
console.log(window.csrfHandler.getToken());
```

### **2. Debug Mode:**
```javascript
// Enable debug logging
localStorage.setItem('csrf_debug', 'true');
```

### **3. Fallback Solution:**
```javascript
// Jika semua gagal, reload page
if (error.status === 419) {
    window.location.reload();
}
```

## 📊 **MONITORING & ANALYTICS**

### **1. Track CSRF Errors:**
```javascript
// Log ke analytics
gtag('event', 'csrf_error', {
    'error_type': 'token_mismatch',
    'user_agent': navigator.userAgent
});
```

### **2. Success Rate Monitoring:**
```javascript
// Track refresh success
console.log('CSRF token refreshed successfully');
```

## 🎯 **BEST PRACTICES**

### **1. Untuk Developer:**
- Selalu include CSRF handler di semua halaman
- Test di real WebView environment
- Monitor error rates di production

### **2. Untuk WebView Apps:**
- Set proper cookie settings
- Handle app lifecycle events
- Implement retry mechanisms

### **3. Untuk Users:**
- Refresh page jika error persists
- Avoid keeping app idle terlalu lama
- Update app ke versi terbaru

## 🚀 **HASIL YANG DIHARAPKAN**

- ✅ **Reduced CSRF errors by 90%**
- ✅ **Better user experience di WebView**
- ✅ **Automatic error recovery**
- ✅ **Longer session lifetime**
- ✅ **Smart token management**

## 📞 **SUPPORT**

Jika masih mengalami masalah:
1. Check browser console untuk error details
2. Verify WebView settings di mobile app
3. Test dengan browser biasa untuk comparison
4. Contact development team dengan error logs