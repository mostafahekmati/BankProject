<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransferRequest;
use App\Services\Transaction\TransactionService;


class TransactionController extends Controller
{

    private TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function transfer(TransferRequest $request): \Illuminate\Http\JsonResponse
    {

        $result = $this->transactionService->transfer($request);

        return response()->json([
            'message' => $result['message'],
        ], $result['status_code']);
    }

}
