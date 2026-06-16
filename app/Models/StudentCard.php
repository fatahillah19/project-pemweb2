<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentCard extends Model
{
    use HasFactory;

    protected $table = 'kartu_pelajar';

    protected $primaryKey = 'id_kartu';

    public $timestamps = false;

    protected $fillable = [
        'nomor_kartu',
        'barcode_token',
        'qr_token',
        'id_siswa',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'id_siswa', 'id_siswa');
    }
}
