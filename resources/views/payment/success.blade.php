@extends('layouts.app')

@section('title', 'Payment Successful')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-check-circle"></i> {{ __('Payment Successful') }}
                    </div>

                    <div class="card-body text-center">
                        <div class="mb-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        </div>

                        <h3 class="mb-3">Thank you for your payment!</h3>
                        <p class="lead">Your payment has been processed successfully.</p>

                        @if (session('payment'))
                            <div class="alert alert-info">
                                <strong>Payment Details:</strong><br>
                                Amount: ${{ number_format(session('payment.amount') / 100, 2) }}<br>
                                Currency: {{ strtoupper(session('payment.currency')) }}<br>
                                Status: {{ ucfirst(session('payment.status')) }}<br>
                                Payment ID: {{ session('payment.stripe_payment_intent_id') }}
                            </div>
                        @endif

                        <div class="mt-4">
                            <a href="{{ route('home') }}" class="btn btn-primary">
                                <i class="fas fa-home"></i> Return to Home
                            </a>
                            <a href="{{ route('books.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-book"></i> Browse Books
                            </a>
                        </div>

                        <div class="mt-4">
                            <small class="text-muted">
                                You will receive a confirmation email shortly.<br>
                                If you have any questions, please contact our support team.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
