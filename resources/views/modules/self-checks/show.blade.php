@extends('layouts.app')

@section('title', $selfCheck->title)

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-8">
            {{-- Self-Check Content --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-light text-primary me-2">{{ $selfCheck->check_number }}</span>
                            <h4 class="mb-0 d-inline">{{ $selfCheck->title }}</h4>
                        </div>
                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'instructor')
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-cog"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('self-checks.edit', [$selfCheck->informationSheet, $selfCheck]) }}">
                                        <i class="fas fa-edit me-2"></i>Edit
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('self-checks.destroy', [$selfCheck->informationSheet, $selfCheck]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this self-check?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-trash me-2"></i>Delete
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if($selfCheck->description)
                    <p class="lead">{{ $selfCheck->description }}</p>
                    @endif

                    {{-- Instructions --}}
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Instructions</h6>
                        <p class="mb-0">{{ $selfCheck->instructions }}</p>
                    </div>

                    {{-- Take Self-Check Form (Students) --}}
                    @if(auth()->user()->role === 'student')
                    <form action="{{ route('self-checks.submit', $selfCheck) }}" method="POST" id="selfCheckForm">
                        @csrf
                        @foreach($selfCheck->questions->sortBy('order') as $index => $question)
                        <div class="card mb-4 question-card" data-question-type="{{ $question->question_type }}">
                            <div class="card-header bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-{{ getQuestionTypeBadgeColor($question->question_type) }} me-2">
                                            {{ formatQuestionType($question->question_type) }}
                                        </span>
                                        <span class="fw-bold">Question {{ $index + 1 }}</span>
                                    </div>
                                    <span class="badge bg-primary">{{ $question->points }} point(s)</span>
                                </div>
                            </div>
                            <div class="card-body">
                                {{-- Question Image (if any) --}}
                                @if(!empty($question->options['question_image']))
                                <div class="text-center mb-3">
                                    <img src="{{ $question->options['question_image'] }}" alt="Question Image" class="img-fluid rounded" style="max-height: 300px;">
                                </div>
                                @endif

                                <p class="question-text mb-3 fs-5">
                                    @if($question->question_type === 'fill_blank')
                                        {!! preg_replace('/___+/', '<span class="fill-blank-placeholder">________</span>', e($question->question_text)) !!}
                                    @else
                                        {{ $question->question_text }}
                                    @endif
                                </p>

                                @include('modules.self-checks.partials.question-input', ['question' => $question])
                            </div>
                        </div>
                        @endforeach

                        <div class="d-flex justify-content-between align-items-center">
                            <p class="text-muted mb-0">
                                <i class="fas fa-info-circle me-1"></i>
                                Make sure to answer all questions before submitting.
                            </p>
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>Submit Answers
                            </button>
                        </div>
                    </form>
                    @else
                    {{-- Questions Preview (Instructors/Admins) --}}
                    @foreach($selfCheck->questions->sortBy('order') as $index => $question)
                    <div class="card mb-3">
                        <div class="card-header bg-light py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-{{ getQuestionTypeBadgeColor($question->question_type) }} me-2">
                                        {{ formatQuestionType($question->question_type) }}
                                    </span>
                                    <span class="fw-bold">Q{{ $index + 1 }}</span>
                                </div>
                                <span class="badge bg-primary">{{ $question->points }} pts</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="mb-2">{{ $question->question_text }}</p>

                            @if($question->question_type === 'multiple_choice' || $question->question_type === 'image_choice')
                                @php $options = $question->options ?? []; @endphp
                                <div class="ms-3">
                                    @foreach($options as $optIndex => $option)
                                        <div class="{{ $question->correct_answer == $optIndex ? 'text-success fw-bold' : 'text-muted' }}">
                                            @if(is_array($option))
                                                {{ chr(65 + $optIndex) }}. {{ $option['label'] ?? 'Option' }}
                                                @if(!empty($option['image']))
                                                    <img src="{{ $option['image'] }}" alt="Option" class="ms-2" style="max-height: 50px;">
                                                @endif
                                            @else
                                                {{ chr(65 + $optIndex) }}. {{ $option }}
                                            @endif
                                            @if($question->correct_answer == $optIndex)
                                                <i class="fas fa-check ms-2"></i>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($question->question_type === 'matching')
                                @php $pairs = $question->options['pairs'] ?? []; @endphp
                                <div class="row ms-3">
                                    <div class="col-5">
                                        <strong>Column A</strong>
                                        @foreach($pairs as $pair)
                                            <div class="border rounded p-2 mb-1">{{ $pair['left'] }}</div>
                                        @endforeach
                                    </div>
                                    <div class="col-2 text-center">
                                        <i class="fas fa-arrows-alt-h mt-4"></i>
                                    </div>
                                    <div class="col-5">
                                        <strong>Column B</strong>
                                        @foreach($pairs as $pair)
                                            <div class="border rounded p-2 mb-1 bg-success-subtle">{{ $pair['right'] }}</div>
                                        @endforeach
                                    </div>
                                </div>
                            @elseif($question->question_type === 'ordering')
                                @php $items = $question->options['items'] ?? []; @endphp
                                <div class="ms-3">
                                    <strong>Correct Order:</strong>
                                    @foreach($items as $itemIndex => $item)
                                        <div class="d-flex align-items-center mb-1">
                                            <span class="badge bg-secondary me-2">{{ $itemIndex + 1 }}</span>
                                            {{ $item }}
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-success mb-0 ms-3">
                                    <strong>Answer:</strong> {{ $question->correct_answer }}
                                </p>
                            @endif

                            @if($question->explanation)
                                <p class="text-info mt-2 mb-0 ms-3">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    <strong>Explanation:</strong> {{ $question->explanation }}
                                </p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Self-Check Info --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Test Information</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <i class="fas fa-question-circle text-info me-2"></i>
                            <strong>Questions:</strong> {{ $selfCheck->questions->count() }}
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-star text-warning me-2"></i>
                            <strong>Total Points:</strong> {{ $selfCheck->total_points }}
                        </li>
                        @if($selfCheck->time_limit)
                        <li class="mb-3">
                            <i class="fas fa-clock text-danger me-2"></i>
                            <strong>Time Limit:</strong> {{ $selfCheck->time_limit }} minutes
                        </li>
                        @endif
                        @if($selfCheck->passing_score)
                        <li>
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <strong>Passing Score:</strong> {{ $selfCheck->passing_score }}%
                        </li>
                        @endif
                    </ul>
                </div>
            </div>

            {{-- Question Types Legend --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-list me-2"></i>Question Types</h6>
                </div>
                <div class="card-body">
                    @php
                        $typeCounts = $selfCheck->questions->groupBy('question_type')->map->count();
                    @endphp
                    @foreach($typeCounts as $type => $count)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>
                            <span class="badge bg-{{ getQuestionTypeBadgeColor($type) }} me-2">{{ $count }}</span>
                            {{ formatQuestionType($type) }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Submissions (for instructors) --}}
            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'instructor')
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>Submissions</h5>
                </div>
                <div class="card-body">
                    @if($selfCheck->submissions->count() > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($selfCheck->submissions->take(5) as $submission)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>{{ $submission->user->first_name }} {{ $submission->user->last_name }}</span>
                            <span class="badge bg-{{ $submission->passed ? 'success' : 'danger' }}">
                                {{ number_format($submission->percentage, 1) }}%
                            </span>
                        </li>
                        @endforeach
                    </ul>
                    @if($selfCheck->submissions->count() > 5)
                    <p class="text-muted text-center mt-2 mb-0">+ {{ $selfCheck->submissions->count() - 5 }} more</p>
                    @endif
                    @else
                    <p class="text-muted text-center mb-0">No submissions yet</p>
                    @endif
                </div>
            </div>
            @endif

            {{-- Navigation --}}
            <div class="card shadow-sm">
                <div class="card-body">
                    <a href="{{ route('information-sheets.show', ['module' => $selfCheck->informationSheet->module_id, 'informationSheet' => $selfCheck->informationSheet->id]) }}" class="btn btn-outline-primary w-100">
                        <i class="fas fa-arrow-left me-2"></i>Back to Information Sheet
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.fill-blank-placeholder {
    display: inline-block;
    border-bottom: 2px solid #007bff;
    min-width: 100px;
    color: #007bff;
}

.question-card {
    transition: all 0.3s ease;
}

.question-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* Matching question styles */
.matching-container {
    display: flex;
    gap: 2rem;
}

.matching-column {
    flex: 1;
}

.matching-item {
    padding: 0.75rem 1rem;
    margin-bottom: 0.5rem;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.matching-item:hover {
    border-color: #007bff;
    background-color: #f8f9fa;
}

.matching-item.selected {
    border-color: #007bff;
    background-color: #e7f1ff;
}

.matching-item.matched {
    border-color: #28a745;
    background-color: #d4edda;
}

/* Ordering question styles */
.ordering-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    margin-bottom: 0.5rem;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    cursor: move;
    background: white;
    transition: all 0.2s ease;
}

.ordering-item:hover {
    border-color: #17a2b8;
    background-color: #f8f9fa;
}

.ordering-item.dragging {
    opacity: 0.5;
    border-color: #007bff;
}

.ordering-number {
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #6c757d;
    color: white;
    border-radius: 50%;
    font-weight: bold;
    font-size: 0.875rem;
}

/* Image choice styles */
.image-choice-container {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.image-choice-item {
    border: 3px solid #dee2e6;
    border-radius: 8px;
    padding: 0.5rem;
    cursor: pointer;
    transition: all 0.2s ease;
    text-align: center;
}

.image-choice-item:hover {
    border-color: #007bff;
}

.image-choice-item.selected {
    border-color: #007bff;
    background-color: #e7f1ff;
}

.image-choice-item img {
    max-width: 100%;
    max-height: 150px;
    border-radius: 4px;
}

.image-choice-label {
    margin-top: 0.5rem;
    font-weight: 500;
}

@media (max-width: 1032px) {
    .image-choice-container {
        grid-template-columns: 1fr;
    }

    .matching-container {
        flex-direction: column;
        gap: 1rem;
    }
}
</style>

@php
function formatQuestionType($type) {
    $labels = [
        'multiple_choice' => 'Multiple Choice',
        'true_false' => 'True/False',
        'fill_blank' => 'Fill in the Blank',
        'short_answer' => 'Short Answer',
        'matching' => 'Matching',
        'ordering' => 'Ordering',
        'image_choice' => 'Image Choice',
    ];
    return $labels[$type] ?? ucfirst(str_replace('_', ' ', $type));
}

function getQuestionTypeBadgeColor($type) {
    $colors = [
        'multiple_choice' => 'primary',
        'true_false' => 'info',
        'fill_blank' => 'purple',
        'short_answer' => 'warning',
        'matching' => 'success',
        'ordering' => 'teal',
        'image_choice' => 'pink',
    ];
    return $colors[$type] ?? 'secondary';
}
@endphp
@endsection
