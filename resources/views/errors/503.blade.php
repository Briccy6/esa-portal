@php
    $statusCode = 503;
    $headline = 'Oops..';
    $title = 'System is unavailable at the moment';
    $message = 'The system is currently unavailable. Please try again shortly.';
    $primaryLabel = 'Check Again';
    $primaryAction = 'reload';
    $secondaryLabel = 'Go Back';
    $secondaryAction = 'back';
@endphp
@include('errors.partials.fullscreen')
