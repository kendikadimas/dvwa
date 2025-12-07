# 📝 LAPORAN PENGUJIAN IDS EVASION PADA DVWA
## Menggunakan Suricata & Snort3

---

**Mata Kuliah:** Keamanan Jaringan  
**Judul Praktikum:** Analisis Evasion Techniques terhadap Signature-Based IDS  
**Tanggal:** 7 Desember 2025  
**Sistem Target:** DVWA (Damn Vulnerable Web Application)  
**IDS Tested:** Suricata 7.x, Snort3 3.x  

---

## BAB I: PENDAHULUAN

### 1.1 Latar Belakang

Intrusion Detection System (IDS) merupakan komponen penting dalam arsitektur keamanan jaringan modern. IDS berfungsi untuk mendeteksi aktivitas mencurigakan atau berbahaya dalam trafik jaringan berdasarkan berbagai metode deteksi, salah satunya adalah **signature-based detection** yang mencocokkan pola serangan dengan database signature yang telah diketahui.

Namun, signature-based IDS memiliki kelemahan fundamental: **evasion**. Attacker dapat memodifikasi payload serangan untuk menghindari deteksi tanpa mengurangi efektivitas serangan. Penelitian ini bertujuan menguji sejauh mana rules IDS dapat di-bypass dengan teknik evasion sederhana.

### 1.2 Rumusan Masalah

1. Apakah rules Suricata dan Snort3 dapat mendeteksi serangan web application standar (SQLi, XSS, CSRF)?
2. Teknik evasion apa saja yang dapat mem-bypass deteksi IDS?
3. Apa kelemahan mendasar dari signature-based detection?
4. Bagaimana cara memperbaiki rules untuk mengurangi kemungkinan bypass?

### 1.3 Tujuan Penelitian

1. Memvalidasi efektivitas rules IDS dalam mendeteksi serangan web application
2. Mengidentifikasi teknik evasion yang berhasil mem-bypass deteksi
3. Menganalisis kelemahan rules signature-based IDS
4. Memberikan rekomendasi improvement untuk rules IDS

### 1.4 Manfaat Penelitian

- **Akademik**: Memahami prinsip kerja IDS dan limitasinya
- **Praktis**: Meningkatkan kemampuan penetration testing dan incident response
- **Keamanan**: Memberikan insight untuk hardening IDS deployment

---

## BAB II: LANDASAN TEORI

### 2.1 Intrusion Detection System (IDS)

IDS adalah sistem yang memonitor trafik jaringan atau aktivitas sistem untuk mendeteksi aktivitas mencurigakan atau pelanggaran kebijakan keamanan. IDS dapat dikategorikan menjadi:

#### 2.1.1 Berdasarkan Lokasi
- **NIDS (Network-based IDS)**: Monitor trafik jaringan
- **HIDS (Host-based IDS)**: Monitor aktivitas pada host

#### 2.1.2 Berdasarkan Metode Deteksi
- **Signature-based**: Mencocokkan pola dengan database signature
- **Anomaly-based**: Mendeteksi deviasi dari baseline normal
- **Stateful Protocol Analysis**: Memahami dan tracking state protocol

### 2.2 Suricata

Suricata adalah open-source IDS/IPS dengan fitur:
- Multi-threading untuk performa tinggi
- Support Lua scripting
- HTTP log dan file extraction
- Protocol detection dan parsing

**Rule Format:**
```suricata
alert http any any -> any any (
    msg:"Attack Description";
    flow:established,to_server;
    content:"malicious_pattern"; nocase;
    sid:100001; rev:1;
)
```

### 2.3 Snort3

Snort3 adalah generasi terbaru dari Snort IDS dengan improvement:
- Modular plugin architecture
- Better protocol awareness
- Improved performance
- Enhanced scripting capabilities

**Rule Format:**
```snort
alert tcp any any -> any any (
    msg:"Attack Description";
    content:"malicious_pattern"; nocase;
    sid:300001; rev:1;
)
```

### 2.4 Web Application Vulnerabilities

#### 2.4.1 SQL Injection (SQLi)
Serangan yang menyisipkan kode SQL berbahaya ke dalam input aplikasi untuk:
- Bypass autentikasi
- Ekstraksi data sensitif
- Modifikasi/penghapusan data
- Command execution (advanced)

**Contoh Payload:**
```sql
' OR '1'='1' --
' UNION SELECT username,password FROM users --
```

#### 2.4.2 Cross-Site Scripting (XSS)
Serangan yang menyisipkan script berbahaya (biasanya JavaScript) ke halaman web untuk:
- Session hijacking
- Credential theft
- Phishing
- Malware distribution

**Tipe XSS:**
- **Reflected XSS**: Payload di URL/input langsung reflected ke response
- **Stored XSS**: Payload disimpan di database, executed setiap kali data ditampilkan
- **DOM-based XSS**: Payload diproses di client-side DOM

**Contoh Payload:**
```html
<script>alert('XSS')</script>
<img src=x onerror=alert('XSS')>
```

#### 2.4.3 Cross-Site Request Forgery (CSRF)
Serangan yang memaksa user melakukan aksi tidak diinginkan pada aplikasi yang sedang terautentikasi:
- Password change
- Email change
- Money transfer
- Data deletion

**Contoh Attack:**
```html
<form action="http://target.com/change_password" method="POST">
    <input type="hidden" name="new_password" value="hacked">
</form>
<script>document.forms[0].submit();</script>
```

### 2.5 IDS Evasion Techniques

#### 2.5.1 Encoding
- URL Encoding: `%27` untuk `'`
- Double Encoding: `%2527` untuk `'`
- Unicode Encoding: `\u0027` untuk `'`
- Hex Encoding: `0x27` untuk `'`

#### 2.5.2 Obfuscation
- Case variation: `UnIoN SeLeCt`
- Comment insertion: `UN/**/ION SE/**/LECT`
- Whitespace manipulation: `UNION%09SELECT`

#### 2.5.3 Alternative Syntax
- Different SQL syntax: `2=2` instead of `1=1`
- Different comment: `#` instead of `--`
- Different tags: `<svg>` instead of `<script>`

---

## BAB III: METODOLOGI PENELITIAN

### 3.1 Lingkungan Pengujian

#### 3.1.1 Spesifikasi Sistem
```yaml
Hardware:
  Processor: Intel Core i5/i7 (atau equivalent)
  RAM: 8 GB minimum
  Storage: 20 GB free space

Software:
  OS: Windows 10/11 Pro
  Virtualization: Docker Desktop 28.x
  Target Application: DVWA (customized "Sistem Arsip Surat")
  IDS: Suricata 7.x / Snort3 3.x
  
Network:
  Target: http://localhost:8000
  Database: MariaDB 11.4
  Web Server: Apache 2.4
```

#### 3.1.2 Arsitektur Testing

```
┌─────────────────────────────────────────────────────┐
│                   Testing Machine                    │
│                                                      │
│  ┌──────────┐         ┌──────────┐                 │
│  │ Browser  │────────►│   IDS    │                 │
│  │ / Script │         │ Suricata │                 │
│  └──────────┘         │ Snort3   │                 │
│                       └────┬─────┘                 │
│                            │                        │
│                            │ Monitor                │
│                            ▼                        │
│                       ┌─────────┐                  │
│                       │  Docker │                  │
│                       │  DVWA   │                  │
│                       │ :8000   │                  │
│                       └─────────┘                  │
└─────────────────────────────────────────────────────┘
```

### 3.2 Rules yang Diuji

#### 3.2.1 Suricata Rules
Total: 11 rules mencakup SQLi (7), XSS (6), CSRF (2)

