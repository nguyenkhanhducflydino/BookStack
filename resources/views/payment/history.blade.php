@extends('layouts.app')

@section('title', 'Payment History')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('Payment History') }}</h5>
                        <a href="{{ route('payment.index') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> New Payment
                        </a>
                    </div>

                    <div class="card-body">
                        @if ($payments->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Currency</th>
                                            <th>Status</th>
                                            <th>Payment ID</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($payments as $payment)
                                            <tr>
                                                <td>{{ $payment->created_at->format('M d, Y H:i') }}</td>
                                                <td>${{ number_format($payment->amount / 100, 2) }}</td>
                                                <td>{{ strtoupper($payment->currency) }}</td>
                                                <td>
                                                    <span
                                                        class="badge badge-{{ $payment->status === 'succeeded' ? 'success' : ($payment->status === 'failed' ? 'danger' : 'warning') }}">
                                                        {{ ucfirst($payment->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <small
                                                        class="text-muted">{{ $payment->stripe_payment_intent_id }}</small>
                                                </td>
                                                <td>{{ $payment->description ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-center">
                                {{ $payments->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-credit-card text-muted" style="font-size: 3rem;"></i>
                                <h4 class="mt-3">No payments found</h4>
                                <p class="text-muted">You haven't made any payments yet.</p>
                                <a href="{{ route('payment.index') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Make Your First Payment
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
