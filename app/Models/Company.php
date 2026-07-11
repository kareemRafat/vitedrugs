<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory;
    use HasUlids;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'name_ar',
        'slug',
        'parent_company_id',
        'company_type',
        'logo',
        'description',
        'description_ar',
        'country',
        'address',
        'address_ar',
        'governorate',
        'google_maps_url',
        'coverage_area',
        'registration_number',
        'website',
        'email',
        'phone',
        'mobile',
        'facebook',
        'linkedin',
        'contact_person',
        'whatsapp',
        'telegram',
        'youtube',
        'instagram',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products()
    {
        return $this->belongsToMany(
            Product::class,
            'product_company'
        )->withPivot('role')
            ->withTimestamps();
    }

    public function parentCompany()
    {
        return $this->belongsTo(
            Company::class,
            'parent_company_id'
        );
    }

    public function subsidiaries()
    {
        return $this->hasMany(
            Company::class,
            'parent_company_id'
        );
    }

    public function isManufacturer(): bool
    {
        return $this->company_type === 'manufacturer';
    }

    public function isAgent(): bool
    {
        return $this->company_type === 'agent';
    }

    public function isDistributor(): bool
    {
        return $this->company_type === 'distributor';
    }

    public function scopeManufacturers($query)
    {
        return $query->where(
            'company_type',
            'manufacturer'
        );
    }

    public function scopeAgents($query)
    {
        return $query->where(
            'company_type',
            'agent'
        );
    }

    public function scopeDistributors($query)
    {
        return $query->where(
            'company_type',
            'distributor'
        );
    }
}
