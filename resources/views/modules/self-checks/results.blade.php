@extends('layouts.app')

@section('title', 'Self-Check Results')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-8">
            <!-- Results Header -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-{{ $passed ? 'success' : 'danger' }} text-white">
                    <div class="text-center py-3">
                        <i class="fas {{ $passed ? 'fa-check-circle' : 'fa-times-circle' }} fa-4x mb-3"></i>
                        <h2 class="mb-2">{{ $passed ? 'Congratulations!' : 'Keep Trying!' }}</h2>
                        <p class="lead mb-0">
                            @if($passed)
                            You passed the self-check assessment!
                            @else
                            You didn't pass this time, but you can try again!
                            @endif
                        </p>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Score Display -->
                    <div class="text-center py-4">
                        <div class="display-1 text-{{ $passed ? 'success' : 'danger' }}">
                            {{ number_format($percentage, 1) }}%
                        </div>
                        <p class="text-muted">
                            You scored <strong>{{ $score }}</strong> out of <strong>{{ $totalPoints }}</strong> points
                        </p>
                        <div class="progress mx-auto" style="height: 30px; max-width: 400px;">
                            <div class="progress-bar bg-{{ $passed ? 'success' : 'danger' }}"
                                 role="progressbar"
                                 style="width: {{ $percentage }}%">
                                {{ number_format($percentage, 1) }}%
                            </div>
                        </div>
                        @if($selfCheck->passing_score)
                        <p class="text-muted mt-2">Passing score: {{ $selfCheck->passing_score }}%</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Detailed Results -->
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Detailed Results</h5>
                </div>
                <div class="card-body">
                    @foreach($results as $index => $result)
                    <div class="card mb-3 border-{{ $result['is_correct'] === true ? 'success' : ($result['is_correct'] === null ? 'warning' : 'danger') }}">
                        <div class="card-header bg-{{ $result['is_correct'] === true ? 'success' : ($result['is_correct'] === null ? 'warning' : 'danger') }} bg-opacity-10">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    @if($result['is_correct'] === true)
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    @elseif($result['is_correct'] === null)
                                    <i class="fas fa-clock text-warning me-2"></i>
                                    @else
                                    <i class="fas fa-times-circle text-danger me-2"></i>
                                    @endif
                                    Question {{ $index + 1 }}
                                </h6>
                                <span class="badge bg-{{ $result['is_correct'] === true ? 'success' : ($result['is_correct'] === null ? 'warning' : 'secondary') }}">
                                    {{ $result['points_earned'] }} / {{ $result['question']->points }} pts
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="fw-bold">{{ $result['question']->question_text }}</p>

                            @php $qType = $result['question']->question_type; @endphp

                            @switch($qType)
                                {{-- Multiple Choice / True False / Image Choice --}}
                                @case('multiple_choice')
                                @case('true_false')
                                @case('image_choice')
                                    @php
                                        $options = $result['question']->options ?? [];
                                        $userIndex = $result['user_answer'];
                                        $correctIndex = $result['question']->correct_answer;
                                    @endphp
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Your Answer:</strong></p>
                                            <p class="{{ $result['is_correct'] ? 'text-success' : 'text-danger' }}">
                                                @if($qType === 'true_false')
                                                    {{ ucfirst($userIndex) ?: '(No answer)' }}
                                                @elseif(isset($options[$userIndex]))
                                                    {{ chr(65 + $userIndex) }}. {{ is_array($options[$userIndex]) ? ($options[$userIndex]['label'] ?? $options[$userIndex]) : $options[$userIndex] }}
                                                @else
                                                    (No answer)
                                                @endif
                                            </p>
                                        </div>
                                        @if(!$result['is_correct'])
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Correct Answer:</strong></p>
                                            <p class="text-success">
                                                @if($qType === 'true_false')
                                                    {{ ucfirst($correctIndex) }}
                                                @elseif(isset($options[$correctIndex]))
                                                    {{ chr(65 + $correctIndex) }}. {{ is_array($options[$correctIndex]) ? ($options[$correctIndex]['label'] ?? $options[$correctIndex]) : $options[$correctIndex] }}
                                                @endif
                                            </p>
                                        </div>
                                        @endif
                                    </div>
                                    @break

                                {{-- Multiple Select --}}
                                @case('multiple_select')
                                    @php
                                        $options = $result['question']->options ?? [];
                                        $userAnswers = is_array($result['user_answer']) ? $result['user_answer'] : [];
                                        $correctAnswers = json_decode($result['question']->correct_answer, true) ?? [];
                                    @endphp
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Your Selections:</strong></p>
                                            @if(empty($userAnswers))
                                                <p class="text-muted">(No answer)</p>
                                            @else
                                                @foreach($userAnswers as $idx)
                                                    <span class="badge {{ in_array($idx, $correctAnswers) ? 'bg-success' : 'bg-danger' }} me-1 mb-1">
                                                        {{ chr(65 + $idx) }}. {{ $options[$idx] ?? '' }}
                                                    </span>
                                                @endforeach
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Correct Answers:</strong></p>
                                            @foreach($correctAnswers as $idx)
                                                <span class="badge bg-success me-1 mb-1">
                                                    {{ chr(65 + $idx) }}. {{ $options[$idx] ?? '' }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                    @if($result['partial_credit'])
                                    <p class="text-info mt-2 small">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Partial credit: {{ number_format($result['partial_credit'] * 100, 0) }}%
                                    </p>
                                    @endif
                                    @break

                                {{-- Numeric / Slider --}}
                                @case('numeric')
                                @case('slider')
                                    @php
                                        $tolerance = $result['question']->options['tolerance'] ?? 0;
                                        $unit = $result['question']->options['unit'] ?? '';
                                    @endphp
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Your Answer:</strong></p>
                                            <p class="{{ $result['is_correct'] ? 'text-success' : 'text-danger' }}">
                                                {{ $result['user_answer'] ?: '(No answer)' }} {{ $unit }}
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Correct Answer:</strong></p>
                                            <p class="text-success">
                                                {{ $result['question']->correct_answer }} {{ $unit }}
                                                @if($tolerance > 0)
                                                    (± {{ $tolerance }})
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    @break

                                {{-- Fill Blank / Image Identification --}}
                                @case('fill_blank')
                                @case('image_identification')
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Your Answer:</strong></p>
                                            <p class="{{ $result['is_correct'] ? 'text-success' : 'text-danger' }}">
                                                {{ $result['user_answer'] ?: '(No answer)' }}
                                            </p>
                                        </div>
                                        @if(!$result['is_correct'])
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Acceptable Answers:</strong></p>
                                            <p class="text-success">{{ $result['question']->correct_answer }}</p>
                                        </div>
                                        @endif
                                    </div>
                                    @break

                                {{-- Matching --}}
                                @case('matching')
                                    @php
                                        $pairs = $result['question']->options['pairs'] ?? [];
                                        $userMatches = is_array($result['user_answer']) ? $result['user_answer'] : [];
                                    @endphp
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Column A</th>
                                                    <th>Your Match</th>
                                                    <th>Correct Match</th>
                                                    <th class="text-center">Result</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($pairs as $pairIndex => $pair)
                                                @php
                                                    $userMatchIndex = $userMatches[$pairIndex] ?? null;
                                                    $isMatch = $userMatchIndex !== null && (int)$userMatchIndex === $pairIndex;
                                                @endphp
                                                <tr>
                                                    <td>{{ $pair['left'] }}</td>
                                                    <td class="{{ $isMatch ? 'text-success' : 'text-danger' }}">
                                                        {{ $userMatchIndex !== null && isset($pairs[$userMatchIndex]) ? $pairs[$userMatchIndex]['right'] : '(Not matched)' }}
                                                    </td>
                                                    <td class="text-success">{{ $pair['right'] }}</td>
                                                    <td class="text-center">
                                                        <i class="fas {{ $isMatch ? 'fa-check text-success' : 'fa-times text-danger' }}"></i>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @if($result['partial_credit'])
                                    <p class="text-info small">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Partial credit: {{ number_format($result['partial_credit'] * 100, 0) }}%
                                    </p>
                                    @endif
                                    @break

                                {{-- Ordering --}}
                                @case('ordering')
                                    @php
                                        $items = $result['question']->options['items'] ?? [];
                                        $userOrder = is_array($result['user_answer']) ? $result['user_answer'] : [];
                                    @endphp
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Your Order:</strong></p>
                                            <ol class="mb-0">
                                                @foreach($userOrder as $idx)
                                                @php $isCorrectPosition = (int)$idx === array_search($items[$idx] ?? '', $items); @endphp
                                                <li class="{{ isset($items[$idx]) && $loop->index === (int)$idx ? 'text-success' : 'text-danger' }}">
                                                    {{ $items[$idx] ?? '?' }}
                                                </li>
                                                @endforeach
                                            </ol>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Correct Order:</strong></p>
                                            <ol class="mb-0 text-success">
                                                @foreach($items as $item)
                                                <li>{{ $item }}</li>
                                                @endforeach
                                            </ol>
                                        </div>
                                    </div>
                                    @if($result['partial_credit'])
                                    <p class="text-info small mt-2">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Partial credit: {{ number_format($result['partial_credit'] * 100, 0) }}%
                                    </p>
                                    @endif
                                    @break

                                {{-- Classification --}}
                                @case('classification')
                                    @php
                                        $categories = $result['question']->options['categories'] ?? [];
                                        $items = $result['question']->options['items'] ?? [];
                                        $correctMapping = $result['question']->options['item_categories'] ?? [];
                                        $userMapping = is_array($result['user_answer']) ? $result['user_answer'] : [];
                                    @endphp
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Item</th>
                                                    <th>Your Category</th>
                                                    <th>Correct Category</th>
                                                    <th class="text-center">Result</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($items as $itemIndex => $item)
                                                @php
                                                    $userCat = $userMapping[$itemIndex] ?? null;
                                                    $correctCat = $correctMapping[$itemIndex] ?? null;
                                                    $isCorrect = $userCat !== null && (string)$userCat === (string)$correctCat;
                                                @endphp
                                                <tr>
                                                    <td>{{ $item }}</td>
                                                    <td class="{{ $isCorrect ? 'text-success' : 'text-danger' }}">
                                                        {{ $userCat !== null && isset($categories[$userCat]) ? $categories[$userCat] : '(Not selected)' }}
                                                    </td>
                                                    <td class="text-success">{{ $categories[$correctCat] ?? '?' }}</td>
                                                    <td class="text-center">
                                                        <i class="fas {{ $isCorrect ? 'fa-check text-success' : 'fa-times text-danger' }}"></i>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @if($result['partial_credit'])
                                    <p class="text-info small">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Partial credit: {{ number_format($result['partial_credit'] * 100, 0) }}%
                                    </p>
                                    @endif
                                    @break

                                {{-- Hotspot --}}
                                @case('hotspot')
                                    @php
                                        $userCoords = is_array($result['user_answer']) ? $result['user_answer'] : [];
                                        $correctX = $result['question']->options['hotspot_x'] ?? 50;
                                        $correctY = $result['question']->options['hotspot_y'] ?? 50;
                                        $radius = $result['question']->options['hotspot_radius'] ?? 10;
                                    @endphp
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Your Click Position:</strong></p>
                                            <p class="{{ $result['is_correct'] ? 'text-success' : 'text-danger' }}">
                                                @if(!empty($userCoords['x']) && !empty($userCoords['y']))
                                                    X: {{ $userCoords['x'] }}%, Y: {{ $userCoords['y'] }}%
                                                @else
                                                    (No click recorded)
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Target Area:</strong></p>
                                            <p class="text-success">
                                                Center: X: {{ $correctX }}%, Y: {{ $correctY }}% (Radius: {{ $radius }}%)
                                            </p>
                                        </div>
                                    </div>
                                    @break

                                {{-- Image Labeling --}}
                                @case('image_labeling')
                                    @php
                                        $correctLabels = $result['question']->options['labels'] ?? [];
                                        $userLabels = is_array($result['user_answer']) ? $result['user_answer'] : [];
                                    @endphp
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Part</th>
                                                    <th>Your Label</th>
                                                    <th>Correct Label</th>
                                                    <th class="text-center">Result</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($correctLabels as $labelIndex => $correctLabel)
                                                @php
                                                    $userLabel = $userLabels[$labelIndex] ?? '';
                                                    $isCorrect = strtolower(trim($userLabel)) === strtolower(trim($correctLabel));
                                                @endphp
                                                <tr>
                                                    <td>{{ $labelIndex + 1 }}</td>
                                                    <td class="{{ $isCorrect ? 'text-success' : 'text-danger' }}">
                                                        {{ $userLabel ?: '(Empty)' }}
                                                    </td>
                                                    <td class="text-success">{{ $correctLabel }}</td>
                                                    <td class="text-center">
                                                        <i class="fas {{ $isCorrect ? 'fa-check text-success' : 'fa-times text-danger' }}"></i>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @if($result['partial_credit'])
                                    <p class="text-info small">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Partial credit: {{ number_format($result['partial_credit'] * 100, 0) }}%
                                    </p>
                                    @endif
                                    @break

                                {{-- Drag & Drop --}}
                                @case('drag_drop')
                                    @php
                                        $draggables = $result['question']->options['draggables'] ?? [];
                                        $dropzones = $result['question']->options['dropzones'] ?? [];
                                        $correctMapping = $result['question']->options['correct_mapping'] ?? [];
                                        $userMapping = is_array($result['user_answer']) ? $result['user_answer'] : [];
                                    @endphp
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Drop Zone</th>
                                                    <th>You Placed</th>
                                                    <th>Correct Item</th>
                                                    <th class="text-center">Result</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($dropzones as $zoneIndex => $zone)
                                                @php
                                                    $userItem = $userMapping[$zoneIndex] ?? null;
                                                    $correctItem = $correctMapping[$zoneIndex] ?? null;
                                                    $isCorrect = $userItem !== null && (string)$userItem === (string)$correctItem;
                                                @endphp
                                                <tr>
                                                    <td>{{ $zone }}</td>
                                                    <td class="{{ $isCorrect ? 'text-success' : 'text-danger' }}">
                                                        {{ $userItem !== null && isset($draggables[$userItem]) ? $draggables[$userItem] : '(Not placed)' }}
                                                    </td>
                                                    <td class="text-success">{{ $draggables[$correctItem] ?? '?' }}</td>
                                                    <td class="text-center">
                                                        <i class="fas {{ $isCorrect ? 'fa-check text-success' : 'fa-times text-danger' }}"></i>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @if($result['partial_credit'])
                                    <p class="text-info small">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Partial credit: {{ number_format($result['partial_credit'] * 100, 0) }}%
                                    </p>
                                    @endif
                                    @break

                                {{-- Short Answer / Essay / Audio / Video --}}
                                @case('short_answer')
                                @case('essay')
                                @case('audio_question')
                                @case('video_question')
                                    <div class="row">
                                        <div class="col-12">
                                            <p class="mb-1"><strong>Your Answer:</strong></p>
                                            <div class="p-3 bg-light rounded {{ $result['is_correct'] === true ? 'border-success' : ($result['is_correct'] === null ? 'border-warning' : 'border-danger') }}" style="border-left: 4px solid;">
                                                {{ $result['user_answer'] ?: '(No answer provided)' }}
                                            </div>
                                        </div>
                                    </div>
                                    @if($result['is_correct'] === null)
                                    <div class="alert alert-warning mb-0 mt-2">
                                        <i class="fas fa-info-circle me-2"></i>
                                        This answer requires manual grading by your instructor.
                                    </div>
                                    @elseif($result['partial_credit'] && $result['partial_credit'] < 1)
                                    <p class="text-info small mt-2">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Keyword match score: {{ number_format($result['partial_credit'] * 100, 0) }}%
                                    </p>
                                    @endif
                                    @break

                                {{-- Default --}}
                                @default
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Your Answer:</strong></p>
                                            <p class="{{ $result['is_correct'] === true ? 'text-success' : ($result['is_correct'] === null ? 'text-warning' : 'text-danger') }}">
                                                @if(is_array($result['user_answer']))
                                                    {{ json_encode($result['user_answer']) }}
                                                @else
                                                    {{ $result['user_answer'] ?: '(No answer provided)' }}
                                                @endif
                                            </p>
                                        </div>
                                        @if($result['question']->correct_answer && $result['is_correct'] === false)
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Correct Answer:</strong></p>
                                            <p class="text-success">{{ $result['question']->correct_answer }}</p>
                                        </div>
                                        @endif
                                    </div>
                            @endswitch

                            @if($result['question']->explanation)
                            <div class="alert alert-info mb-0 mt-3">
                                <i class="fas fa-lightbulb me-2"></i>
                                <strong>Explanation:</strong> {{ $result['question']->explanation }}
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Summary Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Summary</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between mb-2">
                            <span><i class="fas fa-check text-success me-2"></i>Correct</span>
                            <strong class="text-success">{{ collect($results)->where('is_correct', true)->count() }}</strong>
                        </li>
                        <li class="d-flex justify-content-between mb-2">
                            <span><i class="fas fa-times text-danger me-2"></i>Incorrect</span>
                            <strong class="text-danger">{{ collect($results)->where('is_correct', false)->count() }}</strong>
                        </li>
                        @if(collect($results)->whereNull('is_correct')->count() > 0)
                        <li class="d-flex justify-content-between mb-2">
                            <span><i class="fas fa-clock text-warning me-2"></i>Pending Review</span>
                            <strong class="text-warning">{{ collect($results)->whereNull('is_correct')->count() }}</strong>
                        </li>
                        @endif
                        <hr>
                        <li class="d-flex justify-content-between">
                            <span><strong>Total Questions</strong></span>
                            <strong>{{ count($results) }}</strong>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Self-Check Info -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Test Details</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>{{ $selfCheck->title }}</strong></p>
                    <p class="text-muted mb-2">{{ $selfCheck->check_number }}</p>
                    <small class="text-muted">Completed: {{ $submission->completed_at->format('M d, Y H:i') }}</small>
                </div>
            </div>

            <!-- Actions -->
            <div class="card shadow-sm">
                <div class="card-body">
                    @if(!$passed)
                    <a href="{{ route('self-checks.show', $selfCheck) }}" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-redo me-2"></i>Try Again
                    </a>
                    @endif
                    <a href="{{ route('information-sheets.show', ['module' => $selfCheck->informationSheet->module_id, 'informationSheet' => $selfCheck->informationSheet->id]) }}" class="btn btn-outline-primary w-100">
                        <i class="fas fa-arrow-left me-2"></i>Back to Information Sheet
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
