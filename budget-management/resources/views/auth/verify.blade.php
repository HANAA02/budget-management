@extends('layouts.app')

@section('title', 'Vérification d\'email')

@section('content')
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-coins text-primary-600 mr-2"></i>
                Budget Manager
            </h2>
            <p class="mt-2 text-sm text-gray-600">Vérification de l'adresse e-mail</p>
        </div>

        @if (session('resent'))
            <div class="mb-4 font-medium text-sm text-green-600 bg-green-100 p-3 rounded">
                Un nouveau lien de vérification a été envoyé à votre adresse e-mail.
            </div>
        @endif

        <div class="mb-6 text-center">
            <div class="rounded-full h-16 w-16 bg-primary-100 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-envelope text-primary-600 text-2xl"></i>
            </div>

            <p class="text-sm text-gray-600 mb-4">
                Merci de vous être inscrit ! Avant de commencer, pourriez-vous vérifier votre adresse e-mail en cliquant sur le lien que nous venons de vous envoyer ? Si vous n'avez pas reçu l'e-mail, nous vous en enverrons volontiers un autre.
            </p>
        </div>

        <form method="POST" action="{{ route('verification.send') }}" class="mb-4">
            @csrf
            <div>
                <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <i class="fas fa-paper-plane mr-2"></i> Renvoyer l'e-mail de vérification
                </button>
            </div>
        </form>

        <div class="mt-6 flex items-center justify-between">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-gray-600 hover:text-gray-900 underline">
                    Déconnexion
                </button>
            </form>
            
            <a href="{{ route('home') }}" class="text-sm text-primary-600 hover:text-primary-900">
                Retour à l'accueil
            </a>
        </div>
    </div>
</div>
@endsection