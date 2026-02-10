@extends('layouts.app')

@section('title', 'Create Information Sheet - EPAS-E')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Create Information Sheet</h1>
                <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Content Management
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('information-sheets.store', $module) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Course and Module Information (Read-only) -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Course</label>
                                    <input type="text" class="form-control" value="{{ $module->course->course_name }} - {{ $module->course->course_code }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Module</label>
                                    <input type="text" class="form-control" value="Module {{ $module->module_number }}: {{ $module->module_name }}" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Information Sheet Details -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sheet_number" class="form-label">Information Sheet Number *</label>
                                    <input type="text" class="form-control @error('sheet_number') is-invalid @enderror" 
                                           id="sheet_number" name="sheet_number" 
                                           value="{{ old('sheet_number') }}" 
                                           placeholder="e.g., 1.1, 1.2, 2.1" required>
                                    @error('sheet_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Format: ModuleNumber.SheetNumber (e.g., 1.1)</small>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="title" class="form-label">Title *</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" 
                                           value="{{ old('title') }}" 
                                           placeholder="e.g., Introduction to Electronics and Electricity" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- File Upload -->
                        <div class="form-group mt-4">
                            <label for="file" class="form-label">Upload PDF/Excel File (Optional)</label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror"
                                   id="file" name="file" accept=".pdf,.xlsx,.xls">
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Accepted: PDF, Excel (.xlsx, .xls). Max 10MB. Text will be extracted into the content field below.
                            </small>
                        </div>

                        <!-- Content -->
                        <div class="form-group mt-4">
                            <label for="content" class="form-label">Content</label>
                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                      id="content" name="content" 
                                      rows="10" 
                                      placeholder="Enter the main content for this information sheet...">{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Order -->
                        <div class="form-group mt-4">
                            <label for="order" class="form-label">Order</label>
                            <input type="number" class="form-control @error('order') is-invalid @enderror" 
                                   id="order" name="order" 
                                   value="{{ old('order', $nextOrder) }}" 
                                   min="0">
                            @error('order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Determines the display order (lower numbers appear first)</small>
                        </div>

                        <!-- Actions -->
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Create Information Sheet
                            </button>
                            <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.form-label {
    font-weight: 600;
    color: #333;
    margin-bottom: 0.5rem;
}

.form-control:read-only {
    background-color: #f8f9fa;
    opacity: 1;
}

.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-body {
    padding: 2rem;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Simple slug generation for sheet number
    const titleInput = document.getElementById('title');
    const sheetNumberInput = document.getElementById('sheet_number');
    
    // Auto-suggest sheet number based on previous sheets
    titleInput.addEventListener('blur', function() {
        if (!sheetNumberInput.value) {
            // You could add logic here to suggest the next available sheet number
        }
    });
});
</script>
@endpush
@endsection