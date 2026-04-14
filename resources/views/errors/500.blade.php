@php
    $statusCode = 500;
    $headline = 'Oops..';
    $title = 'Server Error';
    $message = 'Something went wrong on our side. Please try again in a moment.';
    $primaryLabel = 'Try Again';
    $primaryAction = 'reload';
    $secondaryLabel = 'Go Back';
    $secondaryAction = 'back';
@endphp
@include('errors.partials.fullscreen')
