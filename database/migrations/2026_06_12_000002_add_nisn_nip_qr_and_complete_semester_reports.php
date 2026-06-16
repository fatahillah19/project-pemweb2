<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('siswa') && ! Schema::hasColumn('siswa', 'nisn')) {
            Schema::table('siswa', fn (Blueprint $table) => $table->string('nisn', 20)->nullable()->after('id_siswa'));
        }

        if (Schema::hasTable('guru') && ! Schema::hasColumn('guru', 'nip')) {
            Schema::table('guru', fn (Blueprint $table) => $table->string('nip', 30)->nullable()->after('id_guru'));
        }

        if (Schema::hasTable('kartu_pelajar') && ! Schema::hasColumn('kartu_pelajar', 'qr_token')) {
            Schema::table('kartu_pelajar', fn (Blueprint $table) => $table->string('qr_token', 80)->nullable()->unique()->after('barcode_token'));
        }

        if (Schema::hasTable('nilai')) {
            Schema::table('nilai', function (Blueprint $table) {
                if (! Schema::hasColumn('nilai', 'tahun_ajaran')) {
                    $table->string('tahun_ajaran', 20)->nullable()->after('id_mapel');
                }

                if (! Schema::hasColumn('nilai', 'semester')) {
                    $table->string('semester', 20)->nullable()->after('tahun_ajaran');
                }
            });
        }

        $this->completeStudents();
        $this->completeTeachers();
        $this->completeStudentCards();
        $this->completeGradesForBothSemesters();
        $this->completeAttendances();
        $this->syncReportCardsForBothSemesters();
    }

    public function down(): void
    {
        if (Schema::hasTable('kartu_pelajar') && Schema::hasColumn('kartu_pelajar', 'qr_token')) {
            Schema::table('kartu_pelajar', fn (Blueprint $table) => $table->dropColumn('qr_token'));
        }

        if (Schema::hasTable('nilai')) {
            Schema::table('nilai', function (Blueprint $table) {
                if (Schema::hasColumn('nilai', 'semester')) {
                    $table->dropColumn('semester');
                }

                if (Schema::hasColumn('nilai', 'tahun_ajaran')) {
                    $table->dropColumn('tahun_ajaran');
                }
            });
        }
    }

    private function completeStudents(): void
    {
        if (! Schema::hasTable('siswa')) {
            return;
        }

        DB::table('siswa')->orderBy('id_siswa')->get()->each(function (object $student): void {
            DB::table('siswa')
                ->where('id_siswa', $student->id_siswa)
                ->update([
                    'nisn' => $student->nisn ?: '006' . str_pad((string) $student->id_siswa, 7, '0', STR_PAD_LEFT),
                    'no_hp' => $student->no_hp ?: '08' . str_pad((string) (2100000000 + $student->id_siswa), 10, '0', STR_PAD_LEFT),
                    'foto' => $student->foto ?: 'students/student-' . str_pad((string) $student->id_siswa, 3, '0', STR_PAD_LEFT) . '.jpg',
                    'alamat' => $student->alamat ?: ['Padang', 'Bukittinggi', 'Solok', 'Pariaman', 'Payakumbuh'][$student->id_siswa % 5],
                ]);
        });
    }

    private function completeTeachers(): void
    {
        if (! Schema::hasTable('guru')) {
            return;
        }

        DB::table('guru')->orderBy('id_guru')->get()->each(function (object $teacher): void {
            DB::table('guru')
                ->where('id_guru', $teacher->id_guru)
                ->update([
                    'nip' => $teacher->nip ?: '1987' . str_pad((string) $teacher->id_guru, 14, '0', STR_PAD_LEFT),
                    'no_hp' => $teacher->no_hp ?: '08' . str_pad((string) (1200000000 + $teacher->id_guru), 10, '0', STR_PAD_LEFT),
                    'foto' => property_exists($teacher, 'foto') && $teacher->foto
                        ? $teacher->foto
                        : 'teachers/teacher-' . str_pad((string) $teacher->id_guru, 3, '0', STR_PAD_LEFT) . '.jpg',
                    'mapel_diampu' => $teacher->mapel_diampu ?: DB::table('mata_pelajaran')->where('id_guru', $teacher->id_guru)->value('nama_mapel'),
                ]);
        });
    }

    private function completeStudentCards(): void
    {
        if (! Schema::hasTable('kartu_pelajar')) {
            return;
        }

        DB::table('siswa')->orderBy('id_siswa')->pluck('id_siswa')->each(function (int $studentId): void {
            DB::table('kartu_pelajar')->updateOrInsert(
                ['id_siswa' => $studentId],
                [
                    'nomor_kartu' => 'KP' . str_pad((string) $studentId, 5, '0', STR_PAD_LEFT),
                    'barcode_token' => 'SISWA-' . str_pad((string) $studentId, 5, '0', STR_PAD_LEFT),
                    'qr_token' => 'QR-SISWA-' . str_pad((string) $studentId, 5, '0', STR_PAD_LEFT),
                ],
            );
        });
    }

    private function completeGradesForBothSemesters(): void
    {
        if (! Schema::hasTable('nilai')) {
            return;
        }

        DB::table('nilai')
            ->whereNull('tahun_ajaran')
            ->update(['tahun_ajaran' => '2025/2026']);

        DB::table('nilai')
            ->whereNull('semester')
            ->update(['semester' => 'Genap']);

        $subjects = DB::table('mata_pelajaran')->pluck('id_mapel');
        $semesters = ['Ganjil', 'Genap'];

        DB::table('siswa')->pluck('id_siswa')->each(function (int $studentId) use ($subjects, $semesters): void {
            $subjects->each(function (int $subjectId) use ($studentId, $semesters): void {
                foreach ($semesters as $semester) {
                    $uts = $this->scoreFor($studentId, $subjectId, $semester, 0);
                    $uas = $this->scoreFor($studentId, $subjectId, $semester, 7);
                    $final = round(($uts + $uas) / 2, 2);

                    DB::table('nilai')->updateOrInsert(
                        [
                            'id_siswa' => $studentId,
                            'id_mapel' => $subjectId,
                            'tahun_ajaran' => '2025/2026',
                            'semester' => $semester,
                        ],
                        [
                            'nilai_uts' => $uts,
                            'nilai_uas' => $uas,
                            'nilai' => $final,
                            'grade' => $this->letterGrade($final),
                        ],
                    );
                }
            });
        });
    }

    private function completeAttendances(): void
    {
        if (! Schema::hasTable('absensi') || ! Schema::hasTable('kartu_pelajar')) {
            return;
        }

        DB::table('absensi')->whereNull('nomor_kartu')->orderBy('id_absensi')->get()->each(function (object $attendance): void {
            $qrToken = DB::table('kartu_pelajar')->where('id_siswa', $attendance->id_siswa)->value('qr_token');

            DB::table('absensi')
                ->where('id_absensi', $attendance->id_absensi)
                ->update([
                    'nomor_kartu' => $qrToken,
                    'tanggal' => $attendance->tanggal ?: '2026-06-01',
                    'status' => $attendance->status ?: 'Hadir',
                ]);
        });
    }

    private function syncReportCardsForBothSemesters(): void
    {
        if (! Schema::hasTable('rapor')) {
            return;
        }

        foreach (['Ganjil', 'Genap'] as $semester) {
            DB::table('siswa')->pluck('id_siswa')->each(function (int $studentId) use ($semester): void {
                $average = DB::table('nilai')
                    ->where('id_siswa', $studentId)
                    ->where('tahun_ajaran', '2025/2026')
                    ->where('semester', $semester)
                    ->whereNotNull('nilai')
                    ->avg('nilai');

                DB::table('rapor')->updateOrInsert(
                    ['id_siswa' => $studentId, 'tahun_ajaran' => '2025/2026', 'semester' => $semester],
                    [
                        'rata_rata' => $average === null ? null : round((float) $average, 2),
                        'grade' => $this->letterGrade($average === null ? null : (float) $average),
                        'catatan' => 'E-Rapor semester ' . strtolower($semester) . ' berdasarkan seluruh nilai mata pelajaran.',
                    ],
                );
            });
        }
    }

    private function scoreFor(int $studentId, int $subjectId, string $semester, int $offset): int
    {
        $semesterOffset = $semester === 'Ganjil' ? 3 : 8;

        return 72 + (($studentId + $subjectId + $semesterOffset + $offset) % 24);
    }

    private function letterGrade(?float $score): ?string
    {
        return match (true) {
            $score === null => null,
            $score >= 90 => 'A',
            $score >= 80 => 'B',
            $score >= 70 => 'C',
            $score >= 60 => 'D',
            default => 'E',
        };
    }
};
