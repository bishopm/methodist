<?php

namespace Bishopm\Methodist\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Plan extends Model
{
    public $table = 'plans';
    protected $guarded = ['id'];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}
