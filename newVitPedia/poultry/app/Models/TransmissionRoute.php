<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TransmissionRoute extends Model
{
    use HasUlids;

    protected $fillable = [

        'name',

        'slug',

        'display_name',

        'description'
    ];

    /*
    |--------------------------------------------------------------------------
    | Primary Key
    |--------------------------------------------------------------------------
    */

    protected $keyType = 'string';

    public $incrementing = false;

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function diseases(): BelongsToMany
    {
        return $this->belongsToMany(
            Disease::class
        );
    }
}