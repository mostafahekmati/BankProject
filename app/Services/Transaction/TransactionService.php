<?php

namespace App\Services\Transaction;

use App\Constants\TransactionConst;
use App\Jobs\SendSmsJob;
use App\Models\Card;
use App\Models\Fee;
use App\Models\Transaction;
use App\Services\Sms\SMSService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionService
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

        $sourceCard = Card::findByCardNumber($sourceCardNumber);
        $destinationCard = Card::findByCardNumber($destinationCardNumber);

        $validationResponse = $this->validateCardsAndBalance($sourceCard, $destinationCard, $amount);
        if ($validationResponse) {
            return $validationResponse;
        }

        return $this->executeTransfer($sourceCard, $destinationCard, $amount);
    }

    private function validateTransfer($sourceCardNumber, $destinationCardNumber, $amount): ?\Illuminate\Http\JsonResponse
    {
        if ($sourceCardNumber === $destinationCardNumber) {
            return response()->json(['message' => 'The origin and destination cards cannot be the same.'], 400);
        }
        if (!$amount || $amount <= 0) {
            return response()->json(['message' => 'Invalid transfer amount.'], 400);
        }
        return null;
    }

    private function validateCardsAndBalance($sourceCard, $destinationCard, $amount): ?\Illuminate\Http\JsonResponse
    {
        if (!$sourceCard || !$destinationCard) {
            return response()->json(['message' => 'One or both card numbers are not recognized.'], 400);
        }
        if ($sourceCard->balance < $amount + TransactionConst::TRANSACTION_FEE) {
            return response()->json(['message' => 'Insufficient funds.'], 400);
        }
        return null;
    }

    private function executeTransfer($sourceCard, $destinationCard, $amount): \Illuminate\Http\JsonResponse
    {
        try {
            DB::transaction(function () use ($sourceCard, $destinationCard, $amount) {
                $transactionFee = TransactionConst::TRANSACTION_FEE;

                $sourceCard->decrementBalance($amount + $transactionFee);
                $destinationCard->incrementBalance($amount);


                $transaction = Transaction::createTransaction($sourceCard->id, $destinationCard->id, $amount);


                Fee::createFee($transaction->id, $transactionFee);


                try {
                    SendSmsJob::dispatch($sourceCard->account->user->mobile, trans('all.balance_decreased', ['amount' => $amount]));
                    SendSmsJob::dispatch($destinationCard->account->user->mobile, trans('all.balance_increased', ['amount' => $amount]));
                } catch (\Exception $dispatchException) {
                    Log::error('Failed to dispatch SMS jobs: ' . $dispatchException->getMessage());
                }
            });

            return response()->json(['message' => 'Transaction completed successfully.'], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Transaction failed. Please try again later.'], 500);
        }
    }
}
