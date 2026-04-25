@extends('layouts.app')
@section('title', 'Booking Details')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Booking Details</h2>
                <a href="{{ route('provider.bookings.index') }}" class="btn btn-secondary">
                    ← Back
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Booking Info -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Booking Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Date:</strong> {{ $booking->booking_date->format('l, F j, Y') }}</p>
                            <p><strong>Time:</strong> {{ $booking->booking_time ?? $booking->booking_date->format('g:i A') }}</p>
                            <p><strong>Status:</strong> 
                                <span class="badge bg-{{ $booking->status == 'completed' ? 'success' : ($booking->status == 'cancelled' ? 'danger' : 'primary') }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            @if($booking->notes)
                            <p><strong>Notes:</strong> {{ $booking->notes }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Client Info -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Client Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> {{ $booking->client->user->name }}</p>
                            <p><strong>National ID:</strong> {{ $booking->client->national_id }}</p>
                            <p><strong>Phone:</strong> {{ $booking->client->phone }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Email:</strong> {{ $booking->client->user->email }}</p>
                            <p><strong>City:</strong> {{ $booking->client->city->name ?? 'N/A' }}</p>
                            <a href="{{ route('provider.clients.history', $booking->client) }}" class="btn btn-sm btn-outline-primary">
                                View Medical History →
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Note -->
            @if($booking->note)
            <div class="card mb-4">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Note</h5>
                    <a href="{{ route('provider.note.edit', $booking) }}" class="btn btn-sm btn-light">
                        ✏️ Edit
                    </a>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Symptoms:</strong>
                        <p>{{ $booking->note->symptoms }}</p>
                    </div>
                    <div class="mb-3">
                        <strong>Note:</strong>
                        <p>{{ $booking->note->note }}</p>
                    </div>
                    @if($booking->note->prescription)
                    <div class="mb-3">
                        <strong>Prescription:</strong>
                        <p style="white-space: pre-line;">{{ $booking->note->prescription }}</p>
                    </div>
                    @endif
                    @if($booking->note->notes)
                    <div class="mb-3">
                        <strong>Notes:</strong>
                        <p>{{ $booking->note->notes }}</p>
                    </div>
                    @endif
                    <small class="text-muted">
                        Recorded on: {{ $booking->note->created_at->format('F j, Y g:i A') }}
                    </small>
                </div>
            </div>
            @else
                @if($booking->status != 'cancelled')
                <div class="card border-warning">
                    <div class="card-body text-center">
                        <h5>No Note Recorded</h5>
                        <p class="text-muted">This booking doesn't have a note yet.</p>
                        <a href="{{ route('provider.note.create', $booking) }}" class="btn btn-success btn-lg">
                            + Add Note
                        </a>
                    </div>
                </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection