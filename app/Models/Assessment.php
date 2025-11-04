<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $table = 'assessment';
    protected $fillable = [
        'audit_form_id',
        'type',
        'title',
        'description',
        'test_date',
        'testing_performed',
    ];

    // 🔹 Relasi ke AuditForm
    public function auditForm()
    {
        return $this->belongsTo(AuditForm::class, 'audit_form_id');
    }

    // 🔹 Relasi ke Recovery (1 Assessment bisa punya banyak Recovery)
    public function recoveries()
    {
        return $this->hasMany(Recovery::class, 'assessment_id');
    }

    protected static function booted()
    {
        static::deleting(function ($assessment) {
            $assessment->recoveries()->delete();
        });
    }

}
