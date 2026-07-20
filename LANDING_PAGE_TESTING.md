# Landing Page - Testing Guide

## 🧪 Panduan Testing Lengkap

### 📋 **Pre-Testing Checklist**

#### 1. **Clear All Cache**
```bash
php artisan view:clear
php artisan route:clear
php artisan config:clear
```

#### 2. **Verify Routes**
```bash
php artisan route:list | Select-String -Pattern "landing"
```

Expected output:
```
GET|HEAD  /                      landing
GET|HEAD  /features              landing.features
GET|HEAD  /landing-countries     landing.countries
GET|HEAD  /about                 landing.about
GET|HEAD  /pricing               landing.pricing
GET|HEAD  /contact               landing.contact
```

---

## 🏠 **1. HOME PAGE TEST**

### **URL**: `http://localhost/`

#### ✅ **Visual Elements to Check:**
- [ ] Navbar muncul dengan logo "HandWorld"
- [ ] Menu navbar: Home, Features, Countries, About, Pricing, Contact
- [ ] Login button di kanan navbar
- [ ] Hero section dengan typewriter animation
- [ ] Text "Global Supply Chain Risk Intelligence Platform" muncul dengan typing effect
- [ ] Particle animation di background hero
- [ ] 2 CTA buttons: "Explore Dashboard" dan "Learn More"

#### ✅ **Statistics Section:**
- [ ] 4 stat boxes muncul:
  - 250+ Countries Monitored
  - 150+ Major Ports Tracked
  - 24/7 Real-time Updates
  - 99.9% Uptime Guarantee

#### ✅ **Dashboard Preview Section:**
- [ ] Heading "Powerful Dashboard at Your Fingertips"
- [ ] Dashboard screenshot placeholder
- [ ] 3 feature items dengan icons:
  - Real-time Monitoring (⚡)
  - Advanced Analytics (📊)
  - Smart Alerts (🔔)

#### ✅ **Why Choose Section:**
- [ ] 3 feature highlights:
  - Reliable Data
  - Fast Performance
  - Easy Integration

#### ✅ **CTA Section:**
- [ ] Green gradient background
- [ ] "Ready to Transform Your Supply Chain?"
- [ ] 2 buttons: "Get Started Free" & "Explore Features"

#### ✅ **Animations:**
- [ ] AOS fade-up animations saat scroll
- [ ] Smooth transitions
- [ ] Hover effects pada cards

---

## ⚡ **2. FEATURES PAGE TEST**

### **URL**: `http://localhost/features`

#### ✅ **Hero Section:**
- [ ] Badge "Platform Features"
- [ ] Heading "Powerful Features for Global Supply Chain Intelligence"

#### ✅ **Feature Cards (6 total):**

**1. Weather Monitoring**
- [ ] Icon cloud-sun (☁️)
- [ ] Gradient biru/hijau
- [ ] 4 bullet points
- [ ] Hover effect: translateY + shadow

**2. Global Port Monitoring**
- [ ] Icon diagram (⚓)
- [ ] Gradient biru
- [ ] 4 bullet points

**3. Exchange Rate**
- [ ] Icon currency-exchange (💱)
- [ ] Gradient kuning
- [ ] 4 bullet points

**4. Global News**
- [ ] Icon newspaper (📰)
- [ ] Gradient merah
- [ ] 4 bullet points

**5. Risk Dashboard**
- [ ] Icon shield (🛡️)
- [ ] Gradient ungu
- [ ] 4 bullet points

**6. Interactive Map**
- [ ] Icon globe (🗺️)
- [ ] Gradient hijau
- [ ] 4 bullet points

#### ✅ **CTA Section:**
- [ ] Green gradient background
- [ ] "Ready to Get Started?"
- [ ] 2 buttons: "Start Free Trial" & "View Pricing"

---

## 🌍 **3. COUNTRIES PAGE TEST**

### **URL**: `http://localhost/landing-countries`

#### ✅ **Hero Section:**
- [ ] Badge "Global Coverage"
- [ ] Heading "Explore Countries Worldwide"
- [ ] Subtitle "Monitor weather, risk levels..."

#### ✅ **Countries Grid:**
- [ ] Cards dalam grid 3 kolom (desktop)
- [ ] Setiap country card menampilkan:
  - [ ] Flag image (48x36px)
  - [ ] Country name (bold)
  - [ ] Region (small text)
  - [ ] Weather temperature dengan icon
  - [ ] Risk level badge dengan warna
  - [ ] "View Details" button

#### ✅ **Data dari Database:**
- [ ] Countries data muncul dari database
- [ ] Flag images load dengan benar
- [ ] Temperature dari weatherCache
- [ ] Risk level dari riskScore
- [ ] Pagination muncul di bawah

#### ✅ **Interactivity:**
- [ ] Hover pada card: translateY + shadow
- [ ] Click "View Details" → redirect ke `/countries/{code}`
- [ ] Pagination links berfungsi

