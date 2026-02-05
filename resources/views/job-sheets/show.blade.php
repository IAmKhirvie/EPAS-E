@extends('layouts.app')

@section('title', $jobSheet->title)

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-8">
            <!-- Job Sheet Content -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-light text-success me-2">{{ $jobSheet->job_number }}</span>
                            <h4 class="mb-0 d-inline">{{ $jobSheet->title }}</h4>
                        </div>
                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'instructor')
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-cog"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('job-sheets.edit', [$jobSheet->informationSheet, $jobSheet]) }}">
                                        <i class="fas fa-edit me-2"></i>Edit
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('job-sheets.destroy', [$jobSheet->informationSheet, $jobSheet]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this job sheet?')">
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
                    @if($jobSheet->description)
                    <p class="lead">{{ $jobSheet->description }}</p>
                    @endif

                    <!-- Steps -->
                    <div class="mb-4">
                        <h5><i class="fas fa-list-ol text-success me-2"></i>Procedure Steps</h5>
                        <div class="timeline">
                            @foreach($jobSheet->steps->sortBy('step_number') as $step)
                            <div class="card mb-3 border-start border-success border-4">
                                <div class="card-body">
                                    <div class="d-flex align-items-start">
                                        <div class="step-number bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; min-width: 40px;">
                                            <strong>{{ $step->step_number }}</strong>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-2">Step {{ $step->step_number }}</h6>
                                            <p class="mb-2">{{ $step->instruction }}</p>
                                            <div class="alert alert-success py-2 mb-0">
                                                <small><strong>Expected Outcome:</strong> {{ $step->expected_outcome }}</small>
                                            </div>
                                            @if($step->image_path)
                                            <div class="mt-2">
                                                <img src="{{ Storage::url($step->image_path) }}" alt="Step {{ $step->step_number }}" class="img-fluid rounded" style="max-height: 200px;">
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student Submission Form -->
            @if(auth()->user()->role === 'student')
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-paper-plane me-2"></i>Submit Your Work</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('job-sheets.submit', $jobSheet) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Completed Steps <span class="text-danger">*</span></label>
                            @foreach($jobSheet->steps->sortBy('step_number') as $step)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="completed_steps[]" value="{{ $step->id }}" id="step{{ $step->id }}">
                                <label class="form-check-label" for="step{{ $step->id }}">
                                    Step {{ $step->step_number }}: {{ Str::limit($step->instruction, 50) }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                        <div class="mb-3">
                            <label for="observations" class="form-label">Observations <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="observations" name="observations" rows="3" required placeholder="Describe what you observed during the job..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="challenges" class="form-label">Challenges Encountered</label>
                            <textarea class="form-control" id="challenges" name="challenges" rows="2" placeholder="Any difficulties you faced..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="solutions" class="form-label">Solutions Applied</label>
                            <textarea class="form-control" id="solutions" name="solutions" rows="2" placeholder="How you solved the challenges..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check me-1"></i>Submit Job Sheet
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Objectives -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-bullseye me-2"></i>Objectives</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach($jobSheet->objectives_list as $objective)
                        <li class="list-group-item">
                            <i class="fas fa-check-circle text-success me-2"></i>{{ $objective }}
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Tools Required -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-tools me-2"></i>Tools Required</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach($jobSheet->tools_required_list as $tool)
                        <li class="list-group-item">
                            <i class="fas fa-wrench text-warning me-2"></i>{{ $tool }}
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Safety Requirements -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Safety Requirements</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach($jobSheet->safety_requirements_list as $safety)
                        <li class="list-group-item list-group-item-danger">
                            <i class="fas fa-shield-alt me-2"></i>{{ $safety }}
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Reference Materials -->
            @if(count($jobSheet->reference_materials_list) > 0)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-book me-2"></i>References</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach($jobSheet->reference_materials_list as $ref)
                        <li class="list-group-item">
                            <i class="fas fa-file-alt text-secondary me-2"></i>{{ $ref }}
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <!-- Navigation -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <a href="{{ route('information-sheets.show', ['module' => $jobSheet->informationSheet->module_id, 'informationSheet' => $jobSheet->informationSheet->id]) }}" class="btn btn-outline-primary w-100">
                        <i class="fas fa-arrow-left me-2"></i>Back to Information Sheet
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
