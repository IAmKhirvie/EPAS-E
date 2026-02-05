@extends('layouts.app')

@section('title', 'Edit Checklist')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Checklist</h4>
                        <a href="{{ route('checklists.show', $checklist) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('checklists.update', [$informationSheet, $checklist]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="checklist_number" class="form-label">Checklist Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="checklist_number" name="checklist_number" value="{{ old('checklist_number', $checklist->checklist_number) }}" required>
                            </div>
                            <div class="col-md-8">
                                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $checklist->title) }}" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $checklist->description) }}</textarea>
                        </div>

                        <!-- Checklist Items -->
                        <div class="mb-4">
                            <label class="form-label">Checklist Items <span class="text-danger">*</span></label>
                            <div id="items-container">
                                @php $items = json_decode($checklist->items, true) ?? []; @endphp
                                @foreach($items as $index => $item)
                                <div class="card mb-3 item-card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-3">
                                            <h6 class="mb-0">Item #{{ $index + 1 }}</h6>
                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeCard(this)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-8 mb-3">
                                                <label class="form-label">Description <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="items[{{ $index }}][description]" value="{{ $item['description'] ?? '' }}" required>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Rating (1-5) <span class="text-danger">*</span></label>
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
                                                <label class="form-label">Remarks</label>
                                                <textarea class="form-control" name="items[{{ $index }}][remarks]" rows="2">{{ $item['remarks'] ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-outline-success" onclick="addChecklistItem()">
                                <i class="fas fa-plus me-1"></i>Add Checklist Item
                            </button>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('checklists.show', $checklist) }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-1"></i>Update Checklist
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let itemCount = {{ count($items) }};

function addChecklistItem() {
    const container = document.getElementById('items-container');
    const cardCount = container.querySelectorAll('.item-card').length;

    const card = document.createElement('div');
    card.className = 'card mb-3 item-card';
    card.innerHTML = `
        <div class="card-body">
            <div class="d-flex justify-content-between mb-3">
                <h6 class="mb-0">Item #${cardCount + 1}</h6>
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeCard(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="row">
                <div class="col-md-8 mb-3">
                    <label class="form-label">Description <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="items[${itemCount}][description]" placeholder="What needs to be checked" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Rating (1-5) <span class="text-danger">*</span></label>
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
                    <label class="form-label">Remarks</label>
                    <textarea class="form-control" name="items[${itemCount}][remarks]" rows="2" placeholder="Additional notes..."></textarea>
                </div>
            </div>
        </div>
    `;
    container.appendChild(card);
    itemCount++;
}

function removeCard(button) {
    const card = button.closest('.item-card');
    if (document.querySelectorAll('.item-card').length > 1) {
        card.remove();
        renumberItems();
    }
}

function renumberItems() {
    const cards = document.querySelectorAll('.item-card');
    cards.forEach((card, index) => {
        card.querySelector('h6').textContent = `Item #${index + 1}`;
    });
}
</script>
@endsection
