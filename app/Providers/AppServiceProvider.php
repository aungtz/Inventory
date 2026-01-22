<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View; // Add this import

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
   // In app/Providers/AppServiceProvider.php
public function boot()
{
    // Add helper functions to all views
    View::composer('*', function ($view) {
        $view->with('getSortUrl', function ($column) {
            $currentSort = request('sort');
            $currentDirection = request('direction', 'asc');
            
            if ($currentSort === $column) {
                // Toggle direction if same column
                $newDirection = $currentDirection === 'asc' ? 'desc' : 'asc';
                return url()->current() . '?' . http_build_query([
                    'sort' => $column,
                    'direction' => $newDirection
                ]);
            } else {
                // New column, default to ascending
                return url()->current() . '?' . http_build_query([
                    'sort' => $column,
                    'direction' => 'asc'
                ]);
            }
        });
        
        $view->with('getSortIconClass', function ($column) {
            $currentSort = request('sort');
            if ($currentSort === $column) {
                return 'text-blue-600';
            }
            return 'text-gray-400';
        });
        
        $view->with('getSortIconPath', function ($column) {
            $currentSort = request('sort');
            $currentDirection = request('direction', 'asc');
            
            if ($currentSort === $column) {
                if ($currentDirection === 'asc') {
                    return 'M7 16V4m0 0L3 8m4-4l4 4m10 4V20m0 0l4-4m-4 4l-4-4';
                } else {
                    return 'M7 4V16m0 0L3 12m4 4l4-4m10 12V8m0 0l-4 4m4-4l4 4';
                }
            }
            return 'M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4';
        });
    });
}
}
