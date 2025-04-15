<?php

namespace Bishopm\Methodist\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Guest extends Model
{
    public $table = 'guests';
    protected $guarded = ['id'];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function circuit(): BelongsTo
    {
        return $this->belongsTo(Circuit::class);
    }

}
