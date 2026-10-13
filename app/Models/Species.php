<?php

namespace App\Models;

use App\Models\Concerns\HasDomain;
use App\Models\Concerns\HasLocalizedName;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Species extends Model
{
    use HasDomain;
    use HasFactory;
    use HasLocalizedName;
    use HasUlids;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'name_ar',
        'slug',
        'description',
        'description_ar',
        'is_active',
        'domain',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
