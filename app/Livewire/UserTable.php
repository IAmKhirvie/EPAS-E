<?php

namespace App\Livewire;

use App\Constants\Roles;
use App\Models\Department;
use App\Models\User;
use App\Services\PendingItemsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class UserTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $roleFilter = '';
    public string $statusFilter = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    public ?string $routeRoleFilter = null;
    public string $pageTitle = 'User Management';

    public array $selectedUsers = [];
    public bool $selectAll = false;

    public bool $showBulkAssign = false;
    public string $bulkSection = '';
    public string $bulkSchoolYear = '';
    public string $bulkDepartment = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => '', 'as' => 'role'],
        'statusFilter' => ['except' => '', 'as' => 'status'],
        'sortField' => ['except' => 'created_at', 'as' => 'sort'],
        'sortDirection' => ['except' => 'desc', 'as' => 'dir'],
    ];

    public function mount(?string $routeRoleFilter = null, string $pageTitle = 'User Management'): void
    {
        $this->routeRoleFilter = $routeRoleFilter;
        $this->pageTitle = $pageTitle;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRoleFilter(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSelectAll(bool $value): void
    {
        $this->selectedUsers = $value
            ? $this->getUserQuery()->pluck('id')->map(fn ($id) => (string) $id)->toArray()
            : [];
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

    public function approveUser(int $userId): void
    {
        User::where('id', $userId)->update(['stat' => 1]);
        session()->flash('success', 'User approved successfully.');
    }

    public function deleteUser(int $userId): void
    {
        if ($userId === Auth::id()) {
            session()->flash('error', 'You cannot delete your own account.');
            return;
        }

        $user = User::find($userId);
        if (!$user) {
            session()->flash('error', 'User not found.');
            return;
        }

        $user->delete();
        $this->selectedUsers = array_diff($this->selectedUsers, [(string) $userId]);
        session()->flash('success', 'User deleted successfully.');
    }

    public function bulkActivate(): void
    {
        if (Auth::user()->role !== Roles::ADMIN) {
            session()->flash('error', 'Only administrators can perform bulk actions.');
            return;
        }

        $updated = User::whereIn('id', $this->selectedUsers)->update(['stat' => 1]);
        $this->selectedUsers = [];
        $this->selectAll = false;
        session()->flash('success', "{$updated} user(s) activated.");
    }

    public function bulkDeactivate(): void
    {
        if (Auth::user()->role !== Roles::ADMIN) {
            session()->flash('error', 'Only administrators can perform bulk actions.');
            return;
        }

        $ids = collect($this->selectedUsers)->filter(fn ($id) => (int) $id !== Auth::id());
        $updated = User::whereIn('id', $ids)->update(['stat' => 0]);
        $this->selectedUsers = [];
        $this->selectAll = false;
        session()->flash('success', "{$updated} user(s) deactivated.");
    }

    public function bulkDelete(): void
    {
        if (Auth::user()->role !== Roles::ADMIN) {
            session()->flash('error', 'Only administrators can perform bulk actions.');
            return;
        }

        $ids = collect($this->selectedUsers)
            ->filter(fn ($id) => (int) $id !== Auth::id());
        $deleted = User::whereIn('id', $ids)->delete();
        $this->selectedUsers = [];
        $this->selectAll = false;
        session()->flash('success', "{$deleted} user(s) deleted.");
    }

    public function bulkAssignSection(): void
    {
        if (Auth::user()->role !== Roles::ADMIN) {
            session()->flash('error', 'Only administrators can perform bulk actions.');
            return;
        }

        $isInstructor = $this->routeRoleFilter === Roles::INSTRUCTOR;

        if ($isInstructor) {
            if (empty($this->bulkSection) && empty($this->bulkDepartment)) {
                session()->flash('error', 'Please select an advisory class or department.');
                return;
            }

            $data = [];
            if (!empty($this->bulkDepartment)) {
                $data['department_id'] = $this->bulkDepartment;
            }

            $updated = 0;
            $instructors = User::whereIn('id', $this->selectedUsers)->get();

            foreach ($instructors as $instructor) {
                if (!empty($data)) {
                    $instructor->update($data);
                }
                if (!empty($this->bulkSection)) {
                    // Assign advisory class via InstructorSection (avoid duplicates)
                    \App\Models\InstructorSection::firstOrCreate([
                        'user_id' => $instructor->id,
                        'section' => $this->bulkSection,
                    ]);
                }
                $updated++;
            }
        } else {
            if (empty($this->bulkSection) && empty($this->bulkSchoolYear)) {
                session()->flash('error', 'Please enter a section or school year.');
                return;
            }

            $data = [];
            if (!empty($this->bulkSection)) {
                $data['section'] = $this->bulkSection;
            }
            if (!empty($this->bulkSchoolYear)) {
                $data['school_year'] = $this->bulkSchoolYear;
            }

            $updated = User::whereIn('id', $this->selectedUsers)->update($data);
        }

        $this->selectedUsers = [];
        $this->selectAll = false;
        $this->showBulkAssign = false;
        $this->bulkSection = '';
        $this->bulkSchoolYear = '';
        $this->bulkDepartment = '';
        session()->flash('success', "{$updated} user(s) assigned successfully.");
    }

    private function getUserQuery()
    {
        $viewer = Auth::user();
        $query = User::with('department');

        $effectiveRole = $this->routeRoleFilter;
        if ($effectiveRole) {
            $query->where('role', $effectiveRole);
        }

        if ($viewer->role === Roles::INSTRUCTOR && $effectiveRole === Roles::STUDENT) {
            $viewer->advisory_section
                ? $query->where('section', $viewer->advisory_section)
                : $query->whereRaw('1 = 0');
        }

        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('ext_name', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%")
                    ->orWhere('student_id', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('section', 'like', "%{$search}%")
                    ->orWhere('room_number', 'like', "%{$search}%")
                    ->orWhereHas('department', fn ($dq) => $dq->where('name', 'like', "%{$search}%"));
            });
        }

        if (!$effectiveRole && $this->roleFilter) {
            $query->where('role', $this->roleFilter);
        }

        if ($this->statusFilter) {
            match ($this->statusFilter) {
                'active' => $query->where('stat', 1),
                'pending' => $query->where('stat', 0),
                'unverified' => $query->whereNull('email_verified_at'),
                default => null,
            };
        }

        return $query->orderBy($this->mapSortColumn($this->sortField), $this->sortDirection);
    }

    private function mapSortColumn(string $field): string
    {
        return match ($field) {
            'name' => 'last_name',
            'status' => 'stat',
            'department' => 'department_id',
            default => in_array($field, [
                'id', 'first_name', 'last_name', 'student_id', 'email',
                'role', 'section', 'school_year', 'stat', 'created_at',
            ]) ? $field : 'created_at',
        };
    }

    private function getFilterCounts(): array
    {
        $viewer = Auth::user();
        $query = User::query();

        if ($this->routeRoleFilter) {
            $query->where('role', $this->routeRoleFilter);
        }

        if ($viewer->role === Roles::INSTRUCTOR && $this->routeRoleFilter === Roles::STUDENT) {
            $viewer->advisory_section
                ? $query->where('section', $viewer->advisory_section)
                : $query->whereRaw('1 = 0');
        }

        $student = Roles::STUDENT;
        $instructor = Roles::INSTRUCTOR;
        $admin = Roles::ADMIN;

        return $query->selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN role = ? THEN 1 ELSE 0 END) as students,
            SUM(CASE WHEN role = ? THEN 1 ELSE 0 END) as instructors,
            SUM(CASE WHEN role = ? THEN 1 ELSE 0 END) as admins,
            SUM(CASE WHEN stat = 0 OR stat IS NULL THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN stat = 1 THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN email_verified_at IS NULL THEN 1 ELSE 0 END) as unverified
        ", [$student, $instructor, $admin])->first()->toArray();
    }

    public function render()
    {
        $availableSections = User::where('role', Roles::STUDENT)
            ->whereNotNull('section')
            ->where('section', '!=', '')
            ->distinct()
            ->orderBy('section')
            ->pluck('section');

        $users = $this->getUserQuery()->paginate(config('joms.pagination.users', 20));

        // Batch-load pending counts for current page users
        $pendingService = app(PendingItemsService::class);
        $pendingCounts = $pendingService->getPendingCountsForUsers($users->pluck('id'));

        return view('livewire.user-table', [
            'users' => $users,
            'pendingCounts' => $pendingCounts,
            'filterCounts' => $this->getFilterCounts(),
            'departments' => Department::all(),
            'availableSections' => $availableSections,
            'canDelete' => Auth::user()->role === Roles::ADMIN,
            'canCreate' => Auth::user()->role === Roles::ADMIN,
        ]);
    }
}
