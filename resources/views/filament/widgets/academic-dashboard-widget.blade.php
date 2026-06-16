<x-filament-widgets::widget>
    <section class="siakad-flight-dashboard">
        <div class="siakad-dashboard-hero">
            <div>
                <p class="siakad-kicker">Academic Command Center</p>
                <h2>Dashboard SIAKAD</h2>
                <span>Ringkasan data sekolah dalam tampilan ringan, rapi, dan mudah dipindai.</span>
            </div>
            <div class="siakad-route-map" aria-hidden="true">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>

        <div class="siakad-stat-grid">
            @foreach ($stats as $stat)
                <article class="siakad-stat-card is-{{ $stat['tone'] }}">
                    <span>{{ $stat['label'] }}</span>
                    <strong>{{ number_format($stat['value']) }}</strong>
                    <p>{{ $stat['meta'] }}</p>
                </article>
            @endforeach
        </div>

        <article class="siakad-role-panel">
            <div>
                <p class="siakad-kicker">Ketentuan Role</p>
                <h3>{{ $currentUser?->name ?? 'Pengguna' }}</h3>
                <span>
                    Role:
                    {{ $currentRoles->isNotEmpty() ? $currentRoles->pluck('label')->join(', ') : 'Belum memiliki role' }}
                </span>
            </div>
            <div class="siakad-privilege-list">
                @forelse ($currentPrivileges as $privilege)
                    <span>{{ str($privilege)->replace('_', ' ')->title() }}</span>
                @empty
                    <span>Tidak ada privilege aktif</span>
                @endforelse
            </div>
            <p>
                Admin: all privilege. Guru: insert, edit, preview. Siswa: preview.
            </p>
        </article>

        <div class="siakad-dashboard-grid">
            @if ($dashboardAccess['students'])
            <article class="siakad-panel siakad-panel-wide">
                <div class="siakad-panel-heading">
                    <div>
                        <h3>Siswa Terbaru</h3>
                        <p>Data pendaftaran terakhir</p>
                    </div>
                    <span>{{ $recentStudents->count() }} data</span>
                </div>

                <div class="siakad-table">
                    @forelse ($recentStudents as $student)
                        <div class="siakad-table-row">
                            <div class="siakad-avatar">
                                {{ strtoupper(str($student->nama_siswa)->substr(0, 1)) }}
                            </div>
                            <div>
                                <strong>{{ $student->nama_siswa }}</strong>
                                <span>{{ $student->alamat ?: 'Alamat belum diisi' }}</span>
                            </div>
                            <span>{{ $student->kelas?->nama_kelas ?: 'Belum ada kelas' }}</span>
                            <span>ID {{ $student->id_siswa }}</span>
                        </div>
                    @empty
                        <div class="siakad-empty-state">Belum ada data siswa.</div>
                    @endforelse
                </div>
            </article>
            @endif

            @if ($dashboardAccess['attendance'])
            <article class="siakad-panel">
                <div class="siakad-panel-heading">
                    <div>
                        <h3>Statistik</h3>
                        <p>Ringkasan absensi</p>
                    </div>
                </div>
                <div class="siakad-bars" aria-hidden="true">
                    <span style="--h: {{ min(100, (($attendanceSummary['Hadir'] ?? 0) * 8) + 18) }}%"></span>
                    <span style="--h: {{ min(100, (($attendanceSummary['Izin'] ?? 0) * 14) + 18) }}%"></span>
                    <span style="--h: {{ min(100, (($attendanceSummary['Sakit'] ?? 0) * 14) + 18) }}%"></span>
                    <span style="--h: {{ min(100, (($attendanceSummary['Alpa'] ?? 0) * 14) + 18) }}%"></span>
                </div>
                <div class="siakad-months">
                    <span>Hadir</span>
                    <span>Izin</span>
                    <span>Sakit</span>
                    <span>Alpa</span>
                </div>
            </article>
            @endif

            @if ($dashboardAccess['grades'])
            <article class="siakad-panel">
                <div class="siakad-panel-heading">
                    <div>
                        <h3>Komposisi</h3>
                        <p>Distribusi akademik</p>
                    </div>
                </div>
                <div class="siakad-donut">
                    <span>{{ number_format($averageGrade ?? 0, 1) }}</span>
                </div>
                <div class="siakad-legend">
                    <span><i class="gold"></i>Siswa</span>
                    <span><i class="teal"></i>Kelas</span>
                    <span><i class="green"></i>Guru</span>
                </div>
            </article>
            @endif

            @if ($dashboardAccess['grades'])
            <article class="siakad-panel siakad-panel-wide">
                <div class="siakad-panel-heading">
                    <div>
                        <h3>Raport Akademik</h3>
                        <p>Ringkasan capaian nilai siswa</p>
                    </div>
                    <span>{{ number_format($gradeCount) }} nilai</span>
                </div>

                <div class="siakad-raport-summary">
                    <div>
                        <span>Rata-rata sekolah</span>
                        <strong>{{ number_format($averageGrade ?? 0, 1) }}</strong>
                    </div>
                    <div>
                        <span>Status raport</span>
                        <strong>{{ $gradeCount > 0 ? 'Tersedia' : 'Belum tersedia' }}</strong>
                    </div>
                </div>

                <div class="siakad-announcements siakad-raport-list">
                    @forelse ($raportRows as $raport)
                        <div>
                            <strong>{{ $raport->student?->nama_siswa ?? 'Siswa tidak ditemukan' }}</strong>
                            <span>Rata-rata {{ number_format($raport->rata_nilai, 1) }} dari {{ $raport->total_mapel }} mata pelajaran</span>
                        </div>
                    @empty
                        <div>
                            <strong>Belum ada data raport</strong>
                            <span>Input nilai siswa akan otomatis tampil sebagai ringkasan raport.</span>
                        </div>
                    @endforelse
                </div>
            </article>
            @endif

            <article class="siakad-panel siakad-panel-wide">
                <div class="siakad-panel-heading">
                    <div>
                        <h3>Jadwal Pelajaran</h3>
                        <p>Agenda belajar terdekat</p>
                    </div>
                </div>
                <div class="siakad-schedule-list">
                    @forelse ($schedules as $schedule)
                        <div class="siakad-schedule-row">
                            <strong>{{ $schedule->hari }}</strong>
                            <span>{{ substr($schedule->jam_mulai, 0, 5) }} - {{ substr($schedule->jam_selesai, 0, 5) }}</span>
                            <div>
                                <strong>{{ $schedule->subject?->nama_mapel }}</strong>
                                <span>{{ $schedule->kelas?->nama_kelas }} · {{ $schedule->guru?->nama_guru }}</span>
                            </div>
                            <span>{{ $schedule->ruangan ?: 'Ruangan belum diatur' }}</span>
                        </div>
                    @empty
                        <div class="siakad-empty-state">Belum ada jadwal pelajaran.</div>
                    @endforelse
                </div>
            </article>
        </div>
    </section>
</x-filament-widgets::widget>
