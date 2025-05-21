<?php

namespace App\Http\Controllers;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    

public function index()
{
    $transactions = Transaction::where('user_id', auth()->id())->orderBy('date', 'desc')->get();
    return view('transactions.index', compact('transactions'));
}

}
