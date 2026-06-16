<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    use HasFactory;

    protected $table = 'jadwal_pelajaran';

    protected $fillable = [
        'hari',
        'jam_mulai',
        'jam_selesai',
        'kelas_id',
        'subject_id',
        'guru_id',
        'ruangan',
    ];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'kelas_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'id_mapel');
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'guru_id', 'id_guru');
    }
}
