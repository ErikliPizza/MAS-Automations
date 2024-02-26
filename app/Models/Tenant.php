<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;
    protected $fillable = ['organization_name', 'expiry_time', 'status'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'tenant_module')
            ->using(TenantModule::class);
    }
}
