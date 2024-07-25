<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Card extends Model
{
    use HasFactory , SoftDeletes;

    protected $fillable = ['account_id', 'card_number', 'balance'];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function sourceTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'source_card_id');
    }

    public function destinationTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'destination_card_id');
    }


    public static function findByCardNumber($cardNumber)
    {
        return self::with('account')->where('card_number', $cardNumber)->first();
    }

    public function decrementBalance($amount): void
    {
        $this->decrement('balance', $amount);
        $this->account->decrement('balance', $amount);
    }

    public function incrementBalance($amount): void
    {
        $this->increment('balance', $amount);
        $this->account->increment('balance', $amount);
    }

}
