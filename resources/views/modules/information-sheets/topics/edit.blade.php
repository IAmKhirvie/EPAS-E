@extends('layouts.app')

@section('title', 'Edit Topic - EPAS-E')

@section('content')
<div class="content-area">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('content.management') }}">Courses</a></li>
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

                    {{-- Block-based Content Editor --}}
                    @if($topic->usesBlocks())
                        @include('components.block-editor', ['existingBlocks' => $topic->blocks])
                    @else
                        {{-- Legacy topic: offer conversion to blocks --}}
                        <div class="cb-section" id="legacyConvertSection">
                            <div class="alert alert-info d-flex align-items-center gap-3">
                                <i class="fas fa-info-circle fa-lg"></i>
                                <div class="flex-grow-1">
                                    <strong>This topic uses the legacy content format.</strong>
                                    Convert it to the new block editor for more flexible layouts.
                                </div>
                                <button type="button" class="btn btn-primary btn-sm" id="convertToBlocksBtn">
                                    <i class="fas fa-magic me-1"></i>Convert to Blocks
                                </button>
                            </div>
                        </div>
                        @include('components.block-editor', ['existingBlocks' => []])
                    @endif

                    @error('blocks')<div class="text-danger small mt-1">{{ $message }}</div>@enderror

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
                    <a href="{{ route('content.management') }}" class="btn btn-outline-secondary">
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

@if(!$topic->usesBlocks())
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const convertBtn = document.getElementById('convertToBlocksBtn');
    if (!convertBtn) return;

    convertBtn.addEventListener('click', function() {
        // Convert legacy content + parts to blocks
        const legacyData = @json([
            'content' => $topic->content,
            'parts' => $topic->parts,
            'document_content' => $topic->document_content,
            'file_path' => $topic->file_path,
            'original_filename' => $topic->original_filename,
        ]);

        const blocksContainer = document.getElementById('blocksContainer');
        const addBlockByType = function(type) {
            const btn = document.querySelector(`.block-type-btn[data-block-type="${type}"]`);
            if (btn) btn.click();
        };

        // Convert document to a document block
        if (legacyData.document_content || legacyData.file_path) {
            addBlockByType('document');
            // Set the existing document data
            setTimeout(function() {
                const lastCard = blocksContainer.lastElementChild;
                if (lastCard) {
                    const docInput = lastCard.querySelector('.block-existing-doc');
                    if (docInput) docInput.value = legacyData.file_path || '';
                    const docNameInput = lastCard.querySelector('.block-existing-doc-name');
                    if (docNameInput) docNameInput.value = legacyData.original_filename || '';
                    // Update the display
                    const docUpload = lastCard.querySelector('.block-doc-upload');
                    if (docUpload && legacyData.original_filename) {
                        docUpload.innerHTML = `
                            <input type="file" name="${docUpload.querySelector('input[type=file]')?.name || ''}" accept=".pdf,.xlsx,.xls,.doc,.docx,.ppt,.pptx" class="d-none" onchange="window.blockEditor.previewDoc(this)">
                            <div class="placeholder-content">
                                <i class="fas fa-file-alt d-block" style="font-size:2rem;color:#6d9773;"></i>
                                <span class="doc-name">${legacyData.original_filename}</span><br>
                                <small>Click to replace</small>
                            </div>
                        `;
                    }
                }
            }, 100);
        }

        // Convert content to a text block
        if (legacyData.content && legacyData.content.trim()) {
            addBlockByType('text');
            setTimeout(function() {
                const lastCard = blocksContainer.lastElementChild;
                if (lastCard) {
                    const editorContent = lastCard.querySelector('.rich-editor-content');
                    const editorHidden = lastCard.querySelector('.rich-editor-hidden');
                    if (editorContent) editorContent.innerHTML = legacyData.content;
                    if (editorHidden) editorHidden.value = legacyData.content;
                }
            }, 200);
        }

        // Convert parts to image_text or text blocks
        if (legacyData.parts && legacyData.parts.length > 0) {
            legacyData.parts.forEach(function(part, idx) {
                const hasImage = part.image && part.image.trim();
                const type = hasImage ? 'image_text' : 'text';

                setTimeout(function() {
                    addBlockByType(type);

                    setTimeout(function() {
                        const lastCard = blocksContainer.lastElementChild;
                        if (!lastCard) return;

                        if (type === 'image_text') {
                            // Set existing image
                            const existingImg = lastCard.querySelector('.block-existing-image');
                            if (existingImg) existingImg.value = part.image;
                            // Show image preview
                            const imgUpload = lastCard.querySelector('.block-image-upload');
                            if (imgUpload) {
                                const fileInput = imgUpload.querySelector('input[type=file]');
                                imgUpload.innerHTML = `
                                    <input type="file" name="${fileInput?.name || ''}" accept="image/*" class="d-none" onchange="window.blockEditor.previewImage(this)">
                                    <img src="${part.image}" alt="Part image">
                                `;
                            }
                        }

                        // Build content from title + explanation
                        let html = '';
                        if (part.title) html += `<h4>${part.title}</h4>`;
                        if (part.explanation) html += `<p>${part.explanation.replace(/\n/g, '<br>')}</p>`;

                        const editorContent = lastCard.querySelector('.rich-editor-content');
                        const editorHidden = lastCard.querySelector('.rich-editor-hidden');
                        // For image_text, get the last editor (the text one, not any other)
                        const editors = lastCard.querySelectorAll('.rich-editor-content');
                        const hiddens = lastCard.querySelectorAll('.rich-editor-hidden');
                        const targetEditor = editors[editors.length - 1];
                        const targetHidden = hiddens[hiddens.length - 1];
                        if (targetEditor) targetEditor.innerHTML = html;
                        if (targetHidden) targetHidden.value = html;
                    }, 100);
                }, 300 + (idx * 200));
            });
        }

        // Hide the conversion section
        document.getElementById('legacyConvertSection').style.display = 'none';
    });
});
</script>
@endpush
@endif
@endsection
