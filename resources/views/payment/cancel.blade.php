@extends('layouts.app')

@section('title', 'Payment Cancelled')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <i class="fas fa-exclamation-triangle"></i> {{ __('Payment Cancelled') }}
                    </div>

                    <div class="card-body text-center">
                        <div class="mb-4">
                            <i class="fas fa-times-circle text-warning" style="font-size: 4rem;"></i>
                        </div>

                        <h3 class="mb-3">Payment was cancelled</h3>
                        <p class="lead">Your payment has been cancelled and no charges were made.</p>

                        <div class="alert alert-info">
                            Don't worry! You can try again anytime or choose a different payment option.
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('payment.index') }}" class="btn btn-primary">
                                <i class="fas fa-credit-card"></i> Try Again
                            </a>
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-home"></i> Return to Home
                            </a>
                        </div>

                        <div class="mt-4">
                            <small class="text-muted">
                                Need help? <a href="#" class="text-decoration-none">Contact our support team</a>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
