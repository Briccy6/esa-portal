@php
    $statusCode = 403;
    $headline = 'Oops..';
    $title = 'Access Denied';
    $message = 'The page you are trying to reach is forbidden for your current access level.';
    $primaryLabel = 'Go Back';
    $primaryAction = 'back';
    $secondaryLabel = 'Check Again';
    $secondaryAction = 'reload';
@endphp
@include('errors.partials.fullscreen')
