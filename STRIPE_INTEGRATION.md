# Stripe Payment Integration

This BookStack project includes a complete Stripe payment integration for subscription-based access.

## Setup Instructions

### 1. Stripe Account Setup
1. Create a Stripe account at https://stripe.com
2. Get your API keys from the Stripe Dashboard
3. Set up webhook endpoints for payment processing

### 2. Environment Configuration
Update your `.env` file with your Stripe keys:

```bash
# Stripe Configuration
STRIPE_PUBLISHABLE_KEY=pk_test_your_actual_publishable_key
STRIPE_SECRET_KEY=sk_test_your_actual_secret_key
STRIPE_WEBHOOK_SECRET=whsec_your_actual_webhook_secret
```

### 3. Webhook Configuration
1. In your Stripe Dashboard, go to Webhooks
2. Add a new endpoint: `https://yourdomain.com/stripe/webhook`
3. Select these events:
   - `payment_intent.succeeded`
   - `payment_intent.payment_failed`
   - `invoice.payment_succeeded`
   - `invoice.payment_failed`
4. Copy the webhook signing secret to your `.env` file

### 4. Database Migration
Run the payment migration:
```bash
php artisan migrate
```

## Features

### Payment Plans
- **Basic Plan**: $9.99/month - Access to all books, basic search, mobile access
- **Premium Plan**: $19.99/month - Everything in Basic + advanced search, downloads, priority support
- **Pro Plan**: $39.99/month - Everything in Premium + team collaboration, custom integrations, white-label options

### Payment Flow
1. User selects a plan on `/payment`
2. Stripe Payment Intent is created via AJAX
3. User enters card details using Stripe Elements
4. Payment is processed securely by Stripe
5. Webhook confirms payment and updates database
6. User is redirected to success page

### Security Features
- CSRF protection on all forms
- Webhook signature verification
- Secure card handling via Stripe Elements
- No card data stored on your server

## File Structure

```
app/
├── Http/Controllers/PaymentController.php    # Payment processing logic
├── Models/Payment.php                        # Payment model
├── Services/StripeService.php               # Stripe API wrapper
└── Providers/StripeServiceProvider.php      # Service provider

config/
└── stripe.php                              # Stripe configuration

database/migrations/
└── create_payments_table.php               # Payment database schema

resources/views/payment/
├── index.blade.php                         # Payment plans page
├── success.blade.php                       # Payment success page
└── cancel.blade.php                        # Payment cancelled page

routes/
└── web.php                                 # Payment routes
```

## Testing

### Test Cards
Use these test card numbers in development:
- **Successful payment**: 4242 4242 4242 4242
- **Payment requires authentication**: 4000 0025 0000 3155
- **Payment is declined**: 4000 0000 0000 9995

### Webhook Testing
Use Stripe CLI for local webhook testing:
```bash
stripe listen --forward-to localhost:8000/stripe/webhook
```

## Production Deployment

1. Replace test keys with live keys in production
2. Update webhook endpoint to production URL
3. Enable SSL/HTTPS for secure payment processing
4. Test all payment flows thoroughly

## Customization

### Adding New Plans
Edit `resources/views/payment/index.blade.php` to add new pricing tiers.

### Subscription Management
Extend the Payment model to include subscription_id for recurring payments.

### Email Notifications
Configure mail settings in `.env` to send payment confirmations.

## Support

For Stripe-related issues, refer to:
- [Stripe Documentation](https://stripe.com/docs)
- [Stripe PHP Library](https://github.com/stripe/stripe-php)
- [Laravel Cashier](https://laravel.com/docs/billing) (alternative approach)
