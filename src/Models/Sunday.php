<?php

namespace Bishopm\Methodist\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sunday extends Model
{
    public $table = 'sundays';
    protected $guarded = ['id'];
    public $timestamps = false;
    protected $casts = [
        'readings' => 'json',
    ];
}
