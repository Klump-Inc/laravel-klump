<?php

namespace Klump\LaravelKlump\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Event;
use Klump\LaravelKlump\Services\KlumpService;
use Klump\LaravelKlump\Events\KlumpPaymentSuccessful;

class WebhookController extends Controller
{
    protected $klumpService;

    public function __construct(KlumpService $klumpService)
    {
        $this->klumpService = $klumpService;
    }

    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Klump-Signature');

        if (!$this->klumpService->verifyWebhookSignature($payload, $signature)) {
            return response()->json(['success' => false, 'message' => 'Invalid signature'], 400);
        }

        $data = json_decode($payload, true);

        if ($data['event'] !== 'klump.payment.transaction.successful') {
            return response()->json(['success' => false, 'message' => 'Invalid event'], 400);
        }

        Event::dispatch(new KlumpPaymentSuccessful($data));

        return response()->json(['success' => true]);
    }
}
