<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kartu_pelajar', function (Blueprint $table) {
            $table->increments('id_kartu');
            $table->string('nomor_kartu', 20)->unique()->nullable();
            $table->unsignedInteger('id_siswa')->unique()->nullable();

            $table->foreign('id_siswa')->references('id_siswa')->on('siswa')->cascadeOnDelete();
        });

        Schema::create('nilai', function (Blueprint $table) {
            $table->increments('id_nilai');
            $table->unsignedInteger('id_siswa')->nullable();
            $table->unsignedInteger('id_mapel')->nullable();
            $table->decimal('nilai', 5, 2)->nullable();

            $table->foreign('id_siswa')->references('id_siswa')->on('siswa')->cascadeOnDelete();
            $table->foreign('id_mapel')->references('id_mapel')->on('mata_pelajaran')->nullOnDelete();
        });

        Schema::create('absensi', function (Blueprint $table) {
            $table->increments('id_absensi');
            $table->unsignedInteger('id_siswa')->nullable();
            $table->date('tanggal')->nullable();
            $table->enum('status', ['Hadir', 'Izin', 'Sakit', 'Alpa'])->nullable();

            $table->foreign('id_siswa')->references('id_siswa')->on('siswa')->cascadeOnDelete();
        });

        DB::table('kartu_pelajar')->insert(
            collect(range(1, 5))
                ->map(fn (int $id) => ['nomor_kartu' => 'KP'.str_pad((string) $id, 3, '0', STR_PAD_LEFT), 'id_siswa' => $id])
                ->all()
        );

        DB::table('nilai')->insert([
            ['id_siswa' => 1, 'id_mapel' => 1, 'nilai' => 90],
            ['id_siswa' => 2, 'id_mapel' => 1, 'nilai' => 88],
            ['id_siswa' => 3, 'id_mapel' => 1, 'nilai' => 85],
            ['id_siswa' => 4, 'id_mapel' => 1, 'nilai' => 92],
            ['id_siswa' => 5, 'id_mapel' => 1, 'nilai' => 87],
        ]);

        DB::table('absensi')->insert(
            collect([
                'Hadir', 'Hadir', 'Izin', 'Hadir', 'Sakit',
            ])->map(fn (string $status, int $index) => [
                'id_siswa' => $index + 1,
                'tanggal' => '2026-06-01',
                'status' => $status,
            ])->all()
        );

    }

    public function down(): void
    {
        Schema::dropIfExists('absensi');
        Schema::dropIfExists('nilai');
        Schema::dropIfExists('kartu_pelajar');
    }
};
