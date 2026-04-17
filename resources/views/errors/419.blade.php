@php
    $statusCode = 419;
    $headline = 'Oops..';
    $title = 'Session Expired';
    $message = 'Your session has expired. Please refresh and try again.';
    $primaryLabel = 'Refresh';
    $primaryAction = 'reload';
    $secondaryLabel = 'Log In Again';
    $secondaryUrl = route('login');
    $secondaryAction = 'link';
@endphp
@include('errors.partials.fullscreen')
