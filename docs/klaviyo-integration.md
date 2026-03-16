# Klaviyo Integration Documentation

## Overview

This document describes the Klaviyo email marketing integration for Apricot Power. The integration tracks customer behavior events and syncs customer profiles to Klaviyo for email marketing automation (welcome series, abandoned cart, post-purchase flows, etc.).

---

## Architecture

The integration follows a clean **Event → Listener → Job → Service** pattern:

```
User Action (e.g., places order)
  → Laravel Event dispatched (e.g., OrderPlaced)
    → Listener handles the event (e.g., HandleOrderPlaced)
      → Queued Job dispatched (e.g., TrackKlaviyoEvent)
        → KlaviyoService makes the API call
          → IntegrationLog records the result
```

All Klaviyo API calls are dispatched as **queued jobs**, so they never slow down the user-facing request.

---

## Setup

### 1. Environment Variables

Add these to your `.env` file:

```env
KLAVIYO_ENABLED=true
KLAVIYO_PRIVATE_KEY=pk_xxxxxxxxxxxxxxxxxxxxxxxx
KLAVIYO_PUBLIC_KEY=XXXXXX
KLAVIYO_LIST_ID=your-default-list-id
KLAVIYO_API_REVISION=2024-10-15
```

| Variable | Description |
|---|---|
| `KLAVIYO_ENABLED` | Set to `true` to enable API calls. `false` disables all calls (safe for local dev). |
| `KLAVIYO_PRIVATE_KEY` | Your Klaviyo private API key (starts with `pk_`). Found in Klaviyo → Settings → API Keys. |
| `KLAVIYO_PUBLIC_KEY` | Your 6-character Klaviyo public key (company ID). |
| `KLAVIYO_LIST_ID` | The default Klaviyo list ID to subscribe customers to. Found in Klaviyo → Lists → select list → Settings → List ID. |
| `KLAVIYO_API_REVISION` | Klaviyo API revision date. Default: `2024-10-15`. |

### 2. Queue Configuration

The integration uses queued jobs. For production, switch from `sync` to a proper queue driver:

```env
QUEUE_CONNECTION=database
```

Then run the queue worker:

```bash
php artisan queue:work
```

> **Note:** With `QUEUE_CONNECTION=sync` (default), jobs run synchronously in the same request. This works for testing but is not recommended for production.

### 3. Getting Your Klaviyo API Keys

