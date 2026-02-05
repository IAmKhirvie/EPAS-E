@extends('layouts.app')

@section('title', 'Edit Task Sheet')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Task Sheet</h4>
                        <a href="{{ route('task-sheets.show', $taskSheet) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        <strong>Information Sheet:</strong> {{ $informationSheet->title }}
                    </p>

                    <form action="{{ route('task-sheets.update', [$informationSheet, $taskSheet]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="task_number" class="form-label">Task Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('task_number') is-invalid @enderror" id="task_number" name="task_number" value="{{ old('task_number', $taskSheet->task_number) }}" required>
                                @error('task_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-8">
                                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $taskSheet->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $taskSheet->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="instructions" class="form-label">Instructions <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('instructions') is-invalid @enderror" id="instructions" name="instructions" rows="4" required>{{ old('instructions', $taskSheet->instructions) }}</textarea>
                            @error('instructions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Objectives -->
                        <div class="mb-4">
                            <label class="form-label">Objectives <span class="text-danger">*</span></label>
                            <div id="objectives-container">
                                @foreach($taskSheet->objectives_list as $objective)
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="objectives[]" value="{{ $objective }}" required>
                                    <button type="button" class="btn btn-outline-danger remove-item" onclick="removeItem(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addItem('objectives-container', 'objectives[]', 'Enter objective')">
                                <i class="fas fa-plus me-1"></i>Add Objective
                            </button>
                        </div>

                        <!-- Materials -->
                        <div class="mb-4">
                            <label class="form-label">Materials/Equipment <span class="text-danger">*</span></label>
                            <div id="materials-container">
                                @foreach($taskSheet->materials_list as $material)
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="materials[]" value="{{ $material }}" required>
                                    <button type="button" class="btn btn-outline-danger remove-item" onclick="removeItem(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addItem('materials-container', 'materials[]', 'Enter material')">
                                <i class="fas fa-plus me-1"></i>Add Material
                            </button>
                        </div>

                        <!-- Safety Precautions -->
                        <div class="mb-4">
                            <label class="form-label">Safety Precautions</label>
                            <div id="safety-container">
                                @forelse($taskSheet->safety_precautions_list as $safety)
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="safety_precautions[]" value="{{ $safety }}">
                                    <button type="button" class="btn btn-outline-danger remove-item" onclick="removeItem(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                @empty
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="safety_precautions[]" placeholder="Enter safety precaution">
                                    <button type="button" class="btn btn-outline-danger remove-item" onclick="removeItem(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                @endforelse
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addItem('safety-container', 'safety_precautions[]', 'Enter safety precaution')">
                                <i class="fas fa-plus me-1"></i>Add Safety Precaution
                            </button>
                        </div>

                        <!-- Image Upload -->
                        <div class="mb-4">
                            <label for="image" class="form-label">Reference Image</label>
                            @if($taskSheet->image_path)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($taskSheet->image_path) }}" alt="Current image" class="img-thumbnail" style="max-height: 150px;">
                                </div>
                            @endif
                            <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                            <small class="text-muted">Leave empty to keep current image</small>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('task-sheets.show', $taskSheet) }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-1"></i>Update Task Sheet
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
        <button type="button" class="btn btn-outline-danger remove-item" onclick="removeItem(this)">
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
