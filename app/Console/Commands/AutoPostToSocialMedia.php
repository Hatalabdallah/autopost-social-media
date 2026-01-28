<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\User;
use App\Models\Setting;
use Noweh\TwitterApi\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;

class AutoPostToSocialMedia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-post';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically post scheduled content to social media.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $timezone = env('APP_TIMEZONE') ?? config('app.timezone');
        $scheduledPosts = Post::where('published_at', '<=', now($timezone))
            ->where(function ($query) {
                $query->where('is_posted_to_twitter', false)
                    ->orWhere('is_posted_to_facebook', false)
                    ->orWhere('is_posted_to_linkedin', false)
                    ->orWhere('is_posted_to_instagram', false)
                    ->orWhere('is_posted_to_tiktok', false)
                    ->orWhere('is_posted_to_whatsapp', false);
            })
            ->get();

        $twitterAutoPost = Setting::where('option_name', 'twitter_autopost')->first();
        $facebookAutoPost = Setting::where('option_name', 'facebook_autopost')->first();
        $linkedinAutoPost = Setting::where('option_name', 'linkedin_autopost')->first();
        $instagramAutoPost = Setting::where('option_name', 'instagram_autopost')->first();
        $tiktokAutoPost = Setting::where('option_name', 'tiktok_autopost')->first();
        $whatsappAutoPost = Setting::where('option_name', 'whatsapp_autopost')->first();

        $users = User::all();
        $isAnyPostSuccessful = false;

        try {
            foreach ($scheduledPosts as $post) {
                $tags = $post->tags->pluck('name')->map(fn($tag) => "#$tag")->implode(' ');
                $siteUrl = 'https://tiptopacademy.org/';
                $description = "{$post->description}\n\n{$siteUrl}\n\n{$tags}";

                // Twitter Posting
                if (!$post->is_posted_to_twitter && ($twitterAutoPost->option_value ?? env('TWITTER_AUTO_POST'))) {
                    try {
                        $twitterClient = app(Client::class);
                        $tweetData = ['text' => $description];

                        if (!empty($post->image)) {
                            $fileUrl = url("storage/$post->image");
                            $fileData = base64_encode(file_get_contents($fileUrl));
                            $mediaInfo = $twitterClient->uploadMedia()->upload($fileData);
                            $tweetData['media'] = [
                                'media_ids' => [(string) $mediaInfo["media_id"]],
                            ];
                        }

                        $response = $twitterClient->tweet()->create()->performRequest($tweetData);
                        Log::info("Twitter response: " . json_encode($response));
                        $post->is_posted_to_twitter = true;
                    } catch (\Exception $e) {
                        Log::error("Error posting Post ID {$post->id} to Twitter: " . $e->getMessage());
                    }
                }

                // Facebook Posting
                if (!$post->is_posted_to_facebook && ($facebookAutoPost->option_value ?? env('FACEBOOK_AUTO_POST'))) {
                    $accessToken = app('facebook.access_token');
                    $facebook = app(\Facebook\Facebook::class);
                    $pageID = Setting::where('option_name', 'facebook_page_id')->first()->option_value ?? env('FACEBOOK_PAGE_ID');

                    try {
                        $data = ['message' => $description];

                        if (!empty($post->image)) {
                            $fileUrl = url("storage/$post->image");
                            $data['source'] = $facebook->fileToUpload($fileUrl);
                            $response = $facebook->post("/$pageID/photos", $data, $accessToken);
                        } else {
                            $response = $facebook->post("/$pageID/feed", $data, $accessToken);
                        }

                        Log::info("Facebook response: " . json_encode($response));
                        $post->is_posted_to_facebook = true;
                    } catch (\Exception $e) {
                        Log::error("Error posting Post ID {$post->id} to Facebook: " . $e->getMessage());
                    }
                }

                // LinkedIn Posting
                if (!$post->is_posted_to_linkedin && ($linkedinAutoPost->option_value ?? env('LINKEDIN_AUTO_POST'))) {
                    $accessToken = app('linkedin.access_token');
                    $linkedin = app(\LinkedIn\LinkedIn::class);
                    $profileID = Setting::where('option_name', 'linkedin_profile_id')->first()->option_value ?? env('LINKEDIN_PROFILE_ID');

                    try {
                        $data = ['comment' => $description];

                        if (!empty($post->image)) {
                            $fileUrl = url("storage/$post->image");
                            $data['content']['media'] = [
                                'title' => $post->title,
                                'description' => $description,
                                'source' => $fileUrl
                            ];
                        }

                        $response = $linkedin->post("/v2/ugcPosts", json_encode($data), [
                            "Authorization" => "Bearer {$accessToken}",
                            "Content-Type" => "application/json"
                        ]);

                        Log::info("LinkedIn response: " . json_encode($response));
                        $post->is_posted_to_linkedin = true;
                    } catch (\Exception $e) {
                        Log::error("Error posting Post ID {$post->id} to LinkedIn: " . $e->getMessage());
                    }
                }

                // Instagram Posting
                if (!$post->is_posted_to_instagram && ($instagramAutoPost->option_value ?? env('INSTAGRAM_AUTO_POST'))) {
                    // Instagram posting logic here
                    $accessToken = app('instagram.access_token');
                    $instagram = app(\Instagram\Instagram::class);
                    $userID = Setting::where('option_name', 'instagram_user_id')->first()->option_value ?? env('INSTAGRAM_USER_ID');
                    try {
                        $data = ['caption' => $description];

                        if (!empty($post->image)) {
                            $fileUrl = url("storage/$post->image");
                            $data['image_url'] = $fileUrl;
                        }

                        $response = $instagram->post("/$userID/media", $data, [
                            "Authorization" => "Bearer {$accessToken}",
                            "Content-Type" => "application/json"
                        ]);

                        Log::info("Instagram response: " . json_encode($response));
                        $post->is_posted_to_instagram = true;
                    } catch (\Exception $e) {
                        Log::error("Error posting Post ID {$post->id} to Instagram: " . $e->getMessage());
                    }
                }

                // TikTok Posting
                if (!$post->is_posted_to_tiktok && ($tiktokAutoPost->option_value ?? env('TIKTOK_AUTO_POST'))) {
                    // TikTok posting logic here
                    $accessToken = app('tiktok.access_token');
                    $tiktok = app(\TikTok\TikTok::class);
                    try {
                        $data = ['caption' => $description];

                        if (!empty($post->image)) {
                            $fileUrl = url("storage/$post->image");
                            $data['video_url'] = $fileUrl;
                        }

                        $response = $tiktok->post("/v2/video/upload", json_encode($data), [
                            "Authorization" => "Bearer {$accessToken}",
                            "Content-Type" => "application/json"
                        ]);

                        Log::info("TikTok response: " . json_encode($response));
                        $post->is_posted_to_tiktok = true;
                    } catch (\Exception $e) {
                        Log::error("Error posting Post ID {$post->id} to TikTok: " . $e->getMessage());
                    }
                }

                // WhatsApp Posting
                if (!$post->is_posted_to_whatsapp && ($whatsappAutoPost->option_value ?? env('WHATSAPP_AUTO_POST'))) {
                    // WhatsApp posting logic here
                    $accessToken = app('whatsapp.access_token');
                    $whatsapp = app(\WhatsApp\WhatsApp::class);
                    $phoneNumberID = Setting::where('option_name', 'whatsapp_phone_number_id')->first()->option_value ?? env('WHATSAPP_PHONE_NUMBER_ID');
                    try {
                        $data = ['text' => ['body' => $description]];
                        $response = $whatsapp->post("/$phoneNumberID/messages", json_encode($data), [
                            "Authorization" => "Bearer {$accessToken}",
                            "Content-Type" => "application/json"
                        ]);
                        Log::info("WhatsApp response: " . json_encode($response));
                        $post->is_posted_to_whatsapp = true;
                    } catch (\Exception $e) {
                        Log::error("Error posting Post ID {$post->id} to WhatsApp: " . $e->getMessage());
                    }
                }

                // Save the updated post status
                $post->save();

                // Flag successful posts
                if ($post->is_posted_to_twitter || $post->is_posted_to_facebook || $post->is_posted_to_whatsapp || $post->is_posted_to_instagram || $post->is_posted_to_linkedin || $post->is_posted_to_tiktok) {
                    $isAnyPostSuccessful = true;
                    Log::info("Post ID: {$post->id} status updated for Twitter/Facebook/Whatsapp/Linkedlin/Tiktok/Instagram.");
                }

                if ($post->is_posted_to_twitter && $post->is_posted_to_facebook && $post->is_posted_to_whatsapp && $post->is_posted_to_instagram && $post->is_posted_to_linkedin && $post->is_posted_to_tiktok) {
                    $post->is_posted = true;
                    $post->save();
                    Log::info("Post ID: {$post->id} status updated for all social media platforms.");
                }
            }

            if ($isAnyPostSuccessful) {
                foreach ($users as $user) {
                    Notification::make()
                        ->title('Scheduled posts successfully posted')
                        ->body('Some or all scheduled posts were successfully posted to social media.')
                        ->success()
                        ->sendToDatabase($user);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error posting to social media: ' . $e->getMessage());

            foreach ($users as $user) {
                Notification::make()
                    ->title('Error posting to social media')
                    ->body('An error occurred while posting to social media. Please check the logs.')
                    ->danger()
                    ->sendToDatabase($user);
            }
        }
    }
}
