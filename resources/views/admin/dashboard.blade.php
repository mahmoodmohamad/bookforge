@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid py-4">
    <h2 class="mb-4">Admin Dashboard</h2>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Users</h6>
                            <h2 class="mb-0">{{ $stats['total_users'] }}</h2>
                        </div>
                        <div class="fs-1">👥</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Clients</h6>
                            <h2 class="mb-0">{{ $stats['total_clients'] }}</h2>
                        </div>
                        <div class="fs-1">🏥</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Providers</h6>
                            <h2 class="mb-0">{{ $stats['total_providers'] }}</h2>
                        </div>
                        <div class="fs-1">👨‍⚕️</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Bookings</h6>
                            <h2 class="mb-0">{{ $stats['total_bookings'] }}</h2>
                        </div>
                        <div class="fs-1">📅</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row Stats -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Today's Bookings</h6>
                    <h3 class="text-primary">{{ $stats['today_bookings'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Scheduled</h6>
                    <h3 class="text-info">{{ $stats['scheduled'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Completed</h6>
                    <h3 class="text-success">{{ $stats['completed'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">This Month</h6>
                    <h3 class="text-warning">{{ $stats['month_bookings'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Charts Column -->
        <div class="col-md-8">
            <!-- Bookings by Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Bookings by Status</h5>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="80"></canvas>
                </div>
            </div>

            <!-- Monthly Trend -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Bookings Trend (Last 6 Months)</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <!-- Side Column -->
        <div class="col-md-4">
            <!-- Top Providers -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">🏆 Top Providers</h5>
                </div>
                <div class="card-body">
                    @foreach($topProviders as $provider)
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <div>
                            <strong>Dr. {{ $provider->user->name }}</strong>
                            <br>
                            <small class="text-muted">{{ $provider->specialization }}</small>
                        </div>
                        <span class="badge bg-primary">{{ $provider->bookings_count }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Recent Clients -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">👤 Recent Clients</h5>
                </div>
                <div class="card-body">
                    @foreach($recentClients as $client)
                    <div class="mb-2 pb-2 border-bottom">
                        <strong>{{ $client->user->name }}</strong>
                        <br>
                        <small class="text-muted">{{ $client->created_at->diffForHumans() }}</small>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Bookings</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Client</th>
                                    <th>Provider</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentBookings as $booking)
                                <tr>
                                    <td>{{ $booking->booking_date->format('Y-m-d H:i') }}</td>
                                    <td>{{ $booking->client->user->name }}</td>
                                    <td>Dr. {{ $booking->provider->user->name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $booking->status == 'completed' ? 'success' : ($booking->status == 'cancelled' ? 'danger' : 'primary') }}">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Status Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: [
            @foreach($bookingsByStatus as $item)
                '{{ ucfirst($item->status) }}',
            @endforeach
        ],
        datasets: [{
            data: [
                @foreach($bookingsByStatus as $item)
                    {{ $item->count }},
                @endforeach
            ],
            backgroundColor: ['#0dcaf0', '#198754', '#dc3545']
        }]
    }
});

// Monthly Chart
const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
new Chart(monthlyCtx, {
    type: 'line',
    data: {
        labels: [
            @foreach($monthlyData as $item)
                '{{ $item->month }}',
            @endforeach
        ],
        datasets: [{
            label: 'Bookings',
            data: [
                @foreach($monthlyData as $item)
                    {{ $item->count }},
                @endforeach
            ],
            borderColor: '#0d6efd',
            tension: 0.4,
            fill: true,
            backgroundColor: 'rgba(13, 110, 253, 0.1)'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true
    }
});
</script>
@endsection