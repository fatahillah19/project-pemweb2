<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Teacher extends Model
{
    use HasFactory;

    protected $table = 'guru';

    protected $primaryKey = 'id_guru';

    public $timestamps = false;

    protected $fillable = [
        'nip',
        'nama_guru',
        'no_hp',
        'foto',
        'mapel_diampu',
    ];

    public function waliKelasDi(): HasOne
    {
        return $this->hasOne(ClassRoom::class, 'wali_kelas_id');
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'id_guru', 'id_guru');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'guru_id');
    }
}
