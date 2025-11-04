<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recovery extends Model
{
    use HasFactory;

    protected $table = 'recovery';

    public $timestamps = false;
    
    protected $fillable = [
        'item',
        'nilai',
        'assessment_id',
    ];

    // 🔹 Relasi ke Assessment
    public function assessment()
    {
        return $this->belongsTo(Assessment::class, 'assessment_id');
    }
}
