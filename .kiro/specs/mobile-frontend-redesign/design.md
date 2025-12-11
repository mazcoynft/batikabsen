# Design Document - Mobile Frontend Redesign

## Overview

Redesign frontend mobile aplikasi USSIBATIK Absen bertujuan untuk menciptakan user experience yang lebih modern, minimalist, dan optimal untuk perangkat mobile. Desain baru akan menggunakan pendekatan mobile-first dengan fokus pada:

- **Visual Hierarchy**: Informasi penting ditampilkan dengan prominence yang jelas
- **Minimalist Aesthetic**: Mengurangi clutter visual dan fokus pada konten
- **Consistent Design Language**: Menggunakan color scheme, typography, dan spacing yang konsisten
- **Touch-Friendly Interface**: Semua elemen interaktif memiliki touch target yang cukup besar
- **Performance Optimization**: Loading time yang cepat dengan optimasi asset

## Architecture

### Technology Stack

**Frontend:**
- Laravel Blade Templates (existing)
- Bootstrap 5.3.0 (existing, akan dioptimalkan)
- Custom CSS untuk styling modern
- Font Awesome 6.0.0 untuk icons
- Leaflet.js untuk maps (existing)
- SweetAlert2 untuk notifications (existing)
- Moment.js untuk date handling (existing)

**Design Approach:**
- Mobile-First Responsive Design
- Component-Based Styling
- CSS Custom Properties untuk theming
- Minimal JavaScript untuk interactivity

### File Structure

```
resources/views/frontend/
├── dashboard.blade.php (redesign)
├── history.blade.php (redesign)
├── profile.blade.php (redesign)
├── absen.blade.php (redesign)
└── izin.blade.php (existing, minor updates)

resources/css/
└── frontend-mobile.css (new)

public/css/
└── frontend-mobile.min.css (compiled)
```

## Components and Interfaces

### 1. Header Component

**Purpose**: Menampilkan informasi user dan navigasi utama

**Structure:**
```html
<div class="header">
  <div class="user-profile">
    <img class="user-avatar" />
    <div class="user-info">
      <p class="user-name"></p>
      <p class="user-position"></p>
    </div>
    <button class="logout-btn"></button>
  </div>
</div>
```

