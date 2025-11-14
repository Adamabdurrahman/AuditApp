<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'year',
        'description',
        'status',
    ];

    public function findings()
    {
        return $this->hasMany(AuditForm::class, 'audit_plan_id');
    }
}
