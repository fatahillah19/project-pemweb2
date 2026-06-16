<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->increments('id_kelas');
            $table->string('nama_kelas', 30);
        });

        Schema::create('guru', function (Blueprint $table) {
            $table->increments('id_guru');
            $table->string('nama_guru', 100);
            $table->string('no_hp', 20)->nullable();
            $table->string('mapel_diampu', 100)->nullable();
        });

        Schema::create('mata_pelajaran', function (Blueprint $table) {
            $table->increments('id_mapel');
            $table->string('nama_mapel', 100);
            $table->unsignedInteger('id_guru')->nullable();

            $table->foreign('id_guru')->references('id_guru')->on('guru')->nullOnDelete();
        });

        DB::table('kelas')->insert(
            collect([
                'X IPA 1', 'X IPA 2', 'X IPA 3', 'X IPA 4', 'X IPS 1',
                'X IPS 2', 'XI IPA 1', 'XI IPA 2', 'XI IPA 3', 'XI IPA 4',
                'XI IPS 1', 'XI IPS 2', 'XII IPA 1', 'XII IPA 2', 'XII IPA 3',
                'XII IPA 4', 'XII IPS 1', 'XII IPS 2', 'X Bahasa 1', 'XI Bahasa 1',
            ])->map(fn (string $namaKelas) => ['nama_kelas' => $namaKelas])->all()
        );

        $mapel = [
            'Matematika', 'Fisika', 'Kimia', 'Biologi', 'Bahasa Indonesia',
            'Bahasa Inggris', 'Sejarah', 'Geografi', 'Ekonomi', 'Sosiologi',
            'Pendidikan Pancasila', 'Agama', 'Informatika', 'Seni Budaya', 'PJOK',
            'Prakarya', 'Bahasa Jepang', 'Bahasa Arab', 'Kewirausahaan', 'Bimbingan Konseling',
        ];

        DB::table('guru')->insert(
            collect([
                'Andi Saputra', 'Budi Santoso', 'Siti Aminah', 'Rina Marlina', 'Dewi Lestari',
                'Rahmat Hidayat', 'Nadia Putri', 'Fajar Nugroho', 'Hendra Wijaya', 'Laras Prameswari',
                'Yusuf Maulana', 'Maya Anggraini', 'Teguh Prasetyo', 'Intan Permata', 'Agus Salim',
                'Fitri Handayani', 'Dimas Arya', 'Nurul Hasanah', 'Bayu Kurniawan', 'Citra Wulandari',
            ])->map(fn (string $namaGuru, int $index) => [
                'nama_guru' => $namaGuru,
                'no_hp' => '08' . str_pad((string) (1200000000 + $index + 1), 10, '0', STR_PAD_LEFT),
                'mapel_diampu' => $mapel[$index],
            ])->all()
        );

        DB::table('mata_pelajaran')->insert(
            collect($mapel)
                ->map(fn (string $namaMapel, int $index) => ['nama_mapel' => $namaMapel, 'id_guru' => $index + 1])
                ->all()
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('mata_pelajaran');
        Schema::dropIfExists('guru');
        Schema::dropIfExists('kelas');
    }
};
