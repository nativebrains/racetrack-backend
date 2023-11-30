<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horse extends Model
{
    use HasFactory;
    protected $fillable = [
        'race_id',
        'track_name',
        'date',
        'previous_race',
        'name',
        'weight_carried',
        'age',
        'gender',
        'equipment_id',
        'jockey',
        'win_odds',
        'claiming_price',
        'finish_position',
        'trainer',
        'owner',
        'data',
    ];

    protected $casts = [
        'data' => 'json',
    ];

    // Override the set accessor for the 'data' attribute
    public function setDataAttribute($value)
    {
        $this->attributes['data'] = json_encode($value);
    }

    // Override the get accessor for the 'data' attribute
    public function getDataAttribute($value)
    {
        return json_decode($value, true);
    }

    public function medicationEquipment(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(MedicationEquipment::class);
    }

}
