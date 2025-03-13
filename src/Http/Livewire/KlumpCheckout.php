<?php

namespace Klump\LaravelKlump\Http\Livewire;

use Livewire\Component;
use Klump\LaravelKlump\Services\KlumpService;

class KlumpCheckout extends Component
{
    public $order;
    public $items;
    public $customer;

    public function mount($order, $items, $customer)
    {
        $this->order = $order;
        $this->items = $items;
        $this->customer = $customer;
    }

    public function checkout()
    {
        $klump = app(KlumpService::class);
        $payload = $klump->formatPayload($this->order, $this->items, $this->customer);
        
        $this->dispatchBrowserEvent('klump-checkout', [
            'payload' => $payload
        ]);
    }

    public function render()
    {
        return view('klump::livewire.checkout');
    }
}
