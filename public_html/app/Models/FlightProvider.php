<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlightProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'api_email',
        'api_password',
        'agency_code',
        'api_base_url',
        'service_class',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    /**
     * Scope a query to only include enabled providers.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }
}
