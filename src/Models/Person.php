<?php

namespace Bishopm\Methodist\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Person extends Model
{

    public $table = 'persons';
    protected $guarded = ['id'];
    protected $casts = [ 
        'leadership' => 'array' 
    ];

    public function minister(): HasOne
    {
        return $this->HasOne(Minister::class);
    }

    public function preacher(): HasOne
    {
        return $this->HasOne(Preacher::class);
    }

    public function circuitroles(): HasMany
    {
        return $this->hasMany(Circuitrole::class);
    }

    public function circuits(): BelongsToMany
    {
        return $this->belongsToMany(Circuit::class,'circuit_person')->withPivot(('status'));
    }

    public function society(): BelongsTo
    {
        return $this->belongsTo(Society::class);
    }

}
