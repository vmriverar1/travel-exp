# Travel Integrations

**Version:** 1.0.0
**Author:** Rogger Palomino Gamboa
**Description:** Third-party API integrations (Reviews, Payments, etc.).

---

## 📋 Overview

Centralized integration system for external APIs including review platforms (TripAdvisor, Google, Facebook) and payment gateways (Stripe).

---

## 🎯 Features

- ✅ **Review Sync**: TripAdvisor, Google Reviews, Facebook
- ✅ **Payment Gateway**: Stripe integration
- ✅ **Webhook Handling**: Stripe payment events
- ✅ **Cron Jobs**: Daily review synchronization
- ✅ **CPT Integration**: Creates Review posts automatically
- ✅ **Error Handling**: Fallback mechanisms

---

## 📦 Integrations

| Integration | Type | Frequency | Status |
|-------------|------|-----------|--------|
| **TripAdvisor API** | Reviews | Daily cron | ✅ Active |
| **Google Reviews** | Reviews | Daily cron | ✅ Active |
| **Facebook Graph** | Reviews | Daily cron | ✅ Active |
| **Stripe** | Payments | Real-time | ✅ Active |
| **Stripe Webhooks** | Events | Real-time | ✅ Active |

---

## 🚀 Installation

1. Upload to `/wp-content/plugins/travel-integrations/`
2. Activate via WordPress admin
3. Go to **Settings → Travel Integrations**
4. Add API keys for each service
5. Test connections

---

## ⚙️ Configuration

**Settings → Travel Integrations:**

### Reviews
- TripAdvisor API Key & Location ID
- Google My Business API credentials
- Facebook Page Access Token
- Sync frequency (default: daily)

### Payments
- Stripe Secret Key
- Stripe Publishable Key
- Stripe Webhook Secret
- Test mode toggle

---

## 🔄 Review Synchronization

**Manual Sync:**
```bash
wp cron event run travel_sync_reviews_daily
```

**Programmatic:**
```php
$syncer = new \Travel\Integrations\Reviews\ReviewsSyncer();
$results = $syncer->sync_all_reviews();
```

**Process:**
1. Fetch reviews from each platform
2. Normalize data structure
3. Check for existing reviews (by external_id)
4. Create or update Review CPT
5. Log results

---

## 💳 Stripe Integration

### Create Payment Intent

```php
$stripe = new \Travel\Integrations\Payments\StripeAPI();

$payment = $stripe->create_payment_intent([
    'amount' => 50000, // $500.00 (in cents)
    'currency' => 'usd',
    'metadata' => [
        'tour_id' => 42,
        'customer_email' => 'john@example.com',
    ],
]);
```

### Webhook Endpoint

**URL:** `/wp-json/travel/v1/webhooks/stripe`

**Events Handled:**
- `payment_intent.succeeded`
- `payment_intent.payment_failed`
- `charge.refunded`

---

## 📊 Database

Reviews are stored as **Review CPT** with ACF fields:
- Platform (tripadvisor, google, facebook)
- Rating (1-5)
- Client name, country, photo
- Review content
- External ID (for deduplication)
- Original URL

---

## 🔒 Security

- ✅ **API Keys**: Stored in options table (encrypted recommended)
- ✅ **Webhook Signature**: Stripe signature verification
- ✅ **Rate Limiting**: API request throttling
- ✅ **Fallback**: Continue on API failures

---

## 📁 Structure

```
travel-integrations/
├── travel-integrations.php
├── README.md
└── src/
    ├── Core/           # APIBase class
    ├── Reviews/        # ReviewsSyncer, APIs
    │   ├── TripAdvisorAPI.php
    │   ├── GoogleReviewsAPI.php
    │   └── FacebookReviewsAPI.php
    ├── Payments/       # Stripe integration
    │   ├── StripeAPI.php
    │   └── WebhookHandler.php
    └── Admin/          # Settings page
```

---

## 🔗 Dependencies

- WordPress 6.0+
- PHP 7.4+
- **Required CPTs**: Review (from Aurora Content Kit)

**External Accounts:**
- TripAdvisor API access
- Google My Business API
- Facebook App credentials
- Stripe Account

---

## 📝 Changelog

### 1.0.0 (2025-10-05)
- Review sync (3 platforms)
- Stripe payment integration
- Webhook handling
- Daily cron jobs

---

**Developer:** Rogger Palomino Gamboa
