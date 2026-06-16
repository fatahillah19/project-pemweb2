<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Grade;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class AcademicDashboardWidget extends Widget
{
    protected string $view = 'filament.widgets.academic-dashboard-widget';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 1;

    protected static bool $isDiscovered = false;

    protected static bool $isLazy = false;

    protected function getViewData(): array
    {
        $user = auth()->user();
        $roles = $user?->roles()->get() ?? collect();
        $privileges = $roles
            ->flatMap(fn ($role) => $role->privileges ?? [])
            ->unique()
            ->values();

        $roleNames = $roles->pluck('name');
        $isAdmin = $roleNames->contains('admin');
        $isGuru = $roleNames->contains('guru');
        $isSiswa = $roleNames->contains('siswa');

        return [
            'currentUser' => $user,
            'currentRoles' => $roles,
            'currentPrivileges' => $privileges,
            'dashboardAccess' => [
                'students' => $isAdmin,
                'attendance' => $isAdmin || $isGuru,
                'grades' => $isAdmin || $isGuru || $isSiswa,
                'schedule' => $isAdmin || $isGuru || $isSiswa,
            ],
            'stats' => [
                [
                    'label' => 'Siswa Aktif',
                    'value' => Student::count(),
                    'tone' => 'gold',
                    'meta' => 'Terdaftar di tabel siswa',
                ],
                [
                    'label' => 'Guru',
                    'value' => Teacher::count(),
                    'tone' => 'green',
                    'meta' => 'Pengajar terdaftar',
                ],
                [
                    'label' => 'Kelas',
                    'value' => ClassRoom::count(),
                    'tone' => 'teal',
                    'meta' => Subject::count().' mata pelajaran',
                ],
            ],
            'recentStudents' => Student::with('kelas')
                ->orderByDesc('id_siswa')
                ->take(4)
                ->get(),
            'attendanceSummary' => Attendance::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status'),
            'averageGrade' => Grade::avg('nilai'),
            'gradeCount' => Grade::count(),
            'raportRows' => Grade::query()
                ->select('id_siswa', DB::raw('avg(nilai) as rata_nilai'), DB::raw('count(*) as total_mapel'))
                ->with('student')
                ->groupBy('id_siswa')
                ->orderByDesc('rata_nilai')
                ->take(4)
                ->get(),
            'schedules' => Schedule::query()
                ->with(['kelas', 'subject', 'guru'])
                ->orderByRaw("CASE hari WHEN 'Senin' THEN 1 WHEN 'Selasa' THEN 2 WHEN 'Rabu' THEN 3 WHEN 'Kamis' THEN 4 WHEN 'Jumat' THEN 5 ELSE 6 END")
                ->orderBy('jam_mulai')
                ->take(6)
                ->get(),
        ];
    }
}
