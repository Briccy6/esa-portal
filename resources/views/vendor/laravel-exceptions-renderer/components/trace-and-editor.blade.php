<div class="laravel-exceptions-renderer-trace-and-editor">
    <!-- Fallback trace-and-editor: show basic trace if available -->
    @if(isset($trace))
        <pre style="white-space:pre-wrap;">{{ $trace }}</pre>
    @endif
</div>
