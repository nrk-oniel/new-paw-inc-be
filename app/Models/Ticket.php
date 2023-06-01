<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $guarded = [
        'id',
    ];

    const ONGOING_STATUS = 1;
    const APPROVED_STATUS = 2;
    const REJECTED_STATUS = 0;

    public function clinic(){
        return $this->belongsTo(Clinic::class);
    }

    public function schedule(){
        return $this->belongsTo(Schedule::class);
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->id = sprintf("C-%05d",count(Ticket::all())+1);
        });
    }
}
