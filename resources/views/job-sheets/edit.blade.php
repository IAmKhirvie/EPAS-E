@extends('layouts.app')

@section('title', 'Edit Job Sheet')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Job Sheet</h4>
                        <a href="{{ route('job-sheets.show', $jobSheet) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('job-sheets.update', [$informationSheet, $jobSheet]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="job_number" class="form-label">Job Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="job_number" name="job_number" value="{{ old('job_number', $jobSheet->job_number) }}" required>
                            </div>
                            <div class="col-md-8">
                                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $jobSheet->title) }}" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $jobSheet->description) }}</textarea>
                        </div>

                        <!-- Objectives -->
                        <div class="mb-4">
                            <label class="form-label">Objectives <span class="text-danger">*</span></label>
                            <div id="objectives-container">
                                @foreach($jobSheet->objectives_list as $objective)
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="objectives[]" value="{{ $objective }}" required>
                                    <button type="button" class="btn btn-outline-danger" onclick="removeItem(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addItem('objectives-container', 'objectives[]', 'Enter objective')">
                                <i class="fas fa-plus me-1"></i>Add Objective
                            </button>
                        </div>

                        <!-- Tools Required -->
                        <div class="mb-4">
                            <label class="form-label">Tools Required <span class="text-danger">*</span></label>
                            <div id="tools-container">
                                @foreach($jobSheet->tools_required_list as $tool)
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="tools_required[]" value="{{ $tool }}" required>
                                    <button type="button" class="btn btn-outline-danger" onclick="removeItem(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addItem('tools-container', 'tools_required[]', 'Enter tool')">
                                <i class="fas fa-plus me-1"></i>Add Tool
                            </button>
                        </div>

                        <!-- Safety Requirements -->
                        <div class="mb-4">
                            <label class="form-label">Safety Requirements <span class="text-danger">*</span></label>
                            <div id="safety-container">
                                @foreach($jobSheet->safety_requirements_list as $safety)
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="safety_requirements[]" value="{{ $safety }}" required>
                                    <button type="button" class="btn btn-outline-danger" onclick="removeItem(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addItem('safety-container', 'safety_requirements[]', 'Enter safety requirement')">
                                <i class="fas fa-plus me-1"></i>Add Safety Requirement
                            </button>
                        </div>

                        <!-- Reference Materials -->
                        <div class="mb-4">
                            <label class="form-label">Reference Materials</label>
                            <div id="references-container">
                                @forelse($jobSheet->reference_materials_list as $ref)
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="reference_materials[]" value="{{ $ref }}">
                                    <button type="button" class="btn btn-outline-danger" onclick="removeItem(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                @empty
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="reference_materials[]" placeholder="Enter reference material">
                                    <button type="button" class="btn btn-outline-danger" onclick="removeItem(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                @endforelse
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addItem('references-container', 'reference_materials[]', 'Enter reference material')">
                                <i class="fas fa-plus me-1"></i>Add Reference
                            </button>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('job-sheets.show', $jobSheet) }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-1"></i>Update Job Sheet
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
