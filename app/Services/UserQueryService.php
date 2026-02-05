<?php

namespace App\Services;

use App\Constants\Roles;
use App\Models\Department;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Service for building complex user queries with filtering, searching, and sorting.
 *
 * Extracted from UserController to encapsulate query-building logic including:
 * - Role-based filtering (student, instructor, admin)
 * - Instructor advisory section scoping
 * - Full-text search across user fields and department name
 * - Status and verification filters
 * - Configurable sorting with column mapping
 * - Filter count aggregation for UI badges
 */
class UserQueryService
{
    /**
     * Build a paginated user query with filters, search, and sorting.
     *
     * @param Request     $request    The HTTP request containing search/filter/sort params
     * @param string|null $roleFilter Role to filter by (student, instructor, admin), or null for all
     * @param User        $viewer     The authenticated user viewing the list
     * @return LengthAwarePaginator
     */
    public function buildUserQuery(Request $request, ?string $roleFilter, User $viewer): LengthAwarePaginator
    {
        $search = $request->get('search');
        $filter = $request->get('filter');
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        if (!in_array(strtolower($direction), ['asc', 'desc'])) {
            $direction = 'desc';
        }

        $query = User::with('department');

        if ($roleFilter) {
            $query->where('role', $roleFilter);
        }

        // Instructors can only see students in their advisory section
        if ($viewer->role === Roles::INSTRUCTOR && $roleFilter === Roles::STUDENT) {
            if ($viewer->advisory_section) {
                $query->where('section', $viewer->advisory_section);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        // Full-text search across multiple fields
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('middle_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('ext_name', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%')
                    ->orWhere('student_id', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('section', 'like', '%' . $search . '%')
                    ->orWhere('room_number', 'like', '%' . $search . '%')
                    ->orWhereHas('department', function ($deptQuery) use ($search) {
                        $deptQuery->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        // Filters when viewing all roles (no role filter)
        if (!$roleFilter && $filter) {
            switch ($filter) {
                case 'role=student':    $query->where('role', Roles::STUDENT); break;
                case 'role=instructor': $query->where('role', Roles::INSTRUCTOR); break;
                case 'role=admin':      $query->where('role', Roles::ADMIN); break;
                case 'status=pending':  $query->where('stat', false); break;
                case 'status=active':   $query->where('stat', true); break;
                case 'verified=no':     $query->whereNull('email_verified_at'); break;
            }
        }

        // Filters when viewing a specific role
        if ($roleFilter && $filter) {
            switch ($filter) {
                case 'status=pending':  $query->where('stat', false); break;
                case 'status=active':   $query->where('stat', true); break;
                case 'verified=no':     $query->whereNull('email_verified_at'); break;
            }
        }

        return $query
            ->orderBy($this->getSortColumn($sort), $direction)
            ->paginate(config('joms.pagination.users', 20));
    }

    /**
     * Get filter counts for the given role scope.
     *
     * Returns aggregate counts for total, students, instructors, admins,
     * pending, active, and unverified users within the scoped query.
     *
     * @param string|null $roleFilter Role to scope counts by, or null for all
     * @param User        $viewer     The authenticated user viewing the list
     * @return array
     */
    public function getFilterCounts(?string $roleFilter, User $viewer): array
    {
        $query = User::query();

        if ($roleFilter) {
            $query->where('role', $roleFilter);
        }

        if ($viewer->role === Roles::INSTRUCTOR && $roleFilter === Roles::STUDENT) {
            if ($viewer->advisory_section) {
                $query->where('section', $viewer->advisory_section);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        return $query
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN role = 'student' THEN 1 ELSE 0 END) as students")
            ->selectRaw("SUM(CASE WHEN role = 'instructor' THEN 1 ELSE 0 END) as instructors")
            ->selectRaw("SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as admins")
            ->selectRaw("SUM(CASE WHEN stat = 0 OR stat IS NULL THEN 1 ELSE 0 END) as pending")
            ->selectRaw("SUM(CASE WHEN stat = 1 THEN 1 ELSE 0 END) as active")
            ->selectRaw("SUM(CASE WHEN email_verified_at IS NULL THEN 1 ELSE 0 END) as unverified")
            ->first()
            ->toArray();
    }

    /**
     * Map a sort parameter to the actual database column name.
     *
     * @param string|null $sort The sort key from the request
     * @return string The database column to sort by
     */
    protected function getSortColumn(?string $sort): string
    {
        $mapping = [
            'id' => 'id', 'first_name' => 'first_name', 'last_name' => 'last_name',
            'student_id' => 'student_id', 'email' => 'email', 'role' => 'role',
            'department_id' => 'department_id', 'section' => 'section',
            'room_number' => 'room_number', 'email_verified_at' => 'email_verified_at',
            'stat' => 'stat', 'created_at' => 'created_at', 'name' => 'last_name',
            'student' => 'student_id', 'department' => 'department_id', 'status' => 'stat',
        ];

        return $mapping[$sort] ?? 'created_at';
    }
}
