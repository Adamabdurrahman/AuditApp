<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fileattachment extends Model
{
    use HasFactory;

    protected $table = 'fileattachment';
    public $timestamps = false; // Karena tidak ada kolom created_at/updated_at

    protected $fillable = [
        'auditform_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size'
    ];

    // Relasi ke AuditForm
    public function auditForm()
    {
        return $this->belongsTo(AuditForm::class, 'auditform_id');
    }
}