---

## ℹ️ **4. ABOUT PAGE TEST**

### **URL**: `http://localhost/about`

#### ✅ **Hero Section:**
- [ ] Badge "About Platform"
- [ ] Heading "About HandWorld"

#### ✅ **About Platform Section:**
- [ ] Dashboard preview image (placeholder)
- [ ] Text "What is HandWorld?"
- [ ] 2 paragraphs penjelasan
- [ ] 3 statistics: 250+, 150+, 24/7

#### ✅ **Vision & Mission Section:**
- [ ] 2 cards side by side
- [ ] Vision card dengan eye icon
- [ ] Mission card dengan target icon
- [ ] Background #F5F7F4

#### ✅ **Technology Stack:**
- [ ] 8 tech cards dalam grid:
  - Laravel 11
  - Bootstrap 5
  - MySQL/SQLite
  - Leaflet.js
  - Chart.js
  - REST APIs
  - Secure Auth
  - Real-time Data
- [ ] Hover effect pada setiap card

#### ✅ **Development Team:**
- [ ] Team member card
- [ ] Avatar placeholder dengan icon
- [ ] "Development Team" & "Full Stack Developer"

---

## 💰 **5. PRICING PAGE TEST**

### **URL**: `http://localhost/pricing`

#### ✅ **Hero Section:**
- [ ] Badge "Pricing Plans"
- [ ] Heading "Choose Your Plan"

#### ✅ **Pricing Cards (3 total):**

**Free Plan:**
- [ ] Icon gift (🎁)
- [ ] Price: $0/month
- [ ] Gray gradient icon
- [ ] 8 features (4 included, 4 disabled)
- [ ] Button "Get Started Free"

**Professional Plan (Featured):**
- [ ] "Most Popular" badge di atas
- [ ] Icon star (⭐)
- [ ] Price: $49/month
- [ ] Green gradient icon
- [ ] Card lebih besar (scale 1.05)
- [ ] Border color hijau
- [ ] 8 features (7 included, 1 disabled)
- [ ] Button "Start 14-Day Trial"

**Enterprise Plan:**
- [ ] Icon building (🏢)
- [ ] Price: "Custom"
- [ ] Purple gradient icon
- [ ] 8 features (all included)
- [ ] Button "Contact Sales"

#### ✅ **Card Behaviors:**
- [ ] Featured card lebih besar
- [ ] Hover: scale + shadow
- [ ] Popular badge visible

#### ✅ **FAQ Section:**
- [ ] Bootstrap accordion
- [ ] 3 FAQ items
- [ ] First item expanded by default
- [ ] Smooth collapse/expand animation

---

## 📧 **6. CONTACT PAGE TEST**

### **URL**: `http://localhost/contact`

#### ✅ **Hero Section:**
- [ ] Badge "Get In Touch"
- [ ] Heading "Contact Us"

#### ✅ **Contact Information (Left):**
- [ ] 3 contact items dengan icons:
  - **Email**: info@handworld.com
  - **Phone**: +1 (234) 567-890
  - **Address**: Jakarta, Indonesia
- [ ] Icons dalam circle dengan gradient background

#### ✅ **Social Media Links:**
- [ ] 4 social links:
  - LinkedIn
  - Twitter/X
  - Facebook
  - Instagram
- [ ] Circle buttons dengan hover effect

#### ✅ **Contact Form (Right):**
- [ ] White card dengan border
- [ ] Form fields:
  - [ ] First Name (required)
  - [ ] Last Name (required)
  - [ ] Email (required)
  - [ ] Phone
  - [ ] Subject (required)
  - [ ] Message textarea (required)
- [ ] Submit button "Send Message" dengan icon
- [ ] Form styling: rounded borders, focus states

#### ✅ **Google Maps Section:**
- [ ] Embedded iframe map
- [ ] Rounded corners
- [ ] Box shadow
- [ ] Full width responsive

---

## 🎨 **GLOBAL UI/UX TESTS**

### **Navbar Tests**

#### ✅ **Initial State:**
- [ ] Transparent/semi-transparent background
- [ ] White text
- [ ] Logo visible

#### ✅ **Scroll State:**
- [ ] Background berubah solid green (`rgba(15, 118, 110, 0.98)`)
- [ ] Box shadow muncul
- [ ] Smooth transition

