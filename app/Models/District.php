<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $fillable = [
        'districtname',
        'province_id', // this is a foreign key reference to Province
    ];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }
}