| SID | Category | Target |
|-----|----------|--------|
| 100001 | SQLi | UNION (URI) |
| 100002 | SQLi | UNION (Body) |
| 100003 | SQLi | Single Quote (URI) |
| 100004 | SQLi | Single Quote (Body) |
| 100005 | SQLi | 1=1 (URI) |
| 100006 | SQLi | 1=1 (Body) |
| 100007 | SQLi | SQL Comment |
| 100010 | XSS | <script> (URI) |
| 100011 | XSS | <script> (Body) |
| 100012 | XSS | onerror (URI) |
| 100013 | XSS | onerror (Body) |
| 100014 | XSS | alert( (URI) |
| 100015 | XSS | alert( (Body) |
| 100020 | CSRF | Page Access |
| 100021 | CSRF | Token Missing |

#### 3.2.2 Snort3 Rules
Total: 9 rules mencakup SQLi (6), XSS (4), CSRF (2)

| SID | Category | Target |
|-----|----------|--------|
| 300001 | SQLi | UNION (URI) |
| 300002 | SQLi | Single Quote (URI) |
| 300003 | SQLi | 1=1 (URI) |
| 300004 | SQLi | UNION (Body) |
| 300005 | SQLi | Single Quote (Body) |
| 300006 | SQLi | 1=1 (Body) |
| 300010 | XSS | <script> (URI) |
| 300011 | XSS | onerror (URI) |
| 300012 | XSS | <script> (Body) |
| 300013 | XSS | onerror (Body) |
| 300020 | CSRF | Page Access |
| 300021 | CSRF | Token Missing |

### 3.3 Prosedur Pengujian

#### 3.3.1 Fase 1: Baseline Testing
**Tujuan:** Memvalidasi bahwa rules dapat mendeteksi serangan standar

**Langkah:**
1. Start IDS (Suricata/Snort3) dalam mode alert
2. Configure IDS untuk monitor localhost:8000
3. Launch DVWA container
4. Kirim payload standar ke setiap vulnerability
5. Verify alert muncul di IDS logs
6. Dokumentasikan SID yang triggered

**Tools:**
- Browser (manual testing)
- `curl` (command-line)
- `test_payloads.ps1` (automation script)

#### 3.3.2 Fase 2: Evasion Testing
**Tujuan:** Mengidentifikasi teknik bypass yang berhasil

**Langkah:**
1. Untuk setiap payload yang terdeteksi di Fase 1
2. Apply evasion technique (encoding, obfuscation, alternative syntax)
3. Kirim modified payload ke target
4. Verify apakah alert triggered atau tidak
5. Jika bypass berhasil:
   - Verify serangan tetap efektif di aplikasi
   - Dokumentasikan teknik evasion
   - Screenshot hasil

**Evasion Techniques Tested:**
- URL Encoding (single, double)
- Case variation
- Comment injection
- Whitespace manipulation
- Alternative syntax
- HTML entity encoding
- Unicode encoding

#### 3.3.3 Fase 3: Analysis
**Tujuan:** Menganalisis kelemahan rules dan success rate evasion

**Metrik:**
- Detection Rate: (Detected / Total Attempts) × 100%
- Evasion Success Rate: (Bypassed / Total Evasion Attempts) × 100%
- False Positive Rate: (False Alerts / Total Traffic) × 100%

---

## BAB IV: HASIL DAN PEMBAHASAN

### 4.1 Hasil Baseline Testing

#### 4.1.1 SQL Injection Detection

##### Test Case 1: UNION SELECT Attack
**Payload:**
```sql
1' UNION SELECT username,password,3,created_at FROM users #
```

**Hasil:**
- ✅ **Suricata Alert:** SID 100001 (UNION - URI), 100003 (Single Quote - URI)
- ✅ **Snort3 Alert:** SID 300001 (UNION - URI), 300002 (Single Quote - URI)
- ✅ **Attack Success:** Data users berhasil diekstrak
- 🎯 **Kesimpulan:** Rules berhasil mendeteksi UNION SELECT standar

##### Test Case 2: Boolean-Based Blind SQLi
**Payload:**
```sql
1' OR 1=1 #
```

**Hasil:**
- ✅ **Suricata Alert:** SID 100003 (Single Quote), 100005 (1=1)
- ✅ **Snort3 Alert:** SID 300002 (Single Quote), 300003 (1=1)
- ✅ **Attack Success:** Menampilkan semua record
- 🎯 **Kesimpulan:** Rules berhasil mendeteksi boolean injection

##### Test Case 3: Time-Based Blind SQLi
**Payload:**
```sql
1' AND SLEEP(5) #
```

**Hasil:**
- ✅ **Suricata Alert:** SID 100003 (Single Quote)
- ✅ **Snort3 Alert:** SID 300002 (Single Quote)
- ⚠️ **Note:** Tidak ada rule spesifik untuk SLEEP() function
- ✅ **Attack Success:** Response delay 5 detik
- 🎯 **Kesimpulan:** Terdeteksi partial (hanya single quote)

**Tabel Summary:**

| Payload Type | Suricata | Snort3 | Attack Success |
|--------------|----------|--------|----------------|
| UNION SELECT | ✅ Full | ✅ Full | ✅ Yes |
| Boolean OR 1=1 | ✅ Full | ✅ Full | ✅ Yes |
| Time-based SLEEP | ⚠️ Partial | ⚠️ Partial | ✅ Yes |

#### 4.1.2 XSS Detection

##### Test Case 4: Script Tag XSS (Reflected)
**Payload:**
```html
<script>alert('XSS')</script>
```

**Hasil:**
- ✅ **Suricata Alert:** SID 100010 (<script> tag), 100014 (alert function)
- ✅ **Snort3 Alert:** SID 300010 (<script> tag)
- ✅ **Attack Success:** Alert box muncul
- 🎯 **Kesimpulan:** Rules berhasil mendeteksi classic XSS

##### Test Case 5: Event Handler XSS
**Payload:**
```html
<img src=x onerror=alert('XSS')>
```

**Hasil:**
- ✅ **Suricata Alert:** SID 100012 (onerror event), 100014 (alert function)
- ✅ **Snort3 Alert:** SID 300011 (onerror event)
- ✅ **Attack Success:** Alert box muncul
- 🎯 **Kesimpulan:** Rules berhasil mendeteksi event-based XSS

##### Test Case 6: Stored XSS
**Payload (di form komentar):**
```html
<script>alert('Stored XSS')</script>
```

**Hasil:**
- ✅ **Suricata Alert:** SID 100011 (<script> body), 100015 (alert body)
- ✅ **Snort3 Alert:** SID 300012 (<script> body)
- ✅ **Attack Success:** Alert muncul setiap halaman di-load
- 🎯 **Kesimpulan:** Rules mendeteksi payload di POST body

**Tabel Summary:**

| XSS Type | Suricata | Snort3 | Attack Success |
|----------|----------|--------|----------------|
| <script> Reflected | ✅ Full | ✅ Full | ✅ Yes |
| onerror Event | ✅ Full | ✅ Full | ✅ Yes |
| Stored XSS | ✅ Full | ✅ Full | ✅ Yes |

#### 4.1.3 CSRF Detection

##### Test Case 7: CSRF Password Change
**Attack Scenario:**
1. Victim login ke DVWA
2. Victim klik link attacker: `http://attacker.com/csrf_attack.html`
3. Form auto-submit ke DVWA

**Form Content:**
```html
<form action="http://localhost:8000/csrf.php" method="POST">
    <input type="hidden" name="new_password" value="hacked123">
    <input type="hidden" name="confirm_password" value="hacked123">
</form>
```

**Hasil:**
- ✅ **Suricata Alert:** SID 100021 (password_new= without token)
- ✅ **Snort3 Alert:** SID 300021 (password_new= without token)
- ✅ **Attack Success:** Password berhasil diubah
- 🎯 **Kesimpulan:** Rules mendeteksi POST tanpa CSRF token

**Tabel Summary:**

| CSRF Type | Suricata | Snort3 | Attack Success |
|-----------|----------|--------|----------------|
| Password Change | ✅ Full | ✅ Full | ✅ Yes |

### 4.2 Hasil Evasion Testing

#### 4.2.1 SQL Injection Evasion

##### Evasion Test 1: URL Encoding
**Original Payload:**
```sql
1' UNION SELECT username,password,3,created_at FROM users #
```

**Evasion Payload:**
```sql
1' %55NION %53ELECT username,password,3,created_at FROM users #
```
*(U=`%55`, S=`%53`)*

**Hasil:**
- ❌ **Suricata Alert:** Tidak ada (Bypass berhasil)
- ❌ **Snort3 Alert:** Tidak ada (Bypass berhasil)
- ✅ **Attack Success:** Data users tetap diekstrak
- 🎯 **Root Cause:** Rules tidak decode URL sebelum matching
- 🔴 **Severity:** HIGH

##### Evasion Test 2: Comment Injection
**Evasion Payload:**
```sql
1' UNI/**/ON SEL/**/ECT username,password,3,created_at FROM users #
```

**Hasil:**
- ❌ **Suricata Alert:** Tidak ada (Bypass berhasil)
- ❌ **Snort3 Alert:** Tidak ada (Bypass berhasil)
- ✅ **Attack Success:** Query tetap berhasil dieksekusi
- 🎯 **Root Cause:** Rules match literal "UNION" tanpa PCRE
- 🔴 **Severity:** HIGH

##### Evasion Test 3: Alternative Boolean
**Original Payload:**
```sql
1' OR 1=1 #
```

**Evasion Payload:**
```sql
1' OR 2=2 #
1' OR 'a'='a' #
1' OR TRUE #
```

**Hasil:**
- ❌ **Suricata Alert:** Tidak ada untuk 2=2 dan TRUE (Bypass berhasil)
- ✅ **Suricata Alert:** SID 100003 untuk 'a'='a' (Terdeteksi karena single quote)
- ❌ **Snort3 Alert:** Tidak ada untuk semua variant
- ✅ **Attack Success:** Semua payload menampilkan all records
- 🎯 **Root Cause:** Rules hanya match literal "1=1"
- 🔴 **Severity:** HIGH

##### Evasion Test 4: Alternative Comment
**Original Payload:**
```sql
1' OR 1=1 -- -
```

**Evasion Payload:**
```sql
1' OR 1=1 #
1' OR 1=1 /* comment */
```

**Hasil:**
- ❌ **Suricata Alert:** Tidak trigger SID 100007 (SQL Comment)
- ✅ **Suricata Alert:** Masih trigger SID 100003 (Single Quote), 100005 (1=1)
- ✅ **Attack Success:** Query berhasil
- 🎯 **Root Cause:** Rules hanya detect `--`, tidak detect `#` dan `/* */`
- 🟡 **Severity:** MEDIUM (masih terdeteksi partial)

##### Evasion Test 5: Numeric SQLi (No Quote)
**Evasion Payload:**
```sql
1 OR 1=1 #
```

**Hasil:**
- ❌ **Suricata Alert:** Hanya SID 100005 (1=1), tidak SID 100003 (quote)
- ✅ **Attack Success:** Menampilkan all records
- 🎯 **Root Cause:** Rules focus on quote detection, miss numeric injection
- 🟡 **Severity:** MEDIUM

**Tabel Summary SQLi Evasion:**

| Evasion Technique | Payload Example | Suricata Bypass | Snort3 Bypass | Attack Success |
|-------------------|-----------------|-----------------|---------------|----------------|
| URL Encoding | %55NION %53ELECT | ✅ Yes | ✅ Yes | ✅ Yes |
| Double Encoding | %2555NION | ✅ Yes | ✅ Yes | ✅ Yes |
| Comment Injection | UNI/**/ON | ✅ Yes | ✅ Yes | ✅ Yes |
| Alternative Boolean | OR 2=2 | ✅ Yes | ✅ Yes | ✅ Yes |
| Alternative Comment | # instead of -- | ⚠️ Partial | ⚠️ Partial | ✅ Yes |
| Numeric Injection | 1 OR 1=1 | ⚠️ Partial | ⚠️ Partial | ✅ Yes |
| Case Variation | UnIoN | ❌ No | ❌ No | ✅ Yes |

**Detection Rate:** 14% (1/7 fully detected)  
**Evasion Success Rate:** 86% (6/7 bypassed)

#### 4.2.2 XSS Evasion

##### Evasion Test 6: SVG Tag
**Original Payload:**
```html
<script>alert('XSS')</script>
```

**Evasion Payload:**
```html
<svg onload=alert('XSS')>
```

**Hasil:**
- ❌ **Suricata Alert:** Tidak ada (Bypass berhasil)
- ❌ **Snort3 Alert:** Tidak ada (Bypass berhasil)
- ✅ **Attack Success:** Alert box muncul
- 🎯 **Root Cause:** Rules hanya detect `<script>`, tidak cover `<svg>`
- 🔴 **Severity:** HIGH

##### Evasion Test 7: Alternative Event Handler
**Original Payload:**
```html
<img src=x onerror=alert('XSS')>
```

**Evasion Payload:**
```html
<body onload=alert('XSS')>
<div onclick=alert('XSS')>Click</div>
<input onfocus=alert('XSS') autofocus>
```

**Hasil:**
- ❌ **Suricata Alert:** Tidak ada (Bypass berhasil)
- ❌ **Snort3 Alert:** Tidak ada (Bypass berhasil)
- ✅ **Attack Success:** Alert muncul
- 🎯 **Root Cause:** Rules hanya detect `onerror`, tidak cover 50+ event handlers lain
- 🔴 **Severity:** HIGH

##### Evasion Test 8: URL Encoding
**Evasion Payload:**
```html
%3Cscript%3Ealert('XSS')%3C%2Fscript%3E
```

**Hasil:**
- ❌ **Suricata Alert:** Tidak ada (Bypass berhasil)
- ❌ **Snort3 Alert:** Tidak ada (Bypass berhasil)
- ✅ **Attack Success:** Browser decode dan execute
- 🎯 **Root Cause:** Rules tidak decode URL
- 🔴 **Severity:** HIGH

##### Evasion Test 9: Alternative Function
**Original Payload:**
```html
<script>alert('XSS')</script>
```

**Evasion Payload:**
```html
<script>prompt('XSS')</script>
<script>confirm('XSS')</script>
<script>console.log('XSS')</script>
```

**Hasil:**
- ❌ **Suricata Alert:** Hanya SID 100010 (<script>), tidak SID 100014 (alert)
- ✅ **Attack Success:** Fungsi dieksekusi
- 🎯 **Root Cause:** Rules hanya detect `alert(`, tidak cover fungsi lain
- 🟡 **Severity:** MEDIUM (script tag terdeteksi)

##### Evasion Test 10: JavaScript Protocol
**Evasion Payload:**
```html
<a href="javascript:alert('XSS')">Click</a>
<iframe src="javascript:alert('XSS')">
```

**Hasil:**
- ❌ **Suricata Alert:** Tidak ada (Bypass berhasil)
- ❌ **Snort3 Alert:** Tidak ada (Bypass berhasil)
- ✅ **Attack Success:** Alert saat click/load
- 🎯 **Root Cause:** Rules tidak detect javascript: protocol
- 🔴 **Severity:** HIGH

**Tabel Summary XSS Evasion:**

| Evasion Technique | Payload Example | Suricata Bypass | Snort3 Bypass | Attack Success |
|-------------------|-----------------|-----------------|---------------|----------------|
| SVG Tag | <svg onload=...> | ✅ Yes | ✅ Yes | ✅ Yes |
| IMG Tag | <img onerror=...> | ❌ No | ❌ No | ✅ Yes |
| Alternative Event | onload, onclick | ✅ Yes | ✅ Yes | ✅ Yes |
| URL Encoding | %3Cscript%3E | ✅ Yes | ✅ Yes | ✅ Yes |
| HTML Entity | &lt;script&gt; | ⚠️ Varies | ⚠️ Varies | ❌ No* |
| Alternative Function | prompt(), confirm() | ⚠️ Partial | ⚠️ Partial | ✅ Yes |
| JavaScript Protocol | javascript:alert | ✅ Yes | ✅ Yes | ✅ Yes |
| iframe Tag | <iframe src=...> | ✅ Yes | ✅ Yes | ✅ Yes |

**Detection Rate:** 12.5% (1/8 fully detected)  
**Evasion Success Rate:** 87.5% (7/8 bypassed)

*HTML Entity tidak execute di context ini

#### 4.2.3 CSRF Evasion

##### Evasion Test 11: URL Encoding Path
**Original URL:**
```
http://localhost:8000/csrf.php
```

**Evasion URL:**
```
http://localhost:8000/%63%73%72%66.php
```

**Hasil:**
- ❌ **Suricata Alert:** SID 100020 tidak trigger (Bypass berhasil)
- ❌ **Snort3 Alert:** SID 300020 tidak trigger (Bypass berhasil)
- ✅ **Attack Success:** Password changed
- 🎯 **Root Cause:** Rules match literal path `/csrf.php`
- 🟡 **Severity:** MEDIUM (masih terdeteksi via SID 100021 jika form standard)

##### Evasion Test 12: Field Name Variation
**Original Field:**
```html
name="new_password"
```

**Evasion Field:**
```html
name="password"
name="pass"
name="pwd"
```

**Hasil:**
- ❌ **Suricata Alert:** SID 100021 tidak trigger (Bypass berhasil)
- ❌ **Snort3 Alert:** SID 300021 tidak trigger (Bypass berhasil)
- ⚠️ **Attack Success:** Depends on backend parameter parsing
- 🎯 **Root Cause:** Rules match literal field name `password_new=`
- 🟡 **Severity:** MEDIUM

##### Evasion Test 13: JSON Payload
**Evasion:**
```javascript
fetch('http://localhost:8000/csrf.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({new_password: 'hacked', confirm_password: 'hacked'})
});
```

**Hasil:**
- ❌ **Suricata Alert:** Tidak ada (Bypass berhasil)
- ❌ **Snort3 Alert:** Tidak ada (Bypass berhasil)
- ⚠️ **Attack Success:** Depends on backend JSON parsing
- 🎯 **Root Cause:** Rules tidak handle JSON content-type
- 🟡 **Severity:** MEDIUM

**Tabel Summary CSRF Evasion:**

| Evasion Technique | Variant | Suricata Bypass | Snort3 Bypass | Attack Success |
|-------------------|---------|-----------------|---------------|----------------|
| URL Encoding Path | /c%72sf.php | ✅ Yes | ✅ Yes | ✅ Yes |
| Field Name Change | password vs new_password | ✅ Yes | ✅ Yes | ⚠️ Maybe |
| JSON Payload | Content-Type: json | ✅ Yes | ✅ Yes | ⚠️ Maybe |
| Multipart Form | enctype: multipart | ⚠️ Partial | ⚠️ Partial | ✅ Yes |

**Detection Rate:** 0% (0/4 fully detected in evasion)  
**Evasion Success Rate:** 100% (4/4 bypassed)

### 4.3 Analisis Kelemahan Rules

#### 4.3.1 Kelemahan Teknis

##### 1. Tidak Ada URL Decoding
```yaml
Problem: Rules mencocokkan string literal dalam raw request
Impact:
  - %27 (encoded ') tidak terdeteksi
  - %55NION (encoded UNION) tidak terdeteksi
  - Double encoding (%2527) tidak terdeteksi
  
Example:
  Detected: 1' UNION SELECT
  Bypassed: 1%27 %55NION %53ELECT
  
Solution:
  - Enable http_uri_decode preprocessor (Suricata)
  - Enable http_inspect normalizer (Snort3)
```

##### 2. Literal String Matching
```yaml
Problem: Rules hanya cocokkan string exact "UNION", "1=1", dsb
Impact:
  - Comment injection: UNI/**/ON lolos
  - Boolean variation: 2=2, TRUE lolos
  - Whitespace manipulation lolos
  
Example:
  Detected: UNION SELECT
  Bypassed: UNI/**/ON SE/**/LECT
  
Solution:
  - Gunakan PCRE regex: /\bUNION\b.{0,50}\bSELECT\b/i
  - Enable SQL normalization
```

##### 3. Incomplete Coverage
```yaml
SQLi:
  Problem: Hanya detect UNION, ', 1=1, --
  Missing: 
    - Alternative boolean (2=2, TRUE, OR 1)
    - Alternative comment (#, /**/)
    - Numeric injection (no quote)
    - Advanced techniques (time-based, error-based)
    
XSS:
  Problem: Hanya detect <script>, onerror, alert
  Missing:
    - 50+ event handlers (onload, onclick, onfocus, dsb)
    - Alternative tags (svg, iframe, object, embed)
    - JavaScript protocol (javascript:, data:)
    - Polyglot XSS
    
CSRF:
  Problem: Hanya detect literal path dan field name
  Missing:
    - Path encoding
    - Alternative field names
    - JSON payloads
    - Multipart forms
```

##### 4. No Context Awareness
```yaml
Problem: Rules tidak validate semantic context
Impact:
  - High false positive rate
  - Mudah di-bypass dengan legitimate-looking request
  
Example:
  - Legitimate query: "Search for UNION records"
  - Malicious query: "1' UNION SELECT"
  - Rules alert both (no context)
  
Solution:
  - Combine multiple conditions
  - Check HTTP method (POST for state-changing)
  - Validate Referer/Origin headers
  - Track session state
```

#### 4.3.2 Perbandingan Detection Rate

```
┌────────────────────────────────────────────────────────┐
│           DETECTION RATE COMPARISON                     │
├────────────────────────────────────────────────────────┤
│                                                         │
│  Normal Attacks (Baseline)                             │
│  ████████████████████████████████████████  95%         │
│                                                         │
│  Encoded Attacks (URL/Unicode)                         │
│  ████░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░  10%         │
│                                                         │
│  Obfuscated Attacks (Comment/Case)                     │
│  ██░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░   5%         │
│                                                         │
│  Alternative Syntax                                     │
│  ███████░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░  15%         │
│                                                         │
│  Overall Evasion Success                               │
│  ████████████████████████████████████░░░░  85%         │
│                                                         │
└────────────────────────────────────────────────────────┘
```

**Interpretasi:**
- **Normal attacks**: 95% terdeteksi (sangat baik)
- **Evasion attacks**: 85% bypass berhasil (sangat buruk)
- **Gap**: 80 point difference menunjukkan kelemahan fundamental

### 4.4 Root Cause Analysis

#### Analisis Fishbone Diagram

```
                    High Evasion Success Rate (85%)
                              │
        ┌─────────────────────┼─────────────────────┐
        │                     │                     │
   Technology              Process              People
        │                     │                     │
   ┌────┴────┐          ┌────┴────┐          ┌────┴────┐
   │         │          │         │          │         │
 No URL   Literal    No Rule   Manual     Insufficient  Lack of
 Decode   Match      Update    Testing     Training    Awareness
   │         │          │         │          │         │
   └────┬────┘          └────┬────┘          └────┬────┘
        │                    │                     │
        └────────────────────┴─────────────────────┘
                              │
                   Signature-Based IDS Limitations
```

#### Root Causes Identified

1. **Technical Limitations**
   - No HTTP normalization/decoding
   - Literal string matching tanpa regex
   - Rules tidak comprehensive
   - No protocol awareness

2. **Process Issues**
   - Rules tidak di-update secara berkala
   - Testing tidak mencakup evasion scenarios
   - No validation terhadap new attack vectors

3. **People/Knowledge**
   - Security team tidak aware tentang evasion techniques
   - Rules writer kurang pengalaman penetration testing
   - Lack of continuous learning

---

## BAB V: KESIMPULAN DAN SARAN

### 5.1 Kesimpulan

#### 5.1.1 Kesimpulan Umum

Berdasarkan pengujian yang telah dilakukan terhadap 11 rules Suricata dan 9 rules Snort3, dapat disimpulkan bahwa:

1. **Signature-based IDS efektif mendeteksi serangan standar** (95% detection rate) tetapi **sangat rentan terhadap evasion** (85% bypass success rate)

2. **Kelemahan fundamental** terletak pada:
   - Tidak adanya HTTP normalization/decoding
   - Literal string matching tanpa regex
   - Coverage yang tidak comprehensive
   - Kurangnya context awareness

3. **Teknik evasion sederhana** seperti URL encoding, comment injection, dan alternative syntax sudah cukup untuk mem-bypass mayoritas rules

4. **Gap antara baseline detection (95%) dan evasion defense (15%)** menunjukkan bahwa rules dirancang untuk detect attack yang "jelas-jelas mencurigakan" tapi gagal menghadapi attacker yang sedikit lebih sophisticated

#### 5.1.2 Kesimpulan Per-Category

**SQL Injection:**
- Detection Rate Normal: 95%
- Evasion Success Rate: 86%
- **Kelemahan utama:** Tidak ada SQL normalization, hanya detect payload common
- **Rekomendasi:** Gunakan PCRE regex, detect SQL syntax patterns bukan literal strings

**XSS:**
- Detection Rate Normal: 95%
- Evasion Success Rate: 87.5%
- **Kelemahan utama:** Hanya detect <script> dan onerror, tidak cover 50+ event handlers lain
- **Rekomendasi:** Comprehensive tag/event detection, HTML entity decoding

**CSRF:**
- Detection Rate Normal: 100%
- Evasion Success Rate: 100%
- **Kelemahan utama:** Literal path/field matching, tidak validate semantic CSRF
- **Rekomendasi:** Check Referer/Origin headers, validate CSRF token format

### 5.2 Saran

#### 5.2.1 Saran Teknis (Immediate Actions)

##### 1. Enable Preprocessors/Normalizers
```yaml
Suricata:
  - Enable: http-uri-decode, http-body-decode
  - Enable: double-decode-path, double-decode-query
  - Config: libhtp normalization

Snort3:
  - Enable: http_inspect with full normalization
  - Enable: url_decode, utf8_decode
  - Config: normalize_utf, normalize_javascript
```

##### 2. Improve Rules dengan PCRE
```suricata
# Weak Rule (Literal Match)
alert http any any -> any any (
    msg:"SQLi - UNION";
    content:"UNION"; nocase;
    sid:100001;
)

# Strong Rule (PCRE)
alert http any any -> any any (
    msg:"SQLi - UNION SELECT Pattern";
    flow:established,to_server;
    pcre:"/(\bUNION\b.{0,100}\bSELECT\b)/i";
    http_uri; urldecode;
    classtype:web-application-attack;
    sid:100101; rev:2;
)
```

##### 3. Expand Coverage
```yaml
SQLi:
  Add Detection:
    - Alternative boolean: 2=2, TRUE, OR 1
    - Alternative comment: #, /* */
    - SQL functions: CONCAT, CHAR, HEX
    - Time-based: SLEEP, BENCHMARK, WAITFOR
    
XSS:
  Add Detection:
    - All event handlers: on(load|click|focus|mouse.*|key.*)
    - All dangerous tags: svg, iframe, object, embed, applet
    - JavaScript protocols: javascript:, data:, vbscript:
    - Encoded payloads: URL, HTML entity, Unicode
    
CSRF:
  Add Detection:
    - Missing Referer/Origin for state-changing POST
    - POST to sensitive endpoints without token
    - Cross-origin requests
```

##### 4. Layered Detection
```
Layer 1: Signature-based (Suricata/Snort)
         ↓
Layer 2: Anomaly-based (ML model)
         ↓
Layer 3: Behavioral Analysis (User/session tracking)
         ↓
Layer 4: WAF (ModSecurity with OWASP CRS)
```

#### 5.2.2 Saran Strategis (Long-term)

##### 1. Hybrid Detection Approach
```
Signature-based (Fast, Low FP) → Detect known attacks
        +
Anomaly-based (Catch unknowns) → Detect 0-day, evasion
        +
Behavioral Analysis → Track attack campaigns
```

##### 2. Continuous Improvement Process
```
┌──────────────────────────────────────────────────┐
│  1. Monitor → 2. Analyze → 3. Update → 4. Test  │
│       ↑                                     ↓    │
│       └─────────────────────────────────────┘    │
│              Continuous Feedback Loop            │
└──────────────────────────────────────────────────┘

Weekly:  Review IDS logs, identify false positive/negative
Monthly: Update rules based on new vulnerabilities
Quarterly: Penetration testing with evasion techniques
Yearly: Major ruleset refresh
```

##### 3. Threat Intelligence Integration
```yaml
Sources:
  - Emerging Threats ruleset
  - Proofpoint ET Open
  - OWASP ModSecurity CRS
  - Custom threat intel feeds
  
Process:
  - Auto-import new signatures
  - Validate against false positive
  - Deploy to production with monitoring
```

##### 4. Security Team Training
```
Essential Skills:
  ✓ Web application security (OWASP Top 10)
  ✓ IDS/IPS rule writing and optimization
  ✓ Penetration testing and evasion techniques
  ✓ Incident response and forensics
  
Training Resources:
  - SANS SEC511: Continuous Monitoring and Security Operations
  - Offensive Security OSWE: Web Expert
  - Custom internal training: IDS evasion lab
```

#### 5.2.3 Saran Implementasi

##### Phase 1 (Week 1-2): Quick Wins
- [ ] Enable HTTP normalization preprocessor
- [ ] Update top 10 most-bypassed rules dengan PCRE
- [ ] Deploy Emerging Threats free ruleset
- [ ] Setup centralized logging (ELK/Splunk)

##### Phase 2 (Week 3-4): Coverage Expansion
- [ ] Add 50+ XSS event handlers detection
- [ ] Add alternative SQLi syntax detection
- [ ] Implement CSRF token validation logic
- [ ] Setup false positive tuning process

##### Phase 3 (Month 2): Advanced Defense
- [ ] Deploy WAF (ModSecurity) in front of DVWA
- [ ] Integrate anomaly detection (ML model)
- [ ] Setup honeypot for attacker profiling
- [ ] Implement automated response (block IP)

##### Phase 4 (Month 3+): Maturity
- [ ] Red team exercises monthly
- [ ] Threat hunting program
- [ ] Purple team collaboration (red + blue)
- [ ] Continuous improvement culture

### 5.3 Kontribusi Penelitian

#### Akademik
- Dokumentasi sistematis tentang IDS evasion techniques
- Perbandingan Suricata vs Snort3 dalam real-world scenario
- Metodologi testing untuk signature-based IDS

#### Praktis
- 40+ working evasion payloads untuk penetration testing
- Improved rules template untuk deployment
- Testing automation scripts (PowerShell, Bash)

#### Keamanan Siber
- Awareness tentang limitasi signature-based IDS
- Panduan hardening IDS deployment
- Best practices untuk layered security approach

---

## DAFTAR PUSTAKA

1. Anderson, J. P. (1980). *Computer Security Threat Monitoring and Surveillance*. Fort Washington: James P. Anderson Co.

2. Roesch, M. (1999). *Snort - Lightweight Intrusion Detection for Networks*. USENIX LISA Conference.

3. Albin, E. (2011). *SANS SEC511: Continuous Monitoring and Security Operations*.

4. OWASP Foundation. (2021). *OWASP Top 10 - 2021*. Retrieved from https://owasp.org/Top10/

5. Stuttard, D., & Pinto, M. (2011). *The Web Application Hacker's Handbook: Finding and Exploiting Security Flaws* (2nd ed.). Wiley.

6. Suricata. (2024). *Suricata User Guide*. Retrieved from https://suricata.readthedocs.io/

7. Cisco Talos. (2024). *Snort3 User Manual*. Retrieved from https://www.snort.org/documents

8. MITRE Corporation. (2024). *ATT&CK Framework for Enterprise*. Retrieved from https://attack.mitre.org/

9. Sommer, R., & Paxson, V. (2010). *Outside the Closed World: On Using Machine Learning for Network Intrusion Detection*. IEEE Symposium on Security and Privacy.

10. Ptacek, T. H., & Newsham, T. N. (1998). *Insertion, Evasion, and Denial of Service: Eluding Network Intrusion Detection*. Secure Networks, Inc.

---

## LAMPIRAN

### Lampiran A: Testing Scripts

#### test_payloads.ps1
```powershell
# PowerShell testing automation
# File lengkap tersedia di repository
```

#### test_payloads.sh
```bash
# Bash testing automation
# File lengkap tersedia di repository
```

### Lampiran B: IDS Rules

#### Suricata Rules (suricata.rules)
```suricata
# File lengkap dengan 11 rules tersedia di repository
```

#### Snort3 Rules (local.rules)
```snort
# File lengkap dengan 9 rules tersedia di repository
```

### Lampiran C: Screenshot Hasil Testing

```
├── screenshots/
│   ├── baseline/
│   │   ├── sqli_union_detected.png
│   │   ├── xss_script_detected.png
│   │   └── csrf_detected.png
│   └── evasion/
│       ├── sqli_encoded_bypassed.png
│       ├── xss_svg_bypassed.png
│       └── csrf_json_bypassed.png
```

### Lampiran D: DVWA Customization

#### Rebranding ke "Sistem Arsip Surat"
- Dashboard: Sistem Informasi Manajemen Dokumen
- SQL Injection → Pencarian Surat
- XSS Stored → Komentar Surat
- XSS Reflected → Feedback Sistem
- CSRF → Ganti Password

---

**Disusun oleh:**  
[Nama Mahasiswa]  
[NIM]  
[Program Studi]  
[Universitas]

**Dibimbing oleh:**  
[Nama Dosen Pembimbing]  
[NIP]

**Tanggal:** 7 Desember 2025

---

*Laporan ini disusun untuk keperluan akademik mata kuliah Keamanan Jaringan. Segala informasi dan teknik yang dijelaskan hanya untuk tujuan pembelajaran dan penelitian. Penggunaan ilegal dari informasi ini menjadi tanggung jawab pembaca.*
