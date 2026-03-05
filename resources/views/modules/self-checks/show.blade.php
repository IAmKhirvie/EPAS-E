@extends('layouts.app')

@section('title', $selfCheck->title)

@section('content')
<div class="content-area sc-content-reset">
    <nav aria-label="breadcrumb" class="mb-2">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('content.management') }}">Content</a></li>
            <li class="breadcrumb-item active">{{ $selfCheck->title }}</li>
        </ol>
    </nav>

    {{-- Header --}}
    <div class="sc-header">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h4 class="mb-1">
                    <i class="fas fa-clipboard-check me-2"></i>{{ $selfCheck->title }}
                    @if($selfCheck->file_path)
                    <a href="{{ route('self-checks.download', $selfCheck) }}" class="badge bg-secondary text-decoration-none ms-2" style="font-size: 0.6em; vertical-align: middle;">
                        <i class="fas fa-paperclip me-1"></i>{{ $selfCheck->original_filename }}
                    </a>
                    @endif
                </h4>
                <p class="text-muted mb-0">{{ $selfCheck->check_number }}</p>
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
        @if($selfCheck->description)
        <p class="mt-2 mb-0" style="color: var(--cb-text-label, #495057);">{{ $selfCheck->description }}</p>
        @endif
    </div>

    @php
        $attemptCount = 0;
        $attemptsExhausted = false;
        if (auth()->user()->role === \App\Constants\Roles::STUDENT) {
            $attemptCount = $selfCheck->submissions()->where('user_id', auth()->id())->count();
            $attemptsExhausted = $selfCheck->max_attempts !== null && $attemptCount >= $selfCheck->max_attempts;
        }
    @endphp

    {{-- Two-column: Questions + Sidebar --}}
    <div class="sc-layout">
        {{-- Left: Questions (full-width feel) --}}
        <div class="sc-questions">

            {{-- Instructions --}}
            @if($selfCheck->instructions)
            <div class="sc-instructions">
                <i class="fas fa-info-circle me-1"></i>
                {{ $selfCheck->instructions }}
            </div>
            @endif

            {{-- Document Viewer --}}
            @if($selfCheck->document_content)
            <div class="doc-viewer" id="docViewer">
                <div class="doc-viewer__page" id="docPage">
                    <div id="docContent">
                        {!! $selfCheck->document_content !!}
                    </div>
                    <div class="doc-viewer__fade-bottom" id="docFade"></div>
                </div>
                <div class="doc-viewer__nav" id="docNav">
                    <button class="doc-viewer__nav-btn" id="docPrev" title="Previous page">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <span class="doc-viewer__page-info" id="docPageInfo">Page 1 of 1</span>
                    <button class="doc-viewer__nav-btn" id="docNext" title="Next page">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
            @elseif($selfCheck->file_path)
            <div class="doc-viewer" style="padding-bottom: 1.5rem;">
                <div class="doc-viewer__page d-flex flex-column align-items-center justify-content-center text-center" style="min-height: 200px;">
                    <i class="fas fa-file-{{ str_contains($selfCheck->original_filename ?? '', '.pdf') ? 'pdf text-danger' : 'alt text-secondary' }}" style="font-size: 2.5rem; margin-bottom: 0.75rem;"></i>
                    <p class="mb-2"><strong>{{ $selfCheck->original_filename }}</strong></p>
                    <a href="{{ route('self-checks.download', $selfCheck) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-download me-1"></i>Download to View
                    </a>
                </div>
            </div>
            @endif

            {{-- ═══════ Student: Quiz Questions ═══════ --}}
            @if(auth()->user()->role === 'student')

                @if($selfCheck->due_date && now()->gt($selfCheck->due_date))
                <div class="alert alert-danger mt-3">
                    <i class="fas fa-clock me-2"></i>
                    <strong>Deadline passed.</strong> This self-check was due {{ $selfCheck->due_date->format('M d, Y h:i A') }}. Submissions are no longer accepted.
                </div>
                @elseif($attemptsExhausted)
                <div class="alert alert-warning mt-3">
                    <i class="fas fa-ban me-2"></i>
                    <strong>No attempts remaining.</strong> You have used all {{ $selfCheck->max_attempts }} attempt(s) for this self-check.
                </div>
                @else

                @if($selfCheck->time_limit)
                <div class="assessment-timer" id="assessmentTimer">
                    <i class="fas fa-stopwatch me-1"></i>
                    <span id="timerDisplay">{{ $selfCheck->time_limit }}:00</span>
                </div>
                @endif

                <form action="{{ route('self-checks.submit', $selfCheck) }}" method="POST" id="selfCheckForm">
                    @csrf
                    @foreach($selfCheck->questions->sortBy('order') as $index => $question)
                    <div class="sc-question-card" data-question-type="{{ $question->question_type }}">
                        <div class="sc-question-card__header">
                            <div class="d-flex align-items-center gap-2">
                                <span class="sc-question-card__number">{{ $index + 1 }}</span>
                                <span class="badge bg-{{ getQuestionTypeBadgeColor($question->question_type) }}">
                                    {{ formatQuestionType($question->question_type) }}
                                </span>
                            </div>
                            <span class="badge bg-primary">{{ $question->points }} pt(s)</span>
                        </div>
                        <div class="sc-question-card__body">
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
                </form>
                @endif

            @else
            {{-- ═══════ Instructor/Admin: Preview ═══════ --}}
            @foreach($selfCheck->questions->sortBy('order') as $index => $question)
            <div class="sc-question-card">
                <div class="sc-question-card__header">
                    <div class="d-flex align-items-center gap-2">
                        <span class="sc-question-card__number">{{ $index + 1 }}</span>
                        <span class="badge bg-{{ getQuestionTypeBadgeColor($question->question_type) }}">
                            {{ formatQuestionType($question->question_type) }}
                        </span>
                    </div>
                    <span class="badge bg-primary">{{ $question->points }} pts</span>
                </div>
                <div class="sc-question-card__body">
                    <p class="mb-2"><strong>{{ $question->question_text }}</strong></p>

                    @if($question->question_type === 'multiple_choice' || $question->question_type === 'image_choice')
                        @php $options = array_filter($question->options ?? [], fn($v, $k) => is_int($k), ARRAY_FILTER_USE_BOTH); @endphp
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
                                <strong class="d-block mb-1" style="font-size: 0.8rem; color: #6c757d;">Column A</strong>
                                @foreach($pairs as $pair)
                                <div class="p-2 mb-1" style="border: 1px solid #dee2e6; border-radius: 6px;">{{ $pair['left'] }}</div>
                                @endforeach
                            </div>
                            <div class="col-2 text-center d-flex align-items-center justify-content-center">
                                <i class="fas fa-arrows-alt-h text-muted"></i>
                            </div>
                            <div class="col-5">
                                <strong class="d-block mb-1" style="font-size: 0.8rem; color: #6c757d;">Column B</strong>
                                @foreach($pairs as $pair)
                                <div class="p-2 mb-1" style="border: 1px solid #d4edda; border-radius: 6px; background: #f8fff9;">{{ $pair['right'] }}</div>
                                @endforeach
                            </div>
                        </div>
                    @elseif($question->question_type === 'ordering')
                        @php $items = $question->options['items'] ?? []; @endphp
                        <div class="ms-3 mt-2">
                            <strong class="d-block mb-1" style="font-size: 0.8rem; color: #6c757d;">Correct Order:</strong>
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

        {{-- Right: Sticky Sidebar --}}
        <div class="sc-sidebar">
            <div class="sc-sidebar__title">Self-Check Info</div>

            {{-- Test Details --}}
            <div class="sc-sidebar__group">
                <div class="sc-sidebar__label"><i class="fas fa-info-circle me-1"></i>Test Details</div>
                <div class="sc-sidebar__row">
                    <span>Questions</span>
                    <strong>{{ $selfCheck->questions->count() }}</strong>
                </div>
                <div class="sc-sidebar__row">
                    <span>Total Points</span>
                    <strong>{{ $selfCheck->total_points }}</strong>
                </div>
                @if($selfCheck->time_limit)
                <div class="sc-sidebar__row">
                    <span>Time Limit</span>
                    <strong>{{ $selfCheck->time_limit }} min</strong>
                </div>
                @endif
                @if($selfCheck->due_date)
                <div class="sc-sidebar__row">
                    <span>Deadline</span>
                    <strong class="{{ now()->gt($selfCheck->due_date) ? 'text-danger' : '' }}">
                        {{ $selfCheck->due_date->format('M d, Y h:i A') }}
                    </strong>
                </div>
                @endif
                @if($selfCheck->passing_score)
                <div class="sc-sidebar__row">
                    <span>Passing Score</span>
                    <strong>{{ $selfCheck->passing_score }}%</strong>
                </div>
                @endif
                @if(auth()->user()->role === \App\Constants\Roles::STUDENT)
                <div class="sc-sidebar__row">
                    <span>Attempts</span>
                    <strong>{{ $attemptCount }} / {{ $selfCheck->max_attempts ?? 'Unlimited' }}</strong>
                </div>
                @endif
            </div>

            {{-- Question Types --}}
            @php $typeCounts = $selfCheck->questions->groupBy('question_type')->map->count(); @endphp
            @if($typeCounts->count() > 0)
            <div class="sc-sidebar__group">
                <div class="sc-sidebar__label"><i class="fas fa-list me-1"></i>Question Types</div>
                @foreach($typeCounts as $type => $count)
                <div class="sc-sidebar__row">
                    <span>{{ formatQuestionType($type) }}</span>
                    <strong>{{ $count }}</strong>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Submit Button (students only) --}}
            @if(auth()->user()->role === 'student' && !($selfCheck->due_date && now()->gt($selfCheck->due_date)) && !($attemptsExhausted))
            <div class="sc-sidebar__group">
                <button type="submit" form="selfCheckForm" class="btn btn-success w-100">
                    <i class="fas fa-paper-plane me-1"></i>Submit Answers
                </button>
                <small class="text-muted d-block text-center mt-1">Answer all questions first</small>
            </div>
            @endif

            {{-- Submissions (Instructors/Admins) --}}
            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'instructor')
            <div class="sc-sidebar__group">
                <div class="sc-sidebar__label"><i class="fas fa-users me-1"></i>Submissions</div>
                @if($selfCheck->submissions->count() > 0)
                    @foreach($selfCheck->submissions->take(5) as $submission)
                    <div class="sc-sidebar__row">
                        <span>{{ $submission->user->first_name }} {{ $submission->user->last_name }}</span>
                        <span class="badge bg-{{ $submission->passed ? 'success' : 'danger' }}">{{ number_format($submission->percentage, 1) }}%</span>
                    </div>
                    @endforeach
                    @if($selfCheck->submissions->count() > 5)
                    <div class="text-center text-muted" style="font-size: 0.8rem; padding: 0.25rem;">
                        + {{ $selfCheck->submissions->count() - 5 }} more
                    </div>
                    @endif
                @else
                <div class="text-muted" style="font-size: 0.85rem;">No submissions yet</div>
                @endif
            </div>
            @endif

            {{-- Back --}}
            <a href="{{ route('information-sheets.show', ['module' => $selfCheck->informationSheet->module_id, 'informationSheet' => $selfCheck->informationSheet->id]) }}"
               class="btn btn-outline-secondary w-100 btn-sm mt-auto">
                <i class="fas fa-arrow-left me-1"></i>Back to Info Sheet
            </a>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Strip the content-area visual container but keep flex/overflow structure */
