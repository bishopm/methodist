<?php

namespace Bishopm\Methodist\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Circuit extends Model
{
    public $table = 'circuits';
    protected $guarded = ['id'];
    protected $casts = [
        'servicetypes' => 'array',
        'midweeks' => 'array'
    ];

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function societies(): HasMany
    {
        return $this->hasMany(Society::class);
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(Meeting::class);
    }

    public function ministers(): HasMany
    {
        return $this->hasMany(Minister::class);
    }

    public function guests(): HasMany
    {
        return $this->hasMany(Minister::class);
    }

    public function persons(): HasMany
    {
        return $this->hasMany(Person::class);
    }
}
