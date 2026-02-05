@extends('layouts.app')

@section('title', $homework->title)

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-8">
            <!-- Homework Content -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-light text-info me-2">{{ $homework->homework_number }}</span>
                            <h4 class="mb-0 d-inline">{{ $homework->title }}</h4>
                        </div>
                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'instructor')
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-cog"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('homeworks.edit', [$homework->informationSheet, $homework]) }}">
                                        <i class="fas fa-edit me-2"></i>Edit
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('homeworks.destroy', [$homework->informationSheet, $homework]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this homework?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-trash me-2"></i>Delete
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Due Date Alert -->
                    @if($homework->is_past_due)
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>This homework is past due!
                    </div>
                    @elseif($homework->days_until_due <= 3)
                    <div class="alert alert-warning">
                        <i class="fas fa-clock me-2"></i>Due in {{ $homework->days_until_due }} day(s)!
                    </div>
                    @endif

                    @if($homework->description)
                    <p class="lead">{{ $homework->description }}</p>
                    @endif

                    <!-- Instructions -->
                    <div class="mb-4">
                        <h5><i class="fas fa-list-ol text-info me-2"></i>Instructions</h5>
                        <div class="bg-light p-3 rounded">
                            {!! nl2br(e($homework->instructions)) !!}
                        </div>
                    </div>

                    <!-- Reference Images -->
                    @if(count($homework->reference_images_list) > 0)
                    <div class="mb-4">
                        <h5><i class="fas fa-images text-info me-2"></i>Reference Images</h5>
                        <div class="row">
                            @foreach($homework->reference_images_list as $image)
                            <div class="col-md-4 mb-3">
                                <a href="{{ Storage::url($image) }}" target="_blank">
                                    <img src="{{ Storage::url($image) }}" alt="Reference" class="img-fluid rounded shadow-sm">
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Student Submission Form -->
            @if(auth()->user()->role === 'student')
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-upload me-2"></i>Submit Your Work</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('homeworks.submit', $homework) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="submission_file" class="form-label">Upload File <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="submission_file" name="submission_file" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.zip" required>
                            <small class="text-muted">Accepted formats: JPG, PNG, PDF, DOC, DOCX, ZIP (Max: 10MB)</small>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description/Notes</label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Add any notes about your submission..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="work_hours" class="form-label">Time Spent (hours)</label>
                            <input type="number" class="form-control" id="work_hours" name="work_hours" step="0.5" min="0" placeholder="How long did this take?">
                        </div>
                        <button type="submit" class="btn btn-primary" {{ $homework->is_past_due ? 'disabled' : '' }}>
                            <i class="fas fa-paper-plane me-1"></i>Submit Homework
                        </button>
                        @if($homework->is_past_due)
                        <small class="text-danger ms-2">Submissions are closed</small>
                        @endif
                    </form>
                </div>
            </div>
            @endif

            <!-- Submissions List (for instructors) -->
            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'instructor')
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Submissions ({{ $homework->submission_count }})</h5>
                </div>
                <div class="card-body">
                    @if($homework->submissions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Submitted</th>
                                    <th>Score</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($homework->submissions as $submission)
                                <tr>
                                    <td>{{ $submission->user->first_name }} {{ $submission->user->last_name }}</td>
                                    <td>
                                        {{ $submission->submitted_at->format('M d, Y H:i') }}
                                        @if($submission->is_late)
                                        <span class="badge bg-danger">Late</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($submission->score !== null)
                                        <span class="badge bg-{{ $submission->score >= 70 ? 'success' : 'warning' }}">
                                            {{ $submission->score }}/{{ $homework->max_points }}
                                        </span>
                                        @else
                                        <span class="badge bg-secondary">Not graded</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ Storage::url($submission->file_path) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted text-center mb-0">No submissions yet</p>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Due Date & Points -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-{{ $homework->is_past_due ? 'danger' : 'primary' }} text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Deadline</h5>
                </div>
                <div class="card-body text-center">
                    <h3 class="text-{{ $homework->is_past_due ? 'danger' : 'primary' }}">
                        {{ $homework->due_date->format('M d, Y') }}
                    </h3>
                    <p class="text-muted mb-0">{{ $homework->due_date->format('h:i A') }}</p>
                    <hr>
                    <div class="d-flex justify-content-around">
                        <div>
                            <h4 class="text-info mb-0">{{ $homework->max_points }}</h4>
                            <small class="text-muted">Max Points</small>
                        </div>
                        <div>
                            <h4 class="text-success mb-0">{{ $homework->submission_count }}</h4>
                            <small class="text-muted">Submissions</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Requirements -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>Requirements</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach($homework->requirements_list as $requirement)
                        <li class="list-group-item">
                            <i class="fas fa-check-square text-warning me-2"></i>{{ $requirement }}
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Submission Guidelines -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-clipboard-check me-2"></i>Submission Guidelines</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach($homework->submission_guidelines_list as $guideline)
                        <li class="list-group-item">
                            <i class="fas fa-info-circle text-success me-2"></i>{{ $guideline }}
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Navigation -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <a href="{{ route('information-sheets.show', ['module' => $homework->informationSheet->module_id, 'informationSheet' => $homework->informationSheet->id]) }}" class="btn btn-outline-primary w-100">
                        <i class="fas fa-arrow-left me-2"></i>Back to Information Sheet
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
