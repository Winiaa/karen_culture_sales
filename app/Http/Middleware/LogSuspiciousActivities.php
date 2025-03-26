<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogSuspiciousActivities
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // List of potentially suspicious parameters
        $suspiciousParams = ['<script', 'javascript:', 'onload=', 'onerror=', 'onclick=', '../', '..\\', 'etc/passwd', 
                            'union select', 'exec(', 'system(', 'passthru', 'eval(', 'base64_decode'];
        
        // Check for suspicious query parameters or input data
        $requestData = array_merge($request->query(), $request->all());
        $suspiciousFound = false;
        $suspiciousValues = [];
        
        // Check through all request data for suspicious patterns
        foreach ($requestData as $key => $value) {
            if (is_string($value)) {
                foreach ($suspiciousParams as $pattern) {
                    if (stripos($value, $pattern) !== false) {
                        $suspiciousFound = true;
                        $suspiciousValues[$key] = $value;
                        break;
                    }
                }
            }
        }
        
        // Log suspicious request if found
        if ($suspiciousFound) {
            Log::warning('Suspicious request detected', [
                'ip' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'suspicious_parameters' => $suspiciousValues,
                'user_id' => $request->user() ? $request->user()->id : 'guest',
                'referrer' => $request->header('referer')
            ]);
        }
        
        // Also log any unusual HTTP methods
        $standardMethods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'];
        if (!in_array($request->method(), $standardMethods)) {
            Log::warning('Unusual HTTP method detected', [
                'method' => $request->method(),
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'user_agent' => $request->header('User-Agent')
            ]);
        }
        
        return $next($request);
    }
} 