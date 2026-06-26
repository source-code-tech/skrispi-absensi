<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Absence;

class AttendanceRecorded // TIDAK PERLU implements ShouldBroadcast untuk notifikasi WhatsApp
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $absence;

    public function __construct(Absence $absence)
    {
        $this->absence = $absence;
    }

    // Karena ini notifikasi WA, kita tidak perlu channel broadcasting
    public function broadcastOn(): array
    {
        return [];
    }
}