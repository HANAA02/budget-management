<footer class="footer mt-auto py-3 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h5>{{ config('app.name') }}</h5>
                <p class="text-muted">
                    Application de gestion de budget personnelle pour maîtriser vos finances au quotidien.
                </p>
            </div>
            <div class="col-md-4">
                <h5>Liens utiles</h5>
                <ul class="list-unstyled">
                    <li><a href="{{ route('user.dashboard') }}">Tableau de bord</a></li>
                    <li><a href="{{ route('user.budgets.index') }}">Budgets</a></li>
                    <li><a href="{{ route('user.expenses.index') }}">Dépenses</a></li>
                    <li><a href="{{ route('user.goals.index') }}">Objectifs</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5>Support</h5>
                <ul class="list-unstyled">
                    <li><a href="{{ route('contact') }}">Contact</a></li>
                    <li><a href="{{ route('faq') }}">FAQ</a></li>
                    <li><a href="{{ route('privacy') }}">Politique de confidentialité</a></li>
                    <li><a href="{{ route('terms') }}">Conditions d'utilisation</a></li>
                </ul>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-12 text-center">
                <p class="mb-0 text-muted">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.
                </p>
            </div>
        </div>
    </div>
</footer>