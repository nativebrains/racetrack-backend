<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicationEquipment extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'slug',
        'symbol',
        'name',
    ];

    public function horses()
    {
        return $this->belongsToMany(Horse::class);
    }
}
