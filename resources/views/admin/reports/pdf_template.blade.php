<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Absensi</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #1a1a2e;
            background: #fff;
        }

        .page { padding: 28px 32px; }

        /* ── KOP ── */
        .kop-table { width: 100%; border-collapse: collapse; padding-bottom: 12px; border-bottom: 3px solid #1e40af; margin-bottom: 16px; }
        .kop-logo { width: 60px; vertical-align: middle; padding-right: 14px; }
        .kop-logo img { width: 54px; height: 54px; object-fit: contain; }
        .kop-text { vertical-align: middle; }
        .kop-text .school-name { font-size: 15px; font-weight: bold; color: #1e40af; text-transform: uppercase; letter-spacing: 0.5px; }
        .kop-text .doc-title { font-size: 10px; color: #64748b; text-transform: uppercase; letter-spacing: 1px; margin-top: 2px; }
        .kop-badge { text-align: right; vertical-align: middle; }
        .kop-badge .badge { background: #1e40af; color: white; font-size: 8px; font-weight: bold; padding: 4px 10px; border-radius: 3px; letter-spacing: 1px; }

        /* ── INFO PERIODE ── */
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
        .info-card { background: #f0f4ff; border-left: 3px solid #1e40af; padding: 9px 13px; border-radius: 0 4px 4px 0; margin-right: 6px; }
        .info-card .lbl { font-size: 8px; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 2px; }
        .info-card .val { font-size: 11px; font-weight: bold; color: #1e3a8a; }

        /* ── RINGKASAN STATISTIK ── */
        .summary-title {
            font-size: 10px;
            font-weight: bold;
            color: #1e40af;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 8px;
            border-left: 3px solid #1e40af;
            padding-left: 8px;
        }
        .summary-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .summary-table td { width: 16.66%; padding: 3px; vertical-align: top; }
        .summary-box {
            text-align: center;
            padding: 10px 6px;
            border-radius: 5px;
        }
        .summary-box .s-count { font-size: 22px; font-weight: bold; line-height: 1; }
        .summary-box .s-label { font-size: 8px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.8px; margin-top: 4px; }

        .box-total    { background: #e0e7ff; color: #1e40af; }
        .box-hadir    { background: #dcfce7; color: #15803d; }
        .box-terlambat{ background: #fef9c3; color: #a16207; }
        .box-izin     { background: #dbeafe; color: #1d4ed8; }
        .box-sakit    { background: #cffafe; color: #0e7490; }
        .box-alpha    { background: #fee2e2; color: #b91c1c; }

        /* ── TABEL UTAMA ── */
        .section-title {
            font-size: 10px;
            font-weight: bold;
            color: #1e40af;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 8px;
            border-left: 3px solid #1e40af;
            padding-left: 8px;
        }
        .main-table { width: 100%; border-collapse: collapse; }
        .main-table thead tr th {
            background: #1e40af;
            color: white;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 7px 8px;
            text-align: left;
        }
        .main-table thead tr th:first-child { width: 24px; text-align: center; }
        .main-table tbody tr td {
            font-size: 9px;
            padding: 5px 8px;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
            vertical-align: middle;
        }
        .main-table tbody tr:nth-child(even) td { background: #f8faff; }
        .main-table tbody tr td:first-child { text-align: center; color: #94a3b8; font-size: 8px; }

        /* Status Pills */
        .pill {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .pill-hadir     { background: #dcfce7; color: #15803d; }
        .pill-terlambat { background: #fef9c3; color: #a16207; }
        .pill-izin      { background: #dbeafe; color: #1d4ed8; }
        .pill-sakit     { background: #cffafe; color: #0e7490; }
        .pill-alpha, .pill-alpa { background: #fee2e2; color: #b91c1c; }

        /* Empty state */
        .empty-row td { text-align: center; padding: 20px; color: #94a3b8; font-style: italic; }

        /* Footer */
        .footer { margin-top: 18px; text-align: center; font-size: 8px; color: #94a3b8; }
        .footer hr { border: none; border-top: 1px solid #e2e8f0; margin-bottom: 6px; }
    </style>
</head>
<body>
<div class="page">

    @php
        $settings  = $settings ?? ['school_name' => 'E-ABSENSI SEKOLAH', 'school_logo' => 'default/logo.png'];
        $logoPath  = public_path('storage/' . ($settings['school_logo'] ?? 'default/logo.png'));

        // ── HITUNG RINGKASAN ──
        $totalSiswa   = $absences->count();
        $totalHadir   = $absences->where('status', 'Hadir')->count();
        $totalTerlambat = $absences->where('status', 'Terlambat')->count();
        $totalIzin    = $absences->where('status', 'Izin')->count();
        $totalSakit   = $absences->where('status', 'Sakit')->count();
        $totalAlpha   = $absences->whereIn('status', ['Alpha', 'Alpa'])->count();
    @endphp

    {{-- ── 1. KOP SURAT ── --}}
    <table class="kop-table">
        <tr>
            @if(file_exists($logoPath))
            <td class="kop-logo">
                <img src="{{ $logoPath }}" alt="Logo">
            </td>
            @endif
            <td class="kop-text">
                <div class="school-name">{{ $settings['school_name'] ?? 'Nama Sekolah' }}</div>
                <div class="doc-title">Laporan Absensi Siswa</div>
            </td>
            <td class="kop-badge">
                <span class="badge">RESMI</span>
            </td>
        </tr>
    </table>

    {{-- ── 2. INFO PERIODE & KELAS ── --}}
    <table class="info-table">
        <tr>
            <td style="width:50%; padding-right:6px;">
                <div class="info-card">
                    <div class="lbl">Periode Laporan</div>
                    <div class="val">{{ $startDate->format('d F Y') }} &ndash; {{ $endDate->format('d F Y') }}</div>
                </div>
            </td>
            <td style="width:50%;">
                <div class="info-card">
                    <div class="lbl">Kelas</div>
                    <div class="val">{{ $class ? $class->name : 'Semua Kelas' }}</div>
                </div>
            </td>
        </tr>
    </table>

    {{-- ── 3. RINGKASAN STATISTIK ── --}}
    <div class="summary-title">Ringkasan Kehadiran</div>
    <table class="summary-table">
        <tr>
            <td>
                <div class="summary-box box-total">
                    <div class="s-count">{{ $totalSiswa }}</div>
                    <div class="s-label">Total Record</div>
                </div>
            </td>
            <td>
                <div class="summary-box box-hadir">
                    <div class="s-count">{{ $totalHadir }}</div>
                    <div class="s-label">Hadir</div>
                </div>
            </td>
            <td>
                <div class="summary-box box-terlambat">
                    <div class="s-count">{{ $totalTerlambat }}</div>
                    <div class="s-label">Terlambat</div>
                </div>
            </td>
            <td>
                <div class="summary-box box-izin">
                    <div class="s-count">{{ $totalIzin }}</div>
                    <div class="s-label">Izin</div>
                </div>
            </td>
            <td>
                <div class="summary-box box-sakit">
                    <div class="s-count">{{ $totalSakit }}</div>
                    <div class="s-label">Sakit</div>
                </div>
            </td>
            <td>
                <div class="summary-box box-alpha">
                    <div class="s-count">{{ $totalAlpha }}</div>
                    <div class="s-label">Alpha</div>
                </div>
            </td>
        </tr>
    </table>

    {{-- ── 4. TABEL DETAIL ── --}}
    <div class="section-title">Detail Absensi</div>
    <table class="main-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Tanggal</th>
                <th>Waktu Masuk</th>
                <th>NISN</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Status</th>
                <th>Terlambat</th>
            </tr>
        </thead>
        <tbody>
            @forelse($absences as $absence)
            @php
                $status = $absence->status ?? 'N/A';
                $pillClass = 'pill-' . strtolower(str_replace([' ', '-'], '', $status));
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $absence->attendance_time->format('d/m/Y') }}</td>
                <td>{{ $absence->attendance_time->format('H:i') }}</td>
                <td>{{ $absence->student->nisn ?? '-' }}</td>
                <td>{{ $absence->student->name ?? '-' }}</td>
                <td>{{ $absence->student->class->name ?? '-' }}</td>
                <td><span class="pill {{ $pillClass }}">{{ $status }}</span></td>
                <td>
                    {{ ($status === 'Terlambat' && $absence->late_duration) ? $absence->late_duration . ' menit' : '-' }}
                </td>
            </tr>
            @empty
            <tr class="empty-row">
                <td colspan="8">Tidak ada data absensi dalam periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ── 5. FOOTER ── --}}
    <div class="footer">
        <hr>
        Dicetak pada {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB
        &bull; {{ $settings['school_name'] ?? 'E-Absensi Sekolah' }}
    </div>

</div>
</body>
</html>