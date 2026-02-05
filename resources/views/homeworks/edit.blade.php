@extends('layouts.app')

@section('title', 'Edit Homework')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Homework</h4>
                        <a href="{{ route('homeworks.show', $homework) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('homeworks.update', [$informationSheet, $homework]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="homework_number" class="form-label">Homework Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="homework_number" name="homework_number" value="{{ old('homework_number', $homework->homework_number) }}" required>
                            </div>
                            <div class="col-md-8">
                                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $homework->title) }}" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $homework->description) }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label for="instructions" class="form-label">Instructions <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="instructions" name="instructions" rows="4" required>{{ old('instructions', $homework->instructions) }}</textarea>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="due_date" name="due_date" value="{{ old('due_date', $homework->due_date->format('Y-m-d\TH:i')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="max_points" class="form-label">Maximum Points <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="max_points" name="max_points" value="{{ old('max_points', $homework->max_points) }}" min="1" required>
                            </div>
                        </div>

                        <!-- Requirements -->
                        <div class="mb-4">
                            <label class="form-label">Requirements <span class="text-danger">*</span></label>
                            <div id="requirements-container">
                                @foreach($homework->requirements_list as $requirement)
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="requirements[]" value="{{ $requirement }}" required>
                                    <button type="button" class="btn btn-outline-danger" onclick="removeItem(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addItem('requirements-container', 'requirements[]', 'Enter requirement')">
                                <i class="fas fa-plus me-1"></i>Add Requirement
                            </button>
                        </div>

                        <!-- Submission Guidelines -->
                        <div class="mb-4">
                            <label class="form-label">Submission Guidelines <span class="text-danger">*</span></label>
                            <div id="guidelines-container">
                                @foreach($homework->submission_guidelines_list as $guideline)
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="submission_guidelines[]" value="{{ $guideline }}" required>
                                    <button type="button" class="btn btn-outline-danger" onclick="removeItem(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addItem('guidelines-container', 'submission_guidelines[]', 'Enter submission guideline')">
                                <i class="fas fa-plus me-1"></i>Add Guideline
                            </button>
                        </div>

                        <!-- Reference Images -->
                        <div class="mb-4">
                            <label for="reference_images" class="form-label">Reference Images</label>
                            @if(count($homework->reference_images_list) > 0)
                            <div class="mb-2">
                                <p class="text-muted">Current images:</p>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($homework->reference_images_list as $image)
                                    <img src="{{ Storage::url($image) }}" alt="Reference" class="img-thumbnail" style="max-height: 100px;">
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            <input type="file" class="form-control" id="reference_images" name="reference_images[]" accept="image/*" multiple>
                            <small class="text-muted">Upload new images to replace existing ones</small>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('homeworks.show', $homework) }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-1"></i>Update Homework
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function addItem(containerId, inputName, placeholder) {
    const container = document.getElementById(containerId);
    const div = document.createElement('div');
    div.className = 'input-group mb-2';
    div.innerHTML = `
        <input type="text" class="form-control" name="${inputName}" placeholder="${placeholder}">
        <button type="button" class="btn btn-outline-danger" onclick="removeItem(this)">
            <i class="fas fa-times"></i>
        </button>
    `;
    container.appendChild(div);
}

function removeItem(button) {
    const container = button.closest('.input-group');
    if (container.parentElement.children.length > 1) {
        container.remove();
    }
}
</script>
@endsection
