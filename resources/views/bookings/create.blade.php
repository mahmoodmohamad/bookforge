@extends('layouts.app')
@section('title', 'New Booking')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Book New Booking</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('bookings.store') }}">
                        @csrf

                        <!-- Client Selection -->
                        <div class="mb-3">
                            <label for="client_id" class="form-label">Client *</label>
                            <select name="client_id" id="client_id" class="form-select @error('client_id') is-invalid @enderror" required>
                                <option value="">Select Client</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->user->name }} - {{ $client->national_id }}
                                    </option>
                                @endforeach
                            </select>
                            @error('client_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Provider Selection -->
                        <div class="mb-3">
                            <label for="provider_id" class="form-label">Provider *</label>
                            <select name="provider_id" id="provider_id" class="form-select @error('provider_id') is-invalid @enderror" required>
                                <option value="">Select Provider</option>
                                @foreach($providers as $provider)
                                    <option value="{{ $provider->id }}" {{ old('provider_id') == $provider->id ? 'selected' : '' }}>
                                        Dr. {{ $provider->user->name }} - {{ $provider->specialization }}
                                    </option>
                                @endforeach
                            </select>
                            @error('provider_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Date -->
                        <div class="mb-3">
                            <label for="booking_date" class="form-label">Booking Date *</label>
                            <input type="date" 
                                   name="booking_date" 
                                   id="booking_date" 
                                   class="form-control @error('booking_date') is-invalid @enderror"
                                   value="{{ old('booking_date') }}"
                                   min="{{ date('Y-m-d') }}"
                                   required>
                            @error('booking_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Time -->
                        <div class="mb-3">
                            <label for="booking_time" class="form-label">Booking Time *</label>
                            <select name="booking_time" id="booking_time" class="form-select @error('booking_time') is-invalid @enderror" required>
                                <option value="">Select Time</option>
                                <!-- Morning Slots -->
                                <optgroup label="Morning (9 AM - 12 PM)">
                                    @for($hour = 9; $hour < 12; $hour++)
                                        @foreach(['00', '30'] as $minute)
                                            @php $time = sprintf('%02d:%s', $hour, $minute); @endphp
                                            <option value="{{ $time }}" {{ old('booking_time') == $time ? 'selected' : '' }}>
                                                {{ date('g:i A', strtotime($time)) }}
                                            </option>
                                        @endforeach
                                    @endfor
                                </optgroup>
                                <!-- Afternoon Slots -->
                                <optgroup label="Afternoon (2 PM - 5 PM)">
                                    @for($hour = 14; $hour < 17; $hour++)
                                        @foreach(['00', '30'] as $minute)
                                            @php $time = sprintf('%02d:%s', $hour, $minute); @endphp
                                            <option value="{{ $time }}" {{ old('booking_time') == $time ? 'selected' : '' }}>
                                                {{ date('g:i A', strtotime($time)) }}
                                            </option>
                                        @endforeach
                                    @endfor
                                </optgroup>
                            </select>
                            @error('booking_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes (Optional)</label>
                            <textarea name="notes" id="notes" rows="3" class="form-control">{{ old('notes') }}</textarea>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('bookings.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Book Booking
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
// Optional: Load available slots dynamically when provider and date are selected
document.addEventListener('DOMContentLoaded', function() {
    const providerSelect = document.getElementById('provider_id');
    const dateInput = document.getElementById('booking_date');
    const timeSelect = document.getElementById('booking_time');

    function loadAvailableSlots() {
        const providerId = providerSelect.value;
        const date = dateInput.value;

        if (providerId && date) {
            // You can implement AJAX call to getAvailableSlots here
            console.log('Loading slots for provider:', providerId, 'on date:', date);
            // fetch(`/bookings/available-slots?provider_id=${providerId}&date=${date}`)
            //     .then(response => response.json())
            //     .then(slots => {
            //         // Update time select with available slots
            //     });
        }
    }

    providerSelect.addEventListener('change', loadAvailableSlots);
    dateInput.addEventListener('change', loadAvailableSlots);
});
</script>
@endsection
@endsection