<?php

namespace App\Repositories;

use App\Constants\TransactionConst;
use App\Models\Card;
use App\Models\Fee;
use App\Models\Transaction;
use App\Services\Sms\SMSService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionRepository extends BaseRepository
{
    protected $smsService;

    public function __construct(SMSService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function transfer(Request $request): \Illuminate\Http\JsonResponse
    {
        $sourceCardNumber = $request->input('source_card_number');

        $destinationCardNumber = $request->input('destination_card_number');

        $amount = $request->input('amount');

        $validationResponse = $this->validateTransfer($sourceCardNumber, $destinationCardNumber, $amount);
        if ($validationResponse) {
            return $validationResponse;
        }

        $sourceCard = Card::with('account')->where('card_number', $sourceCardNumber)->first();

        $destinationCard = Card::with('account')->where('card_number', $destinationCardNumber)->first();

        $validationResponse = $this->validateCardsAndBalance($sourceCard, $destinationCard, $amount);

        if ($validationResponse) {
            return $validationResponse;
        }

        try {

            DB::transaction(function () use ($sourceCard, $destinationCard, $amount) {
                $transactionFee = TransactionConst::TRANSACTION_FEE;


                $sourceCard->decrement('balance', $amount + $transactionFee);
                $destinationCard->increment('balance', $amount);


                $sourceCard->account->decrement('balance', $amount + $transactionFee);
                $destinationCard->account->increment('balance', $amount);


                $transaction = Transaction::create([
                    'source_card_id' => $sourceCard->id,
                    'destination_card_id' => $destinationCard->id,
                    'amount' => $amount,
                ]);

                Fee::create([
                    'transaction_id' => $transaction->id,
                    'amount' => $transactionFee,
                ]);

                $this->smsService->send($sourceCard->account->user->mobile,  trans('all.balance_decreased', ['amount' => $amount]));
                $this->smsService->send($destinationCard->account->user->mobile, trans('all.balance_increased', ['amount' => $amount]));
            });

        } catch (\Exception $e) {

            return $this->returnFailureResponse(null, 'Transaction failed. Please try again later.');
        }

        return $this->returnSuccessResponse(null, 'Transaction completed successfully.');
    }

    private function validateTransfer($sourceCardNumber, $destinationCardNumber, $amount): ?\Illuminate\Http\JsonResponse
    {
        if ($sourceCardNumber === $destinationCardNumber) {
            return $this->returnFailureResponse(null, 'The origin and destination cards cannot be the same.');
        }

        if (!$amount || $amount <= 0) {
            return $this->returnFailureResponse(null, 'Invalid transfer amount.');
        }

        return null;
    }

    private function validateCardsAndBalance($sourceCard, $destinationCard, $amount): ?\Illuminate\Http\JsonResponse
    {
        if (!$sourceCard || !$destinationCard) {
            return $this->returnFailureResponse(null, 'One or both card numbers are not recognized.');
        }

        if ($sourceCard->balance < $amount + TransactionConst::TRANSACTION_FEE) {
            return $this->returnFailureResponse(null, 'Insufficient funds.');
        }

        return null;
    }

}
