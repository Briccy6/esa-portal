<?php
// app/Models/Tariff.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tariff extends Model
{
    protected $fillable = [
        'activity', 'amount', 'has_discount',
        'discount_amount', 'discount_type',
        'deadline_type', 'deadline_date', 'deadline_hours',
    ];
}
