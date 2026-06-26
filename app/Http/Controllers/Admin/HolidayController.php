<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Holiday;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class HolidayController extends Controller
{
    const INDONESIA_CALENDAR_ID = 'en.indonesian#holiday@group.v.calendar.google.com';

    public function index()
    {
        $holidays = Holiday::orderBy('date')->get();
        return view('admin.holidays.index', compact('holidays'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date|unique:holidays,date',
            'name' => 'required|string|max:255',
        ], [
            'date.unique' => 'Tanggal ini sudah ada di daftar hari libur.',
        ]);

        Holiday::create($request->only('date', 'name'));

        return redirect()->back()->with('success', 'Hari libur berhasil ditambahkan.');
    }

    public function destroy(Holiday $holiday)
    {
        $holiday->delete();
        return redirect()->back()->with('success', 'Hari libur berhasil dihapus.');
    }

   
    public function syncFromGoogle(Request $request)
    {
        $apiKey     = config('services.google.calendar_api_key');
        $calendarId = urlencode(self::INDONESIA_CALENDAR_ID);
        $year       = $request->input('year', now()->year);

        $response = Http::withoutVerifying()->get("https://www.googleapis.com/calendar/v3/calendars/{$calendarId}/events", [
        'key'          => $apiKey,
        'timeMin'      => "{$year}-01-01T00:00:00Z",
        'timeMax'      => "{$year}-12-31T23:59:59Z",
        'singleEvents' => 'true',
        'orderBy'      => 'startTime',
        'maxResults'   => 50,
    ]);

        if ($response->failed()) {
            return back()->with('error', 'Gagal koneksi ke Google Calendar. Cek API Key di .env kamu.');
        }

        $events = $response->json('items', []);
        $count  = 0;

        foreach ($events as $event) {
            $date = $event['start']['date'] ?? null;
            $name = $event['summary'] ?? 'Hari Libur';

            if (!$date) continue;

            Holiday::updateOrCreate(
                ['date' => $date],
                ['name' => $name]
            );

            $count++;
        }

        return back()->with('success', "Berhasil sync {$count} hari libur tahun {$year} dari Google Calendar!");
    }
}