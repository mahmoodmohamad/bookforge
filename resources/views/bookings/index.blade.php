@extends('layouts.app')
@section('title', 'Bookings')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>All Bookings</h2>
        <div>
            <a href="{{ route('bookings.calendar') }}" class="btn btn-info me-2">
                📅 Calendar View
            </a>
            <a href="{{ route('bookings.create') }}" class="btn btn-primary">
                ➕ New Booking
            </a>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('bookings.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <a href="{{ route('bookings.index') }}" class="btn btn-secondary w-100">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bookings Table -->
    <div class="card">
        <div class="card-body">
            @if($bookings->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date & Time</th>
                                <th>Client</th>
                                <th>Provider</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $booking)
                            <tr>
                                <td>{{ $booking->id }}</td>
                                <td>
                                    {{ $booking->booking_date->format('Y-m-d') }}
                                    <br>
                                    <small class="text-muted">
                                        {{ $booking->booking_time ?? $booking->booking_date->format('H:i') }}
                                    </small>
                                </td>
                                <td>
                                    <strong>{{ $booking->client->user->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $booking->client->national_id }}</small>
                                </td>
                                <td>
                                    Dr. {{ $booking->provider->user->name }}
                                    <br>
                                    <small class="text-muted">{{ $booking->provider->specialization }}</small>
                                </td>
                                <td>
                                    @if($booking->status == 'scheduled')
                                        <span class="badge bg-primary">Scheduled</span>
                                    @elseif($booking->status == 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($booking->status == 'cancelled')
                                        <span class="badge bg-danger">Cancelled</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('bookings.show', $booking) }}" class="btn btn-sm btn-info">
                                        View
                                    </a>
                                    @if($booking->status == 'scheduled')
                                        <form action="{{ route('bookings.destroy', $booking) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Cancel this booking?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Cancel</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $bookings->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <p class="text-muted">No bookings found.</p>
                    <a href="{{ route('bookings.create') }}" class="btn btn-primary">
                        Create First Booking
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection