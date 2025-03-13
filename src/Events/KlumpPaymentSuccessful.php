<?php

namespace Klump\LaravelKlump\Events;

class KlumpPaymentSuccessful
{
    public $payload;

    public function __construct($payload)
    {
        $this->payload = $payload;
    }
}
