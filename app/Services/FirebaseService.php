<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    private $messaging;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(
                storage_path('app/firebase/firebase_credentials.json')
            );

        $this->messaging = $factory->createMessaging();
    }

    public function sendToToken(
        string $token,
        string $title,
        string $body,
        array $data = []
    ) {
        try {

            $data['title'] = $title;
            $data['body'] = $body;

            $message = CloudMessage::new()
                ->withToken($token)
                ->withData($data);

            $result = $this->messaging->send($message);

            Log::info('FCM notification berhasil dikirim', [
                'token' => $token,
                'title' => $title,
                'body' => $body,                    
                'data' => $data,
                'result' => $result,
            ]);
        
            return $result;

        } catch (\Throwable $e) {

            Log::error('FCM notification gagal dikirim', [
                'token' => $token,
                'error' => $e->getMessage(),
            ]);
            

            throw $e;
        }
    }
}