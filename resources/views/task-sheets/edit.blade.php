@extends('layouts.app')

@section('title', 'Edit Task Sheet')

@section('content')
<div class="content-area">
    <nav aria-label="breadcrumb" class="mb-2">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('content.management') }}">Content</a></li>
            <li class="breadcrumb-item"><a href="{{ route('courses.modules.show', [$informationSheet->module->course_id, $informationSheet->module, $informationSheet->module->slug]) }}">{{ $informationSheet->module->module_name }}</a></li>
            <li class="breadcrumb-item active">Edit Task Sheet</li>
        </ol>
    </nav>

    <form action="{{ route('task-sheets.update', [$informationSheet, $taskSheet]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="cb-builder-layout">
            {{-- MAIN CONTENT --}}
            <div class="cb-main">
                <div class="cb-header cb-header--task">
                    <h4><i class="fas fa-edit me-2"></i>Edit Task Sheet</h4>
                    <p>{{ $taskSheet->task_number }} &mdash; {{ $informationSheet->title }}</p>
                </div>

                <div class="cb-body">
                    {{-- Compact Settings --}}
                    <div class="cb-settings">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="cb-field-label">Task Number <span class="required">*</span></label>
                                <input type="text" class="form-control @error('task_number') is-invalid @enderror" name="task_number" value="{{ old('task_number', $taskSheet->task_number) }}" required>
                                @error('task_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-5 mb-3">
                                <label class="cb-field-label">Title <span class="required">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title', $taskSheet->title) }}" required>
                                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="cb-field-label">Reference Image <span class="optional">(optional)</span></label>
                                <input type="file" class="form-control form-control-sm @error('image') is-invalid @enderror" name="image" accept="image/*">
                                @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                @if($taskSheet->image_path)
                                <div class="cb-field-hint mt-1"><i class="fas fa-image me-1"></i>Current image will be kept if empty</div>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="cb-field-label">Description <span class="optional">(optional)</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="2">{{ old('description', $taskSheet->description) }}</textarea>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="cb-field-label">Instructions <span class="required">*</span></label>
                                <textarea class="form-control @error('instructions') is-invalid @enderror" name="instructions" rows="2" required>{{ old('instructions', $taskSheet->instructions) }}</textarea>
                                @error('instructions')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- Objectives, Materials, Safety in compact row --}}
                    <div class="cb-detail-row">
                        <div class="cb-detail-col">
                            <div class="cb-section__title"><i class="fas fa-bullseye"></i> Objectives</div>
                            <div id="objectives-container">
                                @foreach($taskSheet->objectives_list as $objective)
                                <div class="cb-list-item">
                                    <i class="fas fa-grip-vertical cb-list-item__handle"></i>
                                    <input type="text" class="form-control form-control-sm" name="objectives[]" value="{{ $objective }}" required>
                                    <button type="button" class="cb-list-item__remove" onclick="DynamicForm.removeListItem(this)"><i class="fas fa-times"></i></button>
                                </div>
                                @endforeach
                            </div>
                            <button type="button" class="cb-add-btn mt-1" onclick="DynamicForm.addListItem('objectives-container', 'objectives[]', 'Enter objective', true)">
                                <i class="fas fa-plus"></i> Add Objective
                            </button>
                        </div>
                        <div class="cb-detail-col">
                            <div class="cb-section__title"><i class="fas fa-tools"></i> Materials</div>
                            <div id="materials-container">
                                @foreach($taskSheet->materials_list as $material)
                                <div class="cb-list-item">
                                    <i class="fas fa-grip-vertical cb-list-item__handle"></i>
                                    <input type="text" class="form-control form-control-sm" name="materials[]" value="{{ $material }}" required>
                                    <button type="button" class="cb-list-item__remove" onclick="DynamicForm.removeListItem(this)"><i class="fas fa-times"></i></button>
                                </div>
                                @endforeach
                            </div>
                            <button type="button" class="cb-add-btn mt-1" onclick="DynamicForm.addListItem('materials-container', 'materials[]', 'Enter material', true)">
                                <i class="fas fa-plus"></i> Add Material
                            </button>
                        </div>
                        <div class="cb-detail-col">
                            <div class="cb-section__title"><i class="fas fa-shield-alt"></i> Safety</div>
                            <div id="safety-container">
                                @forelse($taskSheet->safety_precautions_list as $safety)
                                <div class="cb-list-item">
                                    <i class="fas fa-grip-vertical cb-list-item__handle"></i>
                                    <input type="text" class="form-control form-control-sm" name="safety_precautions[]" value="{{ $safety }}">
                                    <button type="button" class="cb-list-item__remove" onclick="DynamicForm.removeListItem(this)"><i class="fas fa-times"></i></button>
                                </div>
                                @empty
                                <div class="cb-list-item">
                                    <i class="fas fa-grip-vertical cb-list-item__handle"></i>
                                    <input type="text" class="form-control form-control-sm" name="safety_precautions[]" placeholder="Enter precaution">
                                    <button type="button" class="cb-list-item__remove" onclick="DynamicForm.removeListItem(this)"><i class="fas fa-times"></i></button>
                                </div>
                                @endforelse
                            </div>
                            <button type="button" class="cb-add-btn mt-1" onclick="DynamicForm.addListItem('safety-container', 'safety_precautions[]', 'Enter precaution')">
                                <i class="fas fa-plus"></i> Add Precaution
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="cb-footer">
                    <a href="{{ route('task-sheets.show', $taskSheet) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Cancel
                    </a>
                    <div class="btn-group-footer">
                        <span class="cb-footer__hint d-none d-md-inline">All fields marked * are required</span>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Task Sheet
                        </button>
                    </div>
                </div>
            </div>

            {{-- RIGHT SIDEBAR --}}
            <div class="cb-sidebar">
                <div class="cb-sidebar__title">
                    <i class="fas fa-edit me-2"></i>Edit Guide
                </div>

                <div class="cb-sidebar__group">
                    <div class="cb-sidebar__group-label"><i class="fas fa-info-circle"></i> Current Info</div>
                    <div class="cb-sidebar__info">
                        <div style="font-size: 0.8rem; line-height: 1.8;">
                            <strong>Items:</strong> {{ $taskSheet->items->count() }}<br>
                            <strong>Objectives:</strong> {{ count($taskSheet->objectives_list) }}<br>
                            <strong>Materials:</strong> {{ count($taskSheet->materials_list) }}
                        </div>
                    </div>
                </div>

                @if($taskSheet->image_path)
                <div class="cb-sidebar__group">
                    <div class="cb-sidebar__group-label"><i class="fas fa-image"></i> Current Image</div>
                    <img src="{{ Storage::url($taskSheet->image_path) }}" alt="Current" class="img-fluid rounded" style="max-height: 150px;">
                </div>
                @endif

                <div class="cb-sidebar__info">
                    <div class="cb-sidebar__info-title"><i class="fas fa-lightbulb"></i> Tips</div>
                    Be specific with objectives and expected findings. Students will use these to self-assess their work.
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
