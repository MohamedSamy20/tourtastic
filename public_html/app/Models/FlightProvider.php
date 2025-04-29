<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlightProvider extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'api_email',
        'api_password',
        'agency_code',
        'api_base_url',
        'enabled',
        'service_class'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'enabled' => 'boolean',
    ];
    
    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        // When enabling a provider, disable all others
        static::saving(function ($model) {
            if ($model->enabled) {
                self::where('id', '!=', $model->id)->update(['enabled' => false]);
            }
        });
    }
}
