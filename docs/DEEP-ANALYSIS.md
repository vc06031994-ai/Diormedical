# Dior Medical — Deep Code & Security Analysis

**Repository:** `vc06031994-ai/Diormedical`
**Analysed commit:** `146fb69` ("Initial commit") — branch `arena/06c325c9-diormedical`
**Analysis date:** 2026-11-04
**Scope:** 100 % of tracked PHP + JS + CSS (107 files, ~46,000 lines of PHP)

---

## 1. Executive Summary

This repo is **not a typical small WordPress plugin** — it is a full **telehealth / urgent-care application** built *inside* a WordPress plugin. It provides two complete portals (patient + doctor), appointment scheduling, video-visit integration (Zoom / Google Meet), clinical encounters (SOAP notes), e-prescriptions, documents vault, invoices/payments, notifications + transactional email, audit logging, and a HIPAAtizer intake webhook bridge.

**Verdict:** impressive feature surface and a genuine attempt at HIPAA-style architecture (custom tables, audit log, secure file vault, anti-IDOR helper, nonces), **but it is not production-safe in its current state.** The most serious problems are not exotic — they are architectural: an authorization layer that fails open, three unauthenticated PHI write paths, a financial screen that fakes payment success, and a "design-review" template that injects **fabricated clinical data into real patient dashboards**.

| Area | Rating | One-line verdict |
|---|---|---|
| Feature breadth | 8/10 | Genuinely complete telehealth workflow |
| Architecture | 4/10 | Two 5.8k/2.7k-line god-files, duplicated logic, dual-write storage |
| Security / access control | **2/10** | Fails open in several critical paths |
| Clinical safety | **1/10** | Fake records shown to real patients |
| Performance | 3/10 | ~4 MB of assets loaded on **every** page of the site |
| HIPAA / compliance posture | 3/10 | Good intent, no enforcement, no BAAs, PHI over plain email |
| Maintainability | 3/10 | No tests, no README, no CI, no build step |
| Code hygiene | 6/10 | `$wpdb->prepare()` used widely; escaping mostly consistent |

**Do not deploy to a live medical site until Section 6 (Critical / High) is fixed.**

---

## 2. What The Product Is

**Plugin name:** `Dior Medical - Patient Portal & Dashboard` (v17.2.19, author: Vulture Concepts)
**Type:** Single-file-rooted plugin, everything else `require_once`d from `dior-medical-dashboard.php`.

### Portals

| Portal | Shortcode | Access gate |
|---|---|---|
| Patient dashboard | `[dior_patient_dashboard]` | logged-in (any role) |
| Doctor dashboard | `[dior_doctor_dashboard]` | `administrator`/`manage_options` or role `doctor` |
| Login / Register / Lost / Reset password | `[dior_login_form]`, `[dior_registration_form]`, `[dior_lost_password_form]`, `[dior_reset_password_form]` | public |
| Standalone booking page | `[dior_book_visit_page]` | public |

### Feature map (by module)

- **Auth & onboarding** — custom AJAX login/register/reset (bypasses `wp-login.php`), 18+ age verification, role-based redirect, auto-created pages.
- **Appointments** — booking, reschedule, cancel, status machine (`Pending → Confirmed → In Progress → Completed / Cancelled / No Show`), lifecycle history table, reminders.
- **Telemedicine** — Zoom Server-to-Server OAuth (auto meeting creation) with Google Meet OAuth as fallback; `tel:` for phone visits; in-app "participant joined" events.
- **Clinical** — encounters with SOAP fields, vitals, ICD-10, SHA-256 attestation hash; consultation notes; digital prescriptions (with an "FDA prescription modal"); patient questionnaires.
- **Documents vault** — private upload dir with `.htaccess` deny + `index.php`, magic-byte MIME validation, authenticated streaming download, path-traversal guard, attestation hash for letters.
- **Revenue** — invoices, payments, income/expense reports, "Stripe" payment button (see §6.5 — it is fake).
- **Notifications** — relational in-app notifications + 15 HTML email templates (confirmation, reschedule, cancel, reminder, payment, password, account created).
- **Integrations** — HIPAAtizer (intake + questionnaire + sync job), DocBooker (`wpddb_*` post types) sync into WP users.
- **Analytics** — admin-only ApexCharts dashboard + CSV/report generator.
- **Ops** — audit log table, demo-data seeder (admin tool), admin settings pages for Zoom / Google Meet.

