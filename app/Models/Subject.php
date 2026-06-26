<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['name', 'code'];

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'subject_code', 'code');
    }
}
