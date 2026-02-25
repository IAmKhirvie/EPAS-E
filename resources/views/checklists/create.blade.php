@extends('layouts.app')

@section('title', 'Create Checklist')

@section('content')
<div class="content-area">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('content.management') }}">Content</a></li>
            <li class="breadcrumb-item"><a href="{{ route('modules.show', $informationSheet->module_id) }}">{{ $informationSheet->module->module_name }}</a></li>
            <li class="breadcrumb-item active">Create Checklist</li>
        </ol>
    </nav>

    <form action="{{ route('checklists.store', $informationSheet) }}" method="POST" id="checklistForm">
        @csrf

        <div class="cb-container">
            {{-- Sidebar: Rating Scale Guide --}}
            <div class="cb-sidebar">
                <div class="cb-sidebar__title">Checklist Guide</div>

                <div class="cb-sidebar__group">
                    <div class="cb-sidebar__group-label"><i class="fas fa-star"></i> Rating Scale</div>
                    <div class="cb-sidebar__info">
                        <div class="cb-sidebar__info-title">1-5 Rating Legend</div>
                        <div style="font-size: 0.8rem; line-height: 1.8;">
                            <strong>5</strong> - Excellent<br>
                            <strong>4</strong> - Good<br>
                            <strong>3</strong> - Average<br>
                            <strong>2</strong> - Below Average<br>
                            <strong>1</strong> - Poor
                        </div>
                    </div>
                </div>

                <div class="cb-sidebar__group">
                    <div class="cb-sidebar__group-label"><i class="fas fa-info-circle"></i> Sections</div>
                    <a href="#section-basic" class="cb-sidebar__item">
                        <span class="cb-sidebar__item-icon cb-sidebar__item-icon--purple"><i class="fas fa-pen"></i></span>
                        <span class="cb-sidebar__item-text">
                            <span class="cb-sidebar__item-name">Basic Info</span>
                            <span class="cb-sidebar__item-desc">Title and description</span>
                        </span>
                    </a>
                    <a href="#section-items" class="cb-sidebar__item">
                        <span class="cb-sidebar__item-icon cb-sidebar__item-icon--red"><i class="fas fa-clipboard-check"></i></span>
                        <span class="cb-sidebar__item-text">
                            <span class="cb-sidebar__item-name">Checklist Items</span>
                            <span class="cb-sidebar__item-desc">Evaluation criteria</span>
                        </span>
                    </a>
                </div>

                <div class="cb-sidebar__info">
                    <div class="cb-sidebar__info-title"><i class="fas fa-lightbulb"></i> Tips</div>
                    Each item should be specific and observable. Use clear language that students can understand.
                </div>
            </div>

            {{-- Main Panel --}}
            <div class="cb-main">
                <div class="cb-header cb-header--checklist">
                    <h4><i class="fas fa-clipboard-check me-2"></i>Create Checklist</h4>
                    <p>Build evaluation checklists with rated items and remarks</p>
                </div>

                <div class="cb-body">
                    <div class="cb-context-badge">
                        <i class="fas fa-file-alt"></i>
                        <span>Information Sheet: <strong>{{ $informationSheet->title }}</strong></span>
                    </div>

                    {{-- Basic Info --}}
                    <div class="cb-section" id="section-basic">
                        <div class="cb-section__title"><i class="fas fa-pen"></i> Basic Information</div>
                        <div class="cb-settings">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="cb-field-label">Checklist Number <span class="required">*</span></label>
                                    <input type="text" class="form-control @error('checklist_number') is-invalid @enderror" name="checklist_number" value="{{ old('checklist_number') }}" placeholder="e.g., CL-1.1" required>
                                    @error('checklist_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-8 mb-3">
                                    <label class="cb-field-label">Title <span class="required">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}" required>
                                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div>
                                <label class="cb-field-label">Description <span class="optional">(optional)</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="3">{{ old('description') }}</textarea>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- Checklist Items --}}
                    <div class="cb-section" id="section-items">
                        <div class="cb-items-header">
                            <h5><i class="fas fa-clipboard-check"></i> Checklist Items <span class="cb-count-badge">1</span></h5>
                        </div>

                        <div id="items-container">
                            <div class="cb-item-card item-card">
                                <div class="cb-item-card__header">
                                    <div class="left-section">
                                        <span class="cb-item-card__number">1</span>
                                        <span class="cb-item-card__title">Item #1</span>
                                    </div>
                                    <div class="right-section">
                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="DynamicForm.removeItemCard(this, 'item-card', 'Item')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="cb-item-card__body">
                                    <div class="row">
                                        <div class="col-md-8 mb-3">
                                            <label class="cb-field-label">Description <span class="required">*</span></label>
                                            <input type="text" class="form-control" name="items[0][description]" placeholder="What needs to be checked" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="cb-field-label">Rating (1-5) <span class="required">*</span></label>
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
                                            <label class="cb-field-label">Remarks <span class="optional">(optional)</span></label>
                                            <textarea class="form-control" name="items[0][remarks]" rows="2" placeholder="Additional notes..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="cb-add-btn" onclick="addChecklistItem()">
                            <i class="fas fa-plus"></i> Add Checklist Item
                        </button>
                    </div>
                </div>

                <div class="cb-footer">
                    <a href="{{ route('information-sheets.show', ['module' => $informationSheet->module_id, 'informationSheet' => $informationSheet->id]) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Cancel
                    </a>
                    <div class="btn-group-footer">
                        <span class="cb-footer__hint d-none d-md-inline">All fields marked * are required</span>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Create Checklist
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let itemCount = 1;

function addChecklistItem() {
    DynamicForm.addItemCard('items-container', 'item-card', (count) => `
        <div class="row">
            <div class="col-md-8 mb-3">
                <label class="cb-field-label">Description <span class="required">*</span></label>
                <input type="text" class="form-control" name="items[${count}][description]" placeholder="What needs to be checked" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="cb-field-label">Rating (1-5) <span class="required">*</span></label>
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
                <label class="cb-field-label">Remarks <span class="optional">(optional)</span></label>
                <textarea class="form-control" name="items[${count}][remarks]" rows="2" placeholder="Additional notes..."></textarea>
            </div>
        </div>
    `, 'Item');
    itemCount++;
}
</script>
@endsection