1. Log in to [Klaviyo](https://www.klaviyo.com/)
2. Go to **Settings** → **API Keys**
3. Create a new **Private API Key** with these scopes:
   - `profiles:read`, `profiles:write`
   - `events:read`, `events:write`
   - `lists:read`, `lists:write`
   - `subscriptions:read`, `subscriptions:write`
4. Copy the private key (starts with `pk_`)
5. Your **Public Key** (6-char company ID) is shown at the top of the API Keys page

### 4. Getting Your List ID

1. In Klaviyo, go to **Audience** → **Lists & Segments**
2. Click on your target list (or create one called "Newsletter" or "Email Subscribers")
3. Go to **Settings**
4. Copy the **List ID**

---

## Files Created / Modified

### New Files

| File | Purpose |
|---|---|
| `config/klaviyo.php` | Configuration file for API keys, list ID, enabled flag |
| `app/Services/KlaviyoService.php` | Core service class — handles all Klaviyo API requests |
| `app/Events/Klaviyo/CustomerRegistered.php` | Fired when a new customer registers |
| `app/Events/Klaviyo/OrderPlaced.php` | Fired when a Lunar order is created |
| `app/Events/Klaviyo/SubscriptionChanged.php` | Fired when a customer subscribes/unsubscribes from the email list |
| `app/Events/Klaviyo/AddedToCart.php` | Fired when a logged-in customer adds a product to cart |
| `app/Events/Klaviyo/StartedCheckout.php` | Fired when checkout page is loaded |
| `app/Listeners/Klaviyo/HandleCustomerRegistered.php` | Syncs profile + tracks registration event |
| `app/Listeners/Klaviyo/HandleOrderPlaced.php` | Tracks "Placed Order" event with line items |
| `app/Listeners/Klaviyo/HandleSubscriptionChanged.php` | Subscribes/unsubscribes from Klaviyo list |
| `app/Listeners/Klaviyo/HandleAddedToCart.php` | Tracks "Added to Cart" event |
| `app/Listeners/Klaviyo/HandleStartedCheckout.php` | Tracks "Started Checkout" event |
| `app/Jobs/Klaviyo/SyncCustomerToKlaviyo.php` | Queued job to create/update Klaviyo profile |
| `app/Jobs/Klaviyo/TrackKlaviyoEvent.php` | Queued job to send event to Klaviyo |
| `app/Jobs/Klaviyo/UpdateKlaviyoSubscription.php` | Queued job to update list subscription |

### Modified Files

| File | Change |
|---|---|
| `app/Providers/EventServiceProvider.php` | Added Klaviyo event → listener mappings |
| `app/Observers/OrderObserver.php` | Added `OrderPlaced::dispatch()` after order creation |
| `app/Livewire/Auth/RegisterPage.php` | Added `CustomerRegistered::dispatch()` after registration |
| `app/Livewire/Account/EmailPreferencesPage.php` | Added `SubscriptionChanged::dispatch()` on subscribe/unsubscribe |
| `app/Livewire/Components/AddToCart.php` | Added `AddedToCart::dispatch()` for logged-in customers |
| `app/Livewire/CheckoutPage.php` | Added `StartedCheckout::dispatch()` on checkout mount |
| `.env.example` | Added Klaviyo environment variables |

---

## Events Tracked in Klaviyo

| Klaviyo Event Name | Trigger | Properties Sent |
|---|---|---|
| **Customer Registered** | Customer signs up | FirstName, LastName, Email, SubscribedToList, Source |
| **Placed Order** | Lunar order created | OrderId, OrderReference, OrderTotal, ItemCount, Items[], Currency |
| **Added to Cart** | Logged-in customer adds item | ProductName, Quantity, Price, VariantId |
| **Started Checkout** | Checkout page loaded | CartId, CartTotal, ItemCount, Items[] |

### Profile Data Synced

When a customer registers, the following profile attributes are pushed to Klaviyo:

- `email`
- `first_name`
- `last_name`
- `phone_number` (if provided)
- `organization` (company name, if provided)

### List Subscription

- When a customer checks "Subscribe to email list" during registration → subscribed to Klaviyo list
- When a customer subscribes via Email Preferences page → subscribed to Klaviyo list
- When a customer unsubscribes via Email Preferences page → unsubscribed from Klaviyo list

---

## Integration Logging

All Klaviyo API calls are logged in the `integration_logs` table with:

- `integration`: `'klaviyo'`
- `action`: `'api_call'`
- `status`: `pending` → `success` or `failed`
- `request_data`: Full request payload (JSON)
- `response_data`: Full response payload (JSON)
- `error_message`: Error details if failed

### Checking Logs

```php
// View recent Klaviyo logs
IntegrationLog::forIntegration('klaviyo')->latest()->take(20)->get();

// View failed calls
IntegrationLog::forIntegration('klaviyo')->failed()->get();

// View pending calls
IntegrationLog::forIntegration('klaviyo')->pending()->get();
```

---

## How It Works (Flow Examples)

### Customer Registration Flow

1. Customer fills out registration form on `/register`
2. `RegisterPage::register()` creates the customer
3. `CustomerRegistered` event is dispatched
4. `HandleCustomerRegistered` listener fires:
   - Dispatches `SyncCustomerToKlaviyo` job (creates/updates profile, optionally subscribes to list)
   - Dispatches `TrackKlaviyoEvent` job (tracks "Customer Registered" event)

### Order Placed Flow

1. Customer completes checkout → Lunar creates an Order
2. `OrderObserver::created()` fires
3. `OrderPlaced` event is dispatched
4. `HandleOrderPlaced` listener fires:
   - Dispatches `TrackKlaviyoEvent` job with order details and line items

### Email Subscription Flow

1. Customer visits `/email-preferences`
2. Clicks "Subscribe" or "Unsubscribe"
3. `SubscriptionChanged` event is dispatched
4. `HandleSubscriptionChanged` listener fires:
   - Dispatches `UpdateKlaviyoSubscription` job (subscribes or unsubscribes from Klaviyo list)

---

## Disabling the Integration

To completely disable Klaviyo without removing code:

```env
KLAVIYO_ENABLED=false
```

When disabled, all events still fire but the `KlaviyoService` returns `null` without making any HTTP requests.

---

## Job Retry Behavior

All Klaviyo jobs are configured with:

- **3 retries** (`$tries = 3`)
- **30-second backoff** between retries (`$backoff = 30`)

Failed jobs land in the `failed_jobs` table and can be retried with:

```bash
php artisan queue:retry all
```

---

## Recommended Klaviyo Flows to Set Up

Once the integration is live, create these flows in the Klaviyo dashboard:

| Flow | Trigger Metric | Purpose |
|---|---|---|
| **Welcome Series** | Customer Registered | Onboarding emails for new customers |
| **Abandoned Cart** | Added to Cart (no Placed Order within X hours) | Recover abandoned carts |
| **Abandoned Checkout** | Started Checkout (no Placed Order within X hours) | Recover abandoned checkouts |
| **Post-Purchase** | Placed Order | Thank you, review request, cross-sell |
| **Win-Back** | Placed Order (no new order within X days) | Re-engage inactive customers |

---

## Troubleshooting

### Events not appearing in Klaviyo

1. Check `KLAVIYO_ENABLED=true` in `.env`
2. Check `KLAVIYO_PRIVATE_KEY` is set and valid
3. Check `integration_logs` table for failed API calls
4. Ensure queue worker is running: `php artisan queue:work`

### 429 Rate Limit Errors

Klaviyo has rate limits. The queued jobs with backoff/retry handle this automatically. If you see persistent 429s, consider adding a rate limiter middleware to the jobs.

### Profile not syncing

Ensure the customer has a valid email address. Klaviyo requires an email or phone number to create a profile.
