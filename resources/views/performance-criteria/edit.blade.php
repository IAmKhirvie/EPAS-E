@extends('layouts.app')

@section('title', 'Edit Performance Criteria')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Performance Criteria</h4>
                </div>
                <div class="card-body">
                    @if($taskSheet)
                    <div class="alert alert-info">
                        <strong>Task Sheet:</strong> {{ $taskSheet->title }} ({{ $taskSheet->task_number }})
                    </div>
                    @elseif($jobSheet)
                    <div class="alert alert-info">
                        <strong>Job Sheet:</strong> {{ $jobSheet->title }} ({{ $jobSheet->job_number }})
                    </div>
                    @endif

                    <!-- Current Score -->
                    @if($performanceCriteria->score !== null)
                    <div class="alert alert-{{ $performanceCriteria->score >= 80 ? 'success' : ($performanceCriteria->score >= 60 ? 'warning' : 'danger') }}">
                        <strong>Current Score:</strong> {{ number_format($performanceCriteria->score, 1) }}%
                    </div>
                    @endif

                    <form action="{{ route('performance-criteria.update', $performanceCriteria) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Performance Criteria Items -->
                        <div class="mb-4">
                            <h5 class="mb-3">Evaluation Criteria</h5>
                            <div id="criteria-container">
                                @php $criteria = json_decode($performanceCriteria->criteria, true) ?? []; @endphp
                                @foreach($criteria as $index => $criterion)
                                <div class="card mb-3 criteria-card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-3">
                                            <h6 class="mb-0">Criterion #{{ $index + 1 }}</h6>
                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeCard(this)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Description <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="criteria[{{ $index }}][description]" value="{{ $criterion['description'] ?? '' }}" required>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label">Observed <span class="text-danger">*</span></label>
                                                <select class="form-select" name="criteria[{{ $index }}][observed]" required>
                                                    <option value="">Select...</option>
                                                    <option value="1" {{ ($criterion['observed'] ?? '') == '1' || ($criterion['observed'] ?? '') === true ? 'selected' : '' }}>Yes</option>
                                                    <option value="0" {{ ($criterion['observed'] ?? '') == '0' || ($criterion['observed'] ?? '') === false ? 'selected' : '' }}>No</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label">Remarks</label>
                                                <input type="text" class="form-control" name="criteria[{{ $index }}][remarks]" value="{{ $criterion['remarks'] ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-outline-success" onclick="addCriterion()">
                                <i class="fas fa-plus me-1"></i>Add Criterion
                            </button>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('courses.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-1"></i>Update Performance Criteria
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let criterionCount = {{ count($criteria) }};

function addCriterion(description = '') {
    const container = document.getElementById('criteria-container');
    const cardCount = container.querySelectorAll('.criteria-card').length;

    const card = document.createElement('div');
    card.className = 'card mb-3 criteria-card';
    card.innerHTML = `
        <div class="card-body">
            <div class="d-flex justify-content-between mb-3">
                <h6 class="mb-0">Criterion #${cardCount + 1}</h6>
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeCard(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Description <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="criteria[${criterionCount}][description]" value="${description}" placeholder="What is being evaluated" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Observed <span class="text-danger">*</span></label>
                    <select class="form-select" name="criteria[${criterionCount}][observed]" required>
                        <option value="">Select...</option>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Remarks</label>
                    <input type="text" class="form-control" name="criteria[${criterionCount}][remarks]" placeholder="Notes">
                </div>
            </div>
        </div>
    `;
    container.appendChild(card);
    criterionCount++;
}

function removeCard(button) {
    const card = button.closest('.criteria-card');
    if (document.querySelectorAll('.criteria-card').length > 1) {
        card.remove();
        renumberCards();
    }
}

function renumberCards() {
    const cards = document.querySelectorAll('.criteria-card');
    cards.forEach((card, index) => {
        card.querySelector('h6').textContent = `Criterion #${index + 1}`;
    });
}
</script>
@endsection
