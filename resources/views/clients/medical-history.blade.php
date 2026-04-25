@extends('layouts.app')
@section('title', 'Medical History')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Medical History</h2>
            <p class="text-muted mb-0">{{ $client->user->name }} - {{ $client->national_id }}</p>
        </div>
        <a href="{{ route('clients.show', $client) }}" class="btn btn-secondary">
            ← Back to Profile
        </a>
    </div>

    <!-- Timeline -->
    <div class="card">
        <div class="card-body">
            @if($bookings->count() > 0)
                @foreach($bookings as $booking)
                <div class="border-start border-3 border-primary ps-3 pb-4 mb-4">
                    <!-- Booking Date -->
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h5 class="mb-1">{{ $booking->booking_date->format('F j, Y') }}</h5>
                            <p class="text-muted mb-0">
                                {{ $booking->booking_time ?? $booking->booking_date->format('g:i A') }} • 
                                Dr. {{ $booking->provider->user->name }} ({{ $booking->provider->specialization }})
                            </p>
                        </div>
                        <span class="badge bg-{{ $booking->status == 'completed' ? 'success' : ($booking->status == 'cancelled' ? 'danger' : 'primary') }}">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </div>

                    <!-- Note -->
                    @if($booking->note)
                    <div class="bg-light p-3 rounded mt-3">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong class="text-primary">Symptoms:</strong>
                                <p class="mb-0">{{ $booking->note->symptoms }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong class="text-success">Note:</strong>
                                <p class="mb-0">{{ $booking->note->note }}</p>
                            </div>
                            @if($booking->note->prescription)
                            <div class="col-12">
                                <strong class="text-warning">Prescription:</strong>
                                <p class="mb-0">{{ $booking->note->prescription }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @else
                        @if($booking->status == 'completed')
                        <p class="text-muted fst-italic mt-2">No note recorded</p>
                        @endif
                    @endif

                    <!-- Notes -->
                    @if($booking->notes)
                    <div class="mt-2">
                        <small class="text-muted"><strong>Notes:</strong> {{ $booking->notes }}</small>
                    </div>
                    @endif
                </div>
                @endforeach

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $bookings->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <p class="text-muted">No medical history available</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection