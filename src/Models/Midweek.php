<?php

namespace Bishopm\Methodist\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Midweek extends Model
{
    public $table = 'midweeks';
    public $timestamps = false;
    protected $guarded = ['id'];
}
