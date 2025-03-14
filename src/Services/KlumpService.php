<?php

namespace Klump\LaravelKlump\Services;

class KlumpService
{
    protected $publicKey;
    protected $secretKey;
    protected $testMode;

    public function __construct()
    {
        $this->publicKey = config('klump.public_key');
        $this->secretKey = config('klump.secret_key');
        $this->testMode = config('klump.test_mode', false);
    }

    public function verifyWebhookSignature($payload, $signature)
    {
        $expectedSignature = hash_hmac('sha512', $payload, $this->secretKey);
        return hash_equals($expectedSignature, $signature);
    }

    public function formatPayload($order, $items, $customer, $meta_data = [])
    {
        $amount = $order['total'] ?? $order['amount'] ?? 0;
        $reference = $order['reference'] ?? 'ORD-' . uniqid();

        return [
            'publicKey' => $this->publicKey,
            'data' => [
                'amount' => $amount,
                'currency' => $order['currency'] ?? 'NGN',
                'email' => $customer['email'],
                'merchant_reference' => $reference,
                'shipping_fee' => $order['shipping_fee'] ?? 0,
                'redirect_url' => $order['redirect_url'] ?? null,
                'items' => $items,
                'meta_data' => [
                    'order_id' => $order['id'],
                    'custom_fields' => $meta_data,
                    'klump_plugin_source' => 'laravel',
                    'klump_plugin_version' => '1.0.0'
                ]
            ]
        ];
    }
}
