<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class LandingController extends Controller
{
    /**
     * Tampilkan halaman landing page dengan data dinamis dari Settings.
     */
    public function index()
    {
        // Muat semua pengaturan yang diperlukan
        $settings = Setting::pluck('value', 'key')->toArray();
        
        // Pastikan key site_description selalu ada di array $settings sebelum dipanggil di view
        // Menggunakan Arr::get untuk mendapatkan nilai yang ada, jika tidak ada, gunakan default.
        $siteDescription = \Illuminate\Support\Arr::get($settings, 'site_description', 
            'Sistem Absensi Siswa Digital berbasis Barcode yang cepat, akurat, dan terintegrasi dengan notifikasi orang tua.');
            
        // Assign kembali ke array settings
        $settings['site_description'] = $siteDescription;
        
        // Asumsi data ini sudah di-pass dari SettingController
        $settings['app_version'] = '1.0 (Build ' . date('Ymd') . ')';

        return view('landing', compact('settings'));
    }
}