@extends('layouts.app')
@section('title', 'User Details')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>User Details</h2>
                <div>
                    @if($user->id !== auth()->id())
                        <form action="{{ route('admin.users.toggle-activation', $user) }}" 
                              method="POST" 
                              class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-{{ $user->activation ? 'warning' : 'success' }}">
                                {{ $user->activation ? '🔒 Deactivate' : '✅ Activate' }}
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                        ← Back to List
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

            <!-- User Info Card -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Basic Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">ID:</th>
                                    <td>#{{ $user->id }}</td>
                                </tr>
                                <tr>
                                    <th>Name:</th>
                                    <td><strong>{{ $user->name }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th>Role:</th>
                                    <td>
                                        @php
                                            $roleBadge = [
                                                'Admin' => 'danger',
                                                'Provider' => 'primary',
                                                'Staff' => 'info',
                                                'Client' => 'success',
                                            ];
                                            $role = $user->getRoleName();
                                            $badge = $roleBadge[$role] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $badge }}">{{ $role }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">Status:</th>
                                    <td>
                                        @if($user->activation)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Registered:</th>
                                    <td>{{ $user->created_at->format('F j, Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Last Updated:</th>
                                    <td>{{ $user->updated_at->diffForHumans() }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Role-Specific Information -->
            @if($user->isProvider() && $user->provider)
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Provider Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Specialization:</strong> {{ $user->provider->specialization }}</p>
                                <p><strong>Phone:</strong> {{ $user->provider->phone }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>City:</strong> {{ $user->provider->city->name ?? 'N/A' }}</p>
                                <p><strong>Total Bookings:</strong> {{ $user->provider->bookings()->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($user->isStaff() && $user->staff)
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Staff Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Phone:</strong> {{ $user->staff->phone }}</p>
                                <p><strong>City:</strong> {{ $user->staff->city->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Clients Registered:</strong> {{ $user->staff->clients()->count() }}</p>
                                <p><strong>Bookings Created:</strong> {{ $user->staff->bookings()->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($user->isClient() && $user->client)
                <div class="card mb-4">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0">Client Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>National ID:</strong> {{ $user->client->national_id }}</p>
                                <p><strong>Phone:</strong> {{ $user->client->phone }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>City:</strong> {{ $user->client->city->name ?? 'N/A' }}</p>
                                <p><strong>Total Bookings:</strong> {{ $user->client->bookings()->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection