@php
    $statusCode = 422;
    $headline = 'Oops..';
    $title = 'Validation Error';
    $message = 'Please review your input and fix the highlighted errors.';
    $primaryLabel = 'Go Back & Fix';
    $primaryAction = 'back';
    $secondaryLabel = 'Refresh';
    $secondaryAction = 'reload';
    $showErrors = true;
@endphp
@include('errors.partials.fullscreen')
