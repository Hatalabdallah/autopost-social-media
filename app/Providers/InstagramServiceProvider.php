<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class InstagramServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('instagram', function () {
            $data = Setting::where('option_name', 'like', 'instagram_%')->pluck('option_value', 'option_name')->toArray();
            
            $token = $data['instagram_access_token'] ?? env('INSTAGRAM_ACCESS_TOKEN');
            $userId = $data['instagram_user_id'] ?? env('INSTAGRAM_USER_ID');

            return Http::withToken($token)
                ->baseUrl("https://graph.facebook.com/v21.0/{$userId}");
        });
    }
}