@extends('layouts.app')

@section('title', 'Performance Criteria')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h4 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Performance Criteria Checklist</h4>
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

                    <form action="{{ route('performance-criteria.store') }}" method="POST" id="performanceCriteriaForm">
                        @csrf
                        <input type="hidden" name="type" value="{{ $type }}">
                        <input type="hidden" name="related_id" value="{{ $relatedId }}">

                        <!-- Performance Criteria Items -->
                        <div class="mb-4">
                            <h5 class="mb-3">Evaluation Criteria</h5>
                            <div id="criteria-container">
                                <div class="card mb-3 criteria-card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-3">
                                            <h6 class="mb-0">Criterion #1</h6>
                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeCard(this)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Description <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="criteria[0][description]" placeholder="What is being evaluated" required>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label">Observed <span class="text-danger">*</span></label>
                                                <select class="form-select" name="criteria[0][observed]" required>
                                                    <option value="">Select...</option>
                                                    <option value="1">Yes</option>
                                                    <option value="0">No</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label">Remarks</label>
                                                <input type="text" class="form-control" name="criteria[0][remarks]" placeholder="Notes">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-success" onclick="addCriterion()">
                                <i class="fas fa-plus me-1"></i>Add Criterion
                            </button>
                        </div>

                        <!-- Common Performance Criteria Templates -->
                        <div class="mb-4">
                            <h6>Quick Add Common Criteria:</h6>
                            <div class="btn-group flex-wrap" role="group">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addCommonCriteria('safety')">
                                    <i class="fas fa-shield-alt me-1"></i>Safety Practices
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addCommonCriteria('tools')">
                                    <i class="fas fa-tools me-1"></i>Proper Tool Usage
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addCommonCriteria('procedure')">
                                    <i class="fas fa-list-ol me-1"></i>Correct Procedure
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addCommonCriteria('quality')">
                                    <i class="fas fa-check-double me-1"></i>Quality of Work
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addCommonCriteria('time')">
                                    <i class="fas fa-clock me-1"></i>Time Management
                                </button>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('courses.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-dark">
                                <i class="fas fa-save me-1"></i>Submit Performance Criteria
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let criterionCount = 1;

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

const commonCriteria = {
    safety: [
        'Wore appropriate PPE (safety glasses, gloves, etc.)',
        'Followed safety protocols before starting work',
        'Maintained clean and organized workspace',
        'Properly handled hazardous materials'
    ],
    tools: [
        'Selected correct tools for the task',
        'Used tools properly and safely',
        'Returned tools to proper storage after use',
        'Reported damaged or missing tools'
    ],
    procedure: [
        'Followed the correct sequence of steps',
        'Read and understood instructions before starting',
        'Asked questions when unsure',
        'Completed all required steps'
    ],
    quality: [
        'Work meets quality standards',
        'Finished product functions correctly',
        'No visible defects or errors',
        'Proper soldering/connections'
    ],
    time: [
        'Completed task within allocated time',
        'Worked efficiently without wasting time',
        'Prioritized tasks appropriately',
        'Met deadline requirements'
    ]
};

function addCommonCriteria(type) {
    const criteria = commonCriteria[type];
    criteria.forEach(criterion => {
        addCriterion(criterion);
    });
}
</script>
@endsection
