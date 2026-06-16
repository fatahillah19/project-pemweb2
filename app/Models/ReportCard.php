<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportCard extends Model
{
    use HasFactory;

    protected $table = 'rapor';

    protected $primaryKey = 'id_rapor';

    public $timestamps = false;

    protected $fillable = [
        'id_siswa',
        'tahun_ajaran',
        'semester',
        'catatan',
        'rata_rata',
        'grade',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'id_siswa', 'id_siswa');
    }
}
