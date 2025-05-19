<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Gestion de budget</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center min-vh-100">

    <div class="container text-center px-4">
        <h1 class="display-4 fw-bold text-primary mb-4">
            Gérez vos finances personnelles efficacement
        </h1>

        <p class="lead text-secondary mb-5">
            Notre application de gestion de budget vous aide à suivre vos dépenses, planifier votre épargne et atteindre vos objectifs financiers.
        </p>

        <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
            <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                Commencer gratuitement
            </a>
            <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-lg">
                Se connecter
            </a>
        </div>
    </div>

</body>
</html>
