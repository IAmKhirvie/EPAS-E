@extends('layouts.app')

@section('title', 'Edit Information Sheet - EPAS-E')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        Edit Information Sheet: {{ $informationSheet->sheet_number }}
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('information-sheets.update', [$module, $informationSheet]) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sheet_number" class="form-label required">Sheet Number</label>
                                    <input type="text" 
                                           class="form-control @error('sheet_number') is-invalid @enderror" 
                                           id="sheet_number" 
                                           name="sheet_number" 
                                           value="{{ old('sheet_number', $informationSheet->sheet_number) }}" 
                                           required>
                                    @error('sheet_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title" class="form-label required">Sheet Title</label>
                                    <input type="text" 
                                           class="form-control @error('title') is-invalid @enderror" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title', $informationSheet->title) }}" 
                                           required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Current File -->
                        @if($informationSheet->file_path)
                        <div class="alert alert-info d-flex align-items-center mb-3">
                            <i class="fas fa-file-alt me-2"></i>
                            <div class="flex-grow-1">
                                <strong>Current File:</strong> {{ $informationSheet->original_filename }}
                            </div>
                            <a href="{{ route('information-sheets.download', [$module, $informationSheet]) }}"
                               class="btn btn-sm btn-primary">
                                <i class="fas fa-download me-1"></i>Download
                            </a>
                        </div>
                        @endif

                        <!-- File Upload -->
                        <div class="mb-3">
                            <label for="file" class="form-label">Upload PDF/Excel File (Optional)</label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror"
                                   id="file" name="file" accept=".pdf,.xlsx,.xls">
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Accepted: PDF, Excel (.xlsx, .xls). Max 10MB. {{ $informationSheet->file_path ? 'Uploading a new file will replace the current one.' : 'Text will be extracted into the content field.' }}
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Content</label>
                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                      id="content" 
                                      name="content" 
                                      rows="12">{{ old('content', $informationSheet->content) }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('modules.show', $module) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Module
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Information Sheet
                            </button>
                        </div>
                    </form>

                    <!-- Content Items Management -->
                    <div class="card mt-4">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-cubes me-2"></i>Content Items for Information Sheet {{ $informationSheet->sheet_number }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Quick Stats -->
                            <div class="row mb-4">
                                <div class="col-md-3 col-6">
                                    <div class="text-center p-3 border rounded">
                                        <div class="h4 mb-1 text-primary">{{ $informationSheet->topics->count() }}</div>
                                        <small class="text-muted">Topics</small>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="text-center p-3 border rounded">
                                        <div class="h4 mb-1 text-warning">{{ $informationSheet->selfChecks->count() }}</div>
                                        <small class="text-muted">Self-Checks</small>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="text-center p-3 border rounded">
                                        <div class="h4 mb-1 text-success">{{ $informationSheet->taskSheets->count() }}</div>
                                        <small class="text-muted">Task Sheets</small>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="text-center p-3 border rounded">
                                        <div class="h4 mb-1 text-info">{{ $informationSheet->jobSheets->count() }}</div>
                                        <small class="text-muted">Job Sheets</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Add Content Buttons -->
                            <div class="d-flex flex-wrap gap-2 mb-4">
                                <button type="button" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-file-alt me-1"></i>Add Topic
                                </button>
                                <button type="button" class="btn btn-outline-warning btn-sm">
                                    <i class="fas fa-question-circle me-1"></i>Add Self-Check
                                </button>
                                <button type="button" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-tasks me-1"></i>Add Task Sheet
                                </button>
                                <button type="button" class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-briefcase me-1"></i>Add Job Sheet
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-homework me-1"></i>Add Homework
                                </button>
                                <button type="button" class="btn btn-outline-dark btn-sm">
                                    <i class="fas fa-star me-1"></i>Add Performance Criteria
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-check-square me-1"></i>Add Check List
                                </button>
                            </div>

                            <!-- Existing Content Items -->
                            @if($informationSheet->topics->count() > 0 || $informationSheet->selfChecks->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Title/Number</th>
                                            <th>Order</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Topics -->
                                        @foreach($informationSheet->topics as $topic)
                                        <tr>
                                            <td><span class="badge bg-primary">Topic</span></td>
                                            <td>{{ $topic->title }}</td>
                                            <td>{{ $topic->order }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach

                                        <!-- Self Checks -->
                                        @foreach($informationSheet->selfChecks as $selfCheck)
                                        <tr>
                                            <td><span class="badge bg-warning text-dark">Self-Check</span></td>
                                            <td>Self Check {{ $selfCheck->check_number }}</td>
                                            <td>{{ $selfCheck->order }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center py-4">
                                <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                <p class="text-muted">No content items created yet. Use the buttons above to add topics, self-checks, and other content.</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @if($informationSheet->topics->isEmpty() && $informationSheet->selfChecks->isEmpty())
                        <hr class="my-4">
                        <div class="alert alert-warning">
                            <h6 class="alert-heading">
                                <i class="fas fa-exclamation-triangle me-2"></i>Danger Zone
                            </h6>
                            <p class="mb-2">This information sheet has no topics or self-checks. You can delete it if needed.</p>
                            <form action="{{ route('information-sheets.destroy', [$module, $informationSheet]) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this information sheet? This action cannot be undone.')">
                                    <i class="fas fa-trash me-1"></i>Delete Information Sheet
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection