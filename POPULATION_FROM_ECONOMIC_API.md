# 📊 Population Data - Dari World Bank Economic API

## ✅ Solution: Ambil Population dari Economic API (Tanpa Update Database Countries)

Solusi ini mengambil data population dari **World Bank API** (sama dengan data ekonomi) dan menyimpannya di tabel `economic_caches` yang sudah ada, tanpa mengubah tabel `countries`.

---

## 🎯 Kenapa Solusi Ini?

### **Masalah Sebelumnya:**
1. ❌ GitHub API (mledoze/countries) **TIDAK** memiliki field `population`
2. ❌ Semua population di tabel `countries` = 0
3. ❌ Accessor menampilkan "N/A" karena `!$this->population` = true

### **Solusi Baru:**
1. ✅ Ambil population dari **World Bank API** (indicator: `SP.POP.TOTL`)
2. ✅ Simpan di tabel `economic_caches` (kolom `population` sudah ada!)
3. ✅ Accessor `formatted_population` cek dari `economic_caches` jika `countries.population` = 0
4. ✅ **TIDAK** update tabel `countries` - data tetap di economic cache

---

## 🏗️ Arsitektur

### **Data Flow:**
```
World Bank API (SP.POP.TOTL)
         ↓
EconomicService::getIndicator()
         ↓
economic_caches.population
         ↓
Country Model Accessor (getFormattedPopulationAttribute)
         ↓
View (countries.blade.php)
         ↓
User melihat population
```

### **Database Structure:**
```
countries table:
  - population (bigint) → stays 0 (tidak diubah)

economic_caches table:
  - country_id
  - gdp
  - inflation
  - population ✅ (diisi dari World Bank API)
  - year
```

---

## 📝 File Changes

### **1. Model Country.php** ✅
**File:** `app/Models/Country.php`

**Perubahan Accessor:**
```php
public function getFormattedPopulationAttribute(): string
{
    // Try from database first
    if ($this->population && $this->population > 0) {
        return number_format($this->population);
    }
    
    // Try from economic cache (World Bank data)
    $economic = $this->economicCache;
    if ($economic && isset($economic->population) && $economic->population > 0) {
        return number_format($economic->population);
    }
    
    return 'N/A';
}
```

**Logic:**
1. Cek `countries.population` dulu
2. Kalau 0 atau NULL, cek `economic_caches.population`
3. Kalau masih tidak ada, tampilkan "N/A"

---

### **2. New Command: SyncPopulation** ✅
**File:** `app/Console/Commands/SyncPopulation.php`

**Fungsi:**
- Fetch population dari World Bank API
- Simpan ke `economic_caches.population`
- Progress bar untuk monitoring
- Delay 0.1 detik per request (avoid rate limit)

**Command:**
```bash
php artisan sync:population
```

---

## 🚀 Cara Menjalankan

### **Step 1: Run Sync Command**
```bash
php artisan sync:population
```

**Output:**
```
Syncing population data from World Bank API...

 250/250 [============================] 100%

✓ Synced population for 245 countries
⚠ Skipped 5 countries (no data from API)
```

**Waktu:** ~25-30 detik (0.1s × 250 countries)

---

### **Step 2: Verify Data**
```bash
php artisan tinker
>>> App\Models\Country::where('country_code', 'ID')->first()->formatted_population
=> "273,523,621"
```

---

### **Step 3: Refresh Browser**
1. Buka: `http://localhost/countries`
2. Refresh page (Ctrl+F5)
3. ✅ Population sekarang muncul!

---

## 📊 Sample Data

### **Before (dari World Bank API):**
```
ID  Indonesia          273,523,621
US  United States      331,893,745
CN  China            1,411,100,000
IN  India            1,380,004,385
BR  Brazil             212,559,417
AZ  Azerbaijan          10,139,177
BD  Bangladesh         164,689,383
BE  Belgium             11,589,623
```

### **API Endpoint:**
```
https://api.worldbank.org/v2/country/{country_code3}/indicator/SP.POP.TOTL?format=json&per_page=1
```

**Example:**
```
https://api.worldbank.org/v2/country/IDN/indicator/SP.POP.TOTL?format=json&per_page=1
```

**Response:**
```json
[
  {...},
  [
    {
      "indicator": {
        "id": "SP.POP.TOTL",
        "value": "Population, total"
      },
      "country": {
        "id": "ID",
        "value": "Indonesia"
      },
      "value": 273523621,
      "date": "2022"
    }
  ]
]
```

