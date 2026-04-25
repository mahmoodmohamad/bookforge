@extends('layouts.app')
@section('title', 'Client Medical History')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Medical History</h2>
            <p class="text-muted mb-0">{{ $client->user->name }} - {{ $client->national_id }}</p>
        </div>
        <a href="{{ route('provider.dashboard') }}" class="btn btn-secondary">
            ← Back to Dashboard
        </a>
    </div>

    <!-- Client Summary -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <p><strong>National ID:</strong> {{ $client->national_id }}</p>
                    <p><strong>Phone:</strong> {{ $client->phone }}</p>
                </div>
                <div class="col-md-4">
                    <p><strong>Email:</strong> {{ $client->user->email }}</p>
                    <p><strong>City:</strong> {{ $client->city->name ?? 'N/A' }}</p>
                </div>
                <div class="col-md-4 text-end">
                    <h4>{{ $bookings->count() }}</h4>
                    <p class="text-muted mb-0">Total Visits with You</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Medical History Timeline -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Booking History</h5>
        </div>
        <div class="card-body">
            @if($bookings->count() > 0)
                @foreach($bookings as $booking)
                <div class="border-start border-3 border-{{ $booking->status == 'completed' ? 'success' : 'primary' }} ps-4 pb-4 mb-4">
                    <!-- Booking Header -->
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h5 class="mb-1">{{ $booking->booking_date->format('F j, Y') }}</h5>
                            <p class="text-muted mb-0">
                                {{ $booking->booking_time ?? $booking->booking_date->format('g:i A') }}
                            </p>
                        </div>
                        <div>
                            <span class="badge bg-{{ $booking->status == 'completed' ? 'success' : ($booking->status == 'cancelled' ? 'danger' : 'primary') }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                            <a href="{{ route('provider.bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary ms-2">
                                View Details
                            </a>
                        </div>
                    </div>

                    <!-- Note Details -->
                    @if($booking->note)
                    <div class="bg-light p-3 rounded mt-3">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong class="text-primary">Symptoms:</strong>
                                <p class="mb-0 mt-1">{{ $booking->note->symptoms }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong class="text-success">Note:</strong>
                                <p class="mb-0 mt-1">{{ $booking->note->note }}</p>
                            </div>
                            @if($booking->note->prescription)
                            <div class="col-12">
                                <strong class="text-warning">Prescription:</strong>
                                <p class="mb-0 mt-1" style="white-space: pre-line;">{{ $booking->note->prescription }}</p>
                            </div>
                            @endif
                            @if($booking->note->notes)
                            <div class="col-12 mt-2">
                                <strong class="text-info">Notes:</strong>
                                <p class="mb-0 mt-1">{{ $booking->note->notes }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @else
                        @if($booking->status == 'completed')
                        <p class="text-muted fst-italic mt-2">No note recorded for this visit</p>
                        @elseif($booking->status == 'scheduled')
                        <p class="text-info mt-2">
                            <i class="bi bi-clock"></i> Upcoming booking
                        </p>
                        @endif
                    @endif

                    <!-- Booking Notes -->
                    @if($booking->notes)
                    <div class="mt-2">
                        <small class="text-muted"><strong>Booking Notes:</strong> {{ $booking->notes }}</small>
                    </div>
                    @endif
                </div>
                @endforeach
            @else
                <div class="text-center py-5">
                    <p class="text-muted">No booking history with this client</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection