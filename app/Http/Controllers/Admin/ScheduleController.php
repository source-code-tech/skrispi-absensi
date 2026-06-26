<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\ClassModel;
use App\Models\Subject;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Menampilkan daftar kelas untuk memilih jadwal.
     */
    public function index()
    {
        $classes = ClassModel::orderBy('grade')->orderBy('name')->get();
        return view('admin.schedules.index', compact('classes'));
    }

    /**
     * Menampilkan jadwal untuk kelas tertentu.
     */
    /**
     * Menampilkan jadwal untuk kelas tertentu.
     */
    public function show($code)
    {
        $classModel = ClassModel::findOrFail($code);
        
        // Eager load jadwal dengan mapel, urutkan hari dan jam
        $schedules = Schedule::with('subject')
            ->where('class_code', $classModel->code)
            ->orderByRaw("FIELD(day, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat')")
            ->orderBy('start_time')
            ->get()
            ->groupBy('day');

        $subjects = Subject::orderBy('name')->get();
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        return view('admin.schedules.show', compact('classModel', 'schedules', 'subjects', 'days'));
    }

    /**
     * Menampilkan form tambah jadwal.
     */
    public function create(Request $request)
    {
        $class_code = $request->query('class_code');
        $preselectedClass = null;
        if ($class_code) {
            $preselectedClass = ClassModel::find($class_code);
        }
        
        $classes = ClassModel::orderBy('grade')->orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        return view('admin.schedules.create', compact('classes', 'subjects', 'days', 'preselectedClass'));
    }

    /**
     * Menyimpan jadwal baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'class_code' => 'required|exists:classes,code',
            'subject_code' => 'required|exists:subjects,code',
            'day' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ], [
            'class_code.required' => 'Kelas harus dipilih.',
            'subject_code.required' => 'Mata pelajaran harus dipilih.',
            'day.required' => 'Hari harus dipilih.',
            'start_time.required' => 'Jam mulai harus diisi.',
            'end_time.required' => 'Jam selesai harus diisi.',
            'end_time.after' => 'Jam selesai harus setelah jam mulai.',
        ]);

        Schedule::create($request->all());

        return redirect()->route('admin.schedules.show', $request->class_code)->with('success', 'Jadwal berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit jadwal.
     */
    public function edit(Schedule $schedule)
    {
        $classes = ClassModel::orderBy('grade')->orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        return view('admin.schedules.edit', compact('schedule', 'classes', 'subjects', 'days'));
    }

    /**
     * Memperbarui jadwal.
     */
    public function update(Request $request, Schedule $schedule)
    {
        $request->validate([
            'subject_code' => 'required|exists:subjects,code',
            'day' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ], [
            'subject_code.required' => 'Mata pelajaran harus dipilih.',
            'end_time.after' => 'Jam selesai harus setelah jam mulai.',
        ]);

        $schedule->update($request->all());

        return redirect()->route('admin.schedules.show', $schedule->class_code)->with('success', 'Jadwal berhasil diperbarui.');
    }

    /**
     * Menghapus jadwal.
     */
    public function destroy(Schedule $schedule)
    {
        $classCode = $schedule->class_code;
        $schedule->delete();
        return redirect()->route('admin.schedules.show', $classCode)->with('success', 'Jadwal berhasil dihapus.');
    }
}
