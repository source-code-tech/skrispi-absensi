<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard Orang Tua') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    @if(isset($parentRecord))
                        <h4 class="mb-4">Selamat datang, Bapak/Ibu **{{ $parentRecord->name }}**</h4>
                        <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                            Berikut adalah ringkasan absensi anak-anak Anda dalam 30 hari terakhir.
                        </p>
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-200">
                                    <tr>
                                        <th>Nama Anak</th>
                                        <th>Kelas</th>
                                        <th>Tanggal Absen</th>
                                        <th>Waktu</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($absences as $absence)
                                    <tr>
                                        <td>{{ $absence->student->name }}</td>
                                        <td>{{ $absence->student->class?->name ?? 'N/A' }}</td>
                                        <td>{{ $absence->attendance_time->format('d/m/Y') }}</td>
                                        <td>{{ $absence->attendance_time->format('H:i') }}</td>
                                        <td>
                                            @php
                                                $statusClass = $absence->status == 'Hadir' ? 'badge-success' : 'badge-warning';
                                            @endphp
                                            <span class="badge {{ $statusClass }}">{{ $absence->status }}</span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Belum ada riwayat absensi tercatat.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-danger">Akun Anda belum terhubung sebagai Orang Tua. Hubungi administrator.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>