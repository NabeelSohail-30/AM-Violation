<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ViolationApi extends Model
{
    protected $table = 'violation_api';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'violation_type',
        'last_fetched_at',
        'fetch_param',
        'address_fields',
        'is_active',
        'created_by',
        'created_date',
        'updated_by',
        'updated_date'
    ];

    public $timestamps = true;

    // Custom timestamp column names
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    // Model events ka use kar ke created_by, updated_by automatically set karenge
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = Auth::id();
                $model->updated_by = Auth::id();
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });
    }
}
