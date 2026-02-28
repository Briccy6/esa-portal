<div class="laravel-exceptions-renderer-context">
    <!-- Fallback context component -->
    @if(!empty($context) && is_array($context))
        <ul>
            @foreach($context as $k => $v)
                <li><strong>{{ $k }}:</strong> @if(is_scalar($v)){{ $v }}@else{{ json_encode($v) }}@endif</li>
            @endforeach
        </ul>
    @endif
</div>
