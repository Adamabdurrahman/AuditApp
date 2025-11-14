<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FindlossRecovery extends Model
{
    use HasFactory;

    protected $fillable = [
        'findlossdetail_id',
        'amount',
        'amount_usd',
        'recorded_at',
        'notes',
    ];

    protected $casts = [
        'amount' => 'float',
        'amount_usd' => 'float',
        'recorded_at' => 'date',
    ];

    public function detail()
    {
        return $this->belongsTo(Findlossdetail::class, 'findlossdetail_id');
    }
}
