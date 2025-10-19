<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori';
    public $timestamps = false; // Karena tidak ada kolom created_at/updated_at

    protected $fillable = ['name'];

    // Relasi ke AuditForm
    public function auditForms()
    {
        return $this->hasMany(AuditForm::class, 'kategori_id');
    }
}