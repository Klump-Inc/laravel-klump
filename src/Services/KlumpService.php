<?php

namespace Klump\LaravelKlump\Services;

class KlumpService
{
    protected $publicKey;
    protected $secretKey;

    public function __construct()
    {
        $this->publicKey = config('klump.public_key');
        $this->secretKey = config('klump.secret_key');
    }

    public function verifyWebhookSignature($payload, $signature)
    {
        $expectedSignature = hash_hmac('sha512', $payload, $this->secretKey);
        return hash_equals($expectedSignature, $signature);
    }

    public function formatPayload($order, $items, $customer, $meta_data = [])
    {
        // Validate and extract order data
        $amount = $order['total'] ?? $order['amount'] ?? 0;
        $reference = $order['reference'] ?? 'ORD-' . uniqid();
        $currency = $order['currency'] ?? 'NGN';
        
        // Validate redirect URL if provided
        $redirectUrl = null;
        if (isset($order['redirect_url']) && filter_var($order['redirect_url'], FILTER_VALIDATE_URL)) {
            $redirectUrl = $order['redirect_url'];
        }
        
        // Format items to ensure they have unit_price
        $formattedItems = [];
        foreach ($items as $item) {
            $formattedItem = $item;
            if (!isset($formattedItem['unit_price']) && isset($formattedItem['price'])) {
                $formattedItem['unit_price'] = $formattedItem['price'];
            }
            $formattedItems[] = $formattedItem;
        }
        
        // Format customer data
        $customerData = [
            'email' => $customer['email'] ?? '',
        ];
        
        if (isset($customer['name'])) {
            $nameParts = explode(' ', $customer['name'], 2);
            $customerData['first_name'] = $nameParts[0] ?? '';
            $customerData['last_name'] = $nameParts[1] ?? '';
        } else {
            $customerData['first_name'] = $customer['first_name'] ?? '';
            $customerData['last_name'] = $customer['last_name'] ?? '';
        }
        
        if (isset($customer['phone'])) {
            $customerData['phone'] = $customer['phone'];
        }

        return [
            'publicKey' => $this->publicKey,
            'data' => array_merge([
                'amount' => $amount,
                'currency' => $currency,
                'merchant_reference' => $reference,
                'shipping_fee' => $order['shipping_fee'] ?? 0,
                'redirect_url' => $redirectUrl,
                'items' => $formattedItems,
                'meta_data' => [
                    'order_id' => $order['id'] ?? '',
                    'custom_fields' => $meta_data,
                    'klump_plugin_source' => 'laravel',
                    'klump_plugin_version' => '1.0.0'
                ]
            ], $customerData)
        ];
    }
}
