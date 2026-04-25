@extends('layouts.app')
@section('title', 'Staff Dashboard')

@section('content')
<div class="container-fluid py-4">
    <h2 class="mb-4">Welcome, {{ auth()->user()->name }}</h2>

    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h3>{{ auth()->user()->staff->clients()->count() }}</h3>
                    <p class="mb-0">Clients Registered</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h3>{{ auth()->user()->staff->bookings()->count() }}</h3>
                    <p class="mb-0">Bookings Created</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h3>{{ auth()->user()->staff->bookings()->whereDate('created_at', today())->count() }}</h3>
                    <p class="mb-0">Today's Bookings</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h3>{{ auth()->user()->staff->bookings()->where('status', 'scheduled')->count() }}</h3>
                    <p class="mb-0">Active Bookings</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('clients.create') }}" class="btn btn-primary btn-lg">
                            ➕ Register New Client
                        </a>
                        <a href="{{ route('bookings.create') }}" class="btn btn-success btn-lg">
                            📅 Book Booking
                        </a>
                        <a href="{{ route('clients.index') }}" class="btn btn-info btn-lg">
                            👥 View All Clients
                        </a>
                        <a href="{{ route('bookings.calendar') }}" class="btn btn-warning btn-lg">
                            📆 View Calendar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Clients</h5>
                </div>
                <div class="card-body">
                    @php
                        $recentClients = auth()->user()->staff->clients()->with('user')->latest()->take(5)->get();
                    @endphp
                    @if($recentClients->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentClients as $client)
                            <a href="{{ route('clients.show', $client) }}" class="list-group-item list-group-item-action">
                                <strong>{{ $client->user->name }}</strong>
                                <br>
                                <small class="text-muted">{{ $client->created_at->diffForHumans() }}</small>
                            </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center">No clients registered yet</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection