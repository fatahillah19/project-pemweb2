<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    protected $table = 'mata_pelajaran';

    protected $primaryKey = 'id_mapel';

    public $timestamps = false;

    protected $fillable = [
        'nama_mapel',
        'id_guru',
    ];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'id_guru', 'id_guru');
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class, 'id_mapel', 'id_mapel');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'subject_id', 'id_mapel');
    }
}
