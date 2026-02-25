@extends('layouts.app')

@section('title', 'Edit Checklist')

@section('content')
<div class="content-area">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('content.management') }}">Content</a></li>
            <li class="breadcrumb-item active">Edit Checklist</li>
        </ol>
    </nav>

    <form action="{{ route('checklists.update', [$informationSheet, $checklist]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="cb-container">
            {{-- Sidebar --}}
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
                        <span class="cb-sidebar__item-text"><span class="cb-sidebar__item-name">Basic Info</span></span>
                    </a>
                    <a href="#section-items" class="cb-sidebar__item">
                        <span class="cb-sidebar__item-icon cb-sidebar__item-icon--red"><i class="fas fa-clipboard-check"></i></span>
                        <span class="cb-sidebar__item-text"><span class="cb-sidebar__item-name">Checklist Items</span></span>
                    </a>
                </div>
            </div>

            {{-- Main Panel --}}
            <div class="cb-main">
                <div class="cb-header cb-header--checklist">
                    <h4><i class="fas fa-edit me-2"></i>Edit Checklist</h4>
                    <p>Update checklist items and ratings</p>
                </div>

                <div class="cb-body">
                    {{-- Basic Info --}}
                    <div class="cb-section" id="section-basic">
                        <div class="cb-section__title"><i class="fas fa-pen"></i> Basic Information</div>
                        <div class="cb-settings">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="cb-field-label">Checklist Number <span class="required">*</span></label>
                                    <input type="text" class="form-control" name="checklist_number" value="{{ old('checklist_number', $checklist->checklist_number) }}" required>
                                </div>
                                <div class="col-md-8 mb-3">
                                    <label class="cb-field-label">Title <span class="required">*</span></label>
                                    <input type="text" class="form-control" name="title" value="{{ old('title', $checklist->title) }}" required>
                                </div>
                            </div>
                            <div>
                                <label class="cb-field-label">Description <span class="optional">(optional)</span></label>
                                <textarea class="form-control" name="description" rows="3">{{ old('description', $checklist->description) }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Checklist Items --}}
                    <div class="cb-section" id="section-items">
                        <div class="cb-items-header">
                            <h5><i class="fas fa-clipboard-check"></i> Checklist Items <span class="cb-count-badge">{{ count(json_decode($checklist->items, true) ?? []) }}</span></h5>
                        </div>

                        <div id="items-container">
                            @php $items = json_decode($checklist->items, true) ?? []; @endphp
                            @foreach($items as $index => $item)
                            <div class="cb-item-card item-card">
                                <div class="cb-item-card__header">
                                    <div class="left-section">
                                        <span class="cb-item-card__number">{{ $index + 1 }}</span>
                                        <span class="cb-item-card__title">Item #{{ $index + 1 }}</span>
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
                                            <input type="text" class="form-control" name="items[{{ $index }}][description]" value="{{ $item['description'] ?? '' }}" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="cb-field-label">Rating (1-5) <span class="required">*</span></label>
                                            <select class="form-select" name="items[{{ $index }}][rating]" required>
                                                <option value="">Select rating...</option>
                                                @for($i = 1; $i <= 5; $i++)
                                                <option value="{{ $i }}" {{ ($item['rating'] ?? '') == $i ? 'selected' : '' }}>
                                                    {{ $i }} - {{ ['Poor', 'Below Average', 'Average', 'Good', 'Excellent'][$i-1] }}
                                                </option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label class="cb-field-label">Remarks <span class="optional">(optional)</span></label>
                                            <textarea class="form-control" name="items[{{ $index }}][remarks]" rows="2">{{ $item['remarks'] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <button type="button" class="cb-add-btn" onclick="addChecklistItem()">
                            <i class="fas fa-plus"></i> Add Checklist Item
                        </button>
                    </div>
                </div>

                <div class="cb-footer">
                    <a href="{{ route('checklists.show', $checklist) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Cancel
                    </a>
                    <div class="btn-group-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Checklist
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let itemCount = {{ count($items) }};

function addChecklistItem() {
    DynamicForm.addItemCard('items-container', 'item-card', (count) => `
        <div class="row">
            <div class="col-md-8 mb-3">
                <label class="cb-field-label">Description <span class="required">*</span></label>
                <input type="text" class="form-control" name="items[${itemCount}][description]" placeholder="What needs to be checked" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="cb-field-label">Rating (1-5) <span class="required">*</span></label>
                <select class="form-select" name="items[${itemCount}][rating]" required>
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
                <textarea class="form-control" name="items[${itemCount}][remarks]" rows="2" placeholder="Additional notes..."></textarea>
            </div>
        </div>
    `, 'Item');
    itemCount++;
}
</script>
@endsection
