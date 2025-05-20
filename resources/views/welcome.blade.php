<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Page d'accueil - BudgetZen</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #fff;
            color: #000;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        header {
            display: flex;
            justify-content: space-between;
            padding: 20px 40px;
            align-items: center;
            border-bottom: 1px solid #ddd;
        }
        .logo {
            font-size: 80px;
            font-style: italic;
            color: #800020; /* Bordeaux */
            font-weight: bold;
            display: flex;
            align-items: baseline;
        }
        .logo small {
            font-size: 30px;
            color: black;
            font-style: italic;
            margin-left: 5px;
        }
        nav {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 240px;
            min-width: 180px;
        }
        nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        nav ul li {
            margin-bottom: 15px;
        }
        nav ul li a {
            text-decoration: none;
            color: #000;
            font-weight: 600;
            font-size: 16px;
        }
        .menu-footer {
            background-color: #800020;
            color: white;
            padding: 15px 10px;
            border-radius: 8px;
            text-align: center;
        }
        .menu-footer button {
            background-color: white;
            border: none;
            color: #800020;
            padding: 10px 20px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
            border-radius: 4px;
        }
        .menu-footer .message-border {
            border: 2px solid white;
            margin-top: 10px;
            padding: 10px;
            font-size: 14px;
        }
        main {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
            text-align: center;
            max-width: 900px;
            margin: 0 auto;
            font-size: 18px;
            line-height: 1.5;
        }
        footer {
            background-color: #2c2c2c;
            color: white;
            padding: 40px 20px;
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            margin-top: auto;
        }
        footer .section {
            flex: 1 1 250px;
            margin: 10px;
        }
        footer .title {
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 10px;
        }
        footer a {
            display: block;
            color: white;
            text-decoration: none;
            margin-bottom: 8px;
        }
        footer p {
            margin-bottom: 8px;
        }
        footer .contact-icons span {
            margin-right: 10px;
            cursor: pointer;
            font-size: 20px;
        }
    </style>
</head>
<body>

    <header>
        <div class="logo">
            O <small>petit budget</small>
        </div>

        <nav>
            <ul>
                <li><a href="#accueil">Accueil</a></li>
                <li><a href="#apropos">À propos</a></li>
                <li><a href="#rap">Rap</a></li>
                <li><a href="#connexion">Connexion</a></li>
                <li><a href="#inscription">Inscription</a></li>
            </ul>

            <div class="menu-footer">
                <div>Prenez le contrôle de vos finances d'aujourd'hui</div>
                <button>Commencez maintenant</button>
                <div class="message-border">
                    Message important ici
                </div>
            </div>
        </nav>
    </header>

    <main>
        Cette plateforme vous aide à suivre vos revenus et dépenses, définir vos objectifs financiers, recevoir des alertes et analyser votre situation financière grâce à des graphiques clairs et des outils simples.
    </main>

    <footer>
        <div class="section">
            <div class="title">À propos de l'application</div>
            <p><strong>BudgetZen</strong> est une plateforme intuitive qui vous aide à gérer vos revenus, suivre vos dépenses et atteindre vos objectifs financiers avec sécurité.</p>
        </div>

        <div class="section">
            <div class="title">Liens rapides</div>
            <a href="#accueil">Accueil</a>
            <a href="#apropos">À propos</a>
            <a href="#connexion">Connexion</a>
            <a href="#inscription">Inscription</a>
        </div>

        <div class="section">
            <div class="title">Informations de contact</div>
            <p>📧 opibudget@gmail.com</p>
            <p>📞 +212-7724569</p>
            <div class="contact-icons">
                <span>📘</span>
                <span>🐦</span>
                <span>🔗</span>
            </div>
        </div>
    </footer>

</body>
</html>
