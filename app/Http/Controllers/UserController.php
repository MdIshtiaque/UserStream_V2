<?php

namespace App\Http\Controllers;

use App\Events\UpdateDataset;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $users = User::all();
            broadcast(new UpdateDataset($users));
        }catch (Exception $exception) {
            return sendErrorResponse('Something went wrong: '.$exception->getMessage());
        }
        return sendSuccessResponse('List of users', 200, $users);
    }
}
