<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditForm extends Model
{
    use HasFactory;

    protected $table = 'auditform'; // Sesuaikan dengan nama tabel Anda
    public $timestamps = false; // Jika tidak pakai created_at/updated_at

    protected $fillable = [
        'judul_temuan',
        'pic',
        'auditor',
        'temuan_audit',
        'kategori_id',
        'priority_id',
        'subkategori_id',
        'tanggal_temuan',
        'start_date',
        'due_date',
        'reminder_id',
        'rekomendasi_author',
        'catatan_tambahan',
        'status_id'
    ];

    // Relasi
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function priority()
    {
        return $this->belongsTo(Priority::class, 'priority_id');
    }

    public function subkategori()
    {
        return $this->belongsTo(Subkategori::class, 'subkategori_id');
    }

    public function reminder()
    {
        return $this->belongsTo(Reminder::class, 'reminder_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function findlossdetails()
    {
        return $this->hasMany(Findlossdetail::class, 'audit_form_id');
    }

    public function auditorUser()
    {
        return $this->belongsTo(User::class, 'auditor');
    }

    public function fileattachments()
    {
        return $this->hasMany(Fileattachment::class, 'auditform_id');
    }
}