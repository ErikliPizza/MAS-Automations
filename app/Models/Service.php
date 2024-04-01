<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static exists()
 */
class Service extends Model
{
    protected $table = 'services';
    use HasFactory, BelongsToTenant;
    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'phone',
        'external',
        'bio',
        'slug',
        'opening_time',
        'closing_time',
        'status',
        'price'
    ];

    protected $appends = ['gravatar'];

    public function appointments()
    {
        return $this->hasMany(Appointment::class); // Replace Appointment::class with the actual name of your appointment model
    }


    public function getGravatarAttribute($size = 80)
    {
        if (isset($this->attributes['email'])) {
            $hash = md5(strtolower(trim($this->attributes['email'])));
            return "https://www.gravatar.com/avatar/$hash?s=$size&d=mp";
        } else {
            return "https://www.gravatar.com/avatar/?d=mp";
        }

    }

}
