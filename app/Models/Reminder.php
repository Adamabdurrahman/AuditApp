<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory;

    protected $table = 'reminder';
    public $timestamps = false;

    protected $fillable = ['pt', 'nama', 'email'];

    // Relasi ke AuditForm
    public function auditForms()
    {
        return $this->hasMany(AuditForm::class, 'reminder_id');
    }
}