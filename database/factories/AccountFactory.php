<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    protected $model = Account::class;

    public function definition()
    {
        $userIds = User::pluck('id')->toArray();
        $randomUserId = $this->faker->randomElement($userIds);
        return [
            'user_id' => $randomUserId,
            'account_number' => $this->faker->unique()->regexify('^\d{12}$'),
            'balance' => $this->faker->randomFloat(2, 1000, 1000000000),
        ];
    }
}
