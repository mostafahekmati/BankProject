<?php

namespace App\Services\User;

use App\Models\User;

class UserService
{
    public function topUsers()
    {
        $users = User::topUsers();

        foreach ($users as $user) {
            $user->transactions = $user->getRecentTransactions();
        }

        return $users;
    }
}
