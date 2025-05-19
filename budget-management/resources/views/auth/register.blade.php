@extends('layouts.app')

@section('title', 'Inscription')

@section('content')
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-coins text-primary-600 mr-2"></i>
                Budget Manager
            </h2>
            <p class="mt-2 text-sm text-gray-600">Créez votre compte pour commencer</p>
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

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="nom" class="block text-sm font-medium text-gray-700">Nom</label>
                    <input id="nom" type="text" name="nom" value="{{ old('nom') }}" required autofocus
                        class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>

                <div>
                    <label for="prenom" class="block text-sm font-medium text-gray-700">Prénom</label>
                    <input id="prenom" type="text" name="prenom" value="{{ old('prenom') }}" required
                        class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>
            </div>

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Adresse e-mail</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                    class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                <input id="password" type="password" name="password" required
                    class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                <p class="mt-1 text-xs text-gray-500">Le mot de passe doit contenir au moins 8 caractères</p>
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmer le mot de passe</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                    class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
            </div>

            <div class="mb-4">
                <div class="flex items-center">
                    <input id="terms" type="checkbox" name="terms" required
                        class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    <label for="terms" class="ml-2 block text-sm text-gray-700">
                        J'accepte les <a href="{{ route('terms') }}" class="text-primary-600 hover:text-primary-900">conditions d'utilisation</a> et la <a href="{{ route('privacy') }}" class="text-primary-600 hover:text-primary-900">politique de confidentialité</a>
                    </label>
                </div>
            </div>

            <div class="mb-6">
                <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <i class="fas fa-user-plus mr-2"></i> S'inscrire
                </button>
            </div>
        </form>

        <div class="text-center">
            <p class="text-sm text-gray-600">
                Vous avez déjà un compte?
                <a href="{{ route('login') }}" class="font-medium text-primary-600 hover:text-primary-500">
                    Connectez-vous
                </a>
            </p>
        </div>
        
        <div class="mt-6 border-t border-gray-200 pt-6">
            <div class="flex flex-col items-center">
                <p class="text-sm text-gray-600 mb-4">Ou inscrivez-vous avec</p>
                <div class="flex justify-center space-x-4">
                    <a href="#" class="flex items-center justify-center h-10 w-10 rounded-full bg-blue-500 hover:bg-blue-600 text-white transition">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="flex items-center justify-center h-10 w-10 rounded-full bg-blue-400 hover:bg-blue-500 text-white transition">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="flex items-center justify-center h-10 w-10 rounded-full bg-red-500 hover:bg-red-600 text-white transition">
                        <i class="fab fa-google"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection