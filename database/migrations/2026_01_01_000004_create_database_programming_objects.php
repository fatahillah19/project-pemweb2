<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('guru')) {
            Schema::table('guru', fn ($table) => $table->index('nama_guru', 'idx_nama_guru'));
        }

        if (Schema::hasTable('mata_pelajaran')) {
            Schema::table('mata_pelajaran', fn ($table) => $table->index('nama_mapel', 'idx_nama_mapel'));
        }

        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement('CREATE VIEW vw_nilai_siswa AS
            SELECT s.id_siswa, s.nama_siswa, m.nama_mapel, n.nilai
            FROM nilai n
            JOIN siswa s ON n.id_siswa = s.id_siswa
            JOIN mata_pelajaran m ON n.id_mapel = m.id_mapel');

        DB::statement('CREATE VIEW vw_siswa_kelas AS
            SELECT s.nama_siswa, k.nama_kelas
            FROM siswa s
            JOIN kelas k ON s.id_kelas = k.id_kelas');

        DB::statement('CREATE VIEW vw_guru_mapel AS
            SELECT g.nama_guru, m.nama_mapel
            FROM guru g
            JOIN mata_pelajaran m ON g.id_guru = m.id_guru');

        DB::unprepared('CREATE PROCEDURE tambah_siswa(
                IN p_nama VARCHAR(100),
                IN p_alamat VARCHAR(100),
                IN p_kelas INT
            )
            BEGIN
                INSERT INTO siswa (nama_siswa, alamat, id_kelas)
                VALUES (p_nama, p_alamat, p_kelas);
            END');

        DB::unprepared('CREATE PROCEDURE cari_siswa(IN p_nama VARCHAR(100))
            BEGIN
                SELECT * FROM siswa WHERE nama_siswa LIKE CONCAT("%", p_nama, "%");
            END');

        DB::unprepared('CREATE PROCEDURE update_nilai(IN pid INT, IN pnilai DECIMAL(5,2))
            BEGIN
                UPDATE nilai SET nilai = pnilai WHERE id_nilai = pid;
            END');

    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('DROP PROCEDURE IF EXISTS update_nilai');
            DB::statement('DROP PROCEDURE IF EXISTS cari_siswa');
            DB::statement('DROP PROCEDURE IF EXISTS tambah_siswa');
            DB::statement('DROP VIEW IF EXISTS vw_guru_mapel');
            DB::statement('DROP VIEW IF EXISTS vw_siswa_kelas');
            DB::statement('DROP VIEW IF EXISTS vw_nilai_siswa');
        }
    }
};
