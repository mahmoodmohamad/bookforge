@extends('layouts.app')
@section('title', 'Provider Dashboard')

@section('content')
<div class="container-fluid py-4">
    <h2 class="mb-4">Welcome, Dr. {{ auth()->user()->name }}</h2>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h3 class="mb-0">{{ $stats['today_bookings'] }}</h3>
                    <p class="mb-0">Today's Bookings</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h3 class="mb-0">{{ $stats['today_completed'] }}</h3>
                    <p class="mb-0">Completed Today</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h3 class="mb-0">{{ $stats['today_pending'] }}</h3>
                    <p class="mb-0">Pending Today</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h3 class="mb-0">{{ $stats['total_clients'] }}</h3>
                    <p class="mb-0">Total Clients</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Today's Schedule -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">📅 Today's Schedule - {{ now()->format('l, F j, Y') }}</h5>
                </div>
                <div class="card-body">
                    @if($todayBookings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Client</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todayBookings as $booking)
                                    <tr>
                                        <td>
                                            <strong>{{ $booking->booking_time ?? $booking->booking_date->format('H:i') }}</strong>
                                        </td>
                                        <td>
                                            {{ $booking->client->user->name }}
                                            <br>
                                            <small class="text-muted">{{ $booking->client->national_id }}</small>
                                        </td>
                                        <td>
                                            @if($booking->status == 'scheduled')
                                                <span class="badge bg-warning">Scheduled</span>
                                            @elseif($booking->status == 'completed')
                                                <span class="badge bg-success">✓ Completed</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($booking->status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('provider.bookings.show', $booking) }}" 
                                               class="btn btn-sm btn-info">
                                                View
                                            </a>
                                            @if(!$booking->note && $booking->status != 'cancelled')
                                                <a href="{{ route('provider.note.create', $booking) }}" 
                                                   class="btn btn-sm btn-success">
                                                    + Note
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <p class="text-muted">No bookings scheduled for today</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Upcoming Bookings -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">🗓️ Upcoming (Next 7 Days)</h5>
                </div>
                <div class="card-body">
                    @if($upcomingBookings->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($upcomingBookings as $booking)
                            <a href="{{ route('provider.bookings.show', $booking) }}" 
                               class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between">
                                    <strong>{{ $booking->client->user->name }}</strong>
                                    <small class="text-muted">{{ $booking->booking_date->format('M j') }}</small>
                                </div>
                                <small class="text-muted">
                                    {{ $booking->booking_time ?? $booking->booking_date->format('H:i') }}
                                </small>
                            </a>
                            @endforeach
                        </div>
                        <div class="mt-3 text-center">
                            <a href="{{ route('provider.bookings.index') }}" class="btn btn-sm btn-outline-primary">
                                View All Bookings →
                            </a>
                        </div>
                    @else
                        <p class="text-muted text-center py-3">No upcoming bookings</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection