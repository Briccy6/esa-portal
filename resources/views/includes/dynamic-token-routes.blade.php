@php
    $isAdmin = auth()->guard('admin')->check();
    $tokenIndexRoute = $isAdmin ? route('admin.tokens.index') : route('secretary.tokens.index');
    $tokenAddRoute = $isAdmin ? route('admin.tokens.add', ':studentId') : route('secretary.tokens.add', ':studentId');
    $tokenDeductRoute = $isAdmin ? route('admin.tokens.deduct', ':studentId') : route('secretary.tokens.deduct', ':studentId');
    $tokenHistoryRoute = $isAdmin ? route('admin.tokens.history', ':studentId') : route('secretary.tokens.history', ':studentId');
    $tokenHistoryBaseRoute = $isAdmin ? route('admin.tokens.history') : route('secretary.tokens.history');
@endphp
