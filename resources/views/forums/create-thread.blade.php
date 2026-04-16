@extends('layouts.app')

@section('title', 'New Thread - ' . $category->name)

@section('content')
<div class="content-area">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('forums.index') }}">Forums</a></li>
            <li class="breadcrumb-item"><a href="{{ route('forums.category', $category) }}">{{ $category->name }}</a></li>
            <li class="breadcrumb-item active">New Thread</li>
        </ol>
    </nav>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent">
            <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Create New Thread</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('forums.store-thread', $category) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="title" class="form-label">Thread Title</label>
                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
                           value="{{ old('title') }}" required maxlength="255" placeholder="What's your question or topic?">
                    @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="body" class="form-label">Content</label>
                    <textarea name="body" id="body" class="form-control @error('body') is-invalid @enderror"
                              rows="8" required minlength="10" placeholder="Describe your question or topic in detail...">{{ old('body') }}</textarea>
                    @error('body')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-1"></i>Create Thread
                    </button>
                    <a href="{{ route('forums.category', $category) }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