---

## 3. Architecture & File Inventory

```
dior-medical-dashboard.php   5,854 lines  ← MAIN plugin (header, DIOR_Medical_Auth,
                                              Dior_Patient_Portal_Data, HIPAAtizer, enqueues)
dior-doctor-dashboard.php    2,679 lines  ← Dior_Doctor_Dashboard + Dior_Doctor_Resolver
includes/                    ~7,000 lines ← 13 service classes
rest_api/                    ~1,500 lines ← 5 HIPAAtizer / webhook files
templates/                  ~29,000 lines ← 60+ PHP view partials
assets/js/                       2.3 MB  ← 19 files, 3 of them >150 KB hand-written JS
assets/css/dior-custom.css       1.4 MB  ← 54,127 lines in ONE stylesheet
```

### Classes

| Class | File | Role |
|---|---|---|
| `Dior_Patient_Portal_Data` | main | Data provider + HIPAAtizer importer (`save_hipaa_intake`) |
| `Dior_Medical_Auth` | main | Shortcodes, AJAX endpoints, page auto-creation |
| `Dior_Doctor_Dashboard` | doctor file | Doctor portal render + 30 AJAX handlers |
| `Dior_Doctor_Resolver` | doctor file | Legacy account consolidation, default doctor |
| `Dior_Auth_Service` | includes | RBAC, anti-IDOR, nonce helper |
| `Dior_DB` | includes | Schema (11 tables) + legacy `usermeta` migration |
| `Dior_Appointment_Service` | includes | Booking lifecycle, history, reminders |
| `Dior_Encounter_Service` | includes | SOAP notes / attestation |
| `Dior_Notification_Service` | includes | In-app + email (1,024 lines — largest service) |
| `Dior_Medical_Secure_Files` | includes | Upload vault, stream gate, letters |
| `Dior_Audit_Service` | includes | Audit trail |
| `Dior_Medical_Analytics` | includes | Admin analytics |
| `Dior_Medical_REST_API` | includes | `/wp-json/dior/v2/*` (incomplete — see §6.8) |
| `Dior_Doctor_Dynamic` | includes | Patients/reviews/telemedicine/chat CRUD |
| `Dior_Questionnaire_Service` | includes | Questionnaire mapping |
| `Dior_Zoom_Meetings` / `Dior_Google_Meet` | includes | Video providers |
| `Dior_Demo_Data` | includes | Seeder + cleanup |

**Observations**

- `Dior_Doctor_Resolver` and `Dior_Doctor_Dynamic` are *defined inside* `dior-doctor-dashboard.php` and `includes/…` respectively, but `Dior_Doctor_Dynamic` is registered against handlers declared in the doctor file — circular-ish coupling.
- `dior-doctor-dashboard.php` is **not** a separate plugin (no plugin header); it is `require_once`d at line 5721 of the main file. Anyone reading the repo will think it's a second plugin. A `readme` explaining this does not exist.
- Same class of logic is duplicated across both files: `ajax_upload_avatar`, `ajax_upload_signature`, `ajax_change_password`, `ajax_remove_avatar`, `ajax_save_profile`, `ajax_get_live_notifications`, `enqueue_assets`, `install_schema` all exist in both.

---

## 4. Data Model

**11 tables** created by `Dior_DB::install_schema()` (DB version `4.0.4`):

`dior_encounters`, `dior_documents`, `dior_appointments`, `dior_audit_logs`,
`dior_appointment_history`, `dior_notifications`, `dior_prescriptions`,
`dior_invoices`, `dior_payments`, `dior_reviews`, `dior_patients`

**+4 tables** created separately by `Dior_Doctor_Dynamic::install_schema()`:
`dior_telemedicine`, `dior_chat_messages`, `dior_doctor_settings`, (`dior_reviews` re-declared)

**Findings**

1. **Dual-write legacy problem.** Every entity is written both to a relational table *and* to serialized `wp_usermeta` (`dior_appointments`, `dior_documents`, `dior_payments`, `dior_doctor_notes` …), then migrated back (`Dior_DB::migrate_legacy_data`, lines 370–470). Reads prefer meta in several templates (e.g. `templates/doctor-tabs/view-appointment.php:105`). This guarantees drift and makes "which record is truth?" unanswerable.
2. **`patient_id` is ambiguous.** In some paths it is a **WP user ID**, in others a `dior_patients.id` row PK. `Dior_Auth_Service::can_access_patient()` compares `$user_id === $patient_id` (`includes/class-dior-auth-service.php:63-80`) — so it silently checks *"is the patient row ID equal to my user ID?"*. This is the root cause of several IDORs.
3. **Two schema installers, two versions, no single migration path.** `Dior_DB::DB_VERSION` 4.0.4 vs plugin 17.2.19 vs REST API file says "v2.8".
4. `dior_insurance` is read/written as meta only — the `insurance.php` tab has no table; claims are entirely static (§6.6).

---

## 5. Request Flow (as built)

```
Browser ──POST /wp-admin/admin-ajax.php?action=dior_* ──> Dior_Medical_Auth / Dior_Doctor_Dashboard
                                                            │
                       Dior_Auth_Service::verify_ajax_nonce │  ← FAILS OPEN (§6.1)
                                                            ▼
                                          business logic (usermeta + $wpdb tables)
                                                            │
              Dior_Appointment_Service / Notification_Service ──> wp_mail() plain HTML
                                                            │
              HIPAAtizer ──POST /wp-json/dior/v1/hipaatizer ──> save_hipaa_intake()  ← NO AUTH (§6.2)
```

Rendering: shortcode → `render()` → gather data → `include templates/*.php`. Tabs are all **rendered at once** (all 14 patient tabs / 27 doctor tabs are `include`d in a single page load) and toggled client-side — this is why the dashboard payload is enormous.

---

## 6. Security & Correctness Findings

Severity legend: 🔴 **Critical** · 🟠 **High** · 🟡 **Medium** · 🔵 **Low**

### 6.1 🔴 CSRF protection is disabled by design — `verify_ajax_nonce()`

`includes/class-dior-auth-service.php:116-160`

```php
if (!$passed && is_user_logged_in()) {
    $passed = true;          // ← nonce check skipped for ANY logged-in user
}
```

Every AJAX handler that calls this helper (documents upload/delete, appointments, profile, prescriptions, notifications, feedback…) is therefore **only** protected by the session cookie. A malicious page can submit cross-site POSTs on behalf of a logged-in patient/doctor (state-changing actions, PHI uploads, appointment tampering). Anonymous requests *are* checked — so the helper's real effect is inverted from intent.

### 6.2 🔴 Three unauthenticated PHI write endpoints

| Endpoint | File | Guard |
|---|---|---|
| `POST /wp-json/dior/v1/hipaatizer` | `rest_api/hipaatizer_rest_api.php:20-25` | `__return_true`; token check **only if a secret is configured** (`:441-450`) |
| `POST /wp-json/dior/v1/hipaa-webhook` | `dior-medical-dashboard.php:1013-1018` | `__return_true`, **no token check at all** |
| `POST admin-ajax.php?action=dior_hipaa_webhook` | `dior-medical-dashboard.php:1008, 5104` | `nopriv`, **zero checks** |
| `POST /wp-json/dior/v1/questionnaire` | `rest_api/hipaatizer_rest_api.php:27-31` | `__return_true` |

