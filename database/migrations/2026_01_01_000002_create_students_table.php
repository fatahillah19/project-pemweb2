<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siswa', function (Blueprint $table) {
            $table->increments('id_siswa');
            $table->string('nama_siswa', 100);
            $table->string('no_hp', 20)->nullable();
            $table->string('foto')->nullable();
            $table->string('alamat', 100)->nullable();
            $table->unsignedInteger('id_kelas')->nullable();

            $table->foreign('id_kelas')->references('id_kelas')->on('kelas')->nullOnDelete();
            $table->index('nama_siswa', 'idx_nama_siswa');
        });

        $namaSiswa = ['Ahmad', 'Budi', 'Cici', 'Doni', 'Eka'];

        DB::table('siswa')->insert(
            collect($namaSiswa)->map(fn (string $nama, int $index) => [
                'nama_siswa' => $nama.' '.str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                'no_hp' => '08'.str_pad((string) (2100000000 + $index + 1), 10, '0', STR_PAD_LEFT),
                'foto' => 'students/student-'.str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT).'.jpg',
                'alamat' => ['Padang', 'Bukittinggi', 'Solok', 'Pariaman', 'Payakumbuh'][$index % 5],
                'id_kelas' => ($index % 20) + 1,
            ])->all()
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};
