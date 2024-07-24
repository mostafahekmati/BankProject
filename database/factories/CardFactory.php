<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Card;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Card>
 */
class CardFactory extends Factory
{
    protected $model = Card::class;

    public function definition(): array
    {
        $accountIds = Account::pluck('id')->toArray();
        $randomAccountId = $this->faker->randomElement($accountIds);

        // Common Iranian bank card prefixes
        $prefixes = [
            '603799', // Melli Bank
            '589210', // Sepah Bank
            '627353', // Tejarat Bank
            '627760', // Post Bank Iran
            '502229', // Pasargad Bank
            '627488', // Karafarin Bank
            '621986', // Saman Bank
            '639346', // Parsian Bank
            '639607', // Sarmayeh Bank
            '636214', // Ayandeh Bank
            '502806', // Shahr Bank
            '504706', // Saderat Bank
            '505416', // Gardeshgari Bank
            '603769', // Mellat Bank
            '628023', // Maskan Bank
            '627961', // Sanat va Madan Bank
        ];

        $prefix = $this->faker->randomElement($prefixes);

        return [
            'account_id' => $randomAccountId,
            'card_number' => $this->faker->unique()->regexify('^' . $prefix . '\d{10}$'),
            'balance' => $this->faker->randomFloat(2, 0, 1000000000),
        ];
    }
}
