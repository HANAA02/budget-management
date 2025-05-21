@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Liste des Transactions</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Date</th>
                <th>Titre</th>
                <th>Type</th>
                <th>Montant</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->date }}</td>
                    <td>{{ $transaction->titre }}</td>
                    <td>
                        <span class="badge {{ $transaction->type == 'revenu' ? 'bg-success' : 'bg-danger' }}">
                            {{ ucfirst($transaction->type) }}
                        </span>
                    </td>
                    <td>{{ number_format($transaction->montant, 2) }} MAD</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
