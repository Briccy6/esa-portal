<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Middleware\ValidatePostSize as BaseValidatePostSize;
use Illuminate\Http\Request;

class CustomValidatePostSize extends BaseValidatePostSize
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Set higher limits before validation
        ini_set('post_max_size', '50M');
        ini_set('upload_max_filesize', '50M');
        
        $max = $this->getPostMaxSize();

        if ($max > 0 && $request->server('CONTENT_LENGTH') > $max) {
            throw new \Illuminate\Http\Exceptions\PostTooLargeException('The POST data is too large.');
        }

        return $next($request);
    }

    /**
     * Override to return our custom max size
     */
    protected function getPostMaxSize()
    {
        return 50 * 1048576; // 50MB in bytes
    }
}
