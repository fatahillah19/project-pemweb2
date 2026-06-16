<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_jurusan',
        'nama_jurusan',
        'deskripsi',
    ];

    public function classRooms(): HasMany
    {
        return $this->hasMany(ClassRoom::class, 'jurusan_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'jurusan_id');
    }
}
