@extends('layouts.app')

@section('title', 'Create Task Sheet')

@section('content')
<div class="content-area">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('content.management') }}">Content</a></li>
            <li class="breadcrumb-item"><a href="{{ route('modules.show', $informationSheet->module_id) }}">{{ $informationSheet->module->module_name }}</a></li>
            <li class="breadcrumb-item active">Create Task Sheet</li>
        </ol>
    </nav>

    <form action="{{ route('task-sheets.store', $informationSheet) }}" method="POST" enctype="multipart/form-data" id="taskSheetForm">
        @csrf

        <div class="cb-container">
            {{-- Sidebar --}}
            <div class="cb-sidebar">
                <div class="cb-sidebar__title">Task Sheet Guide</div>

                <div class="cb-sidebar__group">
                    <div class="cb-sidebar__group-label"><i class="fas fa-info-circle"></i> Sections</div>
                    <a href="#section-basic" class="cb-sidebar__item">
                        <span class="cb-sidebar__item-icon cb-sidebar__item-icon--blue"><i class="fas fa-pen"></i></span>
                        <span class="cb-sidebar__item-text">
                            <span class="cb-sidebar__item-name">Basic Info</span>
                            <span class="cb-sidebar__item-desc">Title, number, description</span>
                        </span>
                    </a>
                    <a href="#section-objectives" class="cb-sidebar__item">
                        <span class="cb-sidebar__item-icon cb-sidebar__item-icon--green"><i class="fas fa-bullseye"></i></span>
                        <span class="cb-sidebar__item-text">
                            <span class="cb-sidebar__item-name">Objectives</span>
                            <span class="cb-sidebar__item-desc">Learning goals</span>
                        </span>
                    </a>
                    <a href="#section-materials" class="cb-sidebar__item">
                        <span class="cb-sidebar__item-icon cb-sidebar__item-icon--orange"><i class="fas fa-tools"></i></span>
                        <span class="cb-sidebar__item-text">
                            <span class="cb-sidebar__item-name">Materials</span>
                            <span class="cb-sidebar__item-desc">Equipment needed</span>
                        </span>
                    </a>
                    <a href="#section-safety" class="cb-sidebar__item">
                        <span class="cb-sidebar__item-icon cb-sidebar__item-icon--red"><i class="fas fa-shield-alt"></i></span>
                        <span class="cb-sidebar__item-text">
                            <span class="cb-sidebar__item-name">Safety</span>
                            <span class="cb-sidebar__item-desc">Precautions</span>
                        </span>
                    </a>
                    <a href="#section-items" class="cb-sidebar__item">
                        <span class="cb-sidebar__item-icon cb-sidebar__item-icon--purple"><i class="fas fa-list-check"></i></span>
                        <span class="cb-sidebar__item-text">
                            <span class="cb-sidebar__item-name">Task Items</span>
                            <span class="cb-sidebar__item-desc">Parts to evaluate</span>
                        </span>
                    </a>
                </div>

                <div class="cb-sidebar__info">
                    <div class="cb-sidebar__info-title"><i class="fas fa-lightbulb"></i> Tips</div>
                    Be specific with objectives and expected findings. Students will use these to self-assess their work.
                </div>
            </div>

            {{-- Main Panel --}}
            <div class="cb-main">
                <div class="cb-header cb-header--task">
                    <h4><i class="fas fa-clipboard-list me-2"></i>Create Task Sheet</h4>
                    <p>Define tasks with items, objectives, and safety guidelines</p>
                </div>

                <div class="cb-body">
                    {{-- Context Badge --}}
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
                                    <label class="cb-field-label">Task Number <span class="required">*</span></label>
                                    <input type="text" class="form-control @error('task_number') is-invalid @enderror" name="task_number" value="{{ old('task_number') }}" placeholder="e.g., TS-1.1" required>
                                    @error('task_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-8 mb-3">
                                    <label class="cb-field-label">Title <span class="required">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}" required>
                                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="cb-field-label">Description <span class="optional">(optional)</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="3">{{ old('description') }}</textarea>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label class="cb-field-label">Instructions <span class="required">*</span></label>
                                <textarea class="form-control @error('instructions') is-invalid @enderror" name="instructions" rows="4" required>{{ old('instructions') }}</textarea>
                                <div class="cb-field-hint">Provide clear step-by-step instructions for students</div>
                                @error('instructions')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- Objectives --}}
                    <div class="cb-section" id="section-objectives">
                        <div class="cb-section__title"><i class="fas fa-bullseye"></i> Objectives</div>
                        <div id="objectives-container">
                            <div class="cb-list-item">
                                <i class="fas fa-grip-vertical cb-list-item__handle"></i>
                                <input type="text" class="form-control" name="objectives[]" placeholder="Enter objective" required>
                                <button type="button" class="cb-list-item__remove" onclick="DynamicForm.removeListItem(this)"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        <button type="button" class="cb-add-btn mt-2" onclick="DynamicForm.addListItem('objectives-container', 'objectives[]', 'Enter objective', true)">
                            <i class="fas fa-plus"></i> Add Objective
                        </button>
                    </div>

                    {{-- Materials --}}
                    <div class="cb-section" id="section-materials">
                        <div class="cb-section__title"><i class="fas fa-tools"></i> Materials / Equipment</div>
                        <div id="materials-container">
                            <div class="cb-list-item">
                                <i class="fas fa-grip-vertical cb-list-item__handle"></i>
                                <input type="text" class="form-control" name="materials[]" placeholder="Enter material" required>
                                <button type="button" class="cb-list-item__remove" onclick="DynamicForm.removeListItem(this)"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        <button type="button" class="cb-add-btn mt-2" onclick="DynamicForm.addListItem('materials-container', 'materials[]', 'Enter material', true)">
                            <i class="fas fa-plus"></i> Add Material
                        </button>
                    </div>

                    {{-- Safety --}}
                    <div class="cb-section" id="section-safety">
                        <div class="cb-section__title"><i class="fas fa-shield-alt"></i> Safety Precautions</div>
                        <div id="safety-container">
                            <div class="cb-list-item">
                                <i class="fas fa-grip-vertical cb-list-item__handle"></i>
                                <input type="text" class="form-control" name="safety_precautions[]" placeholder="Enter safety precaution">
                                <button type="button" class="cb-list-item__remove" onclick="DynamicForm.removeListItem(this)"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        <button type="button" class="cb-add-btn mt-2" onclick="DynamicForm.addListItem('safety-container', 'safety_precautions[]', 'Enter safety precaution')">
                            <i class="fas fa-plus"></i> Add Safety Precaution
                        </button>
                    </div>

                    {{-- Task Items --}}
                    <div class="cb-section" id="section-items">
                        <div class="cb-items-header">
                            <h5><i class="fas fa-list-check"></i> Task Items <span class="cb-count-badge">1</span></h5>
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
                                        <div class="col-md-6 mb-3">
                                            <label class="cb-field-label">Part Name <span class="required">*</span></label>
                                            <input type="text" class="form-control" name="items[0][part_name]" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="cb-field-label">Description <span class="required">*</span></label>
                                            <input type="text" class="form-control" name="items[0][description]" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="cb-field-label">Expected Finding <span class="required">*</span></label>
                                            <input type="text" class="form-control" name="items[0][expected_finding]" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="cb-field-label">Acceptable Range <span class="required">*</span></label>
                                            <input type="text" class="form-control" name="items[0][acceptable_range]" required>
                                        </div>
                                        <input type="hidden" name="items[0][order]" value="0">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="cb-add-btn" onclick="addTaskItem()">
                            <i class="fas fa-plus"></i> Add Task Item
                        </button>
                    </div>

                    {{-- Image Upload --}}
                    <div class="cb-section">
                        <div class="cb-section__title"><i class="fas fa-image"></i> Reference Image</div>
                        <label class="cb-upload-area" id="imageUploadArea">
                            <input type="file" class="d-none" name="image" accept="image/*" onchange="handleImageSelect(this)">
                            <i class="fas fa-cloud-upload-alt d-block"></i>
                            <div class="cb-upload-area__text">
                                <strong>Click to upload</strong> or drag and drop<br>
                                <small>PNG, JPG, GIF up to 5MB</small>
                            </div>
                            <img id="imagePreview" class="img-fluid mt-2 d-none" style="max-height: 200px; border-radius: 8px;">
                        </label>
                        @error('image')<div class="text-danger mt-1" style="font-size: 0.85rem;">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Footer --}}
                <div class="cb-footer">
                    <a href="{{ route('information-sheets.show', ['module' => $informationSheet->module_id, 'informationSheet' => $informationSheet->id]) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Cancel
                    </a>
                    <div class="btn-group-footer">
                        <span class="cb-footer__hint d-none d-md-inline">All fields marked * are required</span>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Create Task Sheet
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let itemCount = 1;

function addTaskItem() {
    DynamicForm.addItemCard('items-container', 'item-card', (count) => `
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="cb-field-label">Part Name <span class="required">*</span></label>
                <input type="text" class="form-control" name="items[${count}][part_name]" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="cb-field-label">Description <span class="required">*</span></label>
                <input type="text" class="form-control" name="items[${count}][description]" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="cb-field-label">Expected Finding <span class="required">*</span></label>
                <input type="text" class="form-control" name="items[${count}][expected_finding]" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="cb-field-label">Acceptable Range <span class="required">*</span></label>
                <input type="text" class="form-control" name="items[${count}][acceptable_range]" required>
            </div>
            <input type="hidden" name="items[${count}][order]" value="${count}">
        </div>
    `, 'Item');
    itemCount++;
}

function handleImageSelect(input) {
    const area = document.getElementById('imageUploadArea');
    const preview = document.getElementById('imagePreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = (e) => {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
            area.classList.add('has-file');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
