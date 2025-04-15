<?php

namespace Bishopm\Methodist\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Society extends Model
{
    public $table = 'societies';
    protected $guarded = ['id'];
    protected $casts = [
        'location' => 'json',
    ];

    public function circuit(): BelongsTo
    {
        return $this->belongsTo(Circuit::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class)->orderBy('servicetime');
    }

    public function persons(): HasMany
    {
        return $this->hasMany(Person::class);
    }
}
