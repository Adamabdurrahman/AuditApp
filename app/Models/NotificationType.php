<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationType extends Model
{
    use HasFactory;

    protected $table = 'notificationstype'; // sesuai nama tabel di database

    protected $fillable = [
        'name',
    ];

    /**
     * Relasi ke Notification
     * Satu jenis notifikasi bisa memiliki banyak notifikasi.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'notificationstype_id');
    }
}
