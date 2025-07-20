<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class PaymentController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
        $this->middleware('auth');
    }

    /**
     * Show payment form
     */
    public function index(): View
    {
        return view('payment.index', [
            'publishableKey' => config('stripe.publishable_key')
        ]);
    }

    /**
     * Create payment intent
     */
    public function createPaymentIntent(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:50', // Minimum 50 cents
            'currency' => 'nullable|string|size:3',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            $paymentIntent = $this->stripeService->createPaymentIntent(
                $request->amount,
                $request->currency ?? 'usd',
                [
                    'user_id' => Auth::id(),
                    'email' => Auth::user()->email,
                    'description' => $request->description,
                ]
            );

            // Save payment record
            Payment::create([
                'user_id' => Auth::id(),
                'stripe_payment_intent_id' => $paymentIntent->id,
                'amount' => $request->amount,
                'currency' => $request->currency ?? 'usd',
                'status' => 'pending',
                'description' => $request->description,
                'metadata' => json_encode([
                    'created_from' => 'web',
                ]),
            ]);

            return response()->json([
                'success' => true,
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle payment success
     */
    public function handleSuccess(Request $request)
    {
        // Redirect to success page with session data
        return view('payment.success')->with([
            'payment' => session('payment_data')
        ]);
    }

    /**
     * Handle payment cancellation
     */
    public function handleCancel(Request $request)
    {
        return view('payment.cancel');
    }

    /**
     * Show payment history
     */
    public function history(): View
    {
        $payments = Payment::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('payment.history', compact('payments'));
    }

    /**
     * Handle Stripe webhooks
     */
    public function webhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $this->handlePaymentIntentSucceeded($paymentIntent);
                break;

            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                $this->handlePaymentIntentFailed($paymentIntent);
                break;

            default:
                Log::info('Received unknown event type: ' . $event->type);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle successful payment intent
     */
    private function handlePaymentIntentSucceeded($paymentIntent): void
    {
        $payment = Payment::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if ($payment) {
            $payment->update([
                'status' => 'succeeded',
                'payment_method' => $paymentIntent->payment_method,
                'paid_at' => now(),
            ]);
        }
    }

    /**
     * Handle failed payment intent
     */
    private function handlePaymentIntentFailed($paymentIntent): void
    {
        $payment = Payment::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if ($payment) {
            $payment->update([
                'status' => 'failed',
            ]);
        }
    }
}
