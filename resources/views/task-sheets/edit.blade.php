@extends('layouts.app')

@section('title', 'Edit Task Sheet')

@section('content')
<div class="content-area">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('content.management') }}">Content</a></li>
            <li class="breadcrumb-item active">Edit Task Sheet</li>
        </ol>
    </nav>

    <form action="{{ route('task-sheets.update', [$informationSheet, $taskSheet]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

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
                </div>

                <div class="cb-sidebar__info">
                    <div class="cb-sidebar__info-title"><i class="fas fa-lightbulb"></i> Tips</div>
                    Be specific with objectives and expected findings. Students will use these to self-assess their work.
                </div>
            </div>

            {{-- Main Panel --}}
            <div class="cb-main">
                <div class="cb-header cb-header--task">
                    <h4><i class="fas fa-edit me-2"></i>Edit Task Sheet</h4>
                    <p>Update task details, objectives, and items</p>
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
                                    <label class="cb-field-label">Task Number <span class="required">*</span></label>
                                    <input type="text" class="form-control @error('task_number') is-invalid @enderror" name="task_number" value="{{ old('task_number', $taskSheet->task_number) }}" required>
                                    @error('task_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-8 mb-3">
                                    <label class="cb-field-label">Title <span class="required">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title', $taskSheet->title) }}" required>
                                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="cb-field-label">Description <span class="optional">(optional)</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="3">{{ old('description', $taskSheet->description) }}</textarea>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label class="cb-field-label">Instructions <span class="required">*</span></label>
                                <textarea class="form-control @error('instructions') is-invalid @enderror" name="instructions" rows="4" required>{{ old('instructions', $taskSheet->instructions) }}</textarea>
                                @error('instructions')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- Objectives --}}
                    <div class="cb-section" id="section-objectives">
                        <div class="cb-section__title"><i class="fas fa-bullseye"></i> Objectives</div>
                        <div id="objectives-container">
                            @foreach($taskSheet->objectives_list as $objective)
                            <div class="cb-list-item">
                                <i class="fas fa-grip-vertical cb-list-item__handle"></i>
                                <input type="text" class="form-control" name="objectives[]" value="{{ $objective }}" required>
                                <button type="button" class="cb-list-item__remove" onclick="DynamicForm.removeListItem(this)"><i class="fas fa-times"></i></button>
                            </div>
                            @endforeach
                        </div>
                        <button type="button" class="cb-add-btn mt-2" onclick="DynamicForm.addListItem('objectives-container', 'objectives[]', 'Enter objective', true)">
                            <i class="fas fa-plus"></i> Add Objective
                        </button>
                    </div>

                    {{-- Materials --}}
                    <div class="cb-section" id="section-materials">
                        <div class="cb-section__title"><i class="fas fa-tools"></i> Materials / Equipment</div>
                        <div id="materials-container">
                            @foreach($taskSheet->materials_list as $material)
                            <div class="cb-list-item">
                                <i class="fas fa-grip-vertical cb-list-item__handle"></i>
                                <input type="text" class="form-control" name="materials[]" value="{{ $material }}" required>
                                <button type="button" class="cb-list-item__remove" onclick="DynamicForm.removeListItem(this)"><i class="fas fa-times"></i></button>
                            </div>
                            @endforeach
                        </div>
                        <button type="button" class="cb-add-btn mt-2" onclick="DynamicForm.addListItem('materials-container', 'materials[]', 'Enter material', true)">
                            <i class="fas fa-plus"></i> Add Material
                        </button>
                    </div>

                    {{-- Safety --}}
                    <div class="cb-section" id="section-safety">
                        <div class="cb-section__title"><i class="fas fa-shield-alt"></i> Safety Precautions</div>
                        <div id="safety-container">
                            @forelse($taskSheet->safety_precautions_list as $safety)
                            <div class="cb-list-item">
                                <i class="fas fa-grip-vertical cb-list-item__handle"></i>
                                <input type="text" class="form-control" name="safety_precautions[]" value="{{ $safety }}">
                                <button type="button" class="cb-list-item__remove" onclick="DynamicForm.removeListItem(this)"><i class="fas fa-times"></i></button>
                            </div>
                            @empty
                            <div class="cb-list-item">
                                <i class="fas fa-grip-vertical cb-list-item__handle"></i>
                                <input type="text" class="form-control" name="safety_precautions[]" placeholder="Enter safety precaution">
                                <button type="button" class="cb-list-item__remove" onclick="DynamicForm.removeListItem(this)"><i class="fas fa-times"></i></button>
                            </div>
                            @endforelse
                        </div>
                        <button type="button" class="cb-add-btn mt-2" onclick="DynamicForm.addListItem('safety-container', 'safety_precautions[]', 'Enter safety precaution')">
                            <i class="fas fa-plus"></i> Add Safety Precaution
                        </button>
                    </div>

                    {{-- Image Upload --}}
                    <div class="cb-section">
                        <div class="cb-section__title"><i class="fas fa-image"></i> Reference Image</div>
                        @if($taskSheet->image_path)
                        <div class="mb-3">
                            <img src="{{ Storage::url($taskSheet->image_path) }}" alt="Current image" class="img-thumbnail" style="max-height: 150px;">
                        </div>
                        @endif
                        <label class="cb-upload-area {{ $taskSheet->image_path ? 'has-file' : '' }}">
                            <input type="file" class="d-none" name="image" accept="image/*" onchange="handleImageSelect(this)">
                            <i class="fas fa-cloud-upload-alt d-block"></i>
                            <div class="cb-upload-area__text">
                                <strong>Click to upload</strong> a new image<br>
                                <small>Leave empty to keep current image</small>
                            </div>
                        </label>
                        @error('image')<div class="text-danger mt-1" style="font-size: 0.85rem;">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="cb-footer">
                    <a href="{{ route('task-sheets.show', $taskSheet) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Cancel
                    </a>
                    <div class="btn-group-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Task Sheet
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function handleImageSelect(input) {
    if (input.files && input.files[0]) {
        input.closest('.cb-upload-area').classList.add('has-file');
    }
}
</script>
@endsection
