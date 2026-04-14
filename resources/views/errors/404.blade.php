@php
    $statusCode = 404;
    $headline = 'Oops..';
    $title = 'Page Not Found';
    $message = 'The page you are looking for does not exist or may have been moved.';
    $primaryLabel = 'Go Back';
    $primaryAction = 'back';
    $secondaryLabel = 'Check Again';
    $secondaryAction = 'reload';
@endphp
@include('errors.partials.fullscreen')