.sc-content-reset {
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
    border-radius: 0 !important;
    padding: 0 !important;
}

/* ═══════ Self-Check Layout ═══════ */
.sc-header {
    background: var(--cb-surface, #fff);
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    padding: 1.25rem 1.5rem;
    margin-bottom: 1rem;
}
.sc-layout {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 1.25rem;
    align-items: start;
}
.sc-questions {
    min-width: 0;
    overflow: hidden;
}
.sc-instructions {
    background: #e3f2fd;
    border-left: 4px solid #2196f3;
    border-radius: 6px;
    padding: 0.85rem 1rem;
    color: #1565c0;
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

/* Question Cards — lightweight, no heavy container */
.sc-question-card {
    background: var(--cb-surface, #fff);
    border-radius: 10px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    margin-bottom: 1rem;
    overflow: hidden;
    transition: box-shadow 0.2s;
}
.sc-question-card:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.sc-question-card__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 1.25rem;
    background: var(--cb-surface-alt, #f8f9fa);
    border-bottom: 1px solid rgba(0,0,0,0.05);
}
.sc-question-card__number {
    width: 28px;
    height: 28px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: var(--primary, #4361ee);
    color: #fff;
    border-radius: 50%;
    font-weight: 700;
    font-size: 0.8rem;
}
.sc-question-card__body {
    padding: 1.25rem;
}

/* Sticky Sidebar */
.sc-sidebar {
    position: sticky;
    top: 1rem;
    background: var(--cb-surface, #fff);
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    padding: 1.25rem;
    display: flex;
    flex-direction: column;
    max-height: calc(100vh - 2rem);
    overflow-y: auto;
}
.sc-sidebar__title {
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--cb-text-hint, #6c757d);
    padding-bottom: 0.5rem;
    margin-bottom: 0.75rem;
    border-bottom: 1px solid var(--cb-border, #e9ecef);
}
.sc-sidebar__group {
    margin-bottom: 1rem;
}
.sc-sidebar__label {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--cb-text-hint, #6c757d);
    margin-bottom: 0.5rem;
}
.sc-sidebar__row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.35rem 0;
    font-size: 0.85rem;
    border-bottom: 1px solid rgba(0,0,0,0.04);
}
.sc-sidebar__row:last-child {
    border-bottom: none;
}

/* Document viewer */
.doc-viewer { position: relative; background: #f0f0f0; border-radius: 8px; padding: 1.5rem 1.5rem 0; margin-bottom: 1rem; }
.doc-viewer__page { background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border-radius: 4px; padding: 2.5rem 2rem; height: 500px; max-height: 500px; overflow: hidden !important; position: relative; line-height: 1.8; font-size: 0.95rem; word-wrap: break-word; overflow-wrap: break-word; }
.doc-viewer__page img { max-width: 100% !important; height: auto !important; }
.doc-viewer__page * { max-width: 100% !important; box-sizing: border-box; }
.doc-viewer__page table { table-layout: fixed; word-wrap: break-word; }
.doc-viewer__page--scrollable { overflow-y: auto !important; overflow-x: hidden !important; scroll-behavior: smooth; }
.doc-viewer__page h1, .doc-viewer__page h2, .doc-viewer__page h3 { margin-top: 0.8em; margin-bottom: 0.4em; color: #1a1a1a; }
.doc-viewer__page table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
.doc-viewer__page table th, .doc-viewer__page table td { border: 1px solid #dee2e6; padding: 0.5rem 0.75rem; font-size: 0.9rem; }
.doc-viewer__page table th { background: #f8f9fa; font-weight: 600; }
.doc-viewer__page ul, .doc-viewer__page ol { padding-left: 1.5rem; }
.doc-viewer__page p { margin-bottom: 0.75rem; }
.doc-viewer__nav { display: flex; align-items: center; justify-content: center; gap: 1rem; padding: 0.75rem 0; user-select: none; }
.doc-viewer__nav-btn { width: 36px; height: 36px; border-radius: 50%; border: 1px solid #dee2e6; background: #fff; color: #495057; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s; font-size: 0.85rem; }
.doc-viewer__nav-btn:hover:not(:disabled) { background: #e9ecef; border-color: #adb5bd; }
.doc-viewer__nav-btn:disabled { opacity: 0.35; cursor: default; }
.doc-viewer__page-info { font-size: 0.85rem; color: #6c757d; min-width: 90px; text-align: center; }
.doc-viewer__fade-bottom { position: absolute; bottom: 0; left: 0; right: 0; height: 40px; background: linear-gradient(transparent, #fff); pointer-events: none; border-radius: 0 0 4px 4px; }

/* Timer */
.assessment-timer {
    position: fixed; top: 1rem; right: 1rem; z-index: 1050;
    background: #1a1a2e; color: #fff;
    padding: 0.6rem 1.2rem; border-radius: 50px;
    font-weight: 700; font-size: 1.1rem; font-family: 'Courier New', monospace;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    display: flex; align-items: center; gap: 0.5rem;
    transition: background 0.3s;
}
.assessment-timer.warning { background: #f59e0b; color: #1a1a2e; }
.assessment-timer.danger { background: #dc3545; animation: timer-pulse 1s infinite; }
@keyframes timer-pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.7; } }

/* Dark mode */
.dark-mode .sc-header,
.dark-mode .sc-question-card,
.dark-mode .sc-sidebar { background: var(--card-bg); color: var(--card-text); }
.dark-mode .sc-question-card__header { background: rgba(255,255,255,0.03); }
.dark-mode .sc-instructions { background: rgba(33,150,243,0.1); color: #90caf9; }

/* Responsive */
@media (max-width: 992px) {
    .sc-layout { grid-template-columns: 1fr; }
    .sc-sidebar { position: static; max-height: none; order: -1; }
}
</style>
@endpush

@push('scripts')
@if($selfCheck->document_content)
<script>
document.addEventListener('DOMContentLoaded', function() {
    var page = document.getElementById('docPage');
    if (!page) return;
    var content = document.getElementById('docContent');
    var fade = document.getElementById('docFade');
    var nav = document.getElementById('docNav');
    var prevBtn = document.getElementById('docPrev');
    var nextBtn = document.getElementById('docNext');
    var pageInfo = document.getElementById('docPageInfo');
    var PAGE_HEIGHT, totalHeight, totalPages, currentPage = 1;

    function update() {
        totalHeight = content.scrollHeight;
        PAGE_HEIGHT = page.clientHeight;
        totalPages = Math.max(1, Math.ceil(totalHeight / PAGE_HEIGHT));
        if (totalPages <= 1) { nav.style.display = 'none'; fade.style.display = 'none'; return; }
        nav.style.display = 'flex';
        pageInfo.textContent = 'Page ' + currentPage + ' of ' + totalPages;
        prevBtn.disabled = currentPage <= 1;
        nextBtn.disabled = currentPage >= totalPages;
        page.scrollTop = (currentPage - 1) * PAGE_HEIGHT;
        fade.style.display = currentPage < totalPages ? 'block' : 'none';
    }
    page.classList.add('doc-viewer__page--scrollable');
    page.style.scrollbarWidth = 'none';
    var s = document.createElement('style');
    s.textContent = '#docPage::-webkit-scrollbar{display:none}';
    document.head.appendChild(s);
    prevBtn.addEventListener('click', function() { if (currentPage > 1) { currentPage--; update(); } });
    nextBtn.addEventListener('click', function() { if (currentPage < totalPages) { currentPage++; update(); } });
    document.addEventListener('keydown', function(e) {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT') return;
        if (e.key === 'ArrowLeft' && currentPage > 1) { currentPage--; update(); }
        if (e.key === 'ArrowRight' && currentPage < totalPages) { currentPage++; update(); }
    });
    update();
    window.addEventListener('resize', update);
});
</script>
@endif

@if($selfCheck->time_limit && auth()->user()->role === 'student' && !($selfCheck->due_date && now()->gt($selfCheck->due_date)))
<script>
document.addEventListener('DOMContentLoaded', function() {
    var timerEl = document.getElementById('assessmentTimer');
    var displayEl = document.getElementById('timerDisplay');
    var form = document.getElementById('selfCheckForm');
    if (!timerEl || !displayEl || !form) return;

    var totalSeconds = {{ $selfCheck->time_limit }} * 60;
    var storageKey = 'sc_timer_{{ $selfCheck->id }}_' + '{{ auth()->id() }}';

    var saved = localStorage.getItem(storageKey);
    if (saved) {
        var elapsed = Math.floor((Date.now() - parseInt(saved)) / 1000);
        totalSeconds = Math.max(0, totalSeconds - elapsed);
    } else {
        localStorage.setItem(storageKey, Date.now().toString());
    }

    var submitted = false;

    function tick() {
        if (totalSeconds <= 0) {
            displayEl.textContent = '0:00';
            timerEl.className = 'assessment-timer danger';
            if (!submitted) {
                submitted = true;
                localStorage.removeItem(storageKey);
                alert('Time is up! Your answers will be submitted automatically.');
                form.submit();
            }
            return;
        }
        var m = Math.floor(totalSeconds / 60);
        var s = totalSeconds % 60;
        displayEl.textContent = m + ':' + (s < 10 ? '0' : '') + s;
        var pct = totalSeconds / ({{ $selfCheck->time_limit }} * 60);
        if (pct <= 0.1) { timerEl.className = 'assessment-timer danger'; }
        else if (pct <= 0.25) { timerEl.className = 'assessment-timer warning'; }
        totalSeconds--;
        setTimeout(tick, 1000);
    }
    tick();
    form.addEventListener('submit', function() { submitted = true; localStorage.removeItem(storageKey); });
});
</script>
@endif
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
