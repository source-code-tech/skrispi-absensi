<?php

namespace App\Http\Controllers\WaliKelas;

use Carbon\Carbon;
use App\Models\Absence;
use App\Models\IzinRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class IzinProcessorController extends Controller
{
    // Helper untuk mendapatkan class ID Wali Kelas
    protected function getClassId()
    {
        return Auth::user()->homeroomTeacher->class_id ?? null;
    }

    /**
     * Tampilkan daftar permintaan izin yang Pending untuk kelas yang diampu.
     */
    public function index()
    {
        $classId = $this->getClassId();

        if (!$classId) {
            return redirect()->route('walikelas.dashboard')
                             ->with('error', 'Anda belum mengampu kelas.');
        }

        // Ambil semua permintaan Izin/Sakit yang statusnya Pending atau Approved
        $izinRequests = IzinRequest::with(['student.class'])
            ->whereHas('student', function ($query) use ($classId) {
                // Batasi hanya siswa di kelas Wali Kelas ini
                $query->where('class_id', $classId);
            })
            // Urutkan yang Pending di atas, lalu berdasarkan tanggal permintaan terbaru
            ->orderByRaw("FIELD(status, 'Pending', 'Approved', 'Rejected')")
            ->orderBy('request_date', 'desc')
            ->paginate(15);

        return view('walikelas.izin.index', compact('izinRequests'));
    }

    /**
     * Proses persetujuan permintaan izin/sakit.
     */
    public function approve(IzinRequest $izinRequest)
    {
        $classId = $this->getClassId();

        // 🛑 Otorisasi Kritis: Pastikan Wali Kelas berhak memproses request ini
        if ($izinRequest->student->class_id !== $classId) {
            abort(403, 'Akses Ditolak. Permintaan bukan dari kelas yang Anda ampu.');
        }

        if ($izinRequest->status !== 'Pending') {
            return redirect()->back()->with('error', 'Permintaan ini sudah diproses.');
        }
        
        DB::beginTransaction();
        try {
            // 1. Catat status Approved di tabel izin_requests
            $izinRequest->update([
            'status' => 'Approved',
            'approved_by' => Auth::id(),
            'jam_masuk' => Carbon::now()->format('H:i:s'), // Menambahkan jam saat approval
        ]);

            // 2. Buat record Absensi di tabel absences
            // Catatan: Jika ada scan masuk hari itu, record baru ini akan gagal (UNIQUE constraint)
           // 2. Buat/Update record Absensi di tabel absences
            $existingAbsence = Absence::where('student_id', $izinRequest->student_id)
                                      ->whereDate('attendance_time', $izinRequest->request_date)
                                      ->first();

            if ($existingAbsence) {
                // Jika sudah ada record, hanya boleh update jika statusnya 'Alfa'
                if ($existingAbsence->status === 'Alfa') {
                    $existingAbsence->update([
                        'status' => $izinRequest->type, // Sakit/Izin
                        'notes' => "Pengajuan dari Orang Tua: " . $izinRequest->reason,
                        'recorded_by' => 'Wali Kelas: ' . Auth::user()->name,
                        'is_manual_corrected' => true,
                        'correction_note' => 'Disetujui dari status Alfa (Online)',
                        'corrected_by' => Auth::user()->name,
                    ]);
                } else {
                    // Jika statusnya bukan Alpha (misal: Hadir/Terlambat), batalkan & beri pesan error
                    throw new \Exception("Siswa ini sudah memiliki record absensi '{$existingAbsence->status}' pada tanggal tersebut. Data tidak bisa ditimpa.");
                }
            } else {
                // Jika belum ada record sama sekali, buat record baru
                Absence::create([
                    'student_id' => $izinRequest->student_id,
                    'attendance_time' => $izinRequest->request_date,
                    'status' => $izinRequest->type,
                    'notes' => "Pengajuan dari Orang Tua: " . $izinRequest->reason,
                    'recorded_by' => 'Wali Kelas: ' . Auth::user()->name,
                    'is_manual_corrected' => true,
                    'correction_note' => 'Disetujui berdasarkan pengajuan online',
                    'corrected_by' => Auth::user()->name,
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', "Permintaan Izin/Sakit untuk {$izinRequest->student->name} berhasil disetujui dan dicatat di absensi harian.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal approve izin request ID {$izinRequest->id}: " . $e->getMessage());
            return redirect()->back()->with('error', "Gagal memproses persetujuan. Kemungkinan siswa sudah memiliki record absensi (Hadir/Terlambat) hari itu.");
        }
    }

    /**
     * Proses penolakan permintaan izin/sakit.
     */
    public function reject(IzinRequest $izinRequest)
    {
        $classId = $this->getClassId();

        if ($izinRequest->student->class_id !== $classId) {
            abort(403, 'Akses Ditolak.');
        }

        if ($izinRequest->status !== 'Pending') {
            return redirect()->back()->with('error', 'Permintaan ini sudah diproses.');
        }

        $izinRequest->update([
            'status' => 'Rejected',
            'approved_by' => Auth::id(),
            'correction_note' => 'Ditolak oleh Wali Kelas.',
        ]);

        return redirect()->back()->with('success', "Permintaan Izin/Sakit untuk {$izinRequest->student->name} berhasil ditolak.");
    }

    /**
     * Hapus permintaan izin/sakit (opsi bersih-bersih data).
     */
    public function destroy(IzinRequest $izinRequest)
    {
        $classId = $this->getClassId();

        // 🛑 Otorisasi: Pastikan Wali Kelas hanya hapus data kelasnya sendiri
        if ($izinRequest->student->class_id !== $classId) {
            abort(403, 'Akses Ditolak. Data ini bukan milik siswa kelas Anda.');
        }

        // Hapus file lampiran jika ada
        if ($izinRequest->attachment_path && Storage::disk('public')->exists($izinRequest->attachment_path)) {
            Storage::disk('public')->delete($izinRequest->attachment_path);
        }

        $izinRequest->delete();

        return redirect()->back()->with('success', "Data pengajuan izin berhasil dihapus.");
    }
}