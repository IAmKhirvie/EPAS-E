@extends('layouts.app')

@section('title', $checklist->title)

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-8">
            <!-- Checklist Content -->
            <div class="card shadow-sm mb-4">
                <div class="card-header text-white" style="background: #6f42c1;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-light text-dark me-2">{{ $checklist->checklist_number }}</span>
                            <h4 class="mb-0 d-inline">{{ $checklist->title }}</h4>
                        </div>
                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'instructor')
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-cog"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('checklists.edit', [$checklist->informationSheet, $checklist]) }}">
                                        <i class="fas fa-edit me-2"></i>Edit
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('checklists.destroy', [$checklist->informationSheet, $checklist]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this checklist?')">
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
                    @if($checklist->description)
                    <p class="lead">{{ $checklist->description }}</p>
                    @endif

                    <!-- Checklist Items -->
                    @php $items = json_decode($checklist->items, true) ?? []; @endphp
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%;">#</th>
                                    <th style="width: 50%;">Description</th>
                                    <th style="width: 20%;">Rating</th>
                                    <th style="width: 25%;">Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $index => $item)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $item['description'] ?? '' }}</td>
                                    <td class="text-center">
                                        @php $rating = $item['rating'] ?? 0; @endphp
                                        <div class="rating-stars">
                                            @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $rating ? 'text-warning' : 'text-muted' }}"></i>
                                            @endfor
                                        </div>
                                        <small class="text-muted">({{ $rating }}/5)</small>
                                    </td>
                                    <td>{{ $item['remarks'] ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="2" class="text-end"><strong>Total Score:</strong></td>
                                    <td class="text-center">
                                        <strong class="text-primary">{{ $checklist->total_score }} / {{ $checklist->max_score }}</strong>
                                    </td>
                                    <td>
                                        @php $percentage = $checklist->max_score > 0 ? ($checklist->total_score / $checklist->max_score) * 100 : 0; @endphp
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-{{ $percentage >= 80 ? 'success' : ($percentage >= 60 ? 'warning' : 'danger') }}"
                                                 role="progressbar"
                                                 style="width: {{ $percentage }}%">
                                                {{ number_format($percentage, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Evaluation Form (for instructors evaluating students) -->
            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'instructor')
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-clipboard-check me-2"></i>Evaluate Student</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('checklists.evaluate', $checklist) }}" method="POST">
                        @csrf
                        @foreach($items as $index => $item)
                        <div class="card mb-3">
                            <div class="card-body">
                                <p class="mb-2"><strong>{{ $index + 1 }}. {{ $item['description'] ?? '' }}</strong></p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">Rating</label>
                                        <select class="form-select" name="items[{{ $index }}][rating]" required>
                                            @for($i = 1; $i <= 5; $i++)
                                            <option value="{{ $i }}" {{ ($item['rating'] ?? 0) == $i ? 'selected' : '' }}>
                                                {{ $i }} - {{ ['Poor', 'Below Average', 'Average', 'Good', 'Excellent'][$i-1] }}
                                            </option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Remarks</label>
                                        <input type="text" class="form-control" name="items[{{ $index }}][remarks]" value="{{ $item['remarks'] ?? '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Save Evaluation
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Score Summary -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Score Summary</h5>
                </div>
                <div class="card-body text-center">
                    @php $percentage = $checklist->max_score > 0 ? ($checklist->total_score / $checklist->max_score) * 100 : 0; @endphp
                    <div class="display-4 text-{{ $percentage >= 80 ? 'success' : ($percentage >= 60 ? 'warning' : 'danger') }}">
                        {{ number_format($percentage, 1) }}%
                    </div>
                    <p class="text-muted">{{ $checklist->total_score }} out of {{ $checklist->max_score }} points</p>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <h5 class="text-primary">{{ count($items) }}</h5>
                            <small class="text-muted">Total Items</small>
                        </div>
                        <div class="col-6">
                            <h5 class="text-success">{{ $checklist->max_score }}</h5>
                            <small class="text-muted">Max Score</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Completion Info -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Details</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        @if($checklist->completed_by)
                        <li class="mb-2">
                            <i class="fas fa-user text-info me-2"></i>
                            <strong>Completed by:</strong> {{ optional(\App\Models\User::find($checklist->completed_by))->first_name ?? 'Unknown' }}
                        </li>
                        @endif
                        @if($checklist->completed_at)
                        <li class="mb-2">
                            <i class="fas fa-calendar text-info me-2"></i>
                            <strong>Completed:</strong> {{ $checklist->completed_at->format('M d, Y H:i') }}
                        </li>
                        @endif
                        @if($checklist->evaluated_by)
                        <li class="mb-2">
                            <i class="fas fa-user-check text-success me-2"></i>
                            <strong>Evaluated by:</strong> {{ optional(\App\Models\User::find($checklist->evaluated_by))->first_name ?? 'Unknown' }}
                        </li>
                        @endif
                        @if($checklist->evaluated_at)
                        <li>
                            <i class="fas fa-calendar-check text-success me-2"></i>
                            <strong>Evaluated:</strong> {{ $checklist->evaluated_at->format('M d, Y H:i') }}
                        </li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Navigation -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <a href="{{ route('information-sheets.show', ['module' => $checklist->informationSheet->module_id, 'informationSheet' => $checklist->informationSheet->id]) }}" class="btn btn-outline-primary w-100">
                        <i class="fas fa-arrow-left me-2"></i>Back to Information Sheet
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
