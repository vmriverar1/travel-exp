# Travel Forms

**Version:** 1.0.0
**Author:** Rogger Palomino Gamboa
**Description:** Form system with HubSpot integration and dual validation.

---

## 📋 Overview

Complete form system for the travel website with frontend/backend validation, HubSpot CRM integration, and database storage.

---

## 🎯 Features

- ✅ **3 Forms**: Contact, Booking, Brochure Download
- ✅ **Dual Validation**: JavaScript + PHP
- ✅ **HubSpot Integration**: Automatic CRM sync
- ✅ **Database Storage**: Custom table for submissions
- ✅ **Email Notifications**: Admin + user confirmations
- ✅ **Spam Protection**: Honeypot + validation rules
- ✅ **AJAX Submission**: No page reload

---

## 📦 Forms

| Form | Shortcode | Fields | Integration |
|------|-----------|--------|-------------|
| **Contact** | `[travel_contact_form]` | Name, Email, Message | HubSpot |
| **Booking** | `[travel_booking_form]` | Name, Email, Tour, Date, Guests | HubSpot |
| **Brochure** | `[travel_brochure_form]` | Name, Email | HubSpot + Download |

---

## 🚀 Installation

1. Upload to `/wp-content/plugins/travel-forms/`
2. Activate via WordPress admin
3. Go to **Settings → Travel Forms**
4. Add HubSpot API key
5. Use shortcodes in pages

---

## 🔧 Usage

### Shortcode

```php
[travel_contact_form]
[travel_booking_form tour_id="42"]
[travel_brochure_form file_url="/brochure.pdf"]
```

### Template Function

```php
<?php
if (function_exists('travel_render_contact_form')) {
    travel_render_contact_form();
}
?>
```

---

## ⚙️ Configuration

**Settings → Travel Forms:**
- HubSpot API Key
- Admin notification email
- From email/name
- Success/error messages

---

## 📊 Database

**Table:** `wp003_form_submissions`

| Column | Type | Description |
|--------|------|-------------|
| `id` | BIGINT | Primary key |
| `form_type` | VARCHAR | contact, booking, brochure |
| `name` | VARCHAR | Submitter name |
| `email` | VARCHAR | Submitter email |
| `message` | TEXT | Form message |
| `data` | LONGTEXT | JSON serialized data |
| `ip_address` | VARCHAR | Submitter IP |
| `hubspot_sent` | TINYINT | 1 if synced to HubSpot |
| `created_at` | DATETIME | Submission timestamp |

---

## 🔒 Security

- ✅ **Nonces**: CSRF protection
- ✅ **Sanitization**: All inputs sanitized
- ✅ **Validation**: Frontend + backend
- ✅ **Honeypot**: Spam prevention
- ✅ **Rate Limiting**: IP-based throttling (recommended)

---

## 📁 Structure

```
travel-forms/
├── travel-forms.php
├── README.md
└── src/
    ├── Core/           # Database, FormBase
    ├── Forms/          # Form classes
    ├── Validation/     # Validator, Sanitizer
    ├── Integration/    # HubSpotAPI
    └── Admin/          # Settings page
```

---

## 🔗 Dependencies

- WordPress 6.0+
- PHP 7.4+

**Optional:**
- HubSpot Account (for CRM integration)

---

## 📝 Changelog

### 1.0.0 (2025-10-05)
- 3 forms with dual validation
- HubSpot integration
- Custom database table
- Admin settings page

---

**Developer:** Rogger Palomino Gamboa
