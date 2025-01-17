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
    protected SMSService $smsService;

    public function __construct(SMSService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function transfer(Request $request): array
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

    private function validateTransfer($sourceCardNumber, $destinationCardNumber, $amount): ?array
    {
        if ($sourceCardNumber === $destinationCardNumber) {
            return ['status' => 'error', 'message' => 'The origin and destination cards cannot be the same.', 'status_code' => 400];
        }
        if (!$amount || $amount <= 0) {
            return ['status' => 'error', 'message' => 'Invalid transfer amount.', 'status_code' => 400];
        }
        return null;
    }

    private function validateCardsAndBalance($sourceCard, $destinationCard, $amount): ?array
    {
        if (!$sourceCard || !$destinationCard) {
            return ['status' => 'error', 'message' => 'One or both card numbers are not recognized.', 'status_code' => 400];
        }
        if ($sourceCard->balance < $amount + TransactionConst::TRANSACTION_FEE) {
            return ['status' => 'error', 'message' => 'Insufficient funds.', 'status_code' => 400];
        }
        return null;
    }

    private function executeTransfer($sourceCard, $destinationCard, $amount): array
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

            return ['status' => 'success', 'message' => 'Transaction completed successfully.', 'status_code' => 200];
        } catch (\Exception $e) {
            Log::error('Transaction failed: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'Transaction failed. Please try again later.', 'status_code' => 500];
        }
    }
}
