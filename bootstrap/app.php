<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        
        // 1. Logic to INSERT into DB
        $exceptions->reportable(function (Throwable $e) {
            \Illuminate\Support\Facades\Log::info('Attempting to log error to DB: ' . $e->getMessage());

            try {
                // Use a clean DB connection to ensure it doesn't get rolled back
                DB::connection()->getPdo()->prepare("EXEC sp_ErrorHandling ?, ?, ?")
                    ->execute([
                        request()->path(),
                        substr($e->getMessage(), 0, 500),
                        Auth::user()->name ?? 'Guest'
                    ]);
            } catch (\Throwable $fallback) {
                \Illuminate\Support\Facades\Log::error('DB Logging Failed: ' . $fallback->getMessage());
            }
        });

        // 2. Modified Logic to DISPLAY only the latest error
        $exceptions->render(function (Throwable $e, Request $request) {
            // Get only the latest error from database
            $latestError = DB::table('Sys_Error_Log')
                ->orderBy('InsertedDate', 'desc')
                ->first();  // Changed from get() to first()
            
            // Also get current error details to display
            $currentError = [
                'path' => request()->path(),
                'message' => $e->getMessage(),
                'time' => now()->format('Y-m-d H:i:s'),
                'user' => Auth::user()->name ?? 'Guest'
            ];
            
            return response()->view('error_logs', [
                'latestError' => $latestError,  // Single latest error from DB
                'currentError' => $currentError, // Current error details
                'allErrors' => collect([$latestError]) // Kept as collection for compatibility
            ], 500);
        });
        
    })->create();