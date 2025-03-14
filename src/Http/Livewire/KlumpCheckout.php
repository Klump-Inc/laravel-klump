<?php

namespace Klump\LaravelKlump\Http\Livewire;

use Livewire\Component;
use Klump\LaravelKlump\Services\KlumpService;

class KlumpCheckout extends Component
{
    public $order;
    public $items;
    public $customer;
    public $meta_data = [];

    public function mount($order, $items, $customer, $meta_data = [])
    {
        $this->order = $order;
        $this->items = $items;
        $this->customer = $customer;
        $this->meta_data = $meta_data;
    }

    public function checkout()
    {
        try {
            $klump = app(KlumpService::class);
            $payload = $klump->formatPayload($this->order, $this->items, $this->customer, $this->meta_data);
            
            $this->dispatch('klump-checkout', ['payload' => $payload]);
        } catch (\Exception $e) {
            // Log the error or handle it appropriately
            $this->dispatch('klump-error', ['message' => $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('klump::livewire.checkout');
    }
}