`save_hipaa_intake()` (`dior-medical-dashboard.php:5132`) trusts **`user_id` from the request body** (line 5149) and falls back to email lookup. So an anonymous attacker can:
- overwrite **any patient's PHI intake** (allergies, medications, symptoms) → clinical safety risk;
- poison data for arbitrary user IDs;
- use it as an oracle to confirm which emails/user IDs exist.

### 6.3 🔴 IDOR: any logged-in user can dump any patient's record

`rest_api/hipaatizer_rest_api.php:34-40` + `:505`

```php
register_rest_route('dior/v1', '/patient/(?P<id>\d+)', [
    'permission_callback' => function () { return is_user_logged_in(); },
]);
```

The handler then returns `SELECT * FROM wp_dior_patients` + `get_patient_profile()` + **`dior_hipaa_intake`** by incrementing `id`. Any subscriber account (self-registration is open) can enumerate `1..N` and exfiltrate the entire patient database.

Similar shape (different file): `/wp-json/dior/v2/appointments?patient_id=X` and `/v2/prescriptions?patient_id=X` use `check_auth_permission()` = `is_user_logged_in()` and honour a caller-supplied `patient_id` (`includes/class-dior-rest-api.php:375-405, 497-519`).

### 6.4 🔴 Unauthenticated booking can write onto an existing patient's account

`dior-medical-dashboard.php:3980-4017` (also `:3072 handle_book_appointment_from_page`)

```php
if (!$user_id) {
    if ($patient_email && is_email($patient_email)) {
        $existing_user = get_user_by('email', $patient_email);
        if ($existing_user) { $user_id = $existing_user->ID; }   // ← no verification
```

Then the handler writes to that user's meta and creates an appointment with `'payment_status' => 'Paid'`, using an attacker-chosen `doctor_id`. No nonce is required (only checked *if posted*), no rate limit, no captcha. Result: appointment spam, PHI injection, fake "paid" billing records, account enumeration.

Also hardcoded fallbacks everywhere: `doctor_id ?: 1957`, `$doctor_user_id ?: 4` (`:3092, :4062, :4103`).

### 6.5 🔴 "Payment processed via Stripe" — no payment gateway exists

`dior-medical-dashboard.php:4568-4601`

There is **no** Stripe/PayPal/Square integration anywhere in the repo (verified by search). `ajax_pay_invoice()` simply flips `status = 'Paid'` in `usermeta`, then sends a **payment-receipt email** and returns:

> `"Payment processed successfully via Stripe! Receipt issued."`

Patients receive receipts for money that was never taken. This is both a financial-integrity and a consumer-protection problem.

### 6.6 🔴 Fabricated clinical data rendered in the live patient portal

`templates/patient-tabs/_design-static-data.php` (189 lines) is `include`d **unconditionally** at `templates/patient-dashboard.php:290` (only guard is `$dior_patient_design_static = true;` set by itself).

When a patient has few or no real records, the portal fills in:

- **10 fake appointments** with invented doctors ("Dr. Sarah Smith", "Dr. Marcus Sterling"…) and dates in the future;
- **10 fake prescriptions** (Loratadine, Metformin, Amoxicillin…);
- a **fake HIPAA intake** including `'allergies' => 'Penicillin'`, `'medications' => 'Cetirizine 10 mg'`, fake DOB, fake address;
- **3 fake payments** marked Paid, and **2 fake insurance claims**;
- fallback profile identity `Sarah Johnson / PT-20481 / sarah.johnson@example.com`.

A real patient opening their portal can see a **penicillin allergy and a medication list that are not theirs**, plus invoices marked paid. In a clinical context this is the single most dangerous defect in the codebase — it is a patient-safety and liability issue, not a cosmetic one. The same file also loads an avatar from `images.unsplash.com` (external PHI-adjacent leak).

### 6.7 🟠 Doctor account auto-provisioned on every `init`, with a public-domain email

`dior-doctor-dashboard.php:2393-2451` — `Dior_Doctor_Resolver::ensure_default_doctor()` runs on `init` (registered at `:53`):

