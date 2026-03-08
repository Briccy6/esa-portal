# Flutterwave Payment Integration Setup

This guide explains how to set up Flutterwave payment integration for student token purchases in the ESA Portal.

## Overview

The system now supports:
- **Subscription Plans**: Pre-defined token packages with bulk discounts
- **Custom Token Purchases**: Students can buy any amount of tokens (1-1000)
- **Flutterwave Integration**: Secure payment processing via Flutterwave
- **Automatic Token Allocation**: Tokens are added immediately after successful payment
- **Payment Tracking**: Complete transaction history and status tracking

## Features Implemented

### 1. Subscription Plans
- **Basic**: 25 tokens for RWF 12,500
- **Standard**: 75 tokens for RWF 37,500 (Recommended)
- **Premium**: 200 tokens for RWF 100,000
- **Student Monthly**: 50 tokens for RWF 25,000
- **Semester Pass**: 500 tokens for RWF 250,000

### 2. Custom Token Purchases
- 1-1000 tokens per purchase
- Bulk discounts: 5% for 50+ tokens, 10% for 100+ tokens
- Base price: RWF 500 per token

### 3. Payment Flow
1. Student selects subscription or enters custom amount
2. System initializes Flutterwave payment
3. Student is redirected to Flutterwave secure payment page
4. After payment, Flutterwave redirects back with payment status
5. System verifies payment and allocates tokens automatically
6. Student receives confirmation and can see tokens in dashboard

## Setup Instructions

### 1. Flutterwave Account Setup

1. **Create Flutterwave Account**
   - Visit [Flutterwave](https://flutterwave.com)
   - Sign up for a business account
   - Complete verification process

2. **Get API Keys**
   - Go to Settings > API Keys
   - Copy the following keys:
     - Secret Key
     - Public Key
     - Encryption Key

3. **Configure Webhook**
   - Set webhook URL: `https://your-domain.com/flutterwave/webhook`
   - Copy the webhook hash for security

### 2. Environment Configuration

Add these variables to your `.env` file:

```env
# Flutterwave Configuration
FLW_SECRET_KEY=your_secret_key_here
FLW_PUBLIC_KEY=your_public_key_here
FLW_ENCRYPTION_KEY=your_encryption_key_here
FLUTTERWAVE_WEBHOOK_HASH=your_webhook_hash_here
```

### 3. Database Setup

Run the migration to create default subscription plans:

```bash
php artisan migrate
```

This will create the following subscription plans in the database:
- Basic (25 tokens - RWF 12,500)
- Standard (75 tokens - RWF 37,500) - Recommended
- Premium (200 tokens - RWF 100,000)
- Student Monthly (50 tokens - RWF 25,000)
- Semester Pass (500 tokens - RWF 250,000)

### 4. Webhook Configuration

For production deployment, ensure your webhook endpoint is accessible:

```
POST https://your-domain.com/flutterwave/webhook
```

The webhook handles:
- Payment completion notifications
- Failed payment notifications
- Automatic token allocation
- Transaction status updates

## How Students Use the System

### 1. Access Token Purchase
- Log in as student
- Go to Dashboard
- Click "Buy More Tokens" in the Token Management section
- Or navigate directly to `/tokens/dashboard`

### 2. Choose Purchase Option
**Option A: Subscription Plans**
- Browse available subscription plans
- See recommended plan highlighted
- Click "Select Plan" on desired subscription

**Option B: Custom Amount**
- Enter number of tokens (1-1000)
- See bulk discounts applied automatically
- Add optional payment reference
- Click "Purchase Now"

### 3. Complete Payment
- Redirected to Flutterwave secure payment page
- Choose payment method (Card, Bank Transfer, Mobile Money, etc.)
- Complete payment process

### 4. Receive Tokens
- Automatically redirected back to student dashboard
- Tokens added to account immediately
- Receive success confirmation
- View updated token balance and transaction history

## Admin Features

### 1. Subscription Management
- View all subscription plans at `/admin/subscriptions`
- Create new subscription plans
- Edit existing plans (price, tokens, features)
- Activate/deactivate plans
- Set recommended plan

### 2. Payment Tracking
- View all student subscriptions at `/admin/subscriptions/student-subscriptions`
- See payment status and transaction details
- Extend active subscriptions
- Generate revenue reports

### 3. Token Management
- View student token balances
- Add/deduct tokens manually
- View complete transaction history
- Generate token usage reports

## Security Features

### 1. Payment Security
- All payments processed through Flutterwave secure infrastructure
- PCI DSS compliant payment processing
- Encrypted transaction data

### 2. Webhook Security
- Webhook signature verification
- Request validation
- Duplicate transaction prevention

### 3. Access Control
- Student-only payment endpoints
- Admin-only management features
- Proper authentication and authorization

## Testing

### 1. Flutterwave Test Mode
- Use Flutterwave test keys for development
- Test with various payment scenarios
- Verify webhook processing

### 2. Local Testing
- Use tools like ngrok for webhook testing
- Test payment flow end-to-end
- Verify token allocation

## Troubleshooting

### Common Issues

1. **Flutterwave Configuration Error**
   - Check API keys in `.env` file
   - Verify keys are correct for test/live mode
   - Ensure service provider is registered

2. **Payment Not Processing**
   - Check Flutterwave account status
   - Verify webhook URL is accessible
   - Check payment method availability

3. **Tokens Not Allocated**
   - Check payment verification logs
   - Verify webhook processing
   - Check student token initialization

4. **Webhook Issues**
   - Verify webhook hash in `.env`
   - Check webhook URL accessibility
   - Review Flutterwave webhook logs

### Log Monitoring

Monitor these logs for issues:
- Laravel logs (`storage/logs/laravel.log`)
- Flutterwave transaction logs
- Payment verification logs

## Support

For Flutterwave-specific issues:
- Flutterwave Documentation: https://developer.flutterwave.com
- Flutterwave Support: support@flutterwave.com

For application-specific issues:
- Check application logs
- Verify database connections
- Review configuration settings

## Future Enhancements

Potential improvements:
- Recurring subscription options
- Multiple payment gateway support
- Advanced analytics and reporting
- Mobile app integration
- SMS/email payment notifications
