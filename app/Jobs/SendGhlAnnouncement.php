<?php

namespace App\Jobs;

use App\Models\Announcement;
use App\Models\GhlAuth;
use App\Models\GhlUser2;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendGhlAnnouncement implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $announcement;

    public function __construct(Announcement $announcement)
    {
        $this->announcement = $announcement;
    }

    public function handle()
    {
        if (empty($this->announcement->locations)) return;

        foreach ($this->announcement->locations as $target) {
            $ghlUserId  = $target['ghl_user_id'];
            $locationId = $target['location_id'];

            $auth = GhlAuth::where('location_id', $locationId)
                ->where('user_type', 'Location')
                ->first();

            if (!$auth) continue;

            try {
                $client = new Client();
                $client->post("https://services.leadconnectorhq.com/conversations/messages", [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $auth->access_token,
                        'Version'       => '2021-07-28',
                        'Accept'        => 'application/json',
                    ],
                    'json' => [
                        'contactId' => $ghlUserId,
                        'type'      => 'IN_APP',
                        'title'     => $this->announcement->title,
                        'message'   => $this->announcement->body,
                    ],
                ]);
            } catch (\Exception $e) {
                Log::error("GHL announcement send failed", [
                    'user_id' => $ghlUserId,
                    'location_id' => $locationId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->announcement->status = 'sent';
        $this->announcement->save();
    }
}
