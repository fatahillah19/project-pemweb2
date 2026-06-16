<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('guru') && ! Schema::hasColumn('guru', 'foto')) {
            Schema::table('guru', fn (Blueprint $table) => $table->string('foto')->nullable()->after('no_hp'));
        }

        if (Schema::hasTable('kartu_pelajar') && ! Schema::hasColumn('kartu_pelajar', 'barcode_token')) {
            Schema::table('kartu_pelajar', fn (Blueprint $table) => $table->string('barcode_token', 80)->nullable()->unique()->after('nomor_kartu'));
        }

        if (Schema::hasTable('nilai')) {
            Schema::table('nilai', function (Blueprint $table) {
                if (! Schema::hasColumn('nilai', 'nilai_uts')) {
                    $table->decimal('nilai_uts', 5, 2)->nullable()->after('id_mapel');
                }

                if (! Schema::hasColumn('nilai', 'nilai_uas')) {
                    $table->decimal('nilai_uas', 5, 2)->nullable()->after('nilai_uts');
                }

                if (! Schema::hasColumn('nilai', 'grade')) {
                    $table->string('grade', 2)->nullable()->after('nilai');
                }
            });
        }

        if (Schema::hasTable('absensi') && ! Schema::hasColumn('absensi', 'nomor_kartu')) {
            Schema::table('absensi', fn (Blueprint $table) => $table->string('nomor_kartu', 80)->nullable()->after('id_siswa'));
        }

        if (! Schema::hasTable('rapor')) {
            Schema::create('rapor', function (Blueprint $table) {
                $table->increments('id_rapor');
                $table->unsignedInteger('id_siswa')->nullable();
                $table->string('tahun_ajaran', 20)->default('2025/2026');
                $table->string('semester', 20)->default('Genap');
                $table->decimal('rata_rata', 5, 2)->nullable();
                $table->string('grade', 2)->nullable();
                $table->text('catatan')->nullable();

                $table->foreign('id_siswa')->references('id_siswa')->on('siswa')->cascadeOnDelete();
                $table->unique(['id_siswa', 'tahun_ajaran', 'semester'], 'rapor_siswa_tahun_semester_unique');
            });
        }

        $this->disableDeleteHistory();
        $this->fillCardBarcodes();
        $this->fillGradeComponents();
        $this->ensureEveryStudentHasEverySubjectGrade();
        $this->syncReportCards();
    }

    public function down(): void
    {
        Schema::dropIfExists('rapor');
    }

    private function disableDeleteHistory(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('DROP TRIGGER IF EXISTS trg_hapus_siswa');
            DB::statement('DROP TRIGGER IF EXISTS trg_log_siswa');
        }

        if (DB::getDriverName() === 'sqlite') {
            DB::statement('DROP TRIGGER IF EXISTS trg_hapus_siswa');
            DB::statement('DROP TRIGGER IF EXISTS trg_log_siswa');
        }

        if (Schema::hasTable('log_aktivitas')) {
            DB::table('log_aktivitas')->delete();
        }
    }

    private function fillCardBarcodes(): void
    {
        if (! Schema::hasTable('kartu_pelajar')) {
            return;
        }

        DB::table('siswa')
            ->orderBy('id_siswa')
            ->get(['id_siswa'])
            ->each(function (object $student): void {
                $nomorKartu = 'KP' . str_pad((string) $student->id_siswa, 5, '0', STR_PAD_LEFT);
                $token = 'SISWA-' . str_pad((string) $student->id_siswa, 5, '0', STR_PAD_LEFT);

                DB::table('kartu_pelajar')->updateOrInsert(
                    ['id_siswa' => $student->id_siswa],
                    ['nomor_kartu' => $nomorKartu, 'barcode_token' => $token],
                );
            });
    }

    private function fillGradeComponents(): void
    {
        if (! Schema::hasTable('nilai')) {
            return;
        }

        DB::table('nilai')->orderBy('id_nilai')->get()->each(function (object $grade): void {
            $score = $grade->nilai === null ? null : (float) $grade->nilai;

            DB::table('nilai')
                ->where('id_nilai', $grade->id_nilai)
                ->update([
                    'nilai_uts' => $grade->nilai_uts ?? $score,
                    'nilai_uas' => $grade->nilai_uas ?? $score,
                    'nilai' => $score,
                    'grade' => $this->letterGrade($score),
                ]);
        });
    }

    private function ensureEveryStudentHasEverySubjectGrade(): void
    {
        if (! Schema::hasTable('nilai')) {
            return;
        }

        $subjects = DB::table('mata_pelajaran')->pluck('id_mapel');

        DB::table('siswa')->pluck('id_siswa')->each(function (int $studentId) use ($subjects): void {
            $subjects->each(function (int $subjectId) use ($studentId): void {
                $exists = DB::table('nilai')
                    ->where('id_siswa', $studentId)
                    ->where('id_mapel', $subjectId)
                    ->exists();

                if (! $exists) {
                    DB::table('nilai')->insert([
                        'id_siswa' => $studentId,
                        'id_mapel' => $subjectId,
                        'nilai_uts' => null,
                        'nilai_uas' => null,
                        'nilai' => null,
                        'grade' => null,
                    ]);
                }
            });
        });
    }

    private function syncReportCards(): void
    {
        if (! Schema::hasTable('rapor')) {
            return;
        }

        DB::table('siswa')->pluck('id_siswa')->each(function (int $studentId): void {
            $average = DB::table('nilai')
                ->where('id_siswa', $studentId)
                ->whereNotNull('nilai')
                ->avg('nilai');

            DB::table('rapor')->updateOrInsert(
                ['id_siswa' => $studentId, 'tahun_ajaran' => '2025/2026', 'semester' => 'Genap'],
                [
                    'rata_rata' => $average === null ? null : round((float) $average, 2),
                    'grade' => $this->letterGrade($average === null ? null : (float) $average),
                    'catatan' => null,
                ],
            );
        });
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
