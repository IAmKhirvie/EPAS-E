@extends('layouts.app')

@section('title', 'Edit Topic - EPAS-E')

@section('content')
<div class="content-area">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Courses</a></li>
            <li class="breadcrumb-item">{{ $informationSheet->module->course->course_name }}</li>
            <li class="breadcrumb-item">Module {{ $informationSheet->module->module_number }}</li>
            <li class="breadcrumb-item">Info Sheet {{ $informationSheet->sheet_number }}</li>
            <li class="breadcrumb-item active">Edit: {{ $topic->title }}</li>
        </ol>
    </nav>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="cb-container--simple">
        <form action="{{ route('topics.update', [$informationSheet->id, $topic->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="cb-main">
                <div class="cb-header cb-header--topic">
                    <h4><i class="fas fa-edit me-2"></i>Edit Topic</h4>
                    <p>{{ $topic->topic_number }}: {{ $topic->title }}</p>
                </div>

                <div class="cb-body">
                    {{-- Context --}}
                    <div class="cb-context-badge">
                        <i class="fas fa-file-alt"></i>
                        <span>Info Sheet: <strong>{{ $informationSheet->sheet_number }} &mdash; {{ $informationSheet->title }}</strong></span>
                    </div>

                    {{-- Topic Details --}}
                    <div class="cb-section">
                        <div class="cb-section__title"><i class="fas fa-pen"></i> Topic Details</div>
                        <div class="cb-settings">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="cb-field-label">Topic Number <span class="required">*</span></label>
                                    <input type="text" class="form-control @error('topic_number') is-invalid @enderror"
                                           name="topic_number" value="{{ old('topic_number', $topic->topic_number) }}"
                                           placeholder="e.g., 1, 2, 3 or 1.1.1" required>
                                    @error('topic_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-8 mb-3">
                                    <label class="cb-field-label">Title <span class="required">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                           name="title" value="{{ old('title', $topic->title) }}"
                                           placeholder="e.g., Scientists Who Contributed to Electricity" required>
                                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Introduction Content --}}
                    <div class="cb-section">
                        <div class="cb-section__title"><i class="fas fa-align-left"></i> Introduction Content</div>
                        <div>
                            <textarea class="form-control preserve-whitespace @error('content') is-invalid @enderror"
                                      name="content" rows="6"
                                      placeholder="Enter introductory content for this topic (optional if using parts below)...">{{ old('content', $topic->content ?? '') }}</textarea>
                            @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="cb-field-hint">
                                <strong>Formatting tips:</strong> Use empty lines to separate paragraphs.
                                Basic formatting: &lt;b&gt; &lt;i&gt; &lt;u&gt; &lt;strong&gt; &lt;em&gt; &lt;br&gt;
                            </div>
                        </div>
                    </div>

                    {{-- Content Parts --}}
                    <div class="cb-section">
                        <div class="cb-items-header">
                            <h5><i class="fas fa-puzzle-piece"></i> Content Parts <span class="cb-count-badge">{{ ($topic->parts && count($topic->parts) > 0) ? count($topic->parts) : 0 }}</span></h5>
                            <small style="color: var(--cb-text-hint);">Add multiple parts with images and explanations</small>
                        </div>

                        <div id="partsContainer">
                            @if($topic->parts && count($topic->parts) > 0)
                                @foreach($topic->parts as $index => $part)
                                <div class="part-card">
                                    <span class="part-number">Part {{ $index + 1 }}</span>
                                    <button type="button" class="btn btn-outline-danger btn-sm part-remove-btn" onclick="removePart(this)">
                                        <i class="fas fa-times"></i>
                                    </button>

                                    <div class="row mt-2">
                                        <div class="col-md-3">
                                            <label class="cb-field-label small">Image</label>
                                            <div class="image-preview-container" onclick="this.querySelector('input[type=file]').click()">
                                                <input type="file" name="part_images[{{ $index }}]" accept="image/*" class="d-none" onchange="previewPartImage(this)">
                                                <input type="hidden" name="parts[{{ $index }}][existing_image]" value="{{ $part['image'] ?? '' }}">
                                                @if(!empty($part['image']))
                                                    <img src="{{ $part['image'] }}" alt="Part Image">
                                                @else
                                                    <div class="placeholder-content">
                                                        <i class="fas fa-image d-block"></i>
                                                        <small>Click to upload</small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="mb-3">
                                                <label class="cb-field-label small">Title / Name</label>
                                                <input type="text" class="form-control" name="parts[{{ $index }}][title]"
                                                       value="{{ $part['title'] ?? '' }}"
                                                       placeholder="e.g., Benjamin Franklin">
                                            </div>
                                            <div>
                                                <label class="cb-field-label small">Explanation / Description</label>
                                                <textarea class="form-control" name="parts[{{ $index }}][explanation]" rows="3"
                                                          placeholder="e.g., American scientist and inventor...">{{ $part['explanation'] ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @endif
                        </div>

                        <div id="noPartsMessage" class="cb-empty-state" @if($topic->parts && count($topic->parts) > 0) style="display: none;" @endif>
                            <i class="fas fa-puzzle-piece"></i>
                            <p>No parts added yet. Click "Add Part" to create content sections with images.</p>
                        </div>

                        <button type="button" class="cb-add-btn" id="addPartBtn">
                            <i class="fas fa-plus"></i> Add Part
                        </button>
                    </div>

                    {{-- Settings --}}
                    <div class="cb-section">
                        <div class="cb-section__title"><i class="fas fa-cog"></i> Settings</div>
                        <div class="cb-settings">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="cb-field-label">Display Order</label>
                                    <input type="number" class="form-control @error('order') is-invalid @enderror"
                                           name="order" value="{{ old('order', $topic->order) }}" min="0">
                                    @error('order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <div class="cb-field-hint">Lower numbers appear first</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="cb-footer">
                    <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Cancel
                    </a>
                    <div class="btn-group-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Topic
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
.preserve-whitespace {
    white-space: pre-wrap;
    word-wrap: break-word;
    font-family: 'Courier New', Courier, monospace;
    line-height: 1.5;
}

.part-card {
    background: var(--cb-surface);
    border: 1px solid var(--cb-border);
    border-radius: var(--cb-radius-sm);
    padding: 1.25rem;
    margin-bottom: 1rem;
    position: relative;
    transition: box-shadow 0.2s;
}

.part-card:hover {
    box-shadow: var(--cb-shadow-hover);
}

.part-number {
    position: absolute;
    top: -12px;
    left: 15px;
    background: linear-gradient(135deg, #f59e0b, #d97706);
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
    border: 2px dashed var(--cb-border-dashed);
    border-radius: var(--cb-radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    overflow: hidden;
    transition: all 0.2s;
    background: var(--cb-surface-alt);
}

.image-preview-container:hover {
    border-color: #f59e0b;
    background: #fffbeb;
}

.image-preview-container img {
    max-width: 100%;
    max-height: 100%;
    object-fit: cover;
}

.image-preview-container .placeholder-content {
    text-align: center;
    color: var(--cb-text-hint);
}

.image-preview-container .placeholder-content i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.dark-mode .part-card {
    background: var(--card-bg);
    border-color: var(--border);
}

.dark-mode .image-preview-container {
    background: var(--input-bg);
    border-color: var(--border);
}

.dark-mode .image-preview-container:hover {
    border-color: #f59e0b;
    background: rgba(245, 158, 11, 0.1);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let partIndex = {{ ($topic->parts && count($topic->parts) > 0) ? count($topic->parts) : 0 }};
    const partsContainer = document.getElementById('partsContainer');
    const noPartsMessage = document.getElementById('noPartsMessage');
    const addPartBtn = document.getElementById('addPartBtn');

    function updatePartsUI() {
        const parts = partsContainer.querySelectorAll('.part-card');
        noPartsMessage.style.display = parts.length === 0 ? 'flex' : 'none';
        const badge = document.querySelector('.cb-count-badge');
        if (badge) badge.textContent = parts.length;
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
                    <label class="cb-field-label small">Image</label>
                    <div class="image-preview-container" onclick="this.querySelector('input[type=file]').click()">
                        <input type="file" name="part_images[${index}]" accept="image/*" class="d-none" onchange="previewPartImage(this)">
                        <input type="hidden" name="parts[${index}][existing_image]" value="">
                        <div class="placeholder-content">
                            <i class="fas fa-image d-block"></i>
                            <small>Click to upload</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="mb-3">
                        <label class="cb-field-label small">Title / Name</label>
                        <input type="text" class="form-control" name="parts[${index}][title]"
                               placeholder="e.g., Benjamin Franklin">
                    </div>
                    <div>
                        <label class="cb-field-label small">Explanation / Description</label>
                        <textarea class="form-control" name="parts[${index}][explanation]" rows="3"
                                  placeholder="e.g., American scientist and inventor..."></textarea>
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
        updatePartsUI();
    });

    window.removePart = function(btn) {
        const card = btn.closest('.part-card');
        card.remove();
        renumberParts();
        updatePartsUI();

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
                const hiddenInput = container.querySelector('input[type=hidden]');
                const hiddenInputHtml = hiddenInput ? hiddenInput.outerHTML : '';

                container.innerHTML = `
                    <input type="file" name="${input.name}" accept="image/*" class="d-none" onchange="previewPartImage(this)">
                    ${hiddenInputHtml}
                    <img src="${e.target.result}" alt="Preview">
                `;
            };
            reader.readAsDataURL(input.files[0]);
        }
    };

    updatePartsUI();
});
</script>
@endpush
@endsection
