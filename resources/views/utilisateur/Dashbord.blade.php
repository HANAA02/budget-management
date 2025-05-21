<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Utilisateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container mt-5">
        <div class="card shadow-lg">
            <div class="card-body text-center">
                <h2 class="card-title mb-4">Bienvenue, {{ Auth::user()->name }} 👋</h2>
                <p class="card-text">Vous êtes connecté en tant qu’<strong>utilisateur</strong>.</p>

                <div class="mt-4">
                    <a href="{{ route('transactions.create') }}" class="btn btn-success me-2">➕ Ajouter une transaction</a>
                    <a href="{{ route('transactions.index') }}" class="btn btn-primary">📋 Voir mes transactions</a>
                </div>

                <form action="{{ route('logout') }}" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" class="btn btn-danger">🚪 Se déconnecter</button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
