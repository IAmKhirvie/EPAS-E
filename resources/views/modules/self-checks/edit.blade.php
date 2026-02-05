@extends('layouts.app')

@section('title', 'Edit Self-Check')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Self-Check</h4>
                        <a href="{{ route('self-checks.show', $selfCheck) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('self-checks.update', [$informationSheet, $selfCheck]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="check_number" class="form-label">Check Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="check_number" name="check_number" value="{{ old('check_number', $selfCheck->check_number) }}" required>
                            </div>
                            <div class="col-md-8">
                                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $selfCheck->title) }}" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $selfCheck->description) }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label for="instructions" class="form-label">Instructions <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="instructions" name="instructions" rows="4" required>{{ old('instructions', $selfCheck->instructions) }}</textarea>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="time_limit" class="form-label">Time Limit (minutes)</label>
                                <input type="number" class="form-control" id="time_limit" name="time_limit" value="{{ old('time_limit', $selfCheck->time_limit) }}" min="1">
                            </div>
                            <div class="col-md-6">
                                <label for="passing_score" class="form-label">Passing Score (%)</label>
                                <input type="number" class="form-control" id="passing_score" name="passing_score" value="{{ old('passing_score', $selfCheck->passing_score) }}" min="0" max="100">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('self-checks.show', $selfCheck) }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-1"></i>Update Self-Check
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Questions Management -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-question-circle me-2"></i>Questions ({{ $selfCheck->questions->count() }})</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>Questions cannot be edited here to preserve submission integrity. To modify questions, create a new self-check.
                    </div>
                    @foreach($selfCheck->questions as $index => $question)
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-2">Q{{ $index + 1 }}. {{ $question->question_text }}</h6>
                                <span class="badge bg-primary">{{ $question->points }} pts</span>
                            </div>
                            <p class="text-muted mb-1"><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $question->question_type)) }}</p>
                            @if($question->correct_answer)
                            <p class="text-success mb-0"><strong>Answer:</strong> {{ $question->correct_answer }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
