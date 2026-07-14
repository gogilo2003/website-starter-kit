<?php

namespace Gogilo\Downloads\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DownloadThrottleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param int|null $maxAttempts
     * @param int $decayMinutes
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ?int $maxAttempts = 60, int $decayMinutes = 1): mixed
    {
        $key = 'download_throttle:' . $request->ip() . ':' . $request->route('file');
        
        $attempts = cache()->get($key, 0);
        
        if ($attempts >= $maxAttempts) {
            return response()->json(['error' => 'Too many download attempts'], Response::HTTP_TOO_MANY_REQUESTS);
        }
        
        cache()->put($key, $attempts + 1, now()->addMinutes($decayMinutes));
        
        return $next($request);
    }
}