- creates login `doctor` with email **`doctor@gmail.com`** and role `doctor`, then calls `wp_new_user_notification()` → the reset link goes to whoever controls that Gmail address;
- if a *different* existing doctor account is found (by login `doctor`/`Docter`), it **rewrites that user's email** to `doctor@gmail.com` and force-adds the `doctor` role.

Any external owner of `doctor@gmail.com` can plausibly claim password-reset control of a full-PHI doctor account. Runs on every page load (also a performance cost) and reverses the admin's own account decisions.

Related: login flow hardcodes a username allow-list for doctor redirect (`:2725`): `['docter','admin','doctor','jameschen','evelynvance','marcussterling','jon']` — leftover demo/dev data in production logic.

### 6.8 🟠 REST v2 is half-finished and leaks/misbehaves

`includes/class-dior-rest-api.php`

- `get_appointments()` queries `{$wpdb->postmeta} WHERE user_id = %d …` — **`wp_postmeta` has no `user_id` column**. Every call errors out (or returns nothing) and the failure is silent.
- `create_appointment()` writes an orphan `dior_appointment` **post** — a post type that is never registered in this plugin.
- `update_appointment()` (`:471`) only requires `is_user_logged_in()` and then `update_post_meta()` on **any** supplied post ID → arbitrary post-meta write by any subscriber.
- The whole `dior/v2` namespace duplicates `dior_appointments` with a different storage model. Recommendation: delete it, or reimplement on the real service layer.

### 6.9 🟠 No rate limiting anywhere

`handle_login` (`:2674`), `handle_register`, `handle_lost_password`, booking endpoints: no attempt counter, no throttle, no lockout, no captcha. Custom login means common hardening plugins that hook `wp_authenticate` may not apply. Combined with **minimum password length 6** (`:2819`, `:3045`) this is a straightforward credential-stuffing target.

### 6.10 🟠 Nonce results computed and then discarded

`dior-medical-dashboard.php:2677-2681`, `:2767-2771`, `:2896+`:

```php
$nonce_valid = wp_verify_nonce($_POST['nonce'], 'dior_auth_nonce') || …;   // never used
```

Login/register/lost-password are therefore **login-CSRF-able** (attacker logs a victim into an attacker-controlled account; victim then submits PHI into it).

### 6.11 🟡 Audit log integrity is spoofable

`includes/class-dior-audit-service.php:71-80` — `get_client_ip()` trusts `X-Forwarded-For` unconditionally. Any client can forge the IP recorded against PHI access. Also: the audit table is in the same DB with the same credentials (no WORM/append-only store, no hash chain), so "tamper-evident" is aspirational; there is no retention/rotation policy.

### 6.12 🟡 File vault: good checks, weak blast radius

`includes/class-dior-secure-files.php` — genuinely decent (extension allow-list, `finfo` magic-byte check, 25 MB cap, `move_uploaded_file`, `realpath()` prefix check against the vault, audit on failure, `.htaccess` deny).

Gaps:
- The vault lives under `wp-content/uploads/` — protection depends on `.htaccess` (Apache only). **Nginx/IIS ignore it**, and no `web.config` is written → files become publicly fetchable if the filename is known.
- No encryption at rest; filename is guessable-ish (`med_{patient}_{timestamp}_{8 chars}`) but not enumerable in practice.
- `img`/`doc` files aren't scanned for malware; `docx`+`application/zip` allowance widens the surface.
- `add_document()` falls back to `'dior_medical_secret'` salt when `NONCE_SALT` is missing.

### 6.13 🟡 XSS surface in the JS layer

296 `innerHTML` assignments across the three big dashboards; `escapeHtml()` exists but is defined *locally inside individual functions* (`dior-dashboard.js:3377`, `dior-patient-dashboard.js:2404`) and is not applied to every interpolation. All data reaching it is PHI/clinical text entered by other users (doctors write notes/prescriptions; patients submit intake). A stored-XSS via a prescription instruction is plausible and would execute in a doctor's session. PHP-side escaping is comparatively good (795 escaped vs 1 raw `echo $var` in templates).

