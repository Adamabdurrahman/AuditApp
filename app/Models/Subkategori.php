<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subkategori extends Model
{
    use HasFactory;

    protected $table = 'subkategori';
    public $timestamps = false;

    protected $fillable = ['name'];

    // Relasi ke AuditForm
    public function auditForms()
    {
        return $this->hasMany(AuditForm::class, 'subkategori_id');
    }
}