@extends('layouts.app')
@section('title', 'Booking Details')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Booking Details</h2>
                <a href="{{ route('bookings.index') }}" class="btn btn-secondary">
                    ← Back to List
                </a>
            </div>

            <!-- Booking Info -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Booking Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">ID:</th>
                                    <td>#{{ $booking->id }}</td>
                                </tr>
                                <tr>
                                    <th>Date:</th>
                                    <td>{{ $booking->booking_date->format('l, F j, Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Time:</th>
                                    <td>{{ $booking->booking_time ?? $booking->booking_date->format('g:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        @if($booking->status == 'scheduled')
                                            <span class="badge bg-primary">Scheduled</span>
                                        @elseif($booking->status == 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($booking->status == 'cancelled')
                                            <span class="badge bg-danger">Cancelled</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">Booked:</th>
                                    <td>{{ $booking->created_at->format('Y-m-d h:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Booked By:</th>
                                    <td>
                                        @if($booking->staff)
                                            {{ $booking->staff->user->name }} (Staff)
                                        @else
                                            System
                                        @endif
                                    </td>
                                </tr>
                                @if($booking->notes)
                                <tr>
                                    <th>Notes:</th>
                                    <td>{{ $booking->notes }}</td>
                                </tr>
                                @endif
                            </table>
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
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">Name:</th>
                                    <td>{{ $booking->client->user->name }}</td>
                                </tr>
                                <tr>
                                    <th>National ID:</th>
                                    <td>{{ $booking->client->national_id }}</td>
                                </tr>
                                <tr>
                                    <th>Phone:</th>
                                    <td>{{ $booking->client->phone }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">Email:</th>
                                    <td>{{ $booking->client->user->email }}</td>
                                </tr>
                                <tr>
                                    <th>City:</th>
                                    <td>{{ $booking->client->city->name ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Provider Info -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Provider Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">Name:</th>
                                    <td>Dr. {{ $booking->provider->user->name }}</td>
                                </tr>
                                <tr>
                                    <th>Specialization:</th>
                                    <td>{{ $booking->provider->specialization }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">Phone:</th>
                                    <td>{{ $booking->provider->phone }}</td>
                                </tr>
                                <tr>
                                    <th>City:</th>
                                    <td>{{ $booking->provider->city->name ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Note Info -->
            @if($booking->note)
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Note</h5>
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
                        <p>{{ $booking->note->prescription }}</p>
                    </div>
                    @endif
                    <small class="text-muted">
                        Diagnosed on: {{ $booking->note->created_at->format('Y-m-d h:i A') }}
                    </small>
                </div>
            </div>
            @else
                @if($booking->status == 'completed')
                <div class="alert alert-info">
                    This booking is completed but no note has been recorded yet.
                </div>
                @endif
            @endif

            <!-- Actions -->
            @if($booking->status == 'scheduled')
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Actions</h5>
                    <form action="{{ route('bookings.destroy', $booking) }}" 
                          method="POST" 
                          onsubmit="return confirm('Are you sure you want to cancel this booking?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            ❌ Cancel Booking
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection