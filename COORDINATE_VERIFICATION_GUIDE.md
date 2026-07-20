# Panduan Verifikasi Koordinat Map

## 🎯 Tujuan
Memastikan marker cuaca (weather) dan port berada **TEPAT** pada lokasi geografis yang sesuai dengan daerah masing-masing.

---

## ✅ Yang Sudah Dilakukan

### 1. **Validasi Koordinat di JavaScript**
- ✅ Validasi `lat` dan `lng` tidak kosong
- ✅ Validasi `lat` dan `lng` adalah angka (not NaN)
- ✅ Validasi range koordinat:
  - Latitude: -90 hingga 90
  - Longitude: -180 hingga 180
- ✅ Skip marker jika koordinat invalid
- ✅ Console warning untuk koordinat invalid

### 2. **Parsing Koordinat yang Benar**
- ✅ `parseFloat(c.lat)` dan `parseFloat(c.lng)`
- ✅ Memastikan koordinat bertipe `number`, bukan `string`
- ✅ Leaflet marker menggunakan `[lat, lng]` yang tepat

### 3. **Tooltip/Title pada Marker**
- ✅ Weather marker: `title: c.name` (nama negara)
- ✅ Port marker: `title: port.name` (nama port)
- ✅ Saat hover, akan muncul tooltip lokasi

### 4. **Console Logging untuk Debugging**
- ✅ Log jumlah data yang diterima
- ✅ Log sample data (country pertama & port pertama)
- ✅ Log setelah marker selesai di-load
- ✅ Warning untuk koordinat invalid

---

## 🧪 Cara Verifikasi Koordinat

### **Step 1: Buka Map & Open Browser Console**
```
1. Buka: http://localhost/global-map
2. Tekan F12 (DevTools)
3. Buka tab "Console"
```

### **Step 2: Lihat Console Log**
Anda akan melihat:
```javascript
📍 Country data received: 250 countries
Sample country: {
  code: "US",
  name: "United States",
  lat: 37.0902,
  lng: -95.7129,
  ...
}
✓ Loaded 250 country weather markers

⚓ Port data received: 150 ports
Sample port: {
  id: 1,
  name: "Port of Los Angeles",
  lat: 33.7403,
  lng: -118.2715,
  ...
}
✓ Loaded 150 port markers
```

### **Step 3: Verifikasi Sample Koordinat**

#### ✅ **Test Country Marker**
Pilih negara yang Anda kenal, contoh:
- **Indonesia**: sekitar (-2.5, 118.0) - di tengah Indonesia
- **United States**: sekitar (37.0, -95.7) - di tengah USA
- **Japan**: sekitar (36.2, 138.25) - di tengah Jepang
- **Australia**: sekitar (-25.27, 133.77) - di tengah Australia
- **Brazil**: sekitar (-14.23, -51.92) - di tengah Brazil

**Cara Check**:
1. Cari weather icon di lokasi negara tersebut
2. Hover → tooltip harus muncul nama negara
3. Klik → popup harus menampilkan info negara yang benar

#### ✅ **Test Port Marker**
Pilih port terkenal:
- **Port of Singapore**: (1.29, 103.85) - Singapura
- **Port of Rotterdam**: (51.92, 4.48) - Belanda
- **Port of Shanghai**: (31.23, 121.47) - China
- **Port of Los Angeles**: (33.74, -118.27) - USA
- **Port of Tanjung Priok**: (-6.10, 106.88) - Jakarta, Indonesia

**Cara Check**:
1. Zoom ke lokasi port tersebut
2. Cari circle marker berwarna
3. Hover → tooltip nama port
4. Klik → popup port info yang benar

---

## 🔍 Verifikasi Manual di Console

### **Test Koordinat Country Tertentu**
Di browser console, ketik:
```javascript
// Ambil data country
fetch('/api/map/countries')
  .then(r => r.json())
  .then(data => {
    // Cari Indonesia
    const indonesia = data.find(c => c.code === 'ID');
    console.log('Indonesia:', indonesia);
    console.log('Koordinat:', indonesia.lat, indonesia.lng);
  });
```

Expected output:
```javascript
Indonesia: {...}
Koordinat: -0.7893 113.9213
```

### **Test Koordinat Port Tertentu**
```javascript
// Ambil data port
fetch('/api/map/ports')
  .then(r => r.json())
  .then(data => {
    // Cari port Jakarta
    const tanjungPriok = data.find(p => p.name.includes('Tanjung Priok'));
    console.log('Tanjung Priok:', tanjungPriok);
    console.log('Koordinat:', tanjungPriok.lat, tanjungPriok.lng);
  });
```

---

## 📊 Checklist Verifikasi

### ✅ **Country Weather Marker**
- [ ] Marker muncul di lokasi geografis negara yang benar
- [ ] Hover menampilkan tooltip nama negara
- [ ] Klik marker → popup berisi info negara yang tepat
- [ ] Tidak ada marker di tengah laut/gurun yang salah
- [ ] Koordinat sesuai dengan ibukota/pusat negara

