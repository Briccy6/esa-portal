# Report Card Integration Quick Reference

## Frontend Navigation Links

Add these links to your student dashboard/navigation menu:

### Main Report Card Link
```blade
<a href="{{ route('student.report-cards.index') }}" class="nav-link">
    <i class="fas fa-file-pdf"></i> My Report Cards
</a>
```

### Route Names & URLs
```php
// View all report cards
route('student.report-cards.index')
// URL: /student/report-cards

// View specific report card
route('student.report-cards.show', $reportCard->id)
// URL: /student/report-cards/{id}

// Check report card status
route('student.report-cards.status')
// URL: /student/report-cards-status

// Start fee payment (form action)
route('flutterwave.payment.initialize-reportcard-fees')
// URL: /flutterwave/payment/initialize-reportcard-fees (POST)
```

## Integration in Sidebar/Menu

### Code Example (Blade)
```blade
<!-- Report Cards Section in Student Dashboard -->
<div class="menu-section">
    <h3 class="menu-title">Academic</h3>
    <ul class="menu-list">
        <li>
            <a href="{{ route('student.report-cards.index') }}">
                <i class="fas fa-file-pdf"></i>
                <span>Report Cards</span>
                @if(auth('student')->user()->hasOutstandingFees())
                    <span class="badge badge-danger">Action Required</span>
                @endif
            </a>
        </li>
        <li>
            <a href="{{ route('student.report-cards.status') }}">
                <i class="fas fa-info-circle"></i>
                <span>Access Status</span>
            </a>
        </li>
    </ul>
</div>
```

## Admin Settings

### Setting Up Report Card Release Date

Navigate to: `/admin/report-card-settings`

**Form Fields**:
- `release_at` - Date & Time when report cards become visible
- Example: April 15, 2026 at 10:00 AM

**Using the Setting in Code**:
```php
$settings = ReportCardSetting::first();
$releaseDate = $settings->release_at;
```

## Student Model Usage in Views

### Check Outstanding Fees
```blade
@if($student->hasOutstandingFees())
    <p>Outstanding Fees: {{ $student->formatted_outstanding_fees }}</p>
@endif
```

### Get Outstanding Amount
```php
$amount = $student->getOutstandingFees();
$formatted = $student->formatted_outstanding_fees; // Returns "RWF XX,XXX.XX"
```

### Display Status Badge
```blade
@if($student->hasOutstandingFees())
    <span class="badge badge-danger">Outstanding Fees</span>
@elseif($now < ReportCardSetting::first()->release_at)
    <span class="badge badge-warning">Report Card Pending</span>
@else
    <span class="badge badge-success">Report Card Available</span>
@endif
```

## Dashboard Widget Example

```blade
<!-- Report Card Summary Widget -->
<div class="widget widget-report-cards">
    <div class="widget-header">
        <h3>Report Cards</h3>
        <a href="{{ route('student.report-cards.index') }}" class="btn-sm">View All</a>
    </div>
    <div class="widget-body">
        @php
            $reportCards = auth('student')->user()->reportCards;
            $outstanding = auth('student')->user()->getOutstandingFees();
        @endphp

        @if($outstanding > 0)
            <div class="alert alert-danger">
                Outstanding Fees: RWF {{ number_format($outstanding, 2) }}
                <button onclick="location.href='{{ route('student.report-cards.index') }}'" class="btn btn-sm btn-primary ml-2">
                    Pay Now
                </button>
            </div>
        @endif

        <div class="report-cards-list">
            @forelse($reportCards->take(3) as $card)
                <div class="report-card-item">
                    <span>{{ $card->course->name }} - {{ $card->term->name }}</span>
                    <a href="{{ route('student.report-cards.show', $card) }}" class="btn-link">View</a>
                </div>
            @empty
                <p class="text-muted">No report cards yet</p>
            @endforelse
        </div>
    </div>
</div>
```

## Notification/Alert System Integration

### When Report Card Becomes Available
```php
// Send notification to student
auth()->user()->notify(new ReportCardAvailable($reportCard));
```

### Outstanding Fees Reminder
```php
// In a scheduled task (app/Console/Kernel.php)
$studentsWithFees = Student::where(function ($q) {
    $q->where('outstanding_amount', '>', 0)
      ->orWhereHas('schoolFees', fn($q) => $q->where('status', '!=', 'paid'));
})->get();

foreach ($studentsWithFees as $student) {
    $student->notify(new OutstandingFeesReminder($student->getOutstandingFees()));
}
```

## Payment Status Display

### Show Payment Status
```blade
@php
    $latestPayment = auth('student')->user()
        ->transactions()
        ->where('type', 'report_card_fees')
        ->latest()
        ->first();
@endphp

@if($latestPayment && $latestPayment->status === 'successful')
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        Payment confirmed on {{ $latestPayment->paid_at->format('M d, Y H:i A') }}
    </div>
@endif
```

## Troubleshooting

### Check Student's Outstanding Fees
```php
// In tinker or anywhere
$student = Student::find(1);
echo $student->getOutstandingFees();  // Get amount
echo $student->hasOutstandingFees();  // Check if has fees
echo $student->formatted_outstanding_fees;  // Get formatted string
```

### View Payment Transactions
```php
$transactions = StudentTransaction::where([
    'student_id' => 1,
    'type' => 'report_card_fees'
])->get();
```

### Debug Release Date
```php
$settings = ReportCardSetting::first();
echo $settings->release_at;  // Shows datetime
echo now()->lt($settings->release_at) ? 'Locked' : 'Available';  // Check status
```

## Email Template Example

Create: `resources/views/emails/report-card-available.blade.php`

```blade
<h2>Your Report Card is Now Available</h2>

<p>Hello {{ $student->full_name }},</p>

<p>We're pleased to inform you that your report card for 
{{ $reportCard->academicYear->year }} - {{ $reportCard->term->name }} 
is now available for download.</p>

<p>You can view your report card here:</p>

<a href="{{ route('student.report-cards.show', $reportCard) }}" class="btn btn-primary">
    View Report Card
</a>

<p>The report card will remain available for 30 days.</p>

<p>Best regards,<br>{{ config('app.name') }} Administration</p>
```

## CSS Classes for Styling

```css
/* Report card container */
.report-card {
    border-left: 4px solid #16A34A;
}

/* Status badges */
.status-blocked { background: #DC2626; color: white; }
.status-pending { background: #EAB308; color: black; }
.status-available { background: #16A34A; color: white; }

/* Countdown timer */
.countdown-timer {
    font-family: 'Courier New', monospace;
    font-size: 2rem;
    font-weight: bold;
}

/* Payment button */
.btn-pay-fees {
    background: linear-gradient(to right, #16A34A, #15803D);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
}
```

## API Endpoints Summary

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/student/report-cards` | GET | List all student report cards |
| `/student/report-cards/{id}` | GET | View specific report card |
| `/student/report-cards-status` | GET | View access status for all cards |
| `/flutterwave/payment/initialize-reportcard-fees` | POST | Begin fee payment |
| `/flutterwave/payment/callback` | GET | Handle payment confirmation |

---

**Last Updated**: April 3, 2026
