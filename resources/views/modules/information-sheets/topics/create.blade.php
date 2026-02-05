@extends('layouts.app')

@section('title', 'Create Topic - EPAS-E')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Create New Topic</h1>
                <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Content Management
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('topics.store', $informationSheet->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Breadcrumb -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">{{ $informationSheet->module->course->course_name }}</li>
                                        <li class="breadcrumb-item">Module {{ $informationSheet->module->module_number }}</li>
                                        <li class="breadcrumb-item">Info Sheet {{ $informationSheet->sheet_number }}</li>
                                        <li class="breadcrumb-item active">Create New Topic</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>

                        <!-- Information Sheet Info -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Information Sheet</label>
                                    <input type="text" class="form-control"
                                           value="Sheet {{ $informationSheet->sheet_number }}: {{ $informationSheet->title }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Module</label>
                                    <input type="text" class="form-control"
                                           value="Module {{ $informationSheet->module->module_number }}: {{ $informationSheet->module->module_name }}" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Topic Details -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="topic_number" class="form-label">Topic Number *</label>
                                    <input type="text" class="form-control @error('topic_number') is-invalid @enderror"
                                           id="topic_number" name="topic_number"
                                           value="{{ old('topic_number') }}"
                                           placeholder="e.g., 1, 2, 3 or 1.1.1" required>
                                    @error('topic_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="title" class="form-label">Title *</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                           id="title" name="title"
                                           value="{{ old('title') }}"
                                           placeholder="e.g., Scientists Who Contributed to Electricity" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Content (Optional now that we have parts) -->
                        <div class="form-group mt-4">
                            <label for="content" class="form-label">Introduction Content</label>
                            <textarea class="form-control preserve-whitespace @error('content') is-invalid @enderror"
                                    id="content" name="content"
                                    rows="6"
                                    placeholder="Enter introductory content for this topic (optional if using parts below)...">{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <small>
                                    <strong>Formatting tips:</strong> Use empty lines to separate paragraphs.
                                    Basic formatting allowed: <code>&lt;b&gt;</code> <code>&lt;i&gt;</code> <code>&lt;u&gt;</code>
                                    <code>&lt;strong&gt;</code> <code>&lt;em&gt;</code> <code>&lt;br&gt;</code>
                                </small>
                            </div>
                        </div>

                        <!-- Content Parts Section -->
                        <div class="mt-5">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="mb-1"><i class="fas fa-puzzle-piece text-primary me-2"></i>Content Parts</h5>
                                    <small class="text-muted">Add multiple parts with images and explanations (e.g., list of scientists)</small>
                                </div>
                                <button type="button" class="btn btn-success btn-sm" id="addPartBtn">
                                    <i class="fas fa-plus me-1"></i> Add Part
                                </button>
                            </div>

                            <div id="partsContainer">
                                <!-- Parts will be added here dynamically -->
                            </div>

                            <div id="noPartsMessage" class="text-center py-4 bg-light rounded border-dashed">
                                <i class="fas fa-info-circle text-muted fa-2x mb-2"></i>
                                <p class="text-muted mb-0">No parts added yet. Click "Add Part" to create content sections with images.</p>
                            </div>
                        </div>

                        <!-- Order -->
                        <div class="form-group mt-4">
                            <label for="order" class="form-label">Order</label>
                            <input type="number" class="form-control @error('order') is-invalid @enderror"
                                   id="order" name="order"
                                   value="{{ old('order', $nextOrder ?? 0) }}"
                                   min="0">
                            @error('order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Actions -->
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Create Topic
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
.breadcrumb {
    background-color: #f8f9fa;
    padding: 0.75rem 1rem;
    border-radius: 0.375rem;
}

.breadcrumb-item.active {
    color: #6c757d;
}

.preserve-whitespace {
    white-space: pre-wrap;
    word-wrap: break-word;
    font-family: 'Courier New', Courier, monospace;
    line-height: 1.5;
}

.border-dashed {
    border: 2px dashed #dee2e6 !important;
}

.part-card {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 0.5rem;
    padding: 1.25rem;
    margin-bottom: 1rem;
    position: relative;
    transition: box-shadow 0.2s;
}

.part-card:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.part-number {
    position: absolute;
    top: -12px;
    left: 15px;
    background: #0d6efd;
    color: white;
    padding: 2px 12px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
}

.part-remove-btn {
    position: absolute;
    top: 10px;
    right: 10px;
}

.image-preview-container {
    width: 150px;
    height: 150px;
    border: 2px dashed #dee2e6;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    overflow: hidden;
    transition: all 0.2s;
    background: #f8f9fa;
}

.image-preview-container:hover {
    border-color: #0d6efd;
    background: #e7f1ff;
}

.image-preview-container img {
    max-width: 100%;
    max-height: 100%;
    object-fit: cover;
}

.image-preview-container .placeholder-content {
    text-align: center;
    color: #6c757d;
}

.image-preview-container .placeholder-content i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let partIndex = 0;
    const partsContainer = document.getElementById('partsContainer');
    const noPartsMessage = document.getElementById('noPartsMessage');
    const addPartBtn = document.getElementById('addPartBtn');

    function updateNoPartsMessage() {
        const parts = partsContainer.querySelectorAll('.part-card');
        noPartsMessage.style.display = parts.length === 0 ? 'block' : 'none';
    }

    function renumberParts() {
        const parts = partsContainer.querySelectorAll('.part-card');
        parts.forEach((part, index) => {
            part.querySelector('.part-number').textContent = 'Part ' + (index + 1);
        });
    }

    function createPartCard(index) {
        const card = document.createElement('div');
        card.className = 'part-card';
        card.innerHTML = `
            <span class="part-number">Part ${index + 1}</span>
            <button type="button" class="btn btn-outline-danger btn-sm part-remove-btn" onclick="removePart(this)">
                <i class="fas fa-times"></i>
            </button>

            <div class="row mt-2">
                <div class="col-md-3">
                    <label class="form-label small text-muted">Image</label>
                    <div class="image-preview-container" onclick="this.querySelector('input').click()">
                        <input type="file" name="part_images[${index}]" accept="image/*" class="d-none" onchange="previewPartImage(this)">
                        <div class="placeholder-content">
                            <i class="fas fa-image d-block"></i>
                            <small>Click to upload</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="form-group mb-3">
                        <label class="form-label small text-muted">Title / Name</label>
                        <input type="text" class="form-control" name="parts[${index}][title]"
                               placeholder="e.g., Benjamin Franklin">
                    </div>
                    <div class="form-group">
                        <label class="form-label small text-muted">Explanation / Description</label>
                        <textarea class="form-control" name="parts[${index}][explanation]" rows="3"
                                  placeholder="e.g., American scientist and inventor who discovered that lightning is electrical..."></textarea>
                    </div>
                </div>
            </div>
        `;
        return card;
    }

    addPartBtn.addEventListener('click', function() {
        const card = createPartCard(partIndex);
        partsContainer.appendChild(card);
        partIndex++;
        updateNoPartsMessage();
    });

    window.removePart = function(btn) {
        const card = btn.closest('.part-card');
        card.remove();
        renumberParts();
        updateNoPartsMessage();

        // Re-index remaining parts
        const parts = partsContainer.querySelectorAll('.part-card');
        parts.forEach((part, idx) => {
            part.querySelectorAll('input, textarea').forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/\[\d+\]/, `[${idx}]`));
                }
            });
        });
    };

    window.previewPartImage = function(input) {
        const container = input.closest('.image-preview-container');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                container.innerHTML = `
                    <input type="file" name="${input.name}" accept="image/*" class="d-none" onchange="previewPartImage(this)">
                    <img src="${e.target.result}" alt="Preview">
                `;
            };
            reader.readAsDataURL(input.files[0]);
        }
    };

    updateNoPartsMessage();
});
</script>
@endpush

@endsection
