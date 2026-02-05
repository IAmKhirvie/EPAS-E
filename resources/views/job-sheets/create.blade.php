@extends('layouts.app')

@section('title', 'Create Job Sheet')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-hard-hat me-2"></i>Create Job Sheet</h4>
                        <a href="{{ route('information-sheets.show', ['module' => $informationSheet->module_id, 'informationSheet' => $informationSheet->id]) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        <strong>Information Sheet:</strong> {{ $informationSheet->title }}
                    </p>

                    <form action="{{ route('job-sheets.store', $informationSheet) }}" method="POST" enctype="multipart/form-data" id="jobSheetForm">
                        @csrf

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="job_number" class="form-label">Job Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('job_number') is-invalid @enderror" id="job_number" name="job_number" value="{{ old('job_number') }}" placeholder="e.g., JS-1.1" required>
                                @error('job_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-8">
                                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Objectives -->
                        <div class="mb-4">
                            <label class="form-label">Objectives <span class="text-danger">*</span></label>
                            <div id="objectives-container">
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="objectives[]" placeholder="Enter objective" required>
                                    <button type="button" class="btn btn-outline-danger" onclick="removeItem(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addItem('objectives-container', 'objectives[]', 'Enter objective')">
                                <i class="fas fa-plus me-1"></i>Add Objective
                            </button>
                        </div>

                        <!-- Tools Required -->
                        <div class="mb-4">
                            <label class="form-label">Tools Required <span class="text-danger">*</span></label>
                            <div id="tools-container">
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="tools_required[]" placeholder="Enter tool" required>
                                    <button type="button" class="btn btn-outline-danger" onclick="removeItem(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addItem('tools-container', 'tools_required[]', 'Enter tool')">
                                <i class="fas fa-plus me-1"></i>Add Tool
                            </button>
                        </div>

                        <!-- Safety Requirements -->
                        <div class="mb-4">
                            <label class="form-label">Safety Requirements <span class="text-danger">*</span></label>
                            <div id="safety-container">
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="safety_requirements[]" placeholder="Enter safety requirement" required>
                                    <button type="button" class="btn btn-outline-danger" onclick="removeItem(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addItem('safety-container', 'safety_requirements[]', 'Enter safety requirement')">
                                <i class="fas fa-plus me-1"></i>Add Safety Requirement
                            </button>
                        </div>

                        <!-- Reference Materials -->
                        <div class="mb-4">
                            <label class="form-label">Reference Materials</label>
                            <div id="references-container">
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="reference_materials[]" placeholder="Enter reference material">
                                    <button type="button" class="btn btn-outline-danger" onclick="removeItem(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addItem('references-container', 'reference_materials[]', 'Enter reference material')">
                                <i class="fas fa-plus me-1"></i>Add Reference
                            </button>
                        </div>

                        <!-- Steps -->
                        <div class="mb-4">
                            <label class="form-label">Job Steps <span class="text-danger">*</span></label>
                            <div id="steps-container">
                                <div class="card mb-3 step-card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-3">
                                            <h6 class="mb-0">Step #1</h6>
                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeStep(this)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        <input type="hidden" name="steps[0][step_number]" value="1">
                                        <div class="mb-3">
                                            <label class="form-label">Instruction <span class="text-danger">*</span></label>
                                            <textarea class="form-control" name="steps[0][instruction]" rows="2" required></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Expected Outcome <span class="text-danger">*</span></label>
                                            <textarea class="form-control" name="steps[0][expected_outcome]" rows="2" required></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Step Image (Optional)</label>
                                            <input type="file" class="form-control" name="steps[0][image]" accept="image/*">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-success" onclick="addStep()">
                                <i class="fas fa-plus me-1"></i>Add Step
                            </button>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('information-sheets.show', ['module' => $informationSheet->module_id, 'informationSheet' => $informationSheet->id]) }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i>Create Job Sheet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let stepCount = 1;

// Delegate to shared DynamicForm utility
function addItem(containerId, inputName, placeholder) {
    DynamicForm.addItem(containerId, inputName, placeholder);
}

function removeItem(button) {
    DynamicForm.removeItem(button);
}

function addStep() {
    DynamicForm.addCard('steps-container', 'step-card', (count) => `
        <div class="card-body">
            <div class="d-flex justify-content-between mb-3">
                <h6 class="mb-0">Step #${count + 1}</h6>
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeStep(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <input type="hidden" name="steps[${count}][step_number]" value="${count + 1}">
            <div class="mb-3">
                <label class="form-label">Instruction <span class="text-danger">*</span></label>
                <textarea class="form-control" name="steps[${count}][instruction]" rows="2" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Expected Outcome <span class="text-danger">*</span></label>
                <textarea class="form-control" name="steps[${count}][expected_outcome]" rows="2" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Step Image (Optional)</label>
                <input type="file" class="form-control" name="steps[${count}][image]" accept="image/*">
            </div>
        </div>
    `);
    stepCount++;
}

function removeStep(button) {
    DynamicForm.removeCard(button, 'step-card', 'Step');
}
</script>
@endsection
