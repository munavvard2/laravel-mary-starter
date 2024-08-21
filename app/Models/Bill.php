<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function customer()
    {
        return $this->belongsTo(User::class,'customer_id','id');
    }
}
