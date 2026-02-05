@extends('layouts.app')

@section('title', $taskSheet->title)

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-8">
            <!-- Task Sheet Content -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-light text-primary me-2">{{ $taskSheet->task_number }}</span>
                            <h4 class="mb-0 d-inline">{{ $taskSheet->title }}</h4>
                        </div>
                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'instructor')
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-cog"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('task-sheets.edit', [$taskSheet->informationSheet, $taskSheet]) }}">
                                        <i class="fas fa-edit me-2"></i>Edit
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('task-sheets.destroy', [$taskSheet->informationSheet, $taskSheet]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this task sheet?')">
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
                    @if($taskSheet->description)
                    <p class="lead">{{ $taskSheet->description }}</p>
                    @endif

                    @if($taskSheet->image_path)
                    <div class="text-center mb-4">
                        <img src="{{ Storage::url($taskSheet->image_path) }}" alt="{{ $taskSheet->title }}" class="img-fluid rounded" style="max-height: 400px;">
                    </div>
                    @endif

                    <!-- Instructions -->
                    <div class="mb-4">
                        <h5><i class="fas fa-list-ol text-primary me-2"></i>Instructions</h5>
                        <div class="bg-light p-3 rounded">
                            {!! nl2br(e($taskSheet->instructions)) !!}
                        </div>
                    </div>

                    <!-- Task Items -->
                    <div class="mb-4">
                        <h5><i class="fas fa-tasks text-primary me-2"></i>Task Items</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Part Name</th>
                                        <th>Description</th>
                                        <th>Expected Finding</th>
                                        <th>Acceptable Range</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($taskSheet->items as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><strong>{{ $item->part_name }}</strong></td>
                                        <td>{{ $item->description }}</td>
                                        <td>{{ $item->expected_finding }}</td>
                                        <td><span class="badge bg-info">{{ $item->acceptable_range }}</span></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student Submission Form (Only for students) -->
            @if(auth()->user()->role === 'student')
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-paper-plane me-2"></i>Submit Your Findings</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('task-sheets.submit', $taskSheet) }}" method="POST">
                        @csrf
                        @foreach($taskSheet->items as $index => $item)
                        <div class="mb-3">
                            <label class="form-label">
                                <strong>{{ $item->part_name }}:</strong> {{ $item->description }}
                            </label>
                            <input type="text" class="form-control" name="findings[{{ $item->id }}]" placeholder="Enter your finding" required>
                            <small class="text-muted">Expected: {{ $item->expected_finding }} | Acceptable range: {{ $item->acceptable_range }}</small>
                        </div>
                        @endforeach
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check me-1"></i>Submit Task Sheet
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
                        @foreach($taskSheet->objectives_list as $objective)
                        <li class="list-group-item">
                            <i class="fas fa-check-circle text-success me-2"></i>{{ $objective }}
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Materials -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-tools me-2"></i>Materials/Equipment</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach($taskSheet->materials_list as $material)
                        <li class="list-group-item">
                            <i class="fas fa-wrench text-warning me-2"></i>{{ $material }}
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Safety Precautions -->
            @if(count($taskSheet->safety_precautions_list) > 0)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Safety Precautions</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach($taskSheet->safety_precautions_list as $safety)
                        <li class="list-group-item list-group-item-danger">
                            <i class="fas fa-shield-alt me-2"></i>{{ $safety }}
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <!-- Navigation -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <a href="{{ route('information-sheets.show', ['module' => $taskSheet->informationSheet->module_id, 'informationSheet' => $taskSheet->informationSheet->id]) }}" class="btn btn-outline-primary w-100">
                        <i class="fas fa-arrow-left me-2"></i>Back to Information Sheet
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
