<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Subscription;

class PaymentService
{
    protected array $config;
    protected string $flutterwaveApiBaseUrl = 'https://api.flutterwave.com/v3';

    public function __construct()
    {
        $this->config = require CONFIG_PATH . '/app.php';
    }

    /**
     * Makes a cURL request to the Flutterwave API.
     *
     * @param string $method The HTTP method (GET, POST).
     * @param string $endpoint The API endpoint (e.g., '/transactions/verify').
     * @param array|null $data The data to send for POST requests.
     * @return array|null The decoded JSON response.
     */
    private function makeFlutterwaveRequest(string $method, string $endpoint, ?array $data = null): ?array
    {
        $secretKey = $this->config['services']['flutterwave']['secret_key'];
        if (empty($secretKey)) {
            error_log('Flutterwave Secret Key is not configured.');
            return null;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->flutterwaveApiBaseUrl . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $secretKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        }
        
        error_log("Flutterwave API Request Failed. Endpoint: {$endpoint}, HTTP Code: {$httpCode}, Response: {$response}");
        return null;
    }

    /**
     * Initiates a payment with Flutterwave using direct cURL API calls.
     */
    public function initiateFlutterwavePayment(array $user, array $plan): ?string
    {
        $transactionModel = new Transaction();
        $txRef = 'VS-' . uniqid();

        $transactionModel->createPending($user['id'], $plan['id'], $plan['price'], $plan['currency'], 'Flutterwave', $txRef);

        $payload = [
            'tx_ref' => $txRef,
            'amount' => $plan['price'],
            'currency' => $plan['currency'],
            'redirect_url' => env('APP_URL') . '/payment/callback?gateway=flutterwave',
            'customer' => [
                'email' => $user['email'],
                'name' => $user['username']
            ],
            'customizations' => [
                'title' => 'VillaStudio Subscription',
                'description' => 'Payment for ' . $plan['name'],
                'logo' => env('APP_URL') . '/public/images/logo.png'
            ],
            'meta' => [
                'user_id' => $user['id'],
                'plan_id' => $plan['id']
            ]
        ];

        $response = $this->makeFlutterwaveRequest('POST', '/payments', $payload);

        return $response['data']['link'] ?? null;
    }

    /**
     * Verifies a Flutterwave payment via callback using a direct cURL API call.
     */
    public function verifyFlutterwavePayment(string $transactionId): bool
    {
        // Flutterwave's callback uses the transaction ID from their system.
        $response = $this->makeFlutterwaveRequest('GET', '/transactions/' . $transactionId . '/verify');

        if ($response && $response['status'] === 'success' && $response['data']['status'] === 'successful') {
            $txRef = $response['data']['tx_ref'];
            $planId = $response['data']['meta']['plan_id'] ?? null;
            $userId = $response['data']['meta']['user_id'] ?? null;

            if (!$userId || !$planId) {
                error_log("Flutterwave Verification Error: Missing metadata for tx_ref {$txRef}");
                return false;
            }
            
            (new Transaction())->updateStatus($txRef, 'completed');
            $plan = (new Subscription())->getPlanById($planId);
            if ($plan) {
                (new Subscription())->createOrUpdate($userId, $planId, $plan['duration_days'], 'Flutterwave', $transactionId);
                return true;
            }
        }
        return false;
    }
    
    public function handleBankTransfer(array $user, array $plan): bool
    {
        $transactionModel = new Transaction();
        $txRef = 'VS-BANK-' . uniqid();
        $transactionId = $transactionModel->createPending($user['id'], $plan['id'], $plan['price'], $plan['currency'], 'Bank Transfer', $txRef);
        return $transactionId !== null;
    }
}