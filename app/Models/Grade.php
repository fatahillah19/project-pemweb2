<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    use HasFactory;

    protected $table = 'nilai';

    protected $primaryKey = 'id_nilai';

    public $timestamps = false;

    protected $fillable = [
        'id_siswa',
        'id_mapel',
        'tahun_ajaran',
        'semester',
        'nilai_uts',
        'nilai_uas',
        'nilai',
        'grade',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'id_siswa', 'id_siswa');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'id_mapel', 'id_mapel');
    }

    public static function calculateFinalScore(?float $uts, ?float $uas): ?float
    {
        if ($uts === null && $uas === null) {
            return null;
        }

        return round(((float) ($uts ?? 0) + (float) ($uas ?? 0)) / 2, 2);
    }

    public static function calculateGrade(?float $score): ?string
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

    protected static function booted(): void
    {
        static::saving(function (Grade $grade): void {
            $grade->tahun_ajaran = $grade->tahun_ajaran ?: '2025/2026';
            $grade->semester = $grade->semester ?: 'Genap';
            $grade->nilai = self::calculateFinalScore(
                $grade->nilai_uts === null ? null : (float) $grade->nilai_uts,
                $grade->nilai_uas === null ? null : (float) $grade->nilai_uas,
            );
            $grade->grade = self::calculateGrade($grade->nilai === null ? null : (float) $grade->nilai);
        });

        static::saved(function (Grade $grade): void {
            $average = self::query()
                ->where('id_siswa', $grade->id_siswa)
                ->where('tahun_ajaran', $grade->tahun_ajaran)
                ->where('semester', $grade->semester)
                ->whereNotNull('nilai')
                ->avg('nilai');

            ReportCard::query()->updateOrCreate(
                [
                    'id_siswa' => $grade->id_siswa,
                    'tahun_ajaran' => $grade->tahun_ajaran,
                    'semester' => $grade->semester,
                ],
                [
                    'rata_rata' => $average === null ? null : round((float) $average, 2),
                    'grade' => self::calculateGrade($average === null ? null : (float) $average),
                ],
            );
        });
    }
}