#### ✅ **Active States:**
- [ ] Menu aktif punya warna lebih terang (#D1FAE5)
- [ ] Font weight bold

#### ✅ **Responsive:**
- [ ] Mobile: burger menu muncul
- [ ] Click burger → menu expand
- [ ] Menu vertical di mobile

### **Animation Tests**

#### ✅ **AOS Animations:**
- [ ] Fade-up saat scroll pertama kali
- [ ] Fade-left, fade-right untuk sections
- [ ] Zoom-in untuk cards
- [ ] Delay stagger (100ms, 200ms, 300ms)

#### ✅ **Hover Effects:**
- [ ] Cards: translateY(-4px to -8px)
- [ ] Box shadow increase
- [ ] Border color change
- [ ] Smooth transitions (0.3s-0.4s)

#### ✅ **Button Hover:**
- [ ] Primary: translateY + shadow
- [ ] Outline: border & background color change
- [ ] Ripple effect (::before pseudo-element)

### **Responsive Tests**

#### ✅ **Desktop (>1200px):**
- [ ] Cards dalam 3-4 kolom
- [ ] Full width sections
- [ ] Large font sizes

#### ✅ **Tablet (768px-1200px):**
- [ ] Cards dalam 2 kolom
- [ ] Adjusted font sizes
- [ ] Proper spacing

#### ✅ **Mobile (<768px):**
- [ ] Cards stack (1 kolom)
- [ ] Hamburger menu
- [ ] Buttons stack vertical
- [ ] Reduced font sizes
- [ ] Touch-friendly buttons

---

## 🔗 **Link & Navigation Tests**

### **Internal Links:**
- [ ] Home → Home page
- [ ] Features → Features page
- [ ] Countries → Countries listing
- [ ] About → About page
- [ ] Pricing → Pricing page
- [ ] Contact → Contact page

### **CTA Buttons:**
- [ ] "Get Started Free" → `/register`
- [ ] "Login" → `/login`
- [ ] "Explore Dashboard" → # (or `/dashboard` if logged in)
- [ ] "View Details" (countries) → `/countries/{code}`
- [ ] "Contact Sales" → `/contact`

### **External Links:**
- [ ] Social media links → # (placeholder)

---

## ⚠️ **Common Issues & Solutions**

### **Issue 1: AOS animations tidak muncul**
**Solution:**
- Check browser console untuk errors
- Pastikan AOS CDN loaded: `<link href="https://unpkg.com/aos@2.3.1/dist/aos.css">`
- Pastikan AOS.init() dipanggil: `<script>AOS.init();</script>`

### **Issue 2: Navbar tidak sticky**
**Solution:**
- Check CSS class `fixed-top` ada di navbar
- Check z-index navbar (harus > 1000)

### **Issue 3: Countries data tidak muncul**
**Solution:**
- Check database punya data countries
- Run seeder: `php artisan db:seed --class=CountrySeeder`
- Check relasi weatherCache & riskScore

### **Issue 4: Images tidak load**
**Solution:**
- Placeholder images menggunakan `via.placeholder.com`
- Flag images dari `https://flagcdn.com/w80/{code}.png`
- Check koneksi internet

### **Issue 5: Hover effects tidak smooth**
**Solution:**
- Check CSS transitions sudah ada
- Clear browser cache: Ctrl+Shift+R

---

## ✅ **Final Checklist**

### **All Pages Accessible:**
- [ ] Home (/)
- [ ] Features (/features)
- [ ] Countries (/landing-countries)
- [ ] About (/about)
- [ ] Pricing (/pricing)
- [ ] Contact (/contact)

### **All Animations Working:**
- [ ] AOS scroll animations
- [ ] Hover effects
- [ ] Navbar scroll effect
- [ ] Particle animation (home)
- [ ] Typewriter effect (home)

### **All Links Working:**
- [ ] Navbar navigation
- [ ] CTA buttons
- [ ] View Details buttons
- [ ] Login/Register links

### **Responsive:**
- [ ] Desktop view (1920px)
- [ ] Laptop view (1366px)
- [ ] Tablet view (768px)
- [ ] Mobile view (375px)

### **Browser Compatibility:**
- [ ] Chrome
- [ ] Firefox
- [ ] Edge
- [ ] Safari (if available)

---

## 🎯 **Success Criteria**

Landing page dianggap **BERHASIL** jika:

1. ✅ Semua 6 halaman accessible tanpa error
2. ✅ Navbar navigation berfungsi dengan active states
3. ✅ Scroll animations smooth dan tidak lag
4. ✅ Hover effects responsif
5. ✅ Countries data muncul dari database
6. ✅ Design consistent di semua halaman
7. ✅ Responsive di semua screen sizes
8. ✅ No console errors
9. ✅ Load time < 3 seconds
10. ✅ Professional SaaS appearance

---

## 📊 **Performance Check**

### **Page Load Times (Target):**
- Home: < 2s
- Features: < 1.5s
- Countries: < 2.5s (karena query database)
- About: < 1.5s
- Pricing: < 1.5s
- Contact: < 1.5s

### **Lighthouse Scores (Target):**
- Performance: > 90
- Accessibility: > 90
- Best Practices: > 90
- SEO: > 90

---

**Status**: ✅ Ready for Testing
**Date**: 2026-07-19
**Tester**: [Your Name]

---

## 📝 **Testing Notes:**

_Tambahkan catatan hasil testing di sini..._

---

**Result**: [ ] PASSED / [ ] FAILED

**Issues Found**: _List any issues discovered during testing..._
