<?php

namespace Bishopm\Methodist\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Person extends Model
{

    public $table = 'persons';
    protected $guarded = ['id'];
    protected $casts = [ 'leadership' => 'array' ];

    public function minister(): HasOne
    {
        return $this->HasOne(Minister::class);
    }

    public function getStatusAttribute() {
        if ($this->minister){
            return "Minister";
        } else if ($this->preacher) {
            return "Preacher";
        } else {
            return "Leader";
        }
    }

    public function preacher(): HasOne
    {
        return $this->HasOne(Preacher::class);
    }

    public function circuit(): BelongsTo
    {
        return $this->belongsTo(Circuit::class);
    }

    public function society(): BelongsTo
    {
        return $this->belongsTo(Society::class);
    }

    protected function phone(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if ($value){
                    return substr($value,0,3) . " " . substr($value,3,3) . " " . substr($value,6,4);
                } else {
                    return '';
                }
        });
    }

}
