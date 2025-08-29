<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\PaymentService;
use App\Models\Subscription;

class PaymentController
{
    protected PaymentService $paymentService;
    protected Subscription $subscriptionModel;

    public function __construct()
    {
        // All payment routes require the user to be logged in.
        if (!AuthService::check()) {
            $_SESSION['error_message'] = 'You must be logged in to subscribe.';
            redirect('/login');
        }
        $this->paymentService = new PaymentService();
        $this->subscriptionModel = new Subscription();
    }

    /**
     * Display the subscription plans page.
     */
    public function plans()
    {
        $plans = $this->subscriptionModel->getActivePlans();
        $data = ['title' => 'Choose a Plan - VillaStudio', 'plans' => $plans];
        return view('pages.subscribe', $data);
    }

    /**
     * Handle the form submission from the plans page and initiate payment.
     */
    public function initiate()
    {
        $planId = filter_input(INPUT_POST, 'plan_id', FILTER_VALIDATE_INT);
        $paymentMethod = $_POST['payment_method'] ?? '';
        $user = AuthService::user();
        
        if (!$planId || !$paymentMethod) {
            $_SESSION['error_message'] = 'Invalid selection. Please try again.';
            redirect('/subscribe');
        }

        $plan = $this->subscriptionModel->getPlanById($planId);
        if (!$plan) {
            $_SESSION['error_message'] = 'The selected plan does not exist.';
            redirect('/subscribe');
        }

        $paymentLink = null;
        switch ($paymentMethod) {
            case 'flutterwave':
                $paymentLink = $this->paymentService->initiateFlutterwavePayment($user, $plan);
                if ($paymentLink) {
                    redirect($paymentLink);
                }
                break;
            case 'paypal':
                $paymentLink = $this->paymentService->initiatePayPalPayment($user, $plan);
                if ($paymentLink) {
                    redirect($paymentLink);
                }
                // The service sets a specific session message if it fails
                redirect('/subscribe');
                break;
            case 'bank_transfer':
                if ($this->paymentService->handleBankTransfer($user, $plan)) {
                    $_SESSION['success_message'] = 'Your bank transfer request has been received. Please follow the instructions to complete payment.';
                    redirect('/payment/success');
                }
                break;
            default:
                $_SESSION['error_message'] = 'Invalid payment method selected.';
                redirect('/subscribe');
                break;
        }

        // This is a fallback error message if a payment link wasn't generated.
        if (!isset($_SESSION['error_message'])) {
            $_SESSION['error_message'] = 'Could not initiate payment. Please try again or contact support.';
        }
        redirect('/subscribe');
    }
    
    /**
     * Handle the callback from payment gateways after user interaction.
     */
    public function callback()
    {
        $gateway = $_GET['gateway'] ?? '';
        
        if ($gateway === 'flutterwave') {
            $transactionId = $_GET['transaction_id'] ?? '';
            if ($transactionId && $this->paymentService->verifyFlutterwavePayment($transactionId)) {
                $_SESSION['success_message'] = 'Your payment was successful and your subscription is now active!';
                redirect('/payment/success');
            }
        }
        
        if ($gateway === 'paypal') {
            $token = $_GET['token'] ?? ''; // PayPal returns the Order ID in the 'token' parameter
            if ($token && $this->paymentService->verifyPayPalPayment($token)) {
                 $_SESSION['success_message'] = 'Your payment via PayPal was successful and your subscription is now active!';
                redirect('/payment/success');
            }
        }
        
        // If verification fails for any reason, redirect to the cancel page.
        if (!isset($_SESSION['error_message'])) {
            $_SESSION['error_message'] = 'There was an issue verifying your payment. Please contact support if you have been charged.';
        }
        redirect('/payment/cancel');
    }
    
    /**
     * Display the payment success page.
     */
    public function success()
    {
        $data = ['title' => 'Payment Successful - VillaStudio'];
        return view('pages.payment_success', $data);
    }

    /**
     * Display the payment cancelled/failed page.
     */
    public function cancel()
    {
        $data = ['title' => 'Payment Cancelled - VillaStudio'];
        return view('pages.payment_cancel', $data);
    }
}