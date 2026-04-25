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
        if (str_starts_with($cleaned, '08')) {
            return '628' . substr($cleaned, 2);
        }
        return $cleaned;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $leads = DB::table('leads')->whereIn('id', $this->leadIds)->get();
        if ($leads->isEmpty()) {
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
            }
        }

        $phoneList = array_keys($allNormalizedNumbers);
        $chunks = array_chunk($phoneList, 500);

        foreach ($chunks as $batch) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => $this->deviceToken
                ])->asForm()->post('https://api.fonnte.com/validate', [
                    'target' => implode(',', $batch),
                    'countryCode' => '0'
                ]);

                $valResult = $response->json();
                if ($valResult['status'] ?? false) {
                    $registeredNumbers = array_merge($registeredNumbers, $valResult['registered'] ?? []);
                }
            } catch (\Exception $e) {
                Log::error("Broadcast Validation Error: " . $e->getMessage());
            }
        }

        $sentCount = 0;
        $batchLimit = 20;

        foreach ($leads as $lead) {
            if (!$lead->phone) continue;

            $phone = $this->normalizePhone($lead->phone);
            if (!$phone || !in_array($phone, $registeredNumbers)) {
                continue;
            }

            // 2. Random Delay Logic
            if ($sentCount > 0) {
                // Batch rest: after every 20 messages
                if ($sentCount % $batchLimit == 0) {
                    sleep(900); // 15 minutes rest
                } else {
                    $sleepTime = rand($this->delayMin, $this->delayMax);
                    sleep($sleepTime);
                }
            }

            // 3. Personalize Message
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

            // 4. Send to Fonnte
            try {
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
                    }
                }
            } catch (\Exception $e) {
                Log::error("Broadcast Send Error for $phone: " . $e->getMessage());
            }
        }
    }
}