### 6.14 🟡 Hardcoded demo credentials shipped in code

`includes/class-dior-demo-data.php:51` → `'user_pass' => 'DiorDemo!2026'` for `doctor.demo@dior-medical.test` + 8 patients. Fine behind the `manage_options`-gated admin tool, but it is a real, working credential pair in the repo, and `Dior_Doctor_Resolver` will happily treat demo/legacy accounts as doctors.

### 6.15 🔵 Misc hardening

- Third-party CDNs on every page: `cdnjs.cloudflare.com` (Font Awesome) and `cdn.jsdelivr.net` (Bootstrap Icons) — IP/behaviour leaks to third parties on a page that is supposed to be HIPAA-scoped, and neither vendor will sign a BAA.
- `unserialize` surface via `maybe_unserialize()` on usermeta (core-sourced, low risk, but it is the reason object-injection would be fatal).
- Emails carry full clinical/PII detail in plain HTML (`templates/emails/*`) with no "do not include PHI" guard — most transactional email is unencrypted in transit at rest at the provider.
- `wp_ajax_dior_ajax_logout` is registered `nopriv` (`:960-961`) — harmless but odd.
- No `readme.txt`, no LICENSE, no `composer.json`, no CI, no tests, no `.gitignore`. The `/templates/doctor-tabs/form-test.php` debug partial is shipped.

---

## 7. Performance

| Issue | Impact |
|---|---|
| `enqueue_assets()` hooked to `wp_enqueue_scripts` with **no page condition** (`dior-medical-dashboard.php:930, 1040`) | **~4 MB** (1.4 MB CSS + 900 KB html2pdf + 525 KB ApexCharts + Bootstrap + 19 JS files) on *every page of the site*, including the blog and checkout |
| `dior-custom.css` = 54,127 lines / 1.4 MB | Nothing external is minified/merged; no critical-CSS split |
| All 14 patient tabs / 27 doctor tabs `include`d on one load | Hundreds of DOM sections + repeated DB queries per page view |
| `ensure_pages_exist()` on `init` **and** `admin_init` (`:1024-1025`), `ensure_default_doctor()` on `init` | `wp_insert_post`/`wp_insert_user` side-effects on normal page views |
| `dior_patients` lookups by `is_page()` slug lists | Fragile + repeated |
| 3 hand-written JS bundles 150–200 KB each, no build/minify | No bundler, no tree-shaking, no cache-busting beyond `filemtime()` |

---

## 8. What Is Actually Good (do not throw away)

Worth stating clearly, because a lot of this is above typical WP-plugin quality:

- `$wpdb->prepare()` discipline is consistent; no request data concatenated into SQL that I could find (169 raw query sites reviewed).
- Deep MIME validation + `realpath()` traversal guard on uploads.
- A real service layer (`Dior_Auth_Service`, `Dior_Appointment_Service`, `Dior_Encounters`, `Dior_Audit_Service`) instead of procedural spaghetti — the seams are there for a clean refactor.
- Intentional anti-IDOR helper, strict ENUM/date/ID validators, SHA-256 attestation hash on encounters and letters.
- Audit logging on upload/download/denial paths, metadata scrubbing of obvious secrets.
- Template escaping is mostly disciplined (`esc_html`/`esc_attr`/`wp_kses_post`).
- The PHP is structurally consistent (balanced tags/blocks) and reads cleanly. *Caveat: no PHP runtime was available in the analysis sandbox, so `php -l` was **not** run — this bullet is a code-reading judgement, not a machine check.*
- Notification service is genuinely well-covered (15 email templates, reschedule/cancel/reminder/meeting-started).
- Google Meet OAuth uses a hashed one-time state transient (reasonable pattern).

---

## 9. Compliance Reality Check (HIPAA)

The code shows awareness but **does not satisfy** the Security Rule as-is:

