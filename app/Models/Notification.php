<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'auditform_id',
        'notificationstype_id',
        'title',
        'message',
        'is_read',
    ];

    /**
     * Relasi ke user penerima notifikasi
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke audit form (jika ada)
     */
    public function auditForm()
    {
        return $this->belongsTo(AuditForm::class, 'auditform_id');
    }

    /**
     * Relasi ke tipe notifikasi
     */
    public function type()
    {
        return $this->belongsTo(NotificationType::class, 'notificationstype_id');
    }

    /**
     * Scope untuk notifikasi belum dibaca
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
}