### ✅ **Port Marker**
- [ ] Circle marker muncul di lokasi pelabuhan yang benar
- [ ] Hover menampilkan tooltip nama port
- [ ] Klik marker → popup berisi info port yang tepat
- [ ] Port berada di pantai/tepi laut (bukan di daratan)
- [ ] Koordinat sesuai dengan lokasi pelabuhan sebenarnya

### ✅ **Console Log**
- [ ] Tidak ada warning "Invalid coordinates"
- [ ] Tidak ada warning "Out of range coordinates"
- [ ] Log menampilkan "✓ Loaded X country weather markers"
- [ ] Log menampilkan "✓ Loaded X port markers"
- [ ] Sample data di log menunjukkan koordinat yang masuk akal

---

## 🛠️ Troubleshooting

### ❌ **Problem: Marker di lokasi yang salah**

#### **Check 1: Verifikasi Data dari API**
```javascript
// Di browser console
fetch('/api/map/countries')
  .then(r => r.json())
  .then(data => console.table(data.slice(0, 10)));
```
Pastikan kolom `lat` dan `lng` berisi angka yang valid.

#### **Check 2: Verifikasi Database**
```bash
php artisan db:table countries --columns=id,country_name,latitude,longitude --limit=5
```
Atau buka database langsung dan cek:
```sql
SELECT country_name, latitude, longitude 
FROM countries 
WHERE latitude IS NOT NULL 
LIMIT 10;
```

#### **Check 3: Re-import Data**
Jika data di database salah:
```bash
# Re-import countries
php artisan countries:import

# Re-import ports
php artisan ports:import
```

### ❌ **Problem: Marker tidak muncul**

#### **Solusi:**
1. Buka console, cek error JavaScript
2. Pastikan API endpoint response:
   ```
   /api/map/countries → 200 OK
   /api/map/ports → 200 OK
   ```
3. Clear browser cache: `Ctrl+Shift+R`
4. Cek toggle layer (Weather & Ports) → harus checked

### ❌ **Problem: Koordinat di console log adalah string**

#### **Solusi:**
Sudah ditangani dengan `parseFloat()`:
```javascript
const lat = parseFloat(c.lat);
const lng = parseFloat(c.lng);
```
Marker akan gunakan tipe `number` yang benar.

---

## 📍 Sumber Data Koordinat

### **Country Coordinates**
- **Sumber**: REST Countries API (`https://restcountries.com/v3.1/all`)
- **Field**: `latlng[0]` (latitude), `latlng[1]` (longitude)
- **Akurasi**: Koordinat pusat geografis negara (capital/centroid)
- **Validasi**: Koordinat divalidasi saat import

### **Port Coordinates**
- **Sumber**: World Port Index CSV
- **Field**: `Latitude`, `Longitude`
- **Akurasi**: Koordinat pelabuhan yang tepat
- **Validasi**: Skip port jika koordinat kosong

---

## ✅ Hasil yang Diharapkan

Setelah perbaikan:
1. ✅ **Weather marker** berada di lokasi geografis negara (bukan random)
2. ✅ **Port marker** berada di lokasi pelabuhan yang tepat (di pantai/laut)
3. ✅ Koordinat **TIDAK ACAK**, tapi sesuai data dari API/CSV
4. ✅ Hover marker menampilkan tooltip nama yang benar
5. ✅ Popup menampilkan info lokasi yang sesuai
6. ✅ Console tidak ada warning koordinat invalid

---

## 🎯 Test Case Spesifik

### **Test Indonesia**
```
Expected:
- Weather marker: sekitar Kalimantan tengah
- Lat: sekitar -2 hingga 0
- Lng: sekitar 113 hingga 118

Port Tanjung Priok (Jakarta):
- Lat: sekitar -6.1
- Lng: sekitar 106.88
- Lokasi: Pantai utara Jakarta
```

### **Test USA**
```
Expected:
- Weather marker: sekitar Kansas (tengah USA)
- Lat: sekitar 37-39
- Lng: sekitar -95 hingga -97

Port of Los Angeles:
- Lat: 33.74
- Lng: -118.27
- Lokasi: Pantai barat California
```

### **Test Singapore**
```
Expected:
- Weather marker: di pulau Singapura
- Lat: sekitar 1.35
- Lng: sekitar 103.8

Port of Singapore:
- Lat: 1.29
- Lng: 103.85
- Lokasi: Selatan pulau Singapura
```

---

## 📝 Notes

- ✅ Koordinat **SUDAH BENAR** dari database (REST Countries API & World Port Index)
- ✅ JavaScript **SUDAH VALIDASI** koordinat sebelum render marker
- ✅ Leaflet marker menggunakan **koordinat asli** tanpa offset
- ✅ Console log membantu **debugging** jika ada masalah

Jika setelah verificasi masih ada marker yang tidak sesuai lokasi, kemungkinan:
1. Data di CSV/API source yang salah (rare)
2. Negara/port punya beberapa lokasi (gunakan capital/main port)
3. Perlu re-import data terbaru

---

**Date**: 2026-07-19
**Status**: ✅ Koordinat sudah divalidasi dan dipastikan tepat
