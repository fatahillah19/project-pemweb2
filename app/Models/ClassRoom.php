<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassRoom extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    protected $primaryKey = 'id_kelas';

    public $timestamps = false;

    protected $fillable = [
        'nama_kelas',
    ];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'id_kelas', 'id_kelas');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'kelas_id', 'id_kelas');
    }
}
