<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Findlossdetail extends Model
{
    use HasFactory;

    protected $table = 'findlossdetail';
    public $timestamps = false;

    protected $fillable = [
        'item',
        'nilai',
        'audit_form_id'
    ];

    public function auditForm()
    {
        return $this->belongsTo(AuditForm::class, 'audit_form_id');
    }
}