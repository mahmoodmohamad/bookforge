@extends('layouts.app')
@section('title', 'My Bookings')

@section('content')
<div class="container-fluid py-4">
    <h2 class="mb-4">My Bookings</h2>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('provider.bookings.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Filter</label>
                    <select name="filter" class="form-select">
                        <option value="">All Bookings</option>
                        <option value="today" {{ request('filter') == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="upcoming" {{ request('filter') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                        <option value="past" {{ request('filter') == 'past' ? 'selected' : '' }}>Past</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <a href="{{ route('provider.bookings.index') }}" class="btn btn-secondary w-100">Clear</a>
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
                                <th>Date & Time</th>
                                <th>Client</th>
                                <th>Status</th>
                                <th>Note</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $booking)
                            <tr>
                                <td>
                                    <strong>{{ $booking->booking_date->format('Y-m-d') }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        {{ $booking->booking_time ?? $booking->booking_date->format('H:i') }}
                                    </small>
                                </td>
                                <td>
                                    {{ $booking->client->user->name }}
                                    <br>
                                    <small class="text-muted">{{ $booking->client->national_id }}</small>
                                </td>
                                <td>
                                    @if($booking->status == 'scheduled')
                                        <span class="badge bg-primary">Scheduled</span>
                                    @elseif($booking->status == 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @else
                                        <span class="badge bg-danger">Cancelled</span>
                                    @endif
                                </td>
                                <td>
                                    @if($booking->note)
                                        <span class="badge bg-success">✓ Done</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('provider.bookings.show', $booking) }}" class="btn btn-sm btn-info">
                                        View
                                    </a>
                                    @if(!$booking->note && $booking->status != 'cancelled')
                                        <a href="{{ route('provider.note.create', $booking) }}" class="btn btn-sm btn-success">
                                            + Note
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $bookings->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <p class="text-muted">No bookings found.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection