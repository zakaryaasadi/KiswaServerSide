<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReviewLogger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }

    public function terminate($request, $response)
    {
        Log::channel("review_log")->info('ApiLog done===========================');
        Log::channel("review_log")->info('URL: ' . $request->fullUrl());
        Log::channel("review_log")->info('Method: ' . $request->getMethod());
        Log::channel("review_log")->info('IP Address: ' . $request->getClientIp());
        Log::channel("review_log")->info('Data: ',[$request->all()]);
        Log::channel("review_log")->info('Response: '.$response->getContent());
    }
}
