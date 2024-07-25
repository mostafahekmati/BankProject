<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{

    use HasFactory, Notifiable , SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    public static function topUsers($limit = 3)
    {
        $tenMinutesAgo = now()->subMinutes(10);

        return self::select('users.id', 'users.name', 'users.email', 'users.mobile',
            DB::raw('COUNT(DISTINCT sourceTransactions.id) + COUNT(DISTINCT destinationTransactions.id) as transaction_count'))
            ->join('accounts', 'accounts.user_id', '=', 'users.id')
            ->join('cards', 'cards.account_id', '=', 'accounts.id')
            ->leftJoin('transactions as sourceTransactions', 'sourceTransactions.source_card_id', '=', 'cards.id')
            ->leftJoin('transactions as destinationTransactions', 'destinationTransactions.destination_card_id', '=', 'cards.id')
            ->where(function($query) use ($tenMinutesAgo) {
                $query->where('sourceTransactions.created_at', '>=', $tenMinutesAgo)
                    ->orWhere('destinationTransactions.created_at', '>=', $tenMinutesAgo);
            })
            ->groupBy('users.id', 'users.name', 'users.email', 'users.mobile')
            ->orderByDesc('transaction_count')
            ->take($limit)
            ->get();
    }

    public function getRecentTransactions()
    {
        return Transaction::where(function ($query) {
            $query->whereHas('sourceCard.account', function ($query) {
                $query->where('user_id', $this->id);
            })
                ->orWhereHas('destinationCard.account', function ($query) {
                    $query->where('user_id', $this->id);
                });
        })
            ->orderByDesc('created_at')
            ->take(10)
            ->get();
    }
}