| Requirement | Status |
|---|---|
| Unique user identification | ✅ WP users + roles |
| Access control / minimum necessary | ❌ **fails open** (§6.1, §6.3); doctors see *all* patients |
| Audit controls | 🟡 exists, but spoofable + same-DB, no retention |
| Integrity controls | ❌ dual-write drift, fake data, webhook can overwrite PHI |
| Person/entity authentication | ❌ no MFA, no rate limit, 6-char passwords |
| Transmission security | ❌ PHI in plain HTML email; third-party CDNs |
| Encryption at rest | ❌ vault is plaintext files |
| BAAs | ⚠️ Zoom/Google/CDN/Gmail-backed doctor account — none documented |
| Breach/incident process, backups, DR | ❌ not represented in code at all |
| Data retention / right of deletion | ❌ soft-delete flags only |

---

## 10. Prioritised Backlog

### P0 — block release (hours–days)

1. Remove the `if (!$passed && is_user_logged_in())` bypass in `verify_ajax_nonce()`; then audit every handler.
2. Delete `templates/patient-tabs/_design-static-data.php` from the render path (or gate it behind `defined('DIOR_DESIGN_MODE')` and make it opt-in *per developer*, never per patient).
3. Require a webhook secret on **all four** intake endpoints (fail closed when unset); never accept `user_id` from the payload — resolve by verified email only.
4. Fix `/dior/v1/patient/{id}` permission callback to `current_user_can('manage_options') || current_user_can('doctor') || $row_user_id === get_current_user_id()`.
5. Replace `ajax_pay_invoice()` with a real gateway **or** relabel it "mark as paid (staff only)" and drop the fake Stripe receipt.
6. Stop trusting caller-supplied email on anonymous booking — require login or an emailed confirmation token.
7. Kill `ensure_default_doctor()`'s auto-creation / email rewrite; make it an explicit admin action with a configured, clinic-owned address.

### P1 — next sprint

8. Unify `patient_id` semantics (always WP user ID) and rebuild `can_access_patient()` around it.
9. Remove or rewrite `dior/v2` (it is currently broken + unsafe).
10. Add rate limiting + lockout + min-8/no-have-i-been-pwned password policy; use `wp_signon` nonce properly (actually enforce it).
11. Scope doctor data access to `doctor_id` (currently clinic-wide by design — confirm this is intended).
12. Enqueue assets conditionally (`is_page([...])` or shortcode-detection); split `dior-custom.css`.

### P2 — hardening & hygiene

13. Encrypt-at-rest vault + Nginx/IIS protection + malware scan hook.
14. Escape every JS interpolation with one shared `escapeHtml`; consider adding Trusted Types.
15. Self-host Font Awesome / Bootstrap Icons; add SRI if any CDN stays.
16. Append-only audit export (SIEM/S3), retention policy, IP from trusted proxy config.
17. Consolidate the two dashboards into one module namespace; split god-files (<800 lines each).
18. Add `readme.md`, CI (`php -l` + PHPCS WordPress standard), and Playwright smoke tests for login → book → prescribe.
19. Remove hardcoded `doctor_id 1957 / 4` fallbacks and the demo username allow-list.

---

## 11. Open Questions Before The Next Task

1. **Deployment status** — is this live with real patients, or still pre-launch? (Changes the urgency of P0 items 2, 5.)
2. **Single-provider vs multi-provider clinic?** `doctor_can_manage_appointment()` assumes "if only one doctor exists, they own everything".
3. **Payments** — is a gateway (Stripe?) planned, or is billing manual/offline?
4. **Video** — Zoom S2S OAuth is primary; is the Google Meet fallback still wanted?
5. **HIPAAtizer** — is the shared webhook secret already configured in production?
6. **3rd-party CDNs** — acceptable for launch, or must everything be self-hosted for the BAA story?

---

*Prepared by Arena coding agent — static analysis only. No WordPress runtime was available in this sandbox, so runtime behaviour (SQL failures in `dior/v2`, email delivery, OAuth round-trips) is inferred from code, not executed.*
