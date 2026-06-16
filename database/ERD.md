# Entity Relationship Diagram

ERD ini berfokus pada tabel bisnis utama SIAKAD. Tabel teknis Laravel untuk autentikasi, session, cache, queue, role, dan tabel internal Filament Blog tetap digunakan oleh aplikasi.

```mermaid
erDiagram
    users ||--o{ role_user : memiliki
    roles ||--o{ role_user : diberikan
    users ||--o{ fblog_posts : menulis

    kelas ||--o{ siswa : menampung
    siswa ||--o| kartu_pelajar : memiliki
    siswa ||--o{ absensi : tercatat
    siswa ||--o{ nilai : memperoleh
    siswa ||--o{ rapor : menerima

    guru ||--o{ mata_pelajaran : mengampu
    guru ||--o{ jadwal_pelajaran : mengajar
    kelas ||--o{ jadwal_pelajaran : memiliki
    mata_pelajaran ||--o{ jadwal_pelajaran : dijadwalkan
    mata_pelajaran ||--o{ nilai : dinilai

    kelas {
        int id_kelas PK
        string nama_kelas
    }
    siswa {
        int id_siswa PK
        string nisn
        string nama_siswa
        int id_kelas FK
    }
    guru {
        int id_guru PK
        string nip
        string nama_guru
    }
    mata_pelajaran {
        int id_mapel PK
        string nama_mapel
        int id_guru FK
    }
    jadwal_pelajaran {
        bigint id PK
        string hari
        time jam_mulai
        time jam_selesai
        int kelas_id FK
        int subject_id FK
        int guru_id FK
        string ruangan
    }
    kartu_pelajar {
        int id_kartu PK
        string nomor_kartu
        int id_siswa FK
    }
    absensi {
        int id_absensi PK
        int id_siswa FK
        date tanggal
        string status
    }
    nilai {
        int id_nilai PK
        int id_siswa FK
        int id_mapel FK
        decimal nilai
        string grade
    }
    rapor {
        int id_rapor PK
        int id_siswa FK
        string tahun_ajaran
        string semester
        decimal rata_rata
    }
```
