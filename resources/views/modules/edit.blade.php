@extends('layouts.app')

@section('title', 'Edit Module - EPAS-E')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Edit Module: {{ $module->module_number }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('modules.update', $module) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="qualification_title" class="form-label required">Qualification Title</label>
                                    <input type="text" 
                                           class="form-control @error('qualification_title') is-invalid @enderror" 
                                           id="qualification_title" 
                                           name="qualification_title" 
                                           value="{{ old('qualification_title', $module->qualification_title) }}" 
                                           required>
                                    @error('qualification_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="unit_of_competency" class="form-label required">Unit of Competency</label>
                                    <input type="text" 
                                           class="form-control @error('unit_of_competency') is-invalid @enderror" 
                                           id="unit_of_competency" 
                                           name="unit_of_competency" 
                                           value="{{ old('unit_of_competency', $module->unit_of_competency) }}" 
                                           required>
                                    @error('unit_of_competency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="module_title" class="form-label required">Module Title</label>
                                    <input type="text" 
                                           class="form-control @error('module_title') is-invalid @enderror" 
                                           id="module_title" 
                                           name="module_title" 
                                           value="{{ old('module_title', $module->module_title) }}" 
                                           required>
                                    @error('module_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="module_number" class="form-label required">Module Number</label>
                                    <input type="text" 
                                           class="form-control @error('module_number') is-invalid @enderror" 
                                           id="module_number" 
                                           name="module_number" 
                                           value="{{ old('module_number', $module->module_number) }}" 
                                           required>
                                    @error('module_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="module_name" class="form-label required">Module Name</label>
                            <input type="text" 
                                   class="form-control @error('module_name') is-invalid @enderror" 
                                   id="module_name" 
                                   name="module_name" 
                                   value="{{ old('module_name', $module->module_name) }}" 
                                   required>
                            @error('module_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="table_of_contents" class="form-label">Table of Contents</label>
                            <textarea class="form-control @error('table_of_contents') is-invalid @enderror" 
                                      id="table_of_contents" 
                                      name="table_of_contents" 
                                      rows="6">{{ old('table_of_contents', $module->table_of_contents) }}</textarea>
                            @error('table_of_contents')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="how_to_use_cblm" class="form-label">How to Use CBLM</label>
                            <textarea class="form-control @error('how_to_use_cblm') is-invalid @enderror" 
                                      id="how_to_use_cblm" 
                                      name="how_to_use_cblm" 
                                      rows="4">{{ old('how_to_use_cblm', $module->how_to_use_cblm) }}</textarea>
                            @error('how_to_use_cblm')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="introduction" class="form-label">Introduction</label>
                            <textarea class="form-control @error('introduction') is-invalid @enderror" 
                                      id="introduction" 
                                      name="introduction" 
                                      rows="4">{{ old('introduction', $module->introduction) }}</textarea>
                            @error('introduction')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="learning_outcomes" class="form-label">Learning Outcomes</label>
                            <textarea class="form-control @error('learning_outcomes') is-invalid @enderror" 
                                      id="learning_outcomes" 
                                      name="learning_outcomes" 
                                      rows="4">{{ old('learning_outcomes', $module->learning_outcomes) }}</textarea>
                            @error('learning_outcomes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" 
                                   class="form-check-input" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1" 
                                   {{ old('is_active', $module->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active Module</label>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('modules.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Module</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection