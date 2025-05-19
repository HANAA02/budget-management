@extends('layouts.app')

@section('title', 'Confirmer le mot de passe')

@section('content')
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-coins text-primary-600 mr-2"></i>
                Budget Manager
            </h2>
            <p class="mt-2 text-sm text-gray-600">Confirmation du mot de passe</p>
        </div>

        <div class="mb-4 text-sm text-gray-600">
            Cette zone est sécurisée. Veuillez confirmer votre mot de passe avant de continuer.
        </div>

        @if ($errors->any())
            <div class="mb-4">
                <div class="font-medium text-red-600">Oups! Quelque chose s'est mal passé.</div>

                <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
            </div>

            <div class="flex items-center justify-end mt-4">
                <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <i class="fas fa-check-circle mr-2"></i> Confirmer
                </button>
            </div>
        </form>

        @if (Route::has('password.request'))
            <div class="mt-4 text-center">
                <a class="text-sm text-primary-600 hover:text-primary-900" href="{{ route('password.request') }}">
                    Mot de passe oublié ?
                </a>
            </div>
        @endif
    </div>
</div>
@endsection