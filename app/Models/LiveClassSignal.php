<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveClassSignal extends Model
{
    protected $fillable = ['live_class_id', 'from_peer', 'to_peer', 'type', 'payload'];
}