<div class="container" style="max-width:400px;">
    <h3 class="mb-4">Student Login</h3>
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <form method="POST" action="{{ route('student.login.submit') }}">
        @csrf
        <div class="mb-3">
            <label for="reg_number" class="form-label">Registration Number</label>
            <input type="text" class="form-control" id="reg_number" name="reg_number" required autofocus>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
</div>
