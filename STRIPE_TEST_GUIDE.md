## Test Stripe Payment - Debug Guide

### Test URLs:
- Payment Page: http://127.0.0.1:8000/payment
- Success Page: http://127.0.0.1:8000/payment/success  
- Cancel Page: http://127.0.0.1:8000/payment/cancel

### Test Cards:
| Card Number | Purpose | Expected Result |
|-------------|---------|-----------------|
| 4242424242424242 | Successful payment | Success page |
| 4000000000009995 | Declined payment | Error message |
| 4000002500003155 | Requires 3D Secure | Authentication popup |

### Debug Steps:
1. **Check Browser Console** for JavaScript errors
2. **Check Laravel Logs**: `tail -f storage/logs/laravel.log`
3. **Check Payment Database**: `SELECT * FROM payments ORDER BY created_at DESC;`
4. **Stripe Dashboard**: Monitor test payments in real-time

### Common Issues:
- **CSRF Token**: Ensure meta tag exists in layout
- **Config Cache**: Run `php artisan config:clear` after .env changes
- **Route Cache**: Run `php artisan route:clear` if routes not working
- **JavaScript Errors**: Check if Stripe.js loaded correctly

### Test Environment Variables:
```bash
# Current test keys (replace with real ones)
STRIPE_PUBLISHABLE_KEY=pk_test_51QkxyzExampleKey...
STRIPE_SECRET_KEY=sk_test_51QkxyzExampleKey...
```

### Manual Database Check:
```sql
-- Check if payment was recorded
SELECT user_id, amount, currency, status, stripe_payment_intent_id, created_at 
FROM payments 
WHERE user_id = YOUR_USER_ID 
ORDER BY created_at DESC 
LIMIT 5;
```
