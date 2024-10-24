<?php

namespace App\Service\User;

use App\Repository\User\FirebaseRepo;
use Google\Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseService
{

    private FirebaseRepo $firebaseRepo;
    protected Messaging $messaging;

    protected array $errors = [
        "Requested entity was not found.", "The registration token is not a valid FCM registration token"
    ];

    /**
     * @param FirebaseRepo $firebaseRepo
     */
    public function __construct(FirebaseRepo $firebaseRepo)
    {
        $this->firebaseRepo = $firebaseRepo;
        $this->messaging = $this->getMessaging();

    }

    public function saveToken($token)
    {
        return $this->firebaseRepo->save($token);
    }

    /**
     * @return Messaging
     */

    private function getMessaging(): \Kreait\Firebase\Contract\Messaging
    {
        $credentialsFilePath = public_path('cea-service-file.json');
        $projectId = env('VITE_FIREBASE_PROJECT_ID');

        $factory = (new Factory)
            ->withServiceAccount($credentialsFilePath)
            ->withProjectId($projectId);

        return $factory->createMessaging();
    }


    /**
     * @param $data
     * @return JsonResponse
     */
    public function sendFcmNotification($data): \Illuminate\Http\JsonResponse
    {

        $fcmToken = $data['receiver_id'];
        $title = $data['title'];
        $description = $data['description'];

        $message = CloudMessage::withTarget('token', $fcmToken)
            ->withNotification(Notification::create($title, $description,
                asset("/images/cea_logo_400.png")));
        try {
            $this->messaging->send($message);
            return response()->json(['message' => 'Notification sent successfully.'], 200);
        } catch (\Exception $e) {
            $this->invalidateFcm($e->getMessage(), $fcmToken);
            Log::error('Failed to send FCM message: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to send notification.'], 500);
        } catch (MessagingException|FirebaseException $e) {
            Log::error('Failed to send FCM message: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to send notification.'], 500);
        }
    }

    /**
     * @param $data
     * @return JsonResponse
     */
    public function sendNotificationToMultiple($data): \Illuminate\Http\JsonResponse
    {

        $fcmToken = $data['receiver_id'];
        $title = $data['title'];
        $description = $data['description'];

        $message = CloudMessage::new()
            ->withNotification(Notification::create($title, $description,
                asset("/images/cea_logo_400.png")));

        try {
            $report = $this->messaging->sendMulticast($message, $fcmToken);
            if ($report->hasFailures()) {
                $failedFcms = $report->failures();
                foreach ($failedFcms->getItems() as $failure) {
                    $failedTokens[] = $failure->target()->value();
                }
                $this->firebaseRepo->invalidateFcmTokens($failedTokens);
            }
            return response()->json(['message' => 'Notification sent successfully.'], 200);

        } catch (\Exception $e) {
            Log::error('Failed to send FCM message: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to send notification.'], 500);
        } catch (MessagingException|FirebaseException $e) {
            Log::error('Failed to send FCM message: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to send notification.'], 500);
        }
    }


    protected function invalidateFcm($error, $token): void
    {
        if (in_array($error, $this->errors)) {
            $this->firebaseRepo->invalidateFcm($token);
        }
    }
}

