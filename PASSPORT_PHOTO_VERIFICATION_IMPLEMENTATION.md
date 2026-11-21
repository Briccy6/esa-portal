# AI-Powered Student Passport Photograph Verification - Implementation Summary

## Overview
This implementation adds AI-powered passport photograph verification to the ESA School Management System using Google's Gemini AI. The system automatically validates uploaded passport photos against school requirements before accepting them.

## Files Modified

### 1. Configuration Files
- **`.env`** - Added Gemini API configuration:
  - `GEMINI_API_KEY` - API key for Gemini (to be added by administrator)
  - `GEMINI_MODEL` - Model to use (default: gemini-2.0-flash-exp)

- **`config/services.php`** - Added Gemini service configuration:
  - API key, model, and endpoint configuration

### 2. Database
- **`database/migrations/2026_11_21_000000_add_passport_photo_verification_to_students_table.php`** - New migration:
  - `passport_photo_verification_status` (enum: pending, approved, rejected, manual_review)
  - `passport_photo_verified_at` (timestamp)
  - `passport_photo_verification_result` (JSON - full AI response)
  - `passport_photo_rejection_reason` (string)
  - `passport_photo_confidence_score` (decimal)
  - `passport_photo_requires_manual_review` (boolean)
  - `passport_photo_verified_by` (unsignedBigInteger - admin ID)

- **`app/Models/Student.php`** - Updated:
  - Added new fields to `$fillable`
  - Added casts for new fields
  - Added `verifiedByAdmin()` relationship

### 3. Backend Services
- **`app/Services/PassportPhotoVerificationService.php`** - New service:
  - Pre-validates images (size, type, dimensions, aspect ratio, orientation)
  - Calls Gemini API for AI analysis
  - Parses and validates JSON response
  - Maps rejection reasons to user-friendly messages
  - Handles errors gracefully

### 4. Controllers
- **`app/Http/Controllers/StudentController.php`** - Updated:
  - Added `verifyPassportPhoto()` - API endpoint for real-time verification
  - Updated `updateProfile()` - Integrates AI verification before saving
  - Added `updatePassportVerification()` - Admin override functionality

### 5. Routes
- **`routes/web.php`** - Added:
  - `POST /student/profile/verify-passport-photo` - Real-time verification
  - `PUT /admin/students/{student}/update-passport-verification` - Admin override

### 6. Frontend Views
- **`resources/views/students/profile.blade.php`** - Updated:
  - Added "Accepted passport photo example" section with visual guidelines
  - Added "Photos we do not accept" section with rejection examples
  - Added live verification status indicator (loading, success, error)
  - Added JavaScript for real-time AI verification on file selection

- **`resources/views/admin/students/show.blade.php`** - Updated:
  - Added verification status badge (approved/rejected/manual review/pending)
  - Added confidence score display
  - Added verification timestamp
  - Added admin override button and modal
  - Added rejection reason display

## How It Works

### Student Upload Flow
1. Student selects a passport photo on their profile page
2. Frontend validates file size (200 KB max) and type (JPG, JPEG, PNG)
3. Image preview is displayed
4. **Automatic AI verification** is triggered:
   - Image is sent to Gemini API
   - AI evaluates against passport photo requirements
   - Result is displayed in real-time (success or specific error message)
5. If approved, student can save the profile
6. If rejected, student sees specific reason and can try another photo

### Backend Validation Flow
1. Laravel validation runs first (file size, type)
2. `PassportPhotoVerificationService` pre-validates:
   - File size (200 KB)
   - MIME type (JPG, JPEG, PNG)
   - Minimum resolution (300 × 385 pixels)
   - Aspect ratio (approximately 7:9)
   - Orientation (portrait only)
3. If pre-validation passes, Gemini API is called
4. AI evaluates:
   - White background
   - Single face
   - Face centered
   - Facing camera
   - Not blurry
   - Not selfie
   - Not group photo
   - Not document scan
5. Results are stored in database
6. Confidence score < 0.7 triggers manual review flag

### Admin Override Flow
1. Admin views student details
2. Sees verification status and confidence score
3. Can click "Override Verification" button
4. Modal allows:
   - Changing status (approved/rejected/manual review)
   - Selecting rejection reason
   - Adding admin notes
