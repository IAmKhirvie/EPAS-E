@extends('layouts.app')

@section('title', 'Create New Thread - Forums - JOMS LMS')

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('forums.index') }}">Forums</a></li>
            <li class="breadcrumb-item active">Create New Thread</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Create New Thread</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('forums.store-thread') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="category_id" class="form-label required-field">Category</label>
                            <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">Select a category...</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $selectedCategory) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="title" class="form-label required-field">Thread Title</label>
                            <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title') }}" placeholder="Enter a descriptive title..." required maxlength="255">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Keep it clear and descriptive so others can easily find and help.</div>
                        </div>

                        <div class="mb-4">
                            <label for="body" class="form-label required-field">Content</label>
                            <textarea name="body" id="body" class="form-control @error('body') is-invalid @enderror"
                                      rows="10" placeholder="Describe your question or topic in detail..." required>{{ old('body') }}</textarea>
                            @error('body')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Provide enough context and details. The more information you include, the better others can help you.
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('forums.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Create Thread
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tips Card -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Tips for a Great Thread</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Use a clear, specific title that summarizes your question</li>
                        <li>Choose the most appropriate category</li>
                        <li>Provide context and background information</li>
                        <li>Be specific about what you've already tried</li>
                        <li>Include any error messages or relevant details</li>
                        <li>Be respectful and constructive</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
