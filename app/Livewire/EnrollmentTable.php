<?php

namespace App\Livewire;

use App\Constants\Roles;
use App\Models\EnrollmentRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class EnrollmentTable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = 'all';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all', 'as' => 'status'],
        'sortField' => ['except' => 'created_at', 'as' => 'sort'],
        'sortDirection' => ['except' => 'desc', 'as' => 'dir'],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function approveRequest(int $id): void
    {
        if (Auth::user()->role !== Roles::ADMIN) {
            session()->flash('error', 'Only administrators can approve requests.');
            return;
        }

        try {
            $request = EnrollmentRequest::findOrFail($id);
            if (!$request->isPending()) {
                session()->flash('error', 'This request has already been processed.');
                return;
            }

            $request->approve(Auth::user());
            session()->flash('success', "Student {$request->student_display_name} has been enrolled in {$request->section}.");
        } catch (\Exception $e) {
            Log::error('Enrollment approval failed', ['error' => $e->getMessage(), 'enrollment_request_id' => $id]);
            session()->flash('error', 'Failed to approve enrollment request.');
        }
    }

    public function rejectRequest(int $id, ?string $notes = null): void
    {
        if (Auth::user()->role !== Roles::ADMIN) {
            session()->flash('error', 'Only administrators can reject requests.');
            return;
        }

        try {
            $request = EnrollmentRequest::findOrFail($id);
            if (!$request->isPending()) {
                session()->flash('error', 'This request has already been processed.');
                return;
            }

            $request->reject(Auth::user(), $notes ?? 'Rejected by admin.');
            session()->flash('success', 'Enrollment request rejected.');
        } catch (\Exception $e) {
            Log::error('Enrollment rejection failed', ['error' => $e->getMessage(), 'enrollment_request_id' => $id]);
            session()->flash('error', 'Failed to reject enrollment request.');
        }
    }

    public function cancelRequest(int $id): void
    {
        try {
            $request = EnrollmentRequest::findOrFail($id);
            if ($request->instructor_id !== Auth::id()) {
                session()->flash('error', 'You can only cancel your own requests.');
                return;
            }
            if (!$request->isPending()) {
                session()->flash('error', 'Only pending requests can be cancelled.');
                return;
            }

            $request->delete();
            session()->flash('success', 'Enrollment request cancelled.');
        } catch (\Exception $e) {
            Log::error('Enrollment cancellation failed', ['error' => $e->getMessage(), 'enrollment_request_id' => $id]);
            session()->flash('error', 'Failed to cancel enrollment request.');
        }
    }

    private function getQuery()
    {
        $user = Auth::user();

        $query = EnrollmentRequest::with(['instructor', 'student', 'processedBy']);

        // Instructors only see their own requests
        if ($user->role === Roles::INSTRUCTOR) {
            $query->byInstructor($user->id);
        }

        // Status filter
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // Search
        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                    ->orWhere('student_email', 'like', "%{$search}%")
                    ->orWhere('section', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($sq) use ($search) {
                        $sq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('student_id', 'like', "%{$search}%");
                    });
            });
        }

        $sortColumn = in_array($this->sortField, ['id', 'student_name', 'section', 'status', 'created_at'])
            ? $this->sortField : 'created_at';

        return $query->orderBy($sortColumn, $this->sortDirection);
    }

    private function getCounts(): array
    {
        $user = Auth::user();
        $base = EnrollmentRequest::query();

        if ($user->role === Roles::INSTRUCTOR) {
            $base->byInstructor($user->id);
        }

        return [
            'all' => (clone $base)->count(),
            'pending' => (clone $base)->pending()->count(),
            'approved' => (clone $base)->approved()->count(),
            'rejected' => (clone $base)->rejected()->count(),
        ];
    }

    public function render()
    {
        $user = Auth::user();

        return view('livewire.enrollment-table', [
            'requests' => $this->getQuery()->paginate(15),
            'counts' => $this->getCounts(),
            'isAdmin' => $user->role === Roles::ADMIN,
            'isInstructor' => $user->role === Roles::INSTRUCTOR,
        ]);
    }
}
