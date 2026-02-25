@extends('layouts.app')

@section('title', 'Edit Self-Check')

@section('content')
<div class="content-area">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('content.management') }}">Content</a></li>
            <li class="breadcrumb-item"><a href="{{ route('self-checks.show', $selfCheck) }}">{{ $selfCheck->title }}</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>

    <div class="cb-container--simple">
        {{-- Edit Form --}}
        <form action="{{ route('self-checks.update', [$informationSheet, $selfCheck]) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="cb-main">
                <div class="cb-header cb-header--self-check">
                    <h4><i class="fas fa-edit me-2"></i>Edit Self-Check</h4>
                    <p>{{ $selfCheck->check_number }}: {{ $selfCheck->title }}</p>
                </div>

                <div class="cb-body">
                    {{-- Basic Info --}}
                    <div class="cb-section">
                        <div class="cb-section__title"><i class="fas fa-pen"></i> Basic Information</div>
                        <div class="cb-settings">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="cb-field-label">Check Number <span class="required">*</span></label>
                                    <input type="text" class="form-control @error('check_number') is-invalid @enderror"
                                           name="check_number" value="{{ old('check_number', $selfCheck->check_number) }}" required>
                                    @error('check_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-8 mb-3">
                                    <label class="cb-field-label">Title <span class="required">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                           name="title" value="{{ old('title', $selfCheck->title) }}" required>
                                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="cb-field-label">Description <span class="optional">(optional)</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          name="description" rows="3">{{ old('description', $selfCheck->description) }}</textarea>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label class="cb-field-label">Instructions <span class="required">*</span></label>
                                <textarea class="form-control @error('instructions') is-invalid @enderror"
                                          name="instructions" rows="4" required>{{ old('instructions', $selfCheck->instructions) }}</textarea>
                                @error('instructions')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- Settings --}}
                    <div class="cb-section">
                        <div class="cb-section__title"><i class="fas fa-cog"></i> Assessment Settings</div>
                        <div class="cb-settings">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="cb-field-label">Time Limit (minutes) <span class="optional">(optional)</span></label>
                                    <input type="number" class="form-control @error('time_limit') is-invalid @enderror"
                                           name="time_limit" value="{{ old('time_limit', $selfCheck->time_limit) }}" min="1">
                                    @error('time_limit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <div class="cb-field-hint">Leave empty for no time limit</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="cb-field-label">Passing Score (%) <span class="optional">(optional)</span></label>
                                    <input type="number" class="form-control @error('passing_score') is-invalid @enderror"
                                           name="passing_score" value="{{ old('passing_score', $selfCheck->passing_score) }}" min="0" max="100">
                                    @error('passing_score')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <div class="cb-field-hint">Percentage needed to pass (e.g., 75)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="cb-footer">
                    <a href="{{ route('self-checks.show', $selfCheck) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Cancel
                    </a>
                    <div class="btn-group-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Self-Check
                        </button>
                    </div>
                </div>
            </div>
        </form>

        {{-- Questions (Read-Only) --}}
        <div class="cb-main" style="margin-top: 1.5rem;">
            <div class="cb-header" style="background: linear-gradient(135deg, #6c757d 0%, #495057 100%); color: #fff;">
                <h4><i class="fas fa-question-circle me-2"></i>Questions ({{ $selfCheck->questions->count() }})</h4>
                <p>Questions are read-only to preserve submission integrity</p>
            </div>

            <div class="cb-body">
                <div class="cb-context-badge" style="background: #fff3cd; border-left: 4px solid #ffc107;">
                    <i class="fas fa-info-circle" style="color: #856404;"></i>
                    <span style="color: #856404;">Questions cannot be edited to preserve existing submission data. To modify questions, create a new self-check.</span>
                </div>

                @foreach($selfCheck->questions as $index => $question)
                <div class="cb-item-card" style="margin-top: 1rem;">
                    <div class="cb-item-card__header">
                        <div class="left-section">
                            <span class="cb-item-card__number">{{ $index + 1 }}</span>
                            <span class="cb-item-card__title">{{ Str::limit($question->question_text, 60) }}</span>
                        </div>
                        <div class="right-section">
                            <span class="badge bg-primary">{{ $question->points }} pts</span>
                            <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $question->question_type)) }}</span>
                        </div>
                    </div>
                    <div class="cb-item-card__body">
                        <p class="mb-2"><strong>{{ $question->question_text }}</strong></p>
                        @if($question->correct_answer)
                        <p class="text-success mb-0"><i class="fas fa-check me-1"></i><strong>Answer:</strong> {{ $question->correct_answer }}</p>
                        @endif
                        @if($question->explanation)
                        <p class="mb-0 mt-1" style="color: var(--cb-text-hint);"><i class="fas fa-lightbulb me-1"></i>{{ $question->explanation }}</p>
                        @endif
                    </div>
                </div>
                @endforeach

                @if($selfCheck->questions->isEmpty())
                <div class="cb-empty-state">
                    <i class="fas fa-question-circle"></i>
                    <p>No questions have been added to this self-check.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
