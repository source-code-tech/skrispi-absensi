<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ClassModel;
use App\Models\Subject;

class Schedule extends Model
{
    protected $fillable = ['class_code', 'subject_code', 'day', 'start_time', 'end_time'];

    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_code', 'code');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_code', 'code');
    }
}
