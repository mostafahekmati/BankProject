<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fee extends Model
{

    use HasFactory, SoftDeletes;

    protected $fillable = ['transaction_id', 'amount'];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
    public static function createFee($transactionId, $amount)
    {
        return self::create([
            'transaction_id' => $transactionId,
            'amount' => $amount,
        ]);
    }
}
