<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Blade;
use App\Helpers\CurrencyHelper;

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
    public function boot(): void
    {
        Paginator::defaultView('pagination::bootstrap-5');
        Paginator::defaultSimpleView('pagination::bootstrap-5');
        
        // Configure stronger password defaults
        Password::defaults(function () {
            return Password::min(10)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised();
        });
        
        // Add current_password validation rule
        Validator::extend('current_password', function ($attribute, $value, $parameters, $validator) {
            return Hash::check($value, auth()->user()->password);
        }, 'The current password is incorrect.');

        // Register the currency Blade directive
        Blade::directive('currency', function ($expression) {
            return "<?php echo App\Helpers\CurrencyHelper::format($expression); ?>";
        });
        
        // Register a directive for Thai Baht
        Blade::directive('baht', function ($expression) {
            return "<?php echo App\Helpers\CurrencyHelper::baht($expression); ?>";
        });
    }
}
