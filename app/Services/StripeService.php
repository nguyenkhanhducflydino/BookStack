<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Customer;
use Stripe\Subscription;
use Stripe\Price;
use Stripe\Product;
use Exception;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('stripe.secret_key'));
    }

    /**
     * Create a payment intent
     */
    public function createPaymentIntent($amount, $currency = null, $metadata = [])
    {
        try {
            return PaymentIntent::create([
                'amount' => $amount, // Amount is already in cents from frontend
                'currency' => $currency ?? 'usd',
                'metadata' => $metadata,
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);
        } catch (Exception $e) {
            throw new Exception('Failed to create payment intent: ' . $e->getMessage());
        }
    }

    /**
     * Create a customer
     */
    public function createCustomer($email, $name = null, $metadata = [])
    {
        try {
            return Customer::create([
                'email' => $email,
                'name' => $name,
                'metadata' => $metadata,
            ]);
        } catch (Exception $e) {
            throw new Exception('Failed to create customer: ' . $e->getMessage());
        }
    }

    /**
     * Get customer by ID
     */
    public function getCustomer($customerId)
    {
        try {
            return Customer::retrieve($customerId);
        } catch (Exception $e) {
            throw new Exception('Failed to retrieve customer: ' . $e->getMessage());
        }
    }

    /**
     * Create a product
     */
    public function createProduct($name, $description = null)
    {
        try {
            return Product::create([
                'name' => $name,
                'description' => $description,
            ]);
        } catch (Exception $e) {
            throw new Exception('Failed to create product: ' . $e->getMessage());
        }
    }

    /**
     * Create a price for a product
     */
    public function createPrice($productId, $unitAmount, $currency = null, $recurring = null)
    {
        try {
            $priceData = [
                'product' => $productId,
                'unit_amount' => $unitAmount * 100, // Convert to cents
                'currency' => $currency ?? config('stripe.currency'),
            ];

            if ($recurring) {
                $priceData['recurring'] = $recurring;
            }

            return Price::create($priceData);
        } catch (Exception $e) {
            throw new Exception('Failed to create price: ' . $e->getMessage());
        }
    }

    /**
     * Create a subscription
     */
    public function createSubscription($customerId, $priceId, $metadata = [])
    {
        try {
            return Subscription::create([
                'customer' => $customerId,
                'items' => [
                    ['price' => $priceId],
                ],
                'metadata' => $metadata,
            ]);
        } catch (Exception $e) {
            throw new Exception('Failed to create subscription: ' . $e->getMessage());
        }
    }

    /**
     * Cancel a subscription
     */
    public function cancelSubscription($subscriptionId)
    {
        try {
            $subscription = Subscription::retrieve($subscriptionId);
            return $subscription->cancel();
        } catch (Exception $e) {
            throw new Exception('Failed to cancel subscription: ' . $e->getMessage());
        }
    }

    /**
     * Retrieve payment intent
     */
    public function getPaymentIntent($paymentIntentId)
    {
        try {
            return PaymentIntent::retrieve($paymentIntentId);
        } catch (Exception $e) {
            throw new Exception('Failed to retrieve payment intent: ' . $e->getMessage());
        }
    }
}
