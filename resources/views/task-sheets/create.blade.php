@extends('layouts.app')

@section('title', 'Create Task Sheet')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Create Task Sheet</h4>
                        <a href="{{ route('information-sheets.show', ['module' => $informationSheet->module_id, 'informationSheet' => $informationSheet->id]) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        <strong>Information Sheet:</strong> {{ $informationSheet->title }}
                    </p>

                    <form action="{{ route('task-sheets.store', $informationSheet) }}" method="POST" enctype="multipart/form-data" id="taskSheetForm">
                        @csrf

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="task_number" class="form-label">Task Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('task_number') is-invalid @enderror" id="task_number" name="task_number" value="{{ old('task_number') }}" placeholder="e.g., TS-1.1" required>
                                @error('task_number')
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

                        <!-- Objectives -->
                        <div class="mb-4">
                            <label class="form-label">Objectives <span class="text-danger">*</span></label>
                            <div id="objectives-container">
                                <div class="input-group mb-2 objective-item">
                                    <input type="text" class="form-control" name="objectives[]" placeholder="Enter objective" required>
                                    <button type="button" class="btn btn-outline-danger remove-item" onclick="removeItem(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addItem('objectives-container', 'objectives[]', 'Enter objective')">
                                <i class="fas fa-plus me-1"></i>Add Objective
                            </button>
                        </div>

                        <!-- Materials -->
                        <div class="mb-4">
                            <label class="form-label">Materials/Equipment <span class="text-danger">*</span></label>
                            <div id="materials-container">
                                <div class="input-group mb-2 material-item">
                                    <input type="text" class="form-control" name="materials[]" placeholder="Enter material" required>
                                    <button type="button" class="btn btn-outline-danger remove-item" onclick="removeItem(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addItem('materials-container', 'materials[]', 'Enter material')">
                                <i class="fas fa-plus me-1"></i>Add Material
                            </button>
                        </div>

                        <!-- Safety Precautions -->
                        <div class="mb-4">
                            <label class="form-label">Safety Precautions</label>
                            <div id="safety-container">
                                <div class="input-group mb-2 safety-item">
                                    <input type="text" class="form-control" name="safety_precautions[]" placeholder="Enter safety precaution">
                                    <button type="button" class="btn btn-outline-danger remove-item" onclick="removeItem(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addItem('safety-container', 'safety_precautions[]', 'Enter safety precaution')">
                                <i class="fas fa-plus me-1"></i>Add Safety Precaution
                            </button>
                        </div>

                        <!-- Task Items -->
                        <div class="mb-4">
                            <label class="form-label">Task Items <span class="text-danger">*</span></label>
                            <div id="items-container">
                                <div class="card mb-3 item-card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-3">
                                            <h6 class="mb-0">Item #1</h6>
                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeCard(this)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Part Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="items[0][part_name]" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Description <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="items[0][description]" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Expected Finding <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="items[0][expected_finding]" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Acceptable Range <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="items[0][acceptable_range]" required>
                                            </div>
                                            <input type="hidden" name="items[0][order]" value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-success" onclick="addTaskItem()">
                                <i class="fas fa-plus me-1"></i>Add Task Item
                            </button>
                        </div>

                        <!-- Image Upload -->
                        <div class="mb-4">
                            <label for="image" class="form-label">Reference Image (Optional)</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('information-sheets.show', ['module' => $informationSheet->module_id, 'informationSheet' => $informationSheet->id]) }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Create Task Sheet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let itemCount = 1;

// Delegate to shared DynamicForm utility
function addItem(containerId, inputName, placeholder) {
    DynamicForm.addItem(containerId, inputName, placeholder);
}

function removeItem(button) {
    DynamicForm.removeItem(button);
}

function addTaskItem() {
    DynamicForm.addCard('items-container', 'item-card', (count) => `
        <div class="card-body">
            <div class="d-flex justify-content-between mb-3">
                <h6 class="mb-0">Item #${count + 1}</h6>
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeCard(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Part Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="items[${count}][part_name]" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Description <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="items[${count}][description]" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Expected Finding <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="items[${count}][expected_finding]" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Acceptable Range <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="items[${count}][acceptable_range]" required>
                </div>
                <input type="hidden" name="items[${count}][order]" value="${count}">
            </div>
        </div>
    `);
    itemCount++;
}

function removeCard(button) {
    DynamicForm.removeCard(button, 'item-card', 'Item');
}
</script>
@endsection
