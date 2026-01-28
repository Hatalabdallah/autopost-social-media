<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class WhatsAppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('whatsapp', function () {
            $data = Setting::where('option_name', 'like', 'whatsapp_%')->pluck('option_value', 'option_name')->toArray();
            
            $token = $data['whatsapp_access_token'] ?? env('WHATSAPP_ACCESS_TOKEN');
            $phoneNumberId = $data['whatsapp_phone_number_id'] ?? env('WHATSAPP_PHONE_NUMBER_ID');

            return Http::withToken($token)
                ->baseUrl("https://graph.facebook.com/v21.0/{$phoneNumberId}");
        });
    }
}