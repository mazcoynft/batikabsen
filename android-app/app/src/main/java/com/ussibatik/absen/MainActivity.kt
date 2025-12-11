package com.ussibatik.absen

import android.Manifest
import android.annotation.SuppressLint
import android.content.pm.PackageManager
import android.graphics.Bitmap
import android.os.Build
import android.os.Bundle
import android.view.KeyEvent
import android.view.View
import android.webkit.*
import android.widget.Toast
import androidx.activity.result.contract.ActivityResultContracts
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.ContextCompat
import androidx.core.splashscreen.SplashScreen.Companion.installSplashScreen
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout
import com.ussibatik.absen.databinding.ActivityMainBinding

class MainActivity : AppCompatActivity() {
    
    private lateinit var binding: ActivityMainBinding
    private var webView: WebView? = null
    private var swipeRefreshLayout: SwipeRefreshLayout? = null
    
    // Base URL - Change this to your server URL
    private val baseUrl = "http://localhost:8000" // Change to your production URL
    
    // Permission launcher
    private val requestPermissionLauncher = registerForActivityResult(
        ActivityResultContracts.RequestMultiplePermissions()
    ) { permissions ->
        val allGranted = permissions.entries.all { it.value }
        if (allGranted) {
            loadWebView()
        } else {
            Toast.makeText(this, "Permissions required for app to work properly", Toast.LENGTH_LONG).show()
            finish()
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        // Install splash screen
        installSplashScreen()
        
        super.onCreate(savedInstanceState)
        
        binding = ActivityMainBinding.inflate(layoutInflater)
        setContentView(binding.root)
        
        setupViews()
        checkPermissions()
    }
    
    private fun setupViews() {
        webView = binding.webView
        swipeRefreshLayout = binding.swipeRefreshLayout
        
        // Setup swipe refresh
        swipeRefreshLayout?.setOnRefreshListener {
            webView?.reload()
        }
        
        // Setup WebView
        setupWebView()
    }
    
    private fun checkPermissions() {
        val permissions = mutableListOf(
            Manifest.permission.CAMERA,
            Manifest.permission.ACCESS_FINE_LOCATION,
            Manifest.permission.ACCESS_COARSE_LOCATION
        )

        // Add storage permissions based on Android version
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            // Android 13+ (API 33+)
            permissions.addAll(arrayOf(
                Manifest.permission.READ_MEDIA_IMAGES,
                Manifest.permission.READ_MEDIA_VIDEO,
                Manifest.permission.POST_NOTIFICATIONS
            ))
        } else if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.Q) {
            // Android 10-12 (API 29-32)
            permissions.add(Manifest.permission.READ_EXTERNAL_STORAGE)
        }

        val permissionsToRequest = permissions.filter {
            ContextCompat.checkSelfPermission(this, it) != PackageManager.PERMISSION_GRANTED
        }

        if (permissionsToRequest.isNotEmpty()) {
            requestPermissionLauncher.launch(permissionsToRequest.toTypedArray())
        } else {
            loadWebView()
        }
    }
    
    @SuppressLint("SetJavaScriptEnabled")
    private fun setupWebView() {
        webView?.apply {
            settings.apply {
                // Enable JavaScript
                javaScriptEnabled = true
                
                // Enable DOM storage
                domStorageEnabled = true
                
                // Enable database
                databaseEnabled = true
                
                // Enable app cache
                setAppCacheEnabled(true)
                
                // Enable zoom controls
                setSupportZoom(true)
                builtInZoomControls = true
                displayZoomControls = false
                
                // Enable viewport
                useWideViewPort = true
                loadWithOverviewMode = true
                
                // Enable mixed content
                mixedContentMode = WebSettings.MIXED_CONTENT_ALWAYS_ALLOW
                
                // Cache mode
                cacheMode = WebSettings.LOAD_DEFAULT
                
                // User agent for WebView detection
                userAgentString = "$userAgentString UssiBatikAbsenApp/1.0"
                
                // Media playback
                mediaPlaybackRequiresUserGesture = false
                
                // Allow file access
                allowFileAccess = true
                allowContentAccess = true
                
                // Geolocation
                setGeolocationEnabled(true)
                
                // Android 10+ specific settings
                if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.Q) {
                    forceDark = WebSettings.FORCE_DARK_AUTO
                }
                
                // Android 12+ specific settings
                if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.S) {
                    algorithmicDarkeningAllowed = true
                }
            }
            
            // WebView client
            webViewClient = object : WebViewClient() {
                override fun onPageStarted(view: WebView?, url: String?, favicon: Bitmap?) {
                    super.onPageStarted(view, url, favicon)
                    swipeRefreshLayout?.isRefreshing = true
                }
                
                override fun onPageFinished(view: WebView?, url: String?) {
                    super.onPageFinished(view, url)
                    swipeRefreshLayout?.isRefreshing = false
                }
                
                override fun onReceivedError(
                    view: WebView?,
                    request: WebResourceRequest?,
                    error: WebResourceError?
                ) {
                    super.onReceivedError(view, request, error)
                    swipeRefreshLayout?.isRefreshing = false
                    
                    // Show error message
                    Toast.makeText(
                        this@MainActivity,
                        "Network error. Please check your connection.",
                        Toast.LENGTH_LONG
                    ).show()
                }
                
                override fun shouldOverrideUrlLoading(view: WebView?, request: WebResourceRequest?): Boolean {
                    // Keep navigation within the app
                    val url = request?.url?.toString()
                    return if (url?.startsWith(baseUrl) == true) {
                        false // Let WebView handle it
                    } else {
                        true // Block external URLs
                    }
                }
            }
            
            // Chrome client for camera and location
            webChromeClient = object : WebChromeClient() {
                override fun onPermissionRequest(request: PermissionRequest?) {
                    // Grant camera and microphone permissions
                    request?.grant(request.resources)
                }
                
                override fun onGeolocationPermissionsShowPrompt(
                    origin: String?,
                    callback: GeolocationPermissions.Callback?
                ) {
                    // Grant location permission
                    callback?.invoke(origin, true, false)
                }
                
                override fun onProgressChanged(view: WebView?, newProgress: Int) {
                    super.onProgressChanged(view, newProgress)
                    // You can add progress bar here if needed
                }
            }
            
            // Add JavaScript interface for native features
            addJavascriptInterface(WebAppInterface(this@MainActivity), "Android")
        }
    }
    
    private fun loadWebView() {
        webView?.loadUrl(baseUrl)
    }
    
    override fun onBackPressed() {
        if (webView?.canGoBack() == true) {
            webView?.goBack()
        } else {
            super.onBackPressed()
        }
    }
    
    override fun onKeyDown(keyCode: Int, event: KeyEvent?): Boolean {
        // Handle hardware back button
        if (keyCode == KeyEvent.KEYCODE_BACK && webView?.canGoBack() == true) {
            webView?.goBack()
            return true
        }
        return super.onKeyDown(keyCode, event)
    }
    
    override fun onResume() {
        super.onResume()
        webView?.onResume()
    }
    
    override fun onPause() {
        super.onPause()
        webView?.onPause()
    }
    
    override fun onDestroy() {
        webView?.destroy()
        super.onDestroy()
    }
}

// JavaScript Interface for native features
class WebAppInterface(private val context: MainActivity) {
    
    @JavascriptInterface
    fun showToast(message: String) {
        Toast.makeText(context, message, Toast.LENGTH_SHORT).show()
    }
    
    @JavascriptInterface
    fun vibrate() {
        // Add vibration if needed
    }
}