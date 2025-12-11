/**
 * Mobile Performance Optimizations for USSIBATIK ABSEN
 * Optimizes loading, camera, and user experience for mobile devices
 */

class MobileOptimizer {
    constructor() {
        this.init();
    }

    init() {
        // Initialize optimizations when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setupOptimizations());
        } else {
            this.setupOptimizations();
        }
    }

    setupOptimizations() {
        this.optimizeImages();
        this.optimizeCamera();
        this.optimizeTouch();
        this.optimizeKeyboard();
        this.optimizeNetwork();
        this.setupServiceWorker();
        this.optimizeAnimations();
    }

    /**
     * Optimize images for faster loading
     */
    optimizeImages() {
        // Lazy load images
        const images = document.querySelectorAll('img[data-src]');
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    observer.unobserve(img);
                }
            });
        });

        images.forEach(img => imageObserver.observe(img));

        // Preload critical images
        const criticalImages = [
            '/images/login3d.png',
            '/images/logo.png'
        ];

        criticalImages.forEach(src => {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.as = 'image';
            link.href = src;
            document.head.appendChild(link);
        });
    }

    /**
     * Optimize camera performance
     */
    optimizeCamera() {
        // Optimize camera constraints for mobile
        window.getMobileOptimizedConstraints = () => {
            const isMobile = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            
            if (isMobile) {
                return {
                    video: {
                        facingMode: 'user',
                        width: { ideal: 640, max: 1280 },
                        height: { ideal: 480, max: 720 },
                        frameRate: { ideal: 15, max: 30 }
                    }
                };
            } else {
                return {
                    video: {
                        facingMode: 'user',
                        width: { ideal: 1280 },
                        height: { ideal: 720 },
                        frameRate: { ideal: 30 }
                    }
                };
            }
        };

        // Optimize canvas operations
        window.optimizedCanvasCapture = (video, canvas) => {
            const context = canvas.getContext('2d');
            
            // Use lower quality for mobile to improve performance
            canvas.width = Math.min(video.videoWidth, 640);
            canvas.height = Math.min(video.videoHeight, 480);
            
            // Draw with optimized settings
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            // Compress image for mobile
            return canvas.toDataURL('image/jpeg', 0.8);
        };
    }

    /**
     * Optimize touch interactions
     */
    optimizeTouch() {
        // Prevent zoom on double tap
        let lastTouchEnd = 0;
        document.addEventListener('touchend', (event) => {
            const now = (new Date()).getTime();
            if (now - lastTouchEnd <= 300) {
                event.preventDefault();
            }
            lastTouchEnd = now;
        }, false);

        // Optimize scroll performance
        let ticking = false;
        const updateScrollPosition = () => {
            // Update scroll-dependent elements
            ticking = false;
        };

        document.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(updateScrollPosition);
                ticking = true;
            }
        }, { passive: true });

        // Add touch feedback
        document.addEventListener('touchstart', (e) => {
            if (e.target.classList.contains('btn') || e.target.closest('.btn')) {
                e.target.style.transform = 'scale(0.95)';
            }
        }, { passive: true });

        document.addEventListener('touchend', (e) => {
            if (e.target.classList.contains('btn') || e.target.closest('.btn')) {
                setTimeout(() => {
                    e.target.style.transform = '';
                }, 150);
            }
        }, { passive: true });
    }

    /**
     * Optimize keyboard interactions
     */
    optimizeKeyboard() {
        // Handle virtual keyboard
        const viewport = document.querySelector('meta[name=viewport]');
        const originalContent = viewport.content;

        const inputs = document.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            input.addEventListener('focus', () => {
                // Adjust viewport for keyboard
                viewport.content = 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no';
                
                // Scroll to input
                setTimeout(() => {
                    input.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 300);
            });

            input.addEventListener('blur', () => {
                // Restore original viewport
                viewport.content = originalContent;
            });
        });

        // Optimize form submission
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                // Show loading state
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
                    
                    // Restore button after timeout (fallback)
                    setTimeout(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }, 10000);
                }
            });
        });
    }

    /**
     * Optimize network requests
     */
    optimizeNetwork() {
        // Cache frequently used data
        const cache = new Map();
        
        window.cachedFetch = async (url, options = {}) => {
            const cacheKey = `${url}_${JSON.stringify(options)}`;
            
            if (cache.has(cacheKey)) {
                return cache.get(cacheKey);
            }
            
            try {
                const response = await fetch(url, {
                    ...options,
                    headers: {
                        'Cache-Control': 'max-age=300',
                        ...options.headers
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    cache.set(cacheKey, data);
                    
                    // Clear cache after 5 minutes
                    setTimeout(() => cache.delete(cacheKey), 300000);
                    
                    return data;
                }
            } catch (error) {
                console.error('Network error:', error);
                throw error;
            }
        };

        // Preload critical resources
        const criticalUrls = [
            '/csrf-token',
            '/get-jam-kerja',
            '/check-absen-status'
        ];

        criticalUrls.forEach(url => {
            const link = document.createElement('link');
            link.rel = 'prefetch';
            link.href = url;
            document.head.appendChild(link);
        });
    }

    /**
     * Setup Service Worker for offline support
     */
    setupServiceWorker() {
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(registration => {
                    console.log('Service Worker registered:', registration);
                })
                .catch(error => {
                    console.log('Service Worker registration failed:', error);
                });
        }
    }

    /**
     * Optimize animations for mobile
     */
    optimizeAnimations() {
        // Reduce animations on low-end devices
        const isLowEndDevice = navigator.hardwareConcurrency <= 2 || 
                              navigator.deviceMemory <= 2;

        if (isLowEndDevice) {
            document.documentElement.style.setProperty('--animation-duration', '0.1s');
            document.documentElement.style.setProperty('--transition-duration', '0.1s');
        }

        // Use transform instead of changing layout properties
        const animatedElements = document.querySelectorAll('.animate');
        animatedElements.forEach(element => {
            element.style.willChange = 'transform, opacity';
        });

        // Pause animations when page is not visible
        document.addEventListener('visibilitychange', () => {
            const animations = document.getAnimations();
            if (document.hidden) {
                animations.forEach(animation => animation.pause());
            } else {
                animations.forEach(animation => animation.play());
            }
        });
    }

    /**
     * Monitor performance
     */
    monitorPerformance() {
        // Monitor page load performance
        window.addEventListener('load', () => {
            const perfData = performance.getEntriesByType('navigation')[0];
            console.log('Page load time:', perfData.loadEventEnd - perfData.loadEventStart, 'ms');
        });

        // Monitor memory usage
        if ('memory' in performance) {
            setInterval(() => {
                const memory = performance.memory;
                if (memory.usedJSHeapSize > memory.jsHeapSizeLimit * 0.9) {
                    console.warn('High memory usage detected');
                    // Trigger garbage collection if possible
                    if (window.gc) window.gc();
                }
            }, 30000);
        }
    }
}

// Initialize mobile optimizations
const mobileOptimizer = new MobileOptimizer();

// Export for use in other scripts
window.MobileOptimizer = MobileOptimizer;