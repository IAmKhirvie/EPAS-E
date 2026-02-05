@extends('layouts.app')

@section('title', 'Create Homework')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-book-open me-2"></i>Create Homework</h4>
                        <a href="{{ route('information-sheets.show', ['module' => $informationSheet->module_id, 'informationSheet' => $informationSheet->id]) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        <strong>Information Sheet:</strong> {{ $informationSheet->title }}
                    </p>

                    <form action="{{ route('homeworks.store', $informationSheet) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="homework_number" class="form-label">Homework Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('homework_number') is-invalid @enderror" id="homework_number" name="homework_number" value="{{ old('homework_number') }}" placeholder="e.g., HW-1.1" required>
                                @error('homework_number')
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

                        <div class="mb-4">
                            <label for="instructions" class="form-label">Instructions <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('instructions') is-invalid @enderror" id="instructions" name="instructions" rows="4" required>{{ old('instructions') }}</textarea>
                            @error('instructions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('due_date') is-invalid @enderror" id="due_date" name="due_date" value="{{ old('due_date') }}" required>
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="max_points" class="form-label">Maximum Points <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('max_points') is-invalid @enderror" id="max_points" name="max_points" value="{{ old('max_points', 100) }}" min="1" required>
                                @error('max_points')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Requirements -->
                        <div class="mb-4">
                            <label class="form-label">Requirements <span class="text-danger">*</span></label>
                            <div id="requirements-container">
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="requirements[]" placeholder="Enter requirement" required>
                                    <button type="button" class="btn btn-outline-danger" onclick="removeItem(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addItem('requirements-container', 'requirements[]', 'Enter requirement')">
                                <i class="fas fa-plus me-1"></i>Add Requirement
                            </button>
                        </div>

                        <!-- Submission Guidelines -->
                        <div class="mb-4">
                            <label class="form-label">Submission Guidelines <span class="text-danger">*</span></label>
                            <div id="guidelines-container">
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="submission_guidelines[]" placeholder="Enter submission guideline" required>
                                    <button type="button" class="btn btn-outline-danger" onclick="removeItem(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addItem('guidelines-container', 'submission_guidelines[]', 'Enter submission guideline')">
                                <i class="fas fa-plus me-1"></i>Add Guideline
                            </button>
                        </div>

                        <!-- Reference Images -->
                        <div class="mb-4">
                            <label for="reference_images" class="form-label">Reference Images (Optional)</label>
                            <input type="file" class="form-control @error('reference_images') is-invalid @enderror" id="reference_images" name="reference_images[]" accept="image/*" multiple>
                            <small class="text-muted">You can upload multiple images</small>
                            @error('reference_images')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('information-sheets.show', ['module' => $informationSheet->module_id, 'informationSheet' => $informationSheet->id]) }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-save me-1"></i>Create Homework
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Delegate to shared DynamicForm utility
function addItem(containerId, inputName, placeholder) {
    DynamicForm.addItem(containerId, inputName, placeholder);
}

function removeItem(button) {
    DynamicForm.removeItem(button);
}
</script>
@endsection
