<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\ClassModel;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToCollection; // 💡 GANTI ToModel -> ToCollection
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
// use Maatwebsite\Excel\Concerns\WithUpserts; // DIHAPUS: Kita handle manual agar Barcode aman
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Shared\Date; 
use Illuminate\Support\Collection;

class StudentsImport implements ToCollection, WithHeadingRow, WithValidation, WithChunkReading, SkipsEmptyRows
{
    private $classIds;
    private $rows = 0;

    public function __construct()
    {
        // Cache semua Nama Kelas dan ID-nya untuk Lookup Cepat
        $this->classIds = ClassModel::pluck('id', 'name');
    }

    /**
     * Mengembalikan jumlah baris yang berhasil diolah.
     */
    public function getRowCount(): int
    {
        return $this->rows;
    }

    /**
     * Proses Collection dari Excel (Chunked).
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        // 1. Ambil daftar NISN dari chunk ini untuk query efisien
        $nisns = $rows->pluck('nisn')->filter()->map(function($item) {
            return trim($item);
        })->toArray();
        
        // Cek duplikat di dalam file yang di-upload
        $nisnList = $rows->pluck('nisn');
        if ($nisnList->duplicates()->isNotEmpty()) {
            throw new \Exception('Ditemukan NISN ganda di dalam file Excel: ' . $nisnList->duplicates()->implode(', '));
        }
        // 2. Load Existing Students (Keyed by NISN)
        $existingStudents = Student::whereIn('nisn', $nisns)->get()->keyBy('nisn');

        $newStudents = [];

        foreach ($rows as $row) {
            $nisn = trim($row['nisn']);
            $className = trim($row['nama_kelas']);

            // Validasi Kelas (double check, meski sudah ada di rules)
            if (!isset($this->classIds[$className])) {
                continue; 
            }
            $class_id = $this->classIds[$className];

            // Konversi Tanggal Lahir
            $birthDate = null;
            if (isset($row['birth_date']) && !empty($row['birth_date'])) {
                $val = trim($row['birth_date']);
                if (is_numeric($val)) {
                    $birthDate = Date::excelToDateTimeObject($val);
                } else {
                    try {
                        $birthDate = \Carbon\Carbon::createFromFormat('Y-m-d', $val)->toDateString();
                    } catch (\Exception $e) {
                        $birthDate = null;
                    }
                }
            }

            // Helper untuk membersihkan data kosong menjadi NULL agar tidak error Unique SQL
            $cleanInput = function($value) {
                $val = trim($value ?? '');
                return $val === '' ? null : $val;
            };

            // Data yang akan disimpan
            $dataToUpdate = [
                'nis'           => $cleanInput($row['nis'] ?? null),
                'name'          => trim($row['nama_siswa']), // Nama wajib, tidak boleh null
                'gender'        => trim($row['jenis_kelamin']),
                'class_id'      => $class_id,
                'address'       => $cleanInput($row['alamat'] ?? null),
                'birth_place'   => $cleanInput($row['tempat_lahir'] ?? null),
                'birth_date'    => $birthDate,
                'status'        => 'active', // Default active saat import
            ];

            if ($existingStudents->has($nisn)) {
                // UPDATE: Jangan sentuh barcode_data atau photo (biarkan photo lama)
                $student = $existingStudents[$nisn];
                $student->update($dataToUpdate);
            } else {
                // INSERT: Tambahkan barcode_data baru dan default photo
                $dataToUpdate['nisn'] = $nisn;
                $dataToUpdate['barcode_data'] = Str::uuid()->toString();
                $dataToUpdate['photo'] = 'default_avatar.png';
                
                // Masukkan ke array batch insert (atau create langsung)
                // Kita create langsung agar UUID & Events jalan normal
                Student::create($dataToUpdate);
            }
            $this->rows++;
        }
    }

    /**
     * Definisikan aturan validasi untuk setiap kolom.
     */
    public function rules(): array
    {
        $classNamesArray = $this->classIds->keys()->toArray();

        return [
            'nisn'          => 'required|numeric', 
            // 'nis' dan 'nomor_telepon' dihapus dari validasi strict agar tidak error jika Excel mengirim angka (karena rule 'max' pada angka mengecek VALUE bukan LENGTH)
            // 'nis'           => 'nullable|string|max:20', 
            // 'nomor_telepon' => 'nullable|string|max:20', 
            'nama_siswa'    => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'alamat'        => 'nullable|string|max:500', 
            'tempat_lahir'  => 'nullable|string|max:100', 
            'birth_date'    =>  'nullable',
            'nama_kelas' => [
                 'required', 
                 Rule::in($classNamesArray), 
            ],
        ];
    }
    
    // --- PENGATURAN PERFORMA ---

    public function chunkSize(): int
    {
        return 500; 
    }
}