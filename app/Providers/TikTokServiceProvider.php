<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class TikTokServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('tiktok', function () {
            $data = Setting::where('option_name', 'like', 'tiktok_%')->pluck('option_value', 'option_name')->toArray();
            
            $token = $data['tiktok_access_token'] ?? env('TIKTOK_ACCESS_TOKEN');

            return Http::withToken($token)
                ->baseUrl("https://open.tiktokapis.com/v2");
        });
    }
}