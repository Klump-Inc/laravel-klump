# Klump Laravel Payment Package

A Laravel package for integrating Klump's Buy Now Pay Later (BNPL) payment solution into your Laravel applications.

## Requirements

- PHP 8.0 or higher
- Laravel 10.0 or higher
- Livewire 3.0 or higher

## Installation

You can install the package via composer:

```bash
composer require klump/laravel-klump
```

After installing the package, publish the configuration file:

```bash
php artisan vendor:publish --provider="Klump\LaravelKlump\KlumpServiceProvider"
```

## Configuration

After publishing the configuration, you can find the config file at `config/klump.php`. You need to set your Klump API credentials in your `.env` file:

```
KLUMP_PUBLIC_KEY=your_public_key
KLUMP_SECRET_KEY=your_secret_key
KLUMP_ENVIRONMENT=sandbox # or production
```

## Basic Usage

### Setting Up the Checkout Component

The package provides a Livewire component for handling Klump checkout. To use it, you need to include the Klump JavaScript SDK in your layout or view:

```html
<!-- Add this in your layout file or view -->
<script src="https://js.useklump.com/klump.js" defer></script>
```

Then, you can use the Livewire component in your blade files:

```php
<livewire:klump-checkout 
    :order="$order" 
    :items="$items" 
    :customer="$customer" 
/>
```

Where:
- `$order` is an array containing order information (id, amount, currency, etc.)
- `$items` is an array of items in the order
- `$customer` is an array with customer details

### Example Controller

```php
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CheckoutController extends Controller
{
    public function checkout()
    {
        $order = [
            'id' => 'ORD-' . uniqid(),
            'amount' => 50000, // Amount in kobo/cents
            'currency' => 'NGN',
            'redirect_url' => route('payment.success'),
        ];
        
        $items = [
            [
                'name' => 'Product Name',
                'price' => 50000, // Price in kobo/cents
                'quantity' => 1,
                'description' => 'Product description',
            ],
        ];
        
        $customer = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '08012345678',
        ];
        
        return view('checkout', compact('order', 'items', 'customer'));
    }
    
    public function success(Request $request)
    {
        // Handle successful payment
        return view('payment.success');
    }
}
```

### Handling Checkout Events

The Klump checkout component dispatches several events that you can listen for:

- `onSuccess`: Called when payment is successful
- `onError`: Called when payment fails
- `onClose`: Called when the checkout modal is closed

These events are already configured in the Livewire component, but you can customize them by extending the component.

## Advanced Usage

### Custom Checkout Component

If you need to customize the checkout process, you can extend the `KlumpCheckout` component:

```php
<?php

namespace App\Http\Livewire;

use Klump\LaravelKlump\Http\Livewire\KlumpCheckout;

class CustomKlumpCheckout extends KlumpCheckout
{
    public function checkout()
    {
        // Custom logic before checkout
        
        parent::checkout();
        
        // Custom logic after checkout
    }
}
```

Then register your custom component in your `AppServiceProvider`:

```php
Livewire::component('custom-klump-checkout', CustomKlumpCheckout::class);
```

### Webhook Handling

To handle Klump webhooks, you can create a webhook controller:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Klump\LaravelKlump\Services\KlumpService;

class KlumpWebhookController extends Controller
{
    protected $klumpService;
    
    public function __construct(KlumpService $klumpService)
    {
        $this->klumpService = $klumpService;
    }
    
    public function handle(Request $request)
    {
        // Verify webhook signature
        if (!$this->klumpService->verifyWebhook($request)) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }
        
        $payload = $request->all();
        $event = $payload['event'];
        
        // Handle different webhook events
        switch ($event) {
            case 'payment.successful':
                // Handle successful payment
                break;
            case 'payment.failed':
                // Handle failed payment
                break;
            // Add more event handlers as needed
        }
        
        return response()->json(['status' => 'success']);
    }
}
```

Then register the webhook route in your `routes/web.php` or `routes/api.php`:

```php
Route::post('webhooks/klump', [KlumpWebhookController::class, 'handle']);
```

## Testing

```bash
composer test
```

## Security

If you discover any security related issues, please email richienabuk@gmail.com instead of using the issue tracker.

## Credits

- [Imo-owo Nabuk](https://github.com/richienabuk)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.