<?php

namespace App\Repositories;

use App\Http\Resources\UserResource;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserRepository extends BaseRepository
{
    public function topUsers(): \Illuminate\Http\JsonResponse
    {

        $tenMinutesAgo = now()->subMinutes(10);


        $topUsers = User::select('users.id', 'users.name', 'users.email', 'users.mobile',

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

            ->take(3)

            ->get();


        foreach ($topUsers as $user) {

            $user->transactions = Transaction::where(function ($query) use ($user) {
                $query->whereHas('sourceCard.account', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                    ->orWhereHas('destinationCard.account', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    });
            })

                ->orderByDesc('created_at')

                ->take(10)

                ->get();
        }

        $userResourceCollection = UserResource::collection($topUsers);
        return $this->returnSuccessResponse([
            'users' => $userResourceCollection
        ], 'Top users and their transactions fetched successfully.');


    }
}