5. Override is logged with admin ID and timestamp

## Passport Photo Requirements

The system enforces these ESA requirements:
- **Background**: Plain white
- **Image type**: Professional passport-style portrait
- **Number of faces**: Exactly one
- **Face position**: Centered
- **Orientation**: Portrait
- **Aspect ratio**: Approximately 7:9
- **Minimum resolution**: 300 × 385 pixels
- **Maximum file size**: 200 KB
- **Accepted formats**: JPG, JPEG, PNG
- **Image quality**: Clear and not blurry

The system rejects:
- Selfies
- Colored backgrounds
- Group photographs
- Blurry images
- Document scans
- Landscape photographs
- Screenshots
- Images with no face
- Images with multiple faces

## Testing Instructions

### Prerequisites
1. Add Gemini API key to `.env`:
   ```
   GEMINI_API_KEY=your_gemini_api_key_here
   GEMINI_MODEL=gemini-flash-latest
   VERIFY_SSL=false  # Set to true in production
   ```

2. Run the migration (already completed):
   ```bash
   php artisan migrate
   ```

3. Clear caches (already completed):
   ```bash
   php artisan config:clear
   php artisan route:clear
   php artisan cache:clear
   ```

### Test Cases

#### 1. Valid Passport Photo
- Upload a proper passport photo with white background
- Expected: Green success message, status = approved

#### 2. Selfie
- Upload a selfie
- Expected: Rejection with "This photograph does not meet our passport photo requirements"

#### 3. Colored Background
- Upload photo with colored background
- Expected: Rejection with "Your photograph has a colored or unsuitable background"

#### 4. Group Photo
- Upload photo with multiple people
- Expected: Rejection with "More than one person was detected"

#### 5. Blurry Image
- Upload blurry photo
- Expected: Rejection with "Your photograph is blurry"

#### 6. Landscape Orientation
- Upload landscape photo
- Expected: Rejection with "Your image must be in portrait orientation"

#### 7. Document Scan
- Upload scanned ID card
- Expected: Rejection with "Scanned identity cards and documents are not accepted"

#### 8. File Too Large
- Upload image > 200 KB
- Expected: Rejection with "Your photograph exceeds the maximum allowed size of 200 KB"

#### 9. Wrong File Type
- Upload non-image file
- Expected: Rejection with "Only JPG, JPEG and PNG photographs are accepted"

#### 10. No Face
- Upload image with no face
- Expected: Rejection with "No human face was detected"

### Admin Override Testing
1. Log in as admin
2. View a student with passport photo
3. Check verification status display
4. Click "Override Verification"
5. Change status and add notes
6. Verify override is saved and displayed

## Security Considerations

1. **API Key Security**: Gemini API key is stored in `.env` (server-side only)
2. **No Client-Side Verification**: All verification happens server-side
3. **Input Validation**: Multiple layers of validation (Laravel, pre-validation, AI)
4. **Error Handling**: Graceful degradation if AI service fails
5. **Audit Trail**: All overrides are logged with admin ID and timestamp
6. **Data Privacy**: Images are sent to Gemini; ensure compliance with privacy policies

## Performance Notes

1. Verification happens in real-time during file selection
2. Pre-validation filters out invalid images before API call
3. API timeout set to 30 seconds
4. Low confidence scores trigger manual review instead of automatic rejection
5. Verification results are cached in database to avoid re-verification

## Limitations

1. Requires active Gemini API key (configured and tested)
2. Dependent on Gemini API availability (may experience temporary high demand)
3. AI model may occasionally produce false positives/negatives
4. Admin manual review required for edge cases
5. File size limit of 200 KB may restrict some high-quality photos
6. SSL verification disabled in local development (set VERIFY_SSL=true in production)

## Future Enhancements

1. Add bulk verification for existing photos
2. Implement queue-based verification for better performance
3. Add verification history tracking
4. Enable manual review queue for admins
5. Add analytics on rejection reasons
6. Support additional AI models as fallback

## Rollback Instructions

If needed, rollback the migration:
```bash
php artisan migrate:rollback --step=1
```

Then remove the code changes or comment out the verification integration in the controller.
