<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_fee_id',
        'payment_type',
        'discount',
        'payed',
    ];

    public function schoolFee()
    {
        return $this->belongsTo(SchoolFee::class);
    }
}
