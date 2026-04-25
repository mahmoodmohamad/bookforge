@extends('layouts.app')
@section('title', 'Edit Note')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Edit Note</h2>
                <a href="{{ route('provider.bookings.show', $booking) }}" class="btn btn-secondary">
                    ← Back
                </a>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Client Info Summary -->
            <div class="card mb-4 border-info">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>{{ $booking->client->user->name }}</h5>
                            <p class="mb-0">
                                <strong>National ID:</strong> {{ $booking->client->national_id }}<br>
                                <strong>Booking:</strong> {{ $booking->booking_date->format('F j, Y - g:i A') }}
                            </p>
                        </div>
                        <div class="col-md-6 text-end">
                            <small class="text-muted">
                                Original note: {{ $booking->note->created_at->format('M j, Y g:i A') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Note Form -->
            <div class="card">
                <div class="card-header bg-warning">
                    <h5 class="mb-0">Edit Note</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('provider.note.update', $booking) }}">
                        @csrf
                        @method('PUT')

                        <!-- Symptoms -->
                        <div class="mb-4">
                            <label for="symptoms" class="form-label">Symptoms / Chief Complaints *</label>
                            <textarea name="symptoms" 
                                      id="symptoms" 
                                      rows="4"
                                      class="form-control @error('symptoms') is-invalid @enderror"
                                      required>{{ old('symptoms', $booking->note->symptoms) }}</textarea>
                            @error('symptoms')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Note -->
                        <div class="mb-4">
                            <label for="note" class="form-label">Note / Assessment *</label>
                            <textarea name="note" 
                                      id="note" 
                                      rows="4"
                                      class="form-control @error('note') is-invalid @enderror"
                                      required>{{ old('note', $booking->note->note) }}</textarea>
                            @error('note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Prescription -->
                        <div class="mb-4">
                            <label for="prescription" class="form-label">Prescription / Treatment Plan</label>
                            <textarea name="prescription" 
                                      id="prescription" 
                                      rows="6"
                                      class="form-control @error('prescription') is-invalid @enderror">{{ old('prescription', $booking->note->prescription) }}</textarea>
                            @error('prescription')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Additional Notes -->
                        <div class="mb-4">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea name="notes" 
                                      id="notes" 
                                      rows="3"
                                      class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $booking->note->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('provider.bookings.show', $booking) }}" class="btn btn-secondary btn-lg">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-warning btn-lg">
                                💾 Update Note
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection