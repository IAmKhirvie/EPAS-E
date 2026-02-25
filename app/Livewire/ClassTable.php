<?php

namespace App\Livewire;

use App\Constants\Roles;
use App\Models\InstructorSection;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ClassTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $sectionFilter = '';
    public string $sortField = 'last_name';
    public string $sortDirection = 'asc';
    public array $selectedStudents = [];
    public bool $selectAll = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'sectionFilter' => ['except' => '', 'as' => 'section'],
        'sortField' => ['except' => 'last_name', 'as' => 'sort'],
        'sortDirection' => ['except' => 'asc', 'as' => 'dir'],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSectionFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSelectAll(bool $value): void
    {
        if ($this->sectionFilter) {
            $this->selectedStudents = $value
                ? $this->getSectionStudentsQuery($this->sectionFilter)->pluck('id')->map(fn ($id) => (string) $id)->toArray()
                : [];
        } else {
            $this->selectedStudents = [];
            $this->selectAll = false;
        }
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

    public function selectSection(string $section): void
    {
        $this->sectionFilter = $section;
        $this->resetPage();
    }

    public function clearSection(): void
    {
        $this->sectionFilter = '';
        $this->resetPage();
    }

    private function getAccessibleSections()
    {
        $viewer = Auth::user();

        if ($viewer->role === Roles::INSTRUCTOR) {
            return $viewer->getAllAccessibleSections();
        }

        // Admin sees all sections
        return User::where('role', Roles::STUDENT)
            ->whereNotNull('section')
            ->select('section')
            ->distinct()
            ->orderBy('section')
            ->pluck('section')
            ->filter();
    }

    private function getAdvisersBySection($sections)
    {
        $assignments = InstructorSection::with('instructor')
            ->whereIn('section', $sections)
            ->get()
            ->groupBy('section')
            ->map(fn ($items) => $items->map(fn ($item) => $item->instructor)->filter());

        // Include legacy advisory_section
        $legacyAdvisers = User::where('role', Roles::INSTRUCTOR)
            ->whereIn('advisory_section', $sections)
            ->get();

        foreach ($legacyAdvisers as $adviser) {
            $section = $adviser->advisory_section;
            if (!isset($assignments[$section])) {
                $assignments[$section] = collect();
            }
            if (!$assignments[$section]->contains('id', $adviser->id)) {
                $assignments[$section]->push($adviser);
            }
        }

        return $assignments;
    }

    private function getStudentsBySection($sections)
    {
        $query = User::where('role', Roles::STUDENT)
            ->whereIn('section', $sections);

        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('student_id', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('section')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get()
            ->groupBy('section');
    }

    private function getSectionStudentsQuery(string $section)
    {
        $query = User::where('role', Roles::STUDENT)
            ->with('department')
            ->where('section', $section);

        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('student_id', 'like', "%{$search}%");
            });
        }

        $sortColumn = in_array($this->sortField, ['id', 'first_name', 'last_name', 'student_id', 'email'])
            ? $this->sortField : 'last_name';

        return $query->orderBy($sortColumn, $this->sortDirection);
    }

    public function render()
    {
        $viewer = Auth::user();
        $allSections = $this->getAccessibleSections();

        if ($viewer->role === Roles::INSTRUCTOR && $allSections->isEmpty()) {
            return view('livewire.class-table', [
                'allSections' => collect(),
                'studentsBySection' => collect(),
                'students' => null,
                'advisersBySection' => collect(),
                'isInstructor' => true,
                'noAdvisorySection' => true,
                'instructors' => collect(),
            ]);
        }

        // Instructor: validate section filter
        if ($viewer->role === Roles::INSTRUCTOR && $this->sectionFilter && !$allSections->contains($this->sectionFilter)) {
            $this->sectionFilter = '';
        }

        $advisersBySection = $this->getAdvisersBySection($allSections);

        $instructors = $viewer->role === Roles::ADMIN
            ? User::where('role', Roles::INSTRUCTOR)
                ->with('department')
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get()
            : collect();

        // If section selected, show students table
        $students = null;
        if ($this->sectionFilter) {
            $students = $this->getSectionStudentsQuery($this->sectionFilter)->paginate(20);
        }

        $studentsBySection = $this->getStudentsBySection($allSections);

        return view('livewire.class-table', [
            'allSections' => $allSections,
            'studentsBySection' => $studentsBySection,
            'students' => $students,
            'advisersBySection' => $advisersBySection,
            'isInstructor' => $viewer->role === Roles::INSTRUCTOR,
            'instructors' => $instructors,
        ]);
    }
}
