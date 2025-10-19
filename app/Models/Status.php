<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $table = 'status';
    public $timestamps = false;

    protected $fillable = ['status'];

    // Relasi ke AuditForm
    public function auditForms()
    {
        return $this->hasMany(AuditForm::class, 'status_id');
    }
}