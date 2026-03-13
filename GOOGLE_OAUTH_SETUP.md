# Google OAuth Setup for Production

## Overview
This guide will help you set up Google OAuth for client authentication in your production environment.

## Steps to Fix Google OAuth

### 1. Update Google Console Configuration

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Select your project
3. Navigate to **APIs & Services** > **Credentials**
4. Find your OAuth 2.0 Client ID for web application
5. Click **Edit** to update the redirect URIs

### 2. Add Production Redirect URIs

Add the following authorized redirect URIs:

```
https://excellentservantacademy.com/auth/client/google/callback
https://excellentservantacademy.com/google/callback
```

**Important:** Make sure to use your actual production domain, not "excellentservantacademy.com" if it's different.

### 3. Update Environment Variables

In your production environment, update the following variables:

```bash
APP_URL=https://your-actual-domain.com
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI=https://your-actual-domain.com/auth/client/google/callback
```

### 4. Deploy Updated Configuration

1. Copy the `.env.hostinger` file to your production server as `.env`
2. Update the following values:
   - `APP_URL` - Set to your actual production domain
   - `DB_HOST` - Set to your actual database hostname
   - `GOOGLE_REDIRECT_URI` - Set to your actual domain

### 5. Clear Caches

After deployment, run these commands:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### 6. Test the OAuth Flow

1. Go to your production site
2. Click "Continue with Google"
3. Verify it redirects to Google OAuth
4. Verify it redirects back to your site after authentication
5. Verify new users are created and logged in
6. Verify existing users are logged in directly

## How the Fixed OAuth Flow Works

### For New Users:
1. User clicks "Continue with Google"
2. Redirect to Google OAuth
3. User authenticates with Google
4. Redirect back to your site
5. New client account is created automatically
6. User is logged in and redirected to client dashboard
7. Welcome email is sent

### For Existing Users:
1. User clicks "Continue with Google"
2. Redirect to Google OAuth
3. User authenticates with Google
4. Redirect back to your site
5. Existing account is found by email
6. Google ID is updated if not already set
7. User is logged in and redirected to client dashboard

## Troubleshooting

### Error: "redirect_uri_mismatch"
- Make sure the exact URL in your `.env` matches what's in Google Console
- Check for trailing slashes
- Verify HTTPS vs HTTP

### Error: "Unable to login with Google"
- Check Google Client ID and Secret are correct
- Verify Google OAuth is enabled for your project
- Check server logs for detailed error messages

### Users getting redirected to localhost
- Update `APP_URL` in your production `.env`
- Clear caches after updating
- Verify the redirect URI in Google Console

## Security Notes

1. **Keep secrets secure:** Never commit `.env` files to version control
2. **Use HTTPS:** Always use HTTPS in production for OAuth
3. **Limit scope:** Only request necessary permissions from Google
4. **Validate emails:** Consider adding email verification for OAuth users

## Next Steps

1. Update your Google Console with production URLs
2. Deploy the updated `.env.hostinger` as `.env` to production
3. Clear all caches
4. Test the complete OAuth flow
5. Monitor for any errors in logs

## Support

If you encounter issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Verify all environment variables are set correctly
3. Ensure Google Console configuration matches your production URLs
