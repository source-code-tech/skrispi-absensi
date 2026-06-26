<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Pelajar - {{ $student->name }}</title>
    <!-- EXACT SAME CSS AS ADMIN VIEW FOR CONSISTENCY -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        * { box-sizing: border-box; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        body { 
            font-family: 'Poppins', Arial, sans-serif;
            margin: 0; padding: 20px;
            background: #f2f5f9;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            min-height: 100vh;
        }
        @page { size: A4 portrait; margin: 0; }

        .card-container {
            width: 85.6mm; height: 53.98mm;
            background: #fff; border-radius: 8px; position: relative; overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1); border: 1px solid #e5e7eb;
            display: grid; 
            grid-template-rows: 10mm 1fr 4.5mm; /* Header 10mm, Footer 4.5mm */
        }

        .card-bg { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; background: linear-gradient(135deg, #ffffff 60%, #eef2ff 100%); }
        .card-bg::before { content: ''; position: absolute; top: -20px; right: -20px; width: 100px; height: 100px; background: rgba(79, 70, 229, 0.05); border-radius: 50%; }

        .header { position: relative; z-index: 2; background: linear-gradient(90deg, #4f46e5 0%, #4338ca 100%); display: flex; align-items: center; justify-content: space-between; padding: 0 8px; color: white; }
        .header-left { display: flex; align-items: center; gap: 6px; }
        .logo { width: 24px; height: 24px; object-fit: contain; background: #fff; border-radius: 50%; padding: 1px; }
        .school-info h1 { font-size: 8pt; font-weight: 700; margin: 0; line-height: 1.1; text-transform: uppercase; letter-spacing: 0.5px; }
        .school-info p { font-size: 4.5pt; margin: 0; opacity: 0.9; font-weight: 400; }
        .header-title { font-size: 6pt; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; border: 1px solid rgba(255,255,255,0.3); padding: 1px 5px; border-radius: 3px; }

        .content { position: relative; z-index: 2; padding: 4px 10px; display: grid; grid-template-columns: 1fr 20mm; gap: 6px; align-items: center; }
        .data-section { display: flex; flex-direction: column; justify-content: center; gap: 2px; }
        .data-row { display: grid; grid-template-columns: 14mm 3mm 1fr; align-items: baseline; }
        .label { font-size: 6pt; color: #555; font-weight: 600; }
        .colon { font-size: 6pt; color: #555; text-align: center; }
        .value { font-size: 6.5pt; color: #000; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        .photo-qr-section { display: flex; flex-direction: column; align-items: center; justify-content: flex-start; gap: 3px; height: 100%; }
        .photo-box { width: 19mm; height: 22mm; border: 1px solid #d1d5db; padding: 1px; background: #fff; border-radius: 4px; }
        .photo-box img { width: 100%; height: 100%; object-fit: cover; border-radius: 3px; }
        
        .qr-overlay { width: 17mm; height: 17mm; background: #fff; padding: 1px; border-radius: 2px; box-shadow: 0 1px 2px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: center; border: 1px solid #eee; }
        .qr-overlay svg { width: 100%; height: 100%; }

        .footer { position: relative; z-index: 2; background: #4f46e5; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 4.5pt; font-weight: 500; letter-spacing: 0.5px; text-transform: uppercase; }

        .print-btn-container { margin-top: 20px; display: flex; gap: 10px; }
        .btn { padding: 10px 20px; border-radius: 6px; font-weight: 600; cursor: pointer; border: none; font-size: 14px; transition: 0.2s; text-decoration: none; }
        .btn-print { background: #4f46e5; color: white; }
        .btn-back { background: #e5e7eb; color: #374151; }

        @media print {
            body { background: none; margin: 0; padding: 0; display: block; }
            .print-btn-container { display: none; }
            .card-container { margin: 10mm auto; box-shadow: none; border: 1px solid #ddd; page-break-inside: avoid; }
        }
    </style>
</head>
<body>

    <div class="card-container">
        <div class="card-bg"></div>
        <div class="header">
            <div class="header-left">
                <img src="{{ isset($settings['school_logo']) ? asset('storage/'.$settings['school_logo']) : asset('images/default/logo.png') }}" alt="Logo" class="logo">
                <div class="school-info">
                    <h1>{{ $settings['school_name'] ?? 'SMPN 4 KADUPANDAK' }}</h1>
                    <p>{{ $settings['school_address'] ?? 'Jalan Raya Kadupandak' }}</p>
                </div>
            </div>
            <div class="header-title">Kartu Pelajar</div>
        </div>
        <div class="content">
            <div class="data-section">
                <div class="data-row"><span class="label">NAMA</span><span class="colon">:</span><span class="value" style="text-transform: uppercase;">{{ $student->name }}</span></div>
                <div class="data-row"><span class="label">NISN/NIS</span><span class="colon">:</span><span class="value">{{ $student->nisn }} / {{ $student->nis ?? '-' }}</span></div>
                <div class="data-row"><span class="label">TTL</span><span class="colon">:</span><span class="value">{{ $student->birth_place ? $student->birth_place.',' : '' }} {{ $student->birth_date ? $student->birth_date->format('d-m-Y') : '-' }}</span></div>
                <div class="data-row"><span class="label">JENIS KELAMIN</span><span class="colon">:</span><span class="value">{{ $student->gender == 'Laki-laki' ? 'LAKI-LAKI' : 'PEREMPUAN' }}</span></div>
                <div class="data-row"><span class="label">KELAS</span><span class="colon">:</span><span class="value">{{ $student->class->name ?? '-' }}</span></div>
                <div class="data-row"><span class="label">BERLAKU</span><span class="colon">:</span><span class="value" style="color: #4f46e5;">SELAMA JADI SISWA</span></div>
            </div>
            <div class="photo-qr-section">
                <div class="photo-box">
                    @php $photoPath = ($student->photo && $student->photo != 'default_avatar.png') ? asset('storage/' . $student->photo) : asset('images/default/student.png'); @endphp
                    <img src="{{ $photoPath }}" alt="Foto">
                </div>
                <div class="qr-overlay">
                    {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(70)->margin(0)->generate($student->barcode_data) !!}
                </div>
            </div>
        </div>
        <div class="footer">Kartu ini sah jika terdapat stempel sekolah yang berlaku</div>
    </div>

    <div class="print-btn-container">
        <button onclick="window.print()" class="btn btn-print"><i class="fas fa-print"></i> Cetak Kartu</button>
        <button onclick="window.close()" class="btn btn-back">Tutup</button>
    </div>

</body>
</html>