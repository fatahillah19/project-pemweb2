<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Rapor {{ $reportCard->student?->nama_siswa }} {{ $reportCard->semester }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #111827; margin: 32px; }
        .toolbar { display: flex; justify-content: flex-end; margin-bottom: 20px; }
        button { background: #2563eb; border: 0; color: white; padding: 10px 16px; border-radius: 6px; cursor: pointer; }
        h1, h2, p { margin: 0; }
        header { text-align: center; border-bottom: 3px solid #111827; padding-bottom: 16px; margin-bottom: 24px; }
        .meta { display: grid; grid-template-columns: 160px 1fr 160px 1fr; gap: 8px 14px; margin-bottom: 20px; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th, td { border: 1px solid #374151; padding: 8px; }
        th { background: #f3f4f6; }
        .summary { width: 320px; margin-left: auto; margin-top: 18px; }
        .notes { margin-top: 20px; min-height: 70px; border: 1px solid #374151; padding: 10px; }
        .signatures { display: grid; grid-template-columns: 1fr 1fr; gap: 80px; margin-top: 48px; text-align: center; }
        @media print {
            body { margin: 18mm; }
            .toolbar { display: none; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button type="button" onclick="window.print()">Print E-Rapor</button>
    </div>

    <header>
        <h1>E-RAPOR SISWA</h1>
        <p>SMA NURUL FIKRI</p>
    </header>

    <section class="meta">
        <strong>Nama</strong><span>{{ $reportCard->student?->nama_siswa ?? '-' }}</span>
        <strong>NISN</strong><span>{{ $reportCard->student?->nisn ?? '-' }}</span>
        <strong>Kelas</strong><span>{{ $reportCard->student?->kelas?->nama_kelas ?? '-' }}</span>
        <strong>Semester</strong><span>{{ $reportCard->semester }}</span>
        <strong>Tahun Ajaran</strong><span>{{ $reportCard->tahun_ajaran }}</span>
        <strong>Grade Akhir</strong><span>{{ $reportCard->grade ?? '-' }}</span>
    </section>

    <table>
        <thead>
            <tr>
                <th style="width: 42px;">No</th>
                <th>Mata Pelajaran</th>
                <th>Pengajar</th>
                <th style="width: 80px;">UTS</th>
                <th style="width: 80px;">UAS</th>
                <th style="width: 90px;">Akhir</th>
                <th style="width: 70px;">Grade</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($grades as $grade)
                <tr>
                    <td style="text-align:center;">{{ $loop->iteration }}</td>
                    <td>{{ $grade->subject?->nama_mapel ?? '-' }}</td>
                    <td>{{ $grade->subject?->guru?->nama_guru ?? '-' }}</td>
                    <td style="text-align:center;">{{ $grade->nilai_uts ?? '-' }}</td>
                    <td style="text-align:center;">{{ $grade->nilai_uas ?? '-' }}</td>
                    <td style="text-align:center;">{{ $grade->nilai ?? '-' }}</td>
                    <td style="text-align:center;">{{ $grade->grade ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center;">Belum ada nilai mata pelajaran untuk semester ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="summary">
        <tr>
            <th>Rata-rata</th>
            <td style="text-align:center;">{{ $reportCard->rata_rata ?? '-' }}</td>
        </tr>
        <tr>
            <th>Grade</th>
            <td style="text-align:center;">{{ $reportCard->grade ?? '-' }}</td>
        </tr>
    </table>

    <section class="notes">
        <strong>Catatan Wali Kelas</strong>
        <p>{{ $reportCard->catatan ?: 'Terus tingkatkan kedisiplinan dan prestasi belajar.' }}</p>
    </section>

    <section class="signatures">
        <div>
            <p>Orang Tua/Wali</p>
            <br><br><br>
            <p>(______________________)</p>
        </div>
        <div>
            <p>Wali Kelas</p>
            <br><br><br>
            <p>(______________________)</p>
        </div>
    </section>
</body>
</html>
