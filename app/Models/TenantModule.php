<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TenantModule extends Pivot
{
    protected $table = 'tenant_module';

    use HasFactory, BelongsToTenant;
}
