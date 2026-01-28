<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class LinkedInServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('linkedin', function () {
            $data = Setting::where('option_name', 'like', 'linkedin_%')->pluck('option_value', 'option_name')->toArray();
            
            $token = $data['linkedin_access_token'] ?? env('LINKEDIN_ACCESS_TOKEN');

            return Http::withToken($token)
                ->baseUrl("https://api.linkedin.com/v2");
        });
    }
}