<?php

namespace Klump\LaravelKlump\Services;

class KlumpService
{
    protected $publicKey;
    protected $secretKey;
    protected $testMode;

    public function __construct($publicKey, $secretKey, $testMode = false)
    {
        $this->publicKey = $publicKey;
        $this->secretKey = $secretKey;
        $this->testMode = $testMode;
    }

    public function verifyWebhookSignature($payload, $signature)
    {
        $expectedSignature = hash_hmac('sha512', $payload, $this->secretKey);
        return hash_equals($expectedSignature, $signature);
    }

    public function formatPayload($order, $items, $customer)
    {
        return [
            'publicKey' => $this->publicKey,
            'data' => [
                'amount' => $order['total'],
                'currency' => $order['currency'],
                'email' => $customer['email'],
                'merchant_reference' => $order['reference'],
                'shipping_fee' => $order['shipping_fee'] ?? 0,
                'redirect_url' => $order['redirect_url'],
                'items' => $items,
                'meta_data' => [
                    'order_id' => $order['id'],
                    'custom_fields' => [
                        [
                            'display_name' => 'Order ID',
                            'variable_name' => 'order_id',
                            'value' => $order['id']
                        ]
                    ],
                    'klump_plugin_source' => 'laravel',
                    'klump_plugin_version' => '1.0.0'
                ]
            ]
        ];
    }
}