**Styling:**
- Background: Linear gradient (#4a90e2 to #357abd)
- Border radius: 0 0 20px 20px (rounded bottom)
- Shadow: 0 4px 12px rgba(0,0,0,0.1)
- Padding: 16px
- Avatar: 48px circle with 2px white border

### 2. Card Component

**Purpose**: Container untuk mengelompokkan informasi terkait

**Variants:**
- **Primary Card**: Untuk statistik kehadiran (gradient background)
- **Secondary Card**: Untuk konten umum (white background)
- **Info Card**: Untuk pengumuman (gradient background)

**Styling:**
- Border radius: 16px
- Shadow: 0 2px 8px rgba(0,0,0,0.08)
- Padding: 16px
- Margin bottom: 16px

### 3. Bottom Navigation Component

**Purpose**: Navigasi utama aplikasi

**Structure:**
```html
<nav class="bottom-nav">
  <a class="nav-item">
    <i class="icon"></i>
    <span class="label"></span>
  </a>
  <!-- Center camera button -->
  <a class="nav-item nav-camera">
    <div class="circle-icon">
      <i class="fa-camera"></i>
    </div>
  </a>
</nav>
```

**Styling:**
- Position: fixed bottom
- Background: white with backdrop blur
- Shadow: 0 -2px 12px rgba(0,0,0,0.08)
- Border radius: 20px 20px 0 0
- Height: 70px
- Camera button: 56px circle, elevated with shadow

### 4. Status Badge Component

**Purpose**: Menampilkan status kehadiran

**Variants:**
- Hadir: Blue (#4a90e2)
- Ontime: Green (#28a745)
- Late: Red (#dc3545)
- Izin: Orange (#ff9800)
- Sakit: Teal (#009688)
- Cuti: Purple (#9c27b0)
- WFH: Dark gray (#555)
- Onsite: Blue (#2196F3)

**Styling:**
- Padding: 4px 12px
- Border radius: 12px
- Font size: 12px
- Font weight: 600

### 5. List Item Component

**Purpose**: Menampilkan item dalam list (history, profile info)

**Structure:**
```html
<div class="list-item">
  <div class="list-icon"></div>
  <div class="list-content">
    <div class="list-label"></div>
    <div class="list-value"></div>
  </div>
  <div class="list-action"></div>
</div>
```

**Styling:**
- Padding: 16px
- Border bottom: 1px solid #f0f0f0
- Min height: 64px (touch-friendly)

## Data Models

### Dashboard Data

```php
[
    'kehadiran' => int,      // Jumlah hari hadir bulan ini
    'izin' => int,           // Jumlah hari izin/sakit bulan ini
    'terlambat' => int,      // Jumlah hari terlambat bulan ini
    'jam_masuk' => string,   // Waktu check-in hari ini
    'jam_pulang' => string,  // Waktu check-out hari ini
    'foto_masuk' => string,  // Path foto check-in
    'foto_pulang' => string, // Path foto check-out
    'pengumuman' => array,   // List pengumuman aktif
    'presensi' => array,     // List presensi bulan ini
    'leaderboard' => array   // List karyawan dengan kehadiran terbaik
]
```

### History Data

```php
[
    'presensi' => [
        [
            'tgl_presensi' => date,
            'jam_in' => time,
            'jam_out' => time,
            'foto_in' => string,
            'foto_out' => string,
            'status' => string,  // h, i, s, c
            'status_presensi_in' => string,  // 1=ontime, 2=late, 3=onsite, 4=wfh
            'jenis_presensi' => string  // biasa, onsite, wfh
        ]
    ]
]
```

### Profile Data

```php
[
    'user' => [
        'name' => string,
        'nik_app' => string,
        'email' => string,
        'phone' => string,
        'avatar_url' => string,
        'jabatan' => string,
        'karyawan' => [
            'department' => [
                'nama_dept' => string
            ]
        ]
    ]
]
```

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property Reflection

Setelah menganalisis semua acceptance criteria, sebagian besar requirement adalah tentang visual design, styling, dan layout yang tidak dapat di-test dengan property-based testing. Namun, ada beberapa properties yang dapat di-test:

1. Status badge color mapping (2.3)
2. Button color differentiation (4.2)
3. Active navigation indicator (5.2)

Setelah reflection, property 1 dan 2 sebenarnya adalah mapping yang sama (status -> color), sehingga bisa digabungkan. Property 3 adalah tentang UI state yang berbeda.

### Testable Properties

**Property 1: Status-to-color mapping consistency**

*For any* attendance status (hadir, izin, sakit, cuti, ontime, late, wfh, onsite), the rendered badge or button should have a unique and consistent color that matches the defined color scheme

**Validates: Requirements 2.3, 4.2**

**Property 2: Active navigation state**

*For any* navigation item, when it is the active page, the item should have the 'active' class and display with the primary color (#4a90e2)

**Validates: Requirements 5.2**

### Example Tests

**Example 1: Empty state display**

When the history page has no attendance data, the system should display an empty state message

**Validates: Requirements 2.5**

**Example 2: Loading indicator display**

When a form is being submitted, the system should display a loading indicator

**Validates: Requirements 8.2**

## Error Handling

### User Input Errors

1. **Invalid File Upload**
   - Validation: File type (jpg, png, webp), size (max 2MB)
   - Error message: "Format file tidak valid. Gunakan JPG, PNG, atau WEBP dengan ukuran maksimal 2MB"
   - Display: SweetAlert error modal

2. **Password Mismatch**
   - Validation: New password confirmation must match
   - Error message: "Konfirmasi password tidak cocok"
   - Display: Inline error below form field

3. **Empty Form Submission**
   - Validation: Required fields must be filled
   - Error message: "Mohon lengkapi semua field yang wajib diisi"
   - Display: Inline error below form field

### System Errors

1. **Camera Access Denied**
   - Error message: "Tidak dapat mengakses kamera. Pastikan Anda memberikan izin kamera"
   - Display: SweetAlert error modal
   - Fallback: Show instruction to enable camera permission

2. **GPS Access Denied**
   - Error message: "Tidak dapat mengakses lokasi. Pastikan GPS aktif dan Anda memberikan izin lokasi"
   - Display: SweetAlert error modal
   - Fallback: Show instruction to enable location permission

3. **Network Error**
   - Error message: "Koneksi internet bermasalah. Mohon coba lagi"
   - Display: SweetAlert error modal
   - Retry: Provide retry button

4. **Server Error**
   - Error message: "Terjadi kesalahan pada server. Mohon coba beberapa saat lagi"
   - Display: SweetAlert error modal
   - Logging: Log error to server for debugging

### Loading States

1. **Page Load**
   - Show skeleton loading for cards
   - Fade in content when loaded

2. **Form Submission**
   - Disable submit button
   - Show spinner icon in button
   - Display "Memproses..." text

3. **Image Upload**
   - Show progress bar
   - Display preview when loaded

## Testing Strategy

### Manual Testing

**Visual Regression Testing:**
- Screenshot comparison untuk setiap halaman
- Test di berbagai ukuran layar (320px, 375px, 414px, 768px)
- Test di berbagai browser mobile (Chrome, Safari, Firefox)

**Usability Testing:**
- Test dengan real users untuk feedback
- Measure task completion time
- Collect user satisfaction scores

**Performance Testing:**
- Lighthouse audit untuk performance score
- Measure First Contentful Paint (FCP)
- Measure Largest Contentful Paint (LCP)
- Measure Time to Interactive (TTI)

### Automated Testing

**Unit Tests:**
- Test helper functions untuk color mapping
- Test date formatting functions
- Test validation functions

**Property-Based Tests:**

Menggunakan **PHPUnit** dengan **Faker** untuk property-based testing di Laravel.

**Property Test 1: Status-to-color mapping**
```php
/**
 * Feature: mobile-frontend-redesign, Property 1: Status-to-color mapping consistency
 * Validates: Requirements 2.3, 4.2
 * 
 * @test
 */
public function test_status_badge_color_mapping()
{
    $statusColorMap = [
        'hadir' => '#4a90e2',
        'ontime' => '#28a745',
        'late' => '#dc3545',
        'izin' => '#ff9800',
        'sakit' => '#009688',
        'cuti' => '#9c27b0',
        'wfh' => '#555',
        'onsite' => '#2196F3'
    ];
    
    foreach ($statusColorMap as $status => $expectedColor) {
        $actualColor = getBadgeColor($status);
        $this->assertEquals($expectedColor, $actualColor);
    }
}
```

**Property Test 2: Active navigation state**
```php
/**
 * Feature: mobile-frontend-redesign, Property 2: Active navigation state
 * Validates: Requirements 5.2
 * 
 * @test
 */
public function test_active_navigation_indicator()
{
    $pages = ['dashboard', 'history', 'absen', 'izin', 'profile'];
    
    foreach ($pages as $activePage) {
        $response = $this->get(route("frontend.$activePage"));
        $response->assertSee('nav-link active');
    }
}
```

**Example Test 1: Empty state**
```php
/**
 * Feature: mobile-frontend-redesign, Example 1: Empty state display
 * Validates: Requirements 2.5
 * 
 * @test
 */
public function test_empty_state_display()
{
    // Create user with no attendance records
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->get(route('frontend.history'));
    
    $response->assertSee('Tidak ada data presensi');
}
```

**Example Test 2: Loading indicator**
```php
/**
 * Feature: mobile-frontend-redesign, Example 2: Loading indicator display
 * Validates: Requirements 8.2
 * 
 * @test
 */
public function test_loading_indicator_on_form_submit()
{
    $response = $this->get(route('frontend.profile'));
    
    // Check that loading indicator HTML exists
    $response->assertSee('loading-indicator');
}
```

### Integration Tests

- Test navigation flow antar halaman
- Test form submission dengan validasi
- Test camera dan GPS integration
- Test image upload flow

## Design System

### Color Palette

**Primary Colors:**
- Primary Blue: #4a90e2
- Primary Dark: #357abd
- Primary Light: #6ba3e8

**Status Colors:**
- Success Green: #28a745
- Error Red: #dc3545
- Warning Orange: #ff9800
- Info Teal: #009688
- Purple: #9c27b0
- Dark Gray: #555

**Neutral Colors:**
- White: #ffffff
- Light Gray: #f8f9fa
- Medium Gray: #6c757d
- Dark: #333333
- Border: #e0e0e0

### Typography

**Font Family:**
- Primary: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif
- Fallback: system-ui, -apple-system, sans-serif

**Font Sizes:**
- Heading 1: 24px / 1.5rem
- Heading 2: 20px / 1.25rem
- Heading 3: 18px / 1.125rem
- Body: 16px / 1rem
- Small: 14px / 0.875rem
- Tiny: 12px / 0.75rem

**Font Weights:**
- Regular: 400
- Medium: 500
- Semibold: 600
- Bold: 700

### Spacing Scale

Using 4px base unit:
- xs: 4px
- sm: 8px
- md: 12px
- lg: 16px
- xl: 20px
- 2xl: 24px
- 3xl: 32px
- 4xl: 48px

### Border Radius

- Small: 8px
- Medium: 12px
- Large: 16px
- XLarge: 20px
- Circle: 50%

### Shadows

- Small: 0 2px 4px rgba(0,0,0,0.06)
- Medium: 0 2px 8px rgba(0,0,0,0.08)
- Large: 0 4px 12px rgba(0,0,0,0.1)
- XLarge: 0 8px 24px rgba(0,0,0,0.12)

### Transitions

- Fast: 150ms ease-in-out
- Normal: 250ms ease-in-out
- Slow: 350ms ease-in-out

## Implementation Notes

### CSS Organization

```css
/* 1. CSS Custom Properties */
:root {
  --color-primary: #4a90e2;
  --color-primary-dark: #357abd;
  --spacing-md: 12px;
  --spacing-lg: 16px;
  --radius-md: 12px;
  --radius-lg: 16px;
  --shadow-md: 0 2px 8px rgba(0,0,0,0.08);
}

/* 2. Base Styles */
body { }
a { }
button { }

/* 3. Layout Components */
.header { }
.content { }
.bottom-nav { }

/* 4. UI Components */
.card { }
.btn { }
.badge { }
.list-item { }

/* 5. Page-Specific Styles */
.dashboard { }
.history { }
.profile { }
.absen { }

/* 6. Utility Classes */
.mt-lg { }
.text-center { }
.d-flex { }

/* 7. Responsive Overrides */
@media (max-width: 576px) { }
```

### Performance Optimization

1. **Image Optimization**
   - Use WebP format with JPEG fallback
   - Lazy load images below the fold
   - Use appropriate image sizes (no larger than needed)
   - Compress images to 80% quality

2. **CSS Optimization**
   - Inline critical CSS for above-the-fold content
   - Defer non-critical CSS
   - Minify CSS in production
   - Remove unused CSS

3. **JavaScript Optimization**
   - Defer non-critical scripts
   - Use async for independent scripts
   - Minify JavaScript in production
   - Remove console.logs in production

4. **Font Optimization**
   - Use font-display: swap
   - Preload critical fonts
   - Subset fonts to include only used characters

5. **Caching Strategy**
   - Set appropriate cache headers
   - Use service worker for offline support
   - Cache static assets aggressively

### Accessibility Considerations

1. **Color Contrast**
   - Ensure minimum 4.5:1 contrast ratio for text
   - Use WCAG AA compliant colors

2. **Touch Targets**
   - Minimum 44x44px for all interactive elements
   - Adequate spacing between touch targets

3. **Focus States**
   - Visible focus indicators for keyboard navigation
   - Logical tab order

4. **Screen Reader Support**
   - Semantic HTML elements
   - ARIA labels where needed
   - Alt text for images

5. **Responsive Text**
   - Use relative units (rem, em)
   - Allow text zoom up to 200%

## Migration Strategy

### Phase 1: Dashboard Redesign
- Update dashboard.blade.php with new design
- Test on multiple devices
- Gather user feedback

### Phase 2: History & Profile Redesign
- Update history.blade.php
- Update profile.blade.php
- Test navigation flow

### Phase 3: Absen Page Redesign
- Update absen.blade.php
- Test camera and GPS functionality
- Ensure backward compatibility

### Phase 4: Polish & Optimization
- Performance optimization
- Bug fixes
- Final user testing

### Rollback Plan

- Keep backup of original files
- Use feature flags for gradual rollout
- Monitor error rates and user feedback
- Quick rollback if critical issues found
