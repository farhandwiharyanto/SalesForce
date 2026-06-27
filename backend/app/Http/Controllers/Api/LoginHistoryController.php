<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\LoginHistory;

class LoginHistoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $histories = LoginHistory::with('user')->orderBy('id', 'desc')->get();
        return response()->json($histories);
    }
}
