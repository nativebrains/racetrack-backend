<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Race extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'track_name',
        'date',
        'number_of_races',
        'type',
        'age_id',
        'status',
        'distance_type',
        'distance_id',
        'surface_id',
        'track_lookup_id',
        'data',
    ];

    protected $casts = [
        'data' => 'json',
    ];

    // Override the set accessor for the 'data' attribute
    public function setDataAttribute($value): void
    {
        $this->attributes['data'] = json_encode($value);
    }

    // Override the get accessor for the 'data' attribute
    public function getDataAttribute($value)
    {
        return json_decode($value, true);
    }

    public function horses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Horse::class, 'race_id', 'id');
    }
    public function age(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Age::class, 'age_id', 'id');
    }
    public function distance(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    public function surface(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Surface::class, 'surface_id', 'id');
    }
    public function track(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TrackLookup::class, 'track_lookup_id', 'id');
    }
}
