@extends('layouts.app')

@section('title', 'Payment')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Payment Options') }}</div>

                    <div class="card-body">
                        <div class="row">
                            <!-- Basic Plan -->
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title">Basic Plan</h5>
                                        <h6 class="card-subtitle mb-2 text-muted">$9.99/month</h6>
                                        <ul class="list-unstyled">
                                            <li>✓ Access to all books</li>
                                            <li>✓ Basic search functionality</li>
                                            <li>✓ Mobile access</li>
                                        </ul>
                                        <button class="btn btn-primary mt-auto"
                                            onclick="createPaymentIntent(999, 'usd', 'Basic Plan')">
                                            Subscribe Now
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Premium Plan -->
                            <div class="col-md-4 mb-4">
                                <div class="card h-100 border-primary">
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title">Premium Plan</h5>
                                        <h6 class="card-subtitle mb-2 text-muted">$19.99/month</h6>
                                        <ul class="list-unstyled">
                                            <li>✓ Everything in Basic</li>
                                            <li>✓ Advanced search</li>
                                            <li>✓ Download capabilities</li>
                                            <li>✓ Priority support</li>
                                        </ul>
                                        <button class="btn btn-primary mt-auto"
                                            onclick="createPaymentIntent(1999, 'usd', 'Premium Plan')">
                                            Subscribe Now
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Pro Plan -->
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title">Pro Plan</h5>
                                        <h6 class="card-subtitle mb-2 text-muted">$39.99/month</h6>
                                        <ul class="list-unstyled">
                                            <li>✓ Everything in Premium</li>
                                            <li>✓ Team collaboration</li>
                                            <li>✓ Custom integrations</li>
                                            <li>✓ White-label options</li>
                                        </ul>
                                        <button class="btn btn-primary mt-auto"
                                            onclick="createPaymentIntent(3999, 'usd', 'Pro Plan')">
                                            Subscribe Now
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Form (hidden by default) -->
                        <div id="payment-form-container" style="display: none;">
                            <hr>
                            <h5>Complete Your Payment</h5>
                            <form id="payment-form">
                                @csrf
                                <div id="card-element">
                                    <!-- Stripe Elements will create form elements here -->
                                </div>
                                <div id="card-errors" role="alert" class="text-danger mt-2"></div>
                                <button id="submit-payment" class="btn btn-success mt-3">
                                    <span id="button-text">Pay Now</span>
                                    <span id="spinner" class="spinner-border spinner-border-sm d-none"
                                        role="status"></span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stripe JS -->
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        // Initialize Stripe
        const stripe = Stripe('{{ config('stripe.publishable_key') }}');
        const elements = stripe.elements();

        // Create card element
        const cardElement = elements.create('card', {
            style: {
                base: {
                    fontSize: '16px',
                    color: '#424770',
                    '::placeholder': {
                        color: '#aab7c4',
                    },
                },
            },
        });

        let currentClientSecret = null;

        // Mount card element
        cardElement.mount('#card-element');

        // Handle real-time validation errors from the card Element
        cardElement.on('change', ({
            error
        }) => {
            const displayError = document.getElementById('card-errors');
            if (error) {
                displayError.textContent = error.message;
            } else {
                displayError.textContent = '';
            }
        });

        // Create payment intent
        async function createPaymentIntent(amount, currency, planName) {
            try {
                const response = await fetch('{{ route('payment.create-intent') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        amount: amount,
                        currency: currency,
                        description: planName
                    })
                });

                const data = await response.json();
                console.log('Response data:', data); // Debug log

                if (data.success) {
                    currentClientSecret = data.client_secret;
                    document.getElementById('payment-form-container').style.display = 'block';
                    document.getElementById('button-text').textContent =
                        `Pay $${(amount/100).toFixed(2)} for ${planName}`;
                } else {
                    console.error('Payment intent error:', data);
                    alert('Error creating payment intent: ' + (data.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Network error:', error);
                alert('Network error occurred. Please check console for details.');
            }
        }

        // Handle form submission
        const form = document.getElementById('payment-form');
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            if (!currentClientSecret) {
                return;
            }

            setLoading(true);

            const {
                error
            } = await stripe.confirmCardPayment(currentClientSecret, {
                payment_method: {
                    card: cardElement,
                }
            });

            if (error) {
                // Show error to your customer
                const errorElement = document.getElementById('card-errors');
                errorElement.textContent = error.message;
                setLoading(false);
            } else {
                // Payment succeeded
                window.location.href = '{{ route('payment.success') }}';
            }
        });

        function setLoading(loading) {
            const submitButton = document.getElementById('submit-payment');
            const buttonText = document.getElementById('button-text');
            const spinner = document.getElementById('spinner');

            if (loading) {
                submitButton.disabled = true;
                buttonText.style.display = 'none';
                spinner.classList.remove('d-none');
            } else {
                submitButton.disabled = false;
                buttonText.style.display = 'inline';
                spinner.classList.add('d-none');
            }
        }
    </script>
@endsection
