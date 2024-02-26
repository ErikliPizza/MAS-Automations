<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $table = 'appointments';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'service_id',
        'user_id',
        'slug',
        'name',
        'email',
        'phone',
        'start_time',
        'end_time',
        'status',
        'notes',
        'price'
    ];
    use HasFactory, BelongsToTenant;
}
