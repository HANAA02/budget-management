@extends('layouts.app')

@section('title', 'Connexion')

@push('styles')
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-tr from-indigo-100 via-white to-indigo-200">
    <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-xl sm:rounded-2xl border border-gray-200 transition-all duration-300">
        <div class="mb-6 text-center">
            <h2 class="text-3xl font-extrabold text-indigo-700">
                <i class="fas fa-coins text-yellow-500 mr-2"></i>
                Budget Manager
            </h2>
            <p class="mt-2 text-sm text-gray-500">Connectez-vous à votre compte</p>
        </div>

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

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

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Adresse e-mail</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                <input id="password" type="password" name="password" required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
            </div>

            <div class="flex items-center">
    <input id="remember_me" type="checkbox" name="remember" value="1"
        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
    <label for="remember_me" class="ml-2 block text-sm text-gray-600">
        Se souvenir de moi
    </label>
</div>

                @if (Route::has('password.request'))
                    <a class="text-sm text-indigo-600 hover:underline" href="{{ route('password.request') }}">
                        Mot de passe oublié ?
                    </a>
                @endif
            </div>

            <div>
                <button type="submit"
                    class="w-full flex justify-center items-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                    <i class="fas fa-sign-in-alt mr-2"></i> Se connecter
                </button>
            </div>
        </form>

        @if (Route::has('register'))
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Vous n'avez pas de compte ?
                    <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500 underline">
                        Inscrivez-vous
                    </a>
                </p>
            </div>
        @endif

        <div class="mt-6 border-t border-gray-200 pt-6">
            <div class="flex justify-center space-x-6">
                <a href="#" class="text-gray-400 hover:text-indigo-600 transition-colors duration-150">
                    <span class="sr-only">Facebook</span>
                    <i class="fab fa-facebook text-xl"></i>
                </a>
                <a href="#" class="text-gray-400 hover:text-indigo-600 transition-colors duration-150">
                    <span class="sr-only">Twitter</span>
                    <i class="fab fa-twitter text-xl"></i>
                </a>
                <a href="#" class="text-gray-400 hover:text-indigo-600 transition-colors duration-150">
                    <span class="sr-only">GitHub</span>
                    <i class="fab fa-github text-xl"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
