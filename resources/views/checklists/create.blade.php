@extends('layouts.app')

@section('title', 'Create Checklist')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-purple text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-clipboard-check me-2"></i>Create Checklist</h4>
                        <a href="{{ route('information-sheets.show', ['module' => $informationSheet->module_id, 'informationSheet' => $informationSheet->id]) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        <strong>Information Sheet:</strong> {{ $informationSheet->title }}
                    </p>

                    <form action="{{ route('checklists.store', $informationSheet) }}" method="POST" id="checklistForm">
                        @csrf

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="checklist_number" class="form-label">Checklist Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('checklist_number') is-invalid @enderror" id="checklist_number" name="checklist_number" value="{{ old('checklist_number') }}" placeholder="e.g., CL-1.1" required>
                                @error('checklist_number')
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

                        <!-- Checklist Items -->
                        <div class="mb-4">
                            <label class="form-label">Checklist Items <span class="text-danger">*</span></label>
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
                                            <div class="col-md-8 mb-3">
                                                <label class="form-label">Description <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="items[0][description]" placeholder="What needs to be checked" required>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Rating (1-5) <span class="text-danger">*</span></label>
                                                <select class="form-select" name="items[0][rating]" required>
                                                    <option value="">Select rating...</option>
                                                    <option value="1">1 - Poor</option>
                                                    <option value="2">2 - Below Average</option>
                                                    <option value="3">3 - Average</option>
                                                    <option value="4">4 - Good</option>
                                                    <option value="5">5 - Excellent</option>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Remarks</label>
                                                <textarea class="form-control" name="items[0][remarks]" rows="2" placeholder="Additional notes..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-success" onclick="addChecklistItem()">
                                <i class="fas fa-plus me-1"></i>Add Checklist Item
                            </button>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('information-sheets.show', ['module' => $informationSheet->module_id, 'informationSheet' => $informationSheet->id]) }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-purple">
                                <i class="fas fa-save me-1"></i>Create Checklist
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

function addChecklistItem() {
    DynamicForm.addCard('items-container', 'item-card', (count) => `
        <div class="card-body">
            <div class="d-flex justify-content-between mb-3">
                <h6 class="mb-0">Item #${count + 1}</h6>
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeCard(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="row">
                <div class="col-md-8 mb-3">
                    <label class="form-label">Description <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="items[${count}][description]" placeholder="What needs to be checked" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Rating (1-5) <span class="text-danger">*</span></label>
                    <select class="form-select" name="items[${count}][rating]" required>
                        <option value="">Select rating...</option>
                        <option value="1">1 - Poor</option>
                        <option value="2">2 - Below Average</option>
                        <option value="3">3 - Average</option>
                        <option value="4">4 - Good</option>
                        <option value="5">5 - Excellent</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Remarks</label>
                    <textarea class="form-control" name="items[${count}][remarks]" rows="2" placeholder="Additional notes..."></textarea>
                </div>
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
