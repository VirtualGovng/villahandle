<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Subscription;
use App\Models\ActivityLog; // Import the new ActivityLog model

class PaymentService
{
    protected array $config;
    protected string $flutterwaveApiBaseUrl = 'https://api.flutterwave.com/v3';
    protected string $payPalApiBaseUrl;

    public function __construct()
    {
        $this->config = require CONFIG_PATH . '/app.php';
        $this->payPalApiBaseUrl = ($this->config['services']['paypal']['mode'] === 'live')
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }

    // --- cURL API Request Helpers ---

    private function makeFlutterwaveRequest(string $method, string $endpoint, ?array $data = null): ?array
    {
        $secretKey = $this->config['services']['flutterwave']['secret_key'];
        if (empty($secretKey)) { return null; }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->flutterwaveApiBaseUrl . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $secretKey, 'Content-Type: application/json']);
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ($httpCode >= 200 && $httpCode < 300) ? json_decode($response, true) : null;
    }

    private function getPayPalAccessToken(): ?string
    {
        $paypalConfig = $this->config['services']['paypal'];
        if (empty($paypalConfig['client_id']) || empty($paypalConfig['client_secret'])) { return null; }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->payPalApiBaseUrl . '/v1/oauth2/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $paypalConfig['client_id'] . ':' . $paypalConfig['client_secret']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json', 'Accept-Language: en_US']);
        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response, true);
        return $data['access_token'] ?? null;
    }

    private function makePayPalRequest(string $method, string $endpoint, ?array $data = null): ?array
    {
        $accessToken = $this->getPayPalAccessToken();
        if (!$accessToken) { return null; }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->payPalApiBaseUrl . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ]);
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ($httpCode >= 200 && $httpCode < 300) ? json_decode($response, true) : null;
    }

    // --- Payment Initiation Methods ---

    public function initiateFlutterwavePayment(array $user, array $plan): ?string
    {
        $txRef = 'VS-' . uniqid();
        (new Transaction())->createPending($user['id'], $plan['id'], $plan['price'], $plan['currency'], 'Flutterwave', $txRef);
        $payload = [
            'tx_ref' => $txRef, 'amount' => $plan['price'], 'currency' => $plan['currency'],
            'redirect_url' => env('APP_URL') . '/payment/callback?gateway=flutterwave',
            'customer' => ['email' => $user['email'], 'name' => $user['username']],
            'meta' => ['user_id' => $user['id'], 'plan_id' => $plan['id']]
        ];
        $response = $this->makeFlutterwaveRequest('POST', '/payments', $payload);
        return $response['data']['link'] ?? null;
    }

    public function initiatePayPalPayment(array $user, array $plan): ?string
    {
        $localTxId = (new Transaction())->createPending($user['id'], $plan['id'], $plan['price'], $plan['currency'], 'PayPal', 'TEMP-' . uniqid());
        if (!$localTxId) return null;
        $payload = [
            "intent" => "CAPTURE",
            "purchase_units" => [[
                "reference_id" => $localTxId,
                "amount" => ["value" => $plan['price'], "currency_code" => $plan['currency']],
            ]],
            "application_context" => [
                "cancel_url" => env('APP_URL') . '/payment/cancel',
                "return_url" => env('APP_URL') . '/payment/callback?gateway=paypal'
            ]
        ];
        $response = $this->makePayPalRequest('POST', '/v2/checkout/orders', $payload);
        if (isset($response['id'])) {
            (new Transaction())->updateGatewayId($localTxId, $response['id']);
            foreach ($response['links'] as $link) {
                if ($link['rel'] === 'approve') return $link['href'];
            }
        }
        return null;
    }

    // --- Payment Verification Methods ---

    public function verifyFlutterwavePayment(string $transactionId): bool
    {
        $response = $this->makeFlutterwaveRequest('GET', '/transactions/' . $transactionId . '/verify');
        if ($response && $response['status'] === 'success' && $response['data']['status'] === 'successful') {
            $txRef = $response['data']['tx_ref'];
            $planId = $response['data']['meta']['plan_id'] ?? null;
            $userId = $response['data']['meta']['user_id'] ?? null;
            if (!$userId || !$planId) return false;
            
            (new Transaction())->updateStatus($txRef, 'completed');
            $plan = (new Subscription())->getPlanById($planId);
            
            if ($plan) {
                (new Subscription())->createOrUpdate($userId, $planId, $plan['duration_days'], 'Flutterwave', $transactionId);
                // Log the activity
                $logDesc = "Subscription payment of \${$response['data']['amount']} received from user ID {$userId}.";
                (new ActivityLog())->create('payment.success', $logDesc, $userId);
                return true;
            }
        }
        return false;
    }

    public function verifyPayPalPayment(string $token): bool
    {
        $response = $this->makePayPalRequest('POST', "/v2/checkout/orders/{$token}/capture", []);
        if ($response && isset($response['status']) && $response['status'] === 'COMPLETED') {
            $orderId = $response['id'];
            $localTxId = $response['purchase_units'][0]['reference_id'];
            $transaction = (new Transaction())->findById($localTxId);
            if (!$transaction) return false;
            
            (new Transaction())->updateStatusByGatewayId($orderId, 'completed');
            $plan = (new Subscription())->getPlanById($transaction['plan_id']);

            if ($plan) {
                (new Subscription())->createOrUpdate($transaction['user_id'], $plan['id'], $plan['duration_days'], 'PayPal', $orderId);
                // Log the activity
                $logDesc = "Subscription payment of \${$transaction['amount']} received via PayPal from user ID {$transaction['user_id']}.";
                (new ActivityLog())->create('payment.success', $logDesc, $transaction['user_id']);
                return true;
            }
        }
        return false;
    }

    public function handleBankTransfer(array $user, array $plan): bool
    {
        $txRef = 'VS-BANK-' . uniqid();
        return (new Transaction())->createPending($user['id'], $plan['id'], $plan['price'], $plan['currency'], 'Bank Transfer', $txRef) !== null;
    }
}