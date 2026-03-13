@extends('admin.layout')

@section('title', 'Admin Preferences')

@section('content')
<div class="container" style="max-width:600px;">
    <h3 class="mb-4">Admin Preferences</h3>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <form method="POST" action="{{ route('admin.preferences.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label class="form-label">System Name</label>
            <input type="text" name="system_name" class="form-control" value="{{ old('system_name', $prefs['system_name']) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">System Logo</label>
            @if($prefs['system_logo'])
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $prefs['system_logo']) }}" alt="System Logo" style="max-height:60px;">
                </div>
            @endif
            <input type="file" name="system_logo" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">YouTube API Key</label>
            <input type="text" name="youtube_api" class="form-control" value="{{ old('youtube_api', $prefs['youtube_api']) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Payment API Key</label>
            <input type="text" name="payment_api" class="form-control" value="{{ old('payment_api', $prefs['payment_api']) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Google API Key</label>
            <input type="text" name="google_api" class="form-control" value="{{ old('google_api', $prefs['google_api']) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">reCAPTCHA API Key</label>
            <input type="text" name="recaptcha_api" class="form-control" value="{{ old('recaptcha_api', $prefs['recaptcha_api']) }}">
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" name="live_class" class="form-check-input" id="liveClass" value="1" {{ old('live_class', $prefs['live_class']) ? 'checked' : '' }}>
            <label class="form-check-label" for="liveClass">Enable Live Class Feature</label>
        </div>
        <button type="submit" class="btn btn-primary">Save Preferences</button>
    </form>
</div>
@endsection