@php
    $statusCode = 429;
    $headline = 'Oops..';
    $title = 'Too Many Requests';
    $message = 'You have sent too many requests in a short time. Please wait and try again.';
    $primaryLabel = 'Try Again';
    $primaryAction = 'reload';
    $secondaryLabel = 'Go Back';
    $secondaryAction = 'back';
@endphp
@include('errors.partials.fullscreen')
