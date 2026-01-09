<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\DB;   // IMPORTANT: Missing this
use Illuminate\Support\Facades\Auth; // IMPORTANT: Missing this
use Illuminate\Http\Request;         // IMPORTANT: Missing this

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
    // Log to the standard laravel log file too so we can see if THIS fails
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
        // Log the reason why the DB insert failed to storage/logs/laravel.log
        \Illuminate\Support\Facades\Log::error('DB Logging Failed: ' . $fallback->getMessage());
    }
});

        // 2. Logic to DISPLAY the table
        $exceptions->render(function (Throwable $e, Request $request) {
            // This fetches the data for your blade file
            $logs = DB::table('Sys_Error_Log')->orderBy('InsertedDate', 'desc')->get();
            
            return response()->view('error_logs', ['logs' => $logs], 500);
        });
        
    })->create();