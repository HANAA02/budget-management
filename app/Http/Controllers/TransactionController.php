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

use App\Models\Transaction;
use Illuminate\Http\Request;

public function create()
{
    return view('transactions.create');
}

public function store(Request $request)
{
    $request->validate([
        'titre' => 'required|string|max:255',
        'type' => 'required|in:revenu,depense',
        'montant' => 'required|numeric|min:0',
        'date' => 'required|date',
    ]);

    Transaction::create([
        'user_id' => auth()->id(),
        'titre' => $request->titre,
        'type' => $request->type,
        'montant' => $request->montant,
        'date' => $request->date,
    ]);

    return redirect()->route('transactions.index')->with('success', 'Transaction ajoutée avec succès.');
}


}
