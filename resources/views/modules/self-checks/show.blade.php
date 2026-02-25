@extends('layouts.app')

@section('title', $selfCheck->title)

@section('content')
<div class="content-area">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('content.management') }}">Content</a></li>
            <li class="breadcrumb-item active">{{ $selfCheck->title }}</li>
        </ol>
    </nav>

    <div class="cb-container">
        {{-- Sidebar --}}
        <div class="cb-sidebar">
            <div class="cb-sidebar__title">Self-Check Info</div>

            {{-- Test Information --}}
            <div class="cb-sidebar__group">
                <div class="cb-sidebar__group-label"><i class="fas fa-info-circle"></i> Test Details</div>
                <div class="cb-sidebar__info">
                    <div class="cb-sidebar__info-title">Questions</div>
                    {{ $selfCheck->questions->count() }}
                </div>
                <div class="cb-sidebar__info">
                    <div class="cb-sidebar__info-title">Total Points</div>
                    {{ $selfCheck->total_points }}
                </div>
                @if($selfCheck->time_limit)
                <div class="cb-sidebar__info">
                    <div class="cb-sidebar__info-title">Time Limit</div>
                    {{ $selfCheck->time_limit }} minutes
                </div>
                @endif
                @if($selfCheck->passing_score)
                <div class="cb-sidebar__info">
                    <div class="cb-sidebar__info-title">Passing Score</div>
                    {{ $selfCheck->passing_score }}%
                </div>
                @endif
            </div>

            {{-- Question Types --}}
            @php $typeCounts = $selfCheck->questions->groupBy('question_type')->map->count(); @endphp
            @if($typeCounts->count() > 0)
            <div class="cb-sidebar__group">
                <div class="cb-sidebar__group-label"><i class="fas fa-list"></i> Question Types</div>
                @foreach($typeCounts as $type => $count)
                <div class="cb-sidebar__info">
                    <div class="cb-sidebar__info-title">{{ formatQuestionType($type) }}</div>
                    {{ $count }}
                </div>
                @endforeach
            </div>
            @endif

            {{-- Submissions (Instructors/Admins) --}}
            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'instructor')
            <div class="cb-sidebar__group">
                <div class="cb-sidebar__group-label"><i class="fas fa-users"></i> Submissions</div>
                @if($selfCheck->submissions->count() > 0)
                    @foreach($selfCheck->submissions->take(5) as $submission)
                    <div class="cb-sidebar__info">
                        <div class="cb-sidebar__info-title">{{ $submission->user->first_name }} {{ $submission->user->last_name }}</div>
                        <span class="badge bg-{{ $submission->passed ? 'success' : 'danger' }}">{{ number_format($submission->percentage, 1) }}%</span>
                    </div>
                    @endforeach
                    @if($selfCheck->submissions->count() > 5)
                    <div style="text-align: center; color: var(--cb-text-hint); font-size: 0.8rem; padding: 0.5rem;">
                        + {{ $selfCheck->submissions->count() - 5 }} more
                    </div>
                    @endif
                @else
                <div class="cb-sidebar__info">No submissions yet</div>
                @endif
            </div>
            @endif

            {{-- Navigation --}}
            <div class="cb-sidebar__group" style="margin-top: auto;">
                <a href="{{ route('information-sheets.show', ['module' => $selfCheck->informationSheet->module_id, 'informationSheet' => $selfCheck->informationSheet->id]) }}"
                   class="btn btn-outline-secondary w-100 btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Back to Info Sheet
                </a>
            </div>
        </div>

        {{-- Main --}}
        <div class="cb-main">
            <div class="cb-header cb-header--self-check">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h4><i class="fas fa-clipboard-check me-2"></i>{{ $selfCheck->title }}</h4>
                        <p>{{ $selfCheck->check_number }}</p>
                    </div>
                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'instructor')
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
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

            <div class="cb-body">
                @if($selfCheck->description)
                <p class="lead" style="color: var(--cb-text-label);">{{ $selfCheck->description }}</p>
                @endif

                {{-- Instructions --}}
                <div class="cb-context-badge" style="background: #e3f2fd; border-left: 4px solid #2196f3;">
                    <i class="fas fa-info-circle" style="color: #1976d2;"></i>
                    <span style="color: #1565c0;">{{ $selfCheck->instructions }}</span>
                </div>

                {{-- Student: Take Self-Check --}}
                @if(auth()->user()->role === 'student')
                <form action="{{ route('self-checks.submit', $selfCheck) }}" method="POST" id="selfCheckForm">
                    @csrf
                    @foreach($selfCheck->questions->sortBy('order') as $index => $question)
                    <div class="cb-item-card question-card" data-question-type="{{ $question->question_type }}" style="margin-top: 1rem;">
                        <div class="cb-item-card__header">
                            <div class="left-section">
                                <span class="cb-item-card__number">{{ $index + 1 }}</span>
                                <span class="badge bg-{{ getQuestionTypeBadgeColor($question->question_type) }}">
                                    {{ formatQuestionType($question->question_type) }}
                                </span>
                            </div>
                            <div class="right-section">
                                <span class="badge bg-primary">{{ $question->points }} pt(s)</span>
                            </div>
                        </div>
                        <div class="cb-item-card__body">
                            @if(!empty($question->options['question_image']))
                            <div class="text-center mb-3">
                                <img src="{{ $question->options['question_image'] }}" alt="Question Image" class="img-fluid rounded" style="max-height: 300px;">
                            </div>
                            @endif

                            <p class="fs-5 mb-3">
                                @if($question->question_type === 'fill_blank')
                                    {!! preg_replace('/___+/', '<span style="display:inline-block;border-bottom:2px solid #007bff;min-width:100px;color:#007bff;">________</span>', e($question->question_text)) !!}
                                @else
                                    {{ $question->question_text }}
                                @endif
                            </p>

                            @include('modules.self-checks.partials.question-input', ['question' => $question])
                        </div>
                    </div>
                    @endforeach

                    <div class="cb-footer" style="margin-top: 1.5rem;">
                        <p style="color: var(--cb-text-hint); font-size: 0.85rem; margin: 0;">
                            <i class="fas fa-info-circle me-1"></i>Answer all questions before submitting.
                        </p>
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-paper-plane me-1"></i>Submit Answers
                        </button>
                    </div>
                </form>

                @else
                {{-- Instructor/Admin: Preview --}}
                @foreach($selfCheck->questions->sortBy('order') as $index => $question)
                <div class="cb-item-card" style="margin-top: 1rem;">
                    <div class="cb-item-card__header">
                        <div class="left-section">
                            <span class="cb-item-card__number">{{ $index + 1 }}</span>
                            <span class="badge bg-{{ getQuestionTypeBadgeColor($question->question_type) }}">
                                {{ formatQuestionType($question->question_type) }}
                            </span>
                        </div>
                        <div class="right-section">
                            <span class="badge bg-primary">{{ $question->points }} pts</span>
                        </div>
                    </div>
                    <div class="cb-item-card__body">
                        <p class="mb-2"><strong>{{ $question->question_text }}</strong></p>

                        @if($question->question_type === 'multiple_choice' || $question->question_type === 'image_choice')
                            @php $options = $question->options ?? []; @endphp
                            <div class="ms-3">
                                @foreach($options as $optIndex => $option)
                                <div class="{{ $question->correct_answer == $optIndex ? 'text-success fw-bold' : '' }}" style="padding: 0.25rem 0;">
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
                            <div class="row ms-3 mt-2">
                                <div class="col-5">
                                    <strong class="d-block mb-1" style="font-size: 0.8rem; color: var(--cb-text-hint);">Column A</strong>
                                    @foreach($pairs as $pair)
                                    <div class="p-2 mb-1" style="border: 1px solid var(--cb-border); border-radius: var(--cb-radius-sm);">{{ $pair['left'] }}</div>
                                    @endforeach
                                </div>
                                <div class="col-2 text-center d-flex align-items-center justify-content-center">
                                    <i class="fas fa-arrows-alt-h" style="color: var(--cb-text-hint);"></i>
                                </div>
                                <div class="col-5">
                                    <strong class="d-block mb-1" style="font-size: 0.8rem; color: var(--cb-text-hint);">Column B</strong>
                                    @foreach($pairs as $pair)
                                    <div class="p-2 mb-1" style="border: 1px solid #d4edda; border-radius: var(--cb-radius-sm); background: #f8fff9;">{{ $pair['right'] }}</div>
                                    @endforeach
                                </div>
                            </div>
                        @elseif($question->question_type === 'ordering')
                            @php $items = $question->options['items'] ?? []; @endphp
                            <div class="ms-3 mt-2">
                                <strong class="d-block mb-1" style="font-size: 0.8rem; color: var(--cb-text-hint);">Correct Order:</strong>
                                @foreach($items as $itemIndex => $item)
                                <div class="d-flex align-items-center mb-1">
                                    <span class="badge bg-secondary me-2">{{ $itemIndex + 1 }}</span>
                                    {{ $item }}
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-success mb-0 ms-3">
                                <i class="fas fa-check me-1"></i><strong>Answer:</strong> {{ $question->correct_answer }}
                            </p>
                        @endif

                        @if($question->explanation)
                        <div class="mt-2 ms-3" style="color: #0288d1; font-size: 0.85rem;">
                            <i class="fas fa-lightbulb me-1"></i>
                            <strong>Explanation:</strong> {{ $question->explanation }}
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.question-card { transition: all 0.3s ease; }
.question-card:hover { box-shadow: var(--cb-shadow-hover); }

.matching-container { display: flex; gap: 2rem; }
.matching-column { flex: 1; }
.matching-item { padding: 0.75rem 1rem; margin-bottom: 0.5rem; border: 2px solid #dee2e6; border-radius: 8px; cursor: pointer; transition: all 0.2s ease; }
.matching-item:hover { border-color: #007bff; background-color: #f8f9fa; }
.matching-item.selected { border-color: #007bff; background-color: #e7f1ff; }
.matching-item.matched { border-color: #28a745; background-color: #d4edda; }
.ordering-item { display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1rem; margin-bottom: 0.5rem; border: 2px solid #dee2e6; border-radius: 8px; cursor: move; background: white; transition: all 0.2s ease; }
.ordering-item:hover { border-color: #17a2b8; }
.ordering-item.dragging { opacity: 0.5; }
.ordering-number { width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; background: #6c757d; color: white; border-radius: 50%; font-weight: bold; font-size: 0.875rem; }
.image-choice-container { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; }
.image-choice-item { border: 3px solid #dee2e6; border-radius: 8px; padding: 0.5rem; cursor: pointer; transition: all 0.2s ease; text-align: center; }
.image-choice-item:hover { border-color: #007bff; }
.image-choice-item.selected { border-color: #007bff; background-color: #e7f1ff; }
.image-choice-item img { max-width: 100%; max-height: 150px; border-radius: 4px; }

@media (max-width: 1032px) {
    .image-choice-container { grid-template-columns: 1fr; }
    .matching-container { flex-direction: column; gap: 1rem; }
}
</style>
@endpush

@php
function formatQuestionType($type) {
    $labels = [
        'multiple_choice' => 'Multiple Choice', 'true_false' => 'True/False',
        'fill_blank' => 'Fill in the Blank', 'short_answer' => 'Short Answer',
        'matching' => 'Matching', 'ordering' => 'Ordering', 'image_choice' => 'Image Choice',
        'multiple_select' => 'Multiple Select', 'numeric' => 'Numeric',
        'classification' => 'Classification', 'drag_drop' => 'Drag & Drop',
        'image_identification' => 'Image ID', 'hotspot' => 'Hotspot',
        'image_labeling' => 'Image Labeling', 'essay' => 'Essay',
        'audio_question' => 'Audio', 'video_question' => 'Video', 'slider' => 'Slider',
    ];
    return $labels[$type] ?? ucfirst(str_replace('_', ' ', $type));
}

function getQuestionTypeBadgeColor($type) {
    $colors = [
        'multiple_choice' => 'primary', 'true_false' => 'info', 'fill_blank' => 'purple',
        'short_answer' => 'warning', 'matching' => 'success', 'ordering' => 'teal',
        'image_choice' => 'pink', 'multiple_select' => 'indigo', 'numeric' => 'secondary',
    ];
    return $colors[$type] ?? 'secondary';
}
@endphp
@endsection