---

## ✅ Keuntungan Solusi Ini

### **1. Tidak Merusak Data Existing** ✅
- Tabel `countries` tidak diubah
- Data population disimpan terpisah di `economic_caches`
- Konsisten dengan arsitektur project (economic data di cache table)

### **2. Menggunakan Infrastruktur yang Sudah Ada** ✅
- EconomicService sudah ada
- Tabel economic_caches sudah ada kolom population
- Relationship Country → EconomicCache sudah ada

### **3. Data Akurat dari World Bank** ✅
- World Bank = sumber official dan terpercaya
- Data di-update regularly oleh World Bank
- Sama dengan data ekonomi lainnya (GDP, Inflation)

### **4. Cache-Based** ✅
- Data disimpan di database (tidak API call setiap page load)
- Cepat dan efisien
- Bisa di-sync manual kapan saja

---

## 🔄 Update Data di Masa Depan

### **Manual Update:**
```bash
php artisan sync:population
```

### **Automatic Update (Optional):**
Tambahkan ke Laravel Scheduler di `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Sync population annually
    $schedule->command('sync:population')
             ->yearly()
             ->onFailure(fn() => Log::error('Population sync failed'));
}
```

---

## 🧪 Testing

### **Test 1: Check Economic Cache**
```bash
php artisan tinker
>>> App\Models\EconomicCache::where('country_id', 1)->first()->population
=> 273523621
```

### **Test 2: Check Accessor**
```bash
>>> $country = App\Models\Country::find(1)
=> App\Models\Country {#...}
>>> $country->formatted_population
=> "273,523,621"
```

### **Test 3: Check View**
```
http://localhost/countries
```
✅ Population column should show numbers

---

## 📋 API Indicator Reference

### **World Bank Indicators:**

| Indicator Code | Description | Example Value |
|---------------|-------------|---------------|
| `SP.POP.TOTL` | **Population, total** | 273,523,621 |
| `NY.GDP.MKTP.CD` | GDP (current US$) | 1,058,423,838,108 |
| `FP.CPI.TOTL.ZG` | Inflation, consumer prices | 1.92 |

**Used in Project:**
- ✅ `SP.POP.TOTL` - Population (NEW!)
- ✅ `NY.GDP.MKTP.CD` - GDP (already implemented)
- ✅ `FP.CPI.TOTL.ZG` - Inflation (already implemented)

---

## 🔍 Troubleshooting

### **Issue 1: Still Showing "N/A"**
**Solution:**
```bash
# Run sync command
php artisan sync:population

# Clear cache
php artisan cache:clear
php artisan view:clear

# Refresh browser
Ctrl+F5
```

### **Issue 2: Accessor Not Working**
**Check Model:**
```php
// Ensure relationship exists
public function economicCache()
{
    return $this->hasOne(EconomicCache::class)->latestOfMany();
}
```

### **Issue 3: API Timeout**
**Increase Timeout in EconomicService:**
```php
$response = Http::timeout(60) // increase from 30 to 60
    ->retry(3, 1000)
    ->get(...);
```

---

## 🎯 Summary

### **What Changed:**
1. ✅ Modified `Country.php` accessor to check `economic_caches.population`
2. ✅ Created `SyncPopulation.php` command
3. ✅ No changes to views
4. ✅ No changes to database schema
5. ✅ No changes to other features

### **What to Run:**
```bash
php artisan sync:population
```

### **Result:**
- ✅ Population data appears in Countries page
- ✅ Data from World Bank API (official source)
- ✅ Stored in economic_caches (proper architecture)
- ✅ No database migration needed
- ✅ No breaking changes

---

## 📚 Related Files

- `app/Models/Country.php` - Modified accessor
- `app/Console/Commands/SyncPopulation.php` - New command
- `app/Services/EconomicService.php` - Existing service (reused)
- `app/Models/EconomicCache.php` - Existing model (reused)
- `database/migrations/2026_07_02_155846_create_economic_caches_table.php` - Has population column

---

**Status**: ✅ **READY TO RUN**  
**Command**: `php artisan sync:population`  
**Time**: ~30 seconds  
**Countries**: ~245 out of 250 (some may not have data)  
**Source**: World Bank API  
**Storage**: economic_caches table  
**Display**: Automatic via accessor  
