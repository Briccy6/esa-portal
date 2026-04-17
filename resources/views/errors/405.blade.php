@php
    $statusCode = 405;
    $headline = 'Oops..';
    $title = 'Method Not Allowed';
    $message = 'This action is not allowed for this endpoint.';
    $primaryLabel = 'Go Back';
    $primaryAction = 'back';
    $secondaryLabel = 'Check Again';
    $secondaryAction = 'reload';
@endphp
@include('errors.partials.fullscreen')
