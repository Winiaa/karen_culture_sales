<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $e)
    {
        if ($this->isHttpException($e)) {
            $statusCode = $e->getStatusCode();
            
            // Check if user is authenticated and get their type
            if (auth()->check()) {
                $userType = auth()->user()->usertype;
                
                // Route to appropriate error page based on user type
                if ($userType === 'admin') {
                    return response()->view("errors.admin.{$statusCode}", [], $statusCode);
                } elseif ($userType === 'driver') {
                    return response()->view("errors.driver.{$statusCode}", [], $statusCode);
                }
            }
            
            // Default to customer error page
            return response()->view("errors.{$statusCode}", [], $statusCode);
        }

        return parent::render($request, $e);
    }
} 