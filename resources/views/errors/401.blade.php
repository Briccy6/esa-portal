@php
    $statusCode = 401;
    $headline = 'Oops..';
    $title = 'Authentication Required';
    $message = 'Please log in to access this page.';
    $primaryLabel = 'Log In';
    $primaryUrl = route('login');
    $primaryAction = 'link';
    $secondaryLabel = 'Go Back';
    $secondaryAction = 'back';
@endphp
@include('errors.partials.fullscreen')
