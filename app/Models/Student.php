<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends Model
{
    use HasFactory;

    protected $table = 'siswa';

    protected $primaryKey = 'id_siswa';

    public $timestamps = false;

    protected $fillable = [
        'nisn',
        'nama_siswa',
        'no_hp',
        'foto',
        'alamat',
        'id_kelas',
    ];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'id_kelas', 'id_kelas');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'id_siswa', 'id_siswa');
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class, 'id_siswa', 'id_siswa');
    }

    public function studentCard(): HasOne
    {
        return $this->hasOne(StudentCard::class, 'id_siswa', 'id_siswa');
    }

    public function reportCards(): HasMany
    {
        return $this->hasMany(ReportCard::class, 'id_siswa', 'id_siswa');
    }
}
