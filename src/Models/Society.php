<?php

namespace Bishopm\Methodist\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Society extends Model
{
    public $table = 'societies';
    protected $guarded = ['id'];
    protected $casts = [
        'value' => 'json',
    ];

    public function circuit(): BelongsTo
    {
        return $this->belongsTo(Circuit::class);
    }
}
