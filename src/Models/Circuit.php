<?php

namespace Bishopm\Methodist\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Circuit extends Model
{
    public $table = 'circuits';
    protected $guarded = ['id'];

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }
}
