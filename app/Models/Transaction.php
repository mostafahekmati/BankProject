<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['source_card_id', 'destination_card_id', 'amount'];

    public function sourceCard(): BelongsTo
    {
        return $this->belongsTo(Card::class, 'source_card_id');
    }

    public function destinationCard(): BelongsTo
    {
        return $this->belongsTo(Card::class, 'destination_card_id');
    }

    public function fee(): HasOne
    {
        return $this->hasOne(Fee::class);
    }

    public static function createTransaction($sourceCardId, $destinationCardId, $amount)
    {
        $transaction = self::create([
            'source_card_id' => $sourceCardId,
            'destination_card_id' => $destinationCardId,
            'amount' => $amount,
        ]);


        return $transaction;
    }

}
