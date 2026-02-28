<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Payment;
use App\Models\SchoolFee;

class SchoolFeeSegment extends Model
{
 protected $fillable = ['school_fee_id', 'name', 'amount'];

    public function schoolFee()
    {
        return $this->belongsTo(SchoolFee::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'fee_segment_id');
    }

}
