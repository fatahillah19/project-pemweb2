<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_tahun_ajaran',
        'semester',
        'status_aktif',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
    ];

    public function classRooms(): HasMany
    {
        return $this->hasMany(ClassRoom::class, 'tahun_ajaran_id');
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }
}
