<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BroadcastMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 0; // Unlimited

    protected $leadIds;
    protected $messageContent;
    protected $deviceToken;
    protected $userId;
    protected $delayMin;
    protected $delayMax;

    /**
     * Create a new job instance.
     */
    public function __construct(array $leadIds, string $messageContent, string $deviceToken, int $userId, int $delayMin = 30, int $delayMax = 60)
    {
        $this->leadIds = $leadIds;
        $this->messageContent = $messageContent;
        $this->deviceToken = $deviceToken;
        $this->userId = $userId;
        $this->delayMin = $delayMin;
        $this->delayMax = $delayMax;
    }

    /**
     * Normalize phone number
     */
    private function normalizePhone($phone)
    {
        $cleaned = preg_replace('/[^0-9]/', '', $phone);
        
        // If starts with 0, replace with 62 (handles 08... and 021... etc)
        if (str_starts_with($cleaned, '0')) {
            return '62' . substr($cleaned, 1);
        }
        
        // If starts with 8, add 62
        if (str_starts_with($cleaned, '8')) {
            return '62' . $cleaned;
        }
        
        return $cleaned;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        \Illuminate\Support\Facades\Cache::put('broadcast_running_' . $this->userId, true, now()->addHours(2));
        Log::info("Broadcast started for User ID: " . $this->userId . " with " . count($this->leadIds) . " leads.");

        try {
            $leads = DB::table('leads')->whereIn('id', $this->leadIds)->get();
            if ($leads->isEmpty()) {
                Log::warning("Broadcast aborted: No leads found in database for the provided IDs.");
                return;
            }

            // 1. Number Validation Step
            $registeredNumbers = [];
            $allNormalizedNumbers = [];
            foreach ($leads as $lead) {
                if ($lead->phone) {
                    $normalized = $this->normalizePhone($lead->phone);
                    if ($normalized) {
                        $allNormalizedNumbers[$normalized] = $lead->id;
                    }
                } else {
                    Log::info("Lead skipped: " . ($lead->name ?? 'Unknown') . " has no phone number.");
                }
            }

            $phoneList = array_keys($allNormalizedNumbers);
            Log::info("Validating " . count($phoneList) . " phone numbers via Fonnte.");
            
            $sentCount = 0;
            $batchLimit = 20;

            foreach ($leads as $lead) {
                // 0. Check for Stop Signal
                if (\Illuminate\Support\Facades\Cache::has('stop_broadcast_' . $this->userId)) {
                    Log::info("Broadcast Stopped by User: " . $this->userId);
                    \Illuminate\Support\Facades\Cache::forget('stop_broadcast_' . $this->userId);
                    return;
                }

                if (!$lead->phone) {
                    Log::info("Skipping lead: " . ($lead->name ?? 'Unknown') . " - Reason: No phone number.");
                    continue;
                }

                $phone = $this->normalizePhone($lead->phone);
                
                // 1. Random Delay Logic
                if ($sentCount > 0) {
                    // Batch rest: after every 20 messages
                    if ($sentCount % $batchLimit == 0) {
                        Log::info("Batch limit reached ($sentCount). Resting for 15 minutes to keep account safe...");
                        // Sleep in 10-second increments to stay responsive to Stop signal
                        for ($i = 0; $i < 90; $i++) {
                            sleep(10);
                            if (\Illuminate\Support\Facades\Cache::has('stop_broadcast_' . $this->userId)) {
                                Log::info("Broadcast Stopped by User (Detected during 15m rest): " . $this->userId);
                                \Illuminate\Support\Facades\Cache::forget('stop_broadcast_' . $this->userId);
                                return;
                            }
                        }
                    } else {
                        $sleepTime = rand($this->delayMin, $this->delayMax);
                        Log::info("Sleeping for $sleepTime seconds before sending to $phone...");
                        sleep($sleepTime);
                    }

                    // Check Stop Signal AGAIN after sleep
                    if (\Illuminate\Support\Facades\Cache::has('stop_broadcast_' . $this->userId)) {
                        Log::info("Broadcast Stopped by User (Detected after sleep): " . $this->userId);
                        \Illuminate\Support\Facades\Cache::forget('stop_broadcast_' . $this->userId);
                        return;
                    }
                }

                // 2. Personalize Message
                $personalizedMessage = $this->messageContent;
                $placeholders = [
                    '{{name}}' => $lead->name ?? '',
                    '{{address}}' => $lead->address ?? '',
                    '{{phone}}' => $lead->phone ?? '',
                    '{{category}}' => $lead->category ?? '',
                ];

                foreach ($placeholders as $placeholder => $value) {
                    $personalizedMessage = str_replace($placeholder, $value, $personalizedMessage);
                }

                // 3. Send to Fonnte
                try {
                    Log::info("Attempting to send message to: $phone (" . $lead->name . ")");
                    $sendResponse = Http::withHeaders([
                        'Authorization' => $this->deviceToken
                    ])->asForm()->post('https://api.fonnte.com/send', [
                        'target' => $phone,
                        'message' => $personalizedMessage,
                        'countryCode' => '0',
                        'delay' => 3
                    ]);

                    $result = $sendResponse->json();
                    if ($result['status'] ?? false) {
                        $messageId = $result['id'][0] ?? $result['id'] ?? null;
                        if ($messageId) {
                            DB::table('message_histories')->insert([
                                'id' => (string) $messageId,
                                'user_id' => $this->userId,
                                'target' => $phone,
                                'message' => $personalizedMessage,
                                'status' => $result['process'] ?? 'processing',
                                'created_at' => now()
                            ]);
                            $sentCount++;
                            Log::info("Message successfully sent to $phone. Total sent so far: $sentCount");
                        }
                    } else {
                        $reason = $result['reason'] ?? 'Unknown reason';
                        Log::error("Fonnte Send Failed for $phone: $reason. Continuing to next lead...");
                    }
                } catch (\Exception $e) {
                    Log::error("Critical Error sending to $phone: " . $e->getMessage() . ". Continuing to next lead...");
                }
            }
            Log::info("Broadcast completed. Total messages sent: $sentCount");
        } finally {
            \Illuminate\Support\Facades\Cache::forget('broadcast_running_' . $this->userId);
        }
    }
}
