<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransferRequest;
use App\Repositories\TransactionRepository;
use App\Repositories\UserRepository;
use App\Services\User\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{

    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function topUsers(): \Illuminate\Http\JsonResponse
    {
        $users = $this->userService->topUsers();
        return response()->json([
            'message' => 'Top users and their transactions fetched successfully.',
            'data' => $users
        ]);
    }
}
