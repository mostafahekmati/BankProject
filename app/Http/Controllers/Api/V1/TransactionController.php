<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransferRequest;
use App\Repositories\TransactionRepository;


class TransactionController extends Controller
{
    private TransactionRepository $transactionRepository;

    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function transfer(TransferRequest $request): \Illuminate\Http\JsonResponse
    {
       return $this->transactionRepository->transfer($request);
    }

}
