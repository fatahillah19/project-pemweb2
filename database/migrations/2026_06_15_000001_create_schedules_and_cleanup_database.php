<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('blogs');
        Schema::dropIfExists('log_aktivitas');

        if (! Schema::hasTable('jadwal_pelajaran')) {
            Schema::create('jadwal_pelajaran', function (Blueprint $table) {
                $table->id();
                $table->string('hari', 15);
                $table->time('jam_mulai');
                $table->time('jam_selesai');
                $table->unsignedInteger('kelas_id');
                $table->unsignedInteger('subject_id');
                $table->unsignedInteger('guru_id');
                $table->string('ruangan', 50)->nullable();

                $table->foreign('kelas_id')->references('id_kelas')->on('kelas')->cascadeOnDelete();
                $table->foreign('subject_id')->references('id_mapel')->on('mata_pelajaran')->cascadeOnDelete();
                $table->foreign('guru_id')->references('id_guru')->on('guru')->cascadeOnDelete();
                $table->unique(['hari', 'jam_mulai', 'kelas_id'], 'jadwal_hari_jam_kelas_unique');
            });
        }

        $studentIdsToKeep = DB::table('siswa')
            ->orderBy('id_siswa')
            ->limit(5)
            ->pluck('id_siswa');

        if ($studentIdsToKeep->isNotEmpty()) {
            DB::table('siswa')->whereNotIn('id_siswa', $studentIdsToKeep)->delete();
        }

        $this->ensureFiveStudents();

        if (DB::table('jadwal_pelajaran')->doesntExist()) {
            $classes = DB::table('kelas')->orderBy('id_kelas')->limit(4)->pluck('id_kelas')->values();
            $subjects = DB::table('mata_pelajaran')->orderBy('id_mapel')->limit(4)->get(['id_mapel', 'id_guru'])->values();
            $slots = [
                ['Senin', '07:30', '09:00', 'Ruang 101'],
                ['Senin', '09:15', '10:45', 'Lab Fisika'],
                ['Selasa', '07:30', '09:00', 'Ruang 102'],
                ['Rabu', '10:00', '11:30', 'Lab Komputer'],
            ];

            foreach ($slots as $index => [$hari, $mulai, $selesai, $ruangan]) {
                $classId = $classes->get($index % max(1, $classes->count()));
                $subject = $subjects->get($index % max(1, $subjects->count()));

                if ($classId && $subject?->id_guru) {
                    DB::table('jadwal_pelajaran')->insert([
                        'hari' => $hari,
                        'jam_mulai' => $mulai,
                        'jam_selesai' => $selesai,
                        'kelas_id' => $classId,
                        'subject_id' => $subject->id_mapel,
                        'guru_id' => $subject->id_guru,
                        'ruangan' => $ruangan,
                    ]);
                }
            }
        }

        if (Schema::hasTable('fblog_share_snippets') && DB::table('fblog_share_snippets')->doesntExist()) {
            DB::table('fblog_share_snippets')->insert([
                'html_code' => '<div class="siakad-share"><button type="button" onclick="shareSiakad(\'whatsapp\')">WhatsApp</button><button type="button" onclick="shareSiakad(\'facebook\')">Facebook</button></div>',
                'script_code' => '<script>function shareSiakad(platform){const url=encodeURIComponent(window.location.href);const target=platform==="whatsapp"?"https://wa.me/?text="+url:"https://www.facebook.com/sharer/sharer.php?u="+url;window.open(target,"_blank","noopener,noreferrer");}</script>',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_pelajaran');
    }

    private function ensureFiveStudents(): void
    {
        $classId = DB::table('kelas')->orderBy('id_kelas')->value('id_kelas');
        $missing = max(0, 5 - DB::table('siswa')->count());

        foreach (range(1, $missing) as $offset) {
            if ($missing === 0) {
                break;
            }

            $sequence = DB::table('siswa')->count() + 1;
            $studentId = DB::table('siswa')->insertGetId([
                'nisn' => '006'.str_pad((string) $sequence, 7, '0', STR_PAD_LEFT),
                'nama_siswa' => ['Ahmad', 'Budi', 'Cici', 'Doni', 'Eka'][$sequence - 1],
                'no_hp' => '0812345678'.$sequence,
                'foto' => null,
                'alamat' => 'Padang',
                'id_kelas' => $classId,
            ], 'id_siswa');

            DB::table('kartu_pelajar')->insert([
                'nomor_kartu' => 'KP'.str_pad((string) $studentId, 5, '0', STR_PAD_LEFT),
                'barcode_token' => 'SISWA-'.str_pad((string) $studentId, 5, '0', STR_PAD_LEFT),
                'qr_token' => 'QR-SISWA-'.str_pad((string) $studentId, 5, '0', STR_PAD_LEFT),
                'id_siswa' => $studentId,
            ]);
        }
    }
};
