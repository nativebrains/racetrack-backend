<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FurlongLookup extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'distance',
        'type',
        'value'
    ];

    public function races()
    {
        return $this->morphedByMany(Race::class, 'distance');
    }
}
