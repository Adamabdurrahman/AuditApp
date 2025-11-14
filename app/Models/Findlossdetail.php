<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Findlossdetail extends Model
{
    use HasFactory;

    protected $table = 'findlossdetail';
    public $timestamps = false;

    protected $fillable = [
        'item',
        'nilai',
        'nilai_usd',
        'paid_amount',
        'paid_amount_usd',
        'recorded_at',
        'audit_form_id'
    ];

    protected $casts = [
        'nilai' => 'float',
        'nilai_usd' => 'float',
        'paid_amount' => 'float',
        'paid_amount_usd' => 'float',
        'recorded_at' => 'datetime',
    ];

    public function auditForm()
    {
        return $this->belongsTo(AuditForm::class, 'audit_form_id');
    }

    public function recoveries()
    {
        return $this->hasMany(FindlossRecovery::class, 'findlossdetail_id')->orderBy('recorded_at', 'desc');
    }

    public function syncPaidAmount(): void
    {
        $totalIdr = $this->recoveries()->sum('amount');
        $totalUsd = $this->recoveries()->sum('amount_usd');
        $latest = $this->recoveries()->latest('recorded_at')->first();

        $this->paid_amount = $totalIdr;
        $this->paid_amount_usd = $totalUsd;
        $this->recorded_at = optional($latest)->recorded_at;
        $this->save();
    }
}