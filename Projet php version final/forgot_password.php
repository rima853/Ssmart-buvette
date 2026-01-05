<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Mot de passe oublié</title>
<link rel="stylesheet" href="forgot_password.css" />
</head>
<body>
<header>
    <nav>
        <h1>BUVETTE EMSI</h1>
        <div class="logo">
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="menu.php">Menu</a></li>
                <li><a href="reservation.php">Réservation</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="log_in.php">Connexion</a></li>
                <li><a href="register.php">Inscription</a></li>
            </ul>
        </div>
    </nav>
</header>

<main class="forgot-container">
    <section class="forgot-box">
        <h2>Mot de passe oublié</h2>
        <p>Entrez votre adresse email pour recevoir un lien de réinitialisation</p>

        <form action="send-password-reset.php" method="post">
            <div class="form-group">
                <label for="email">Entrez votre email</label>
                <input type="email" id="email" name="email" required placeholder="votre@email.com">
            </div>
            <button type="submit">Envoyer le mail</button>
        </form>

        <p><a href="log_in.php">Retour à la connexion</a></p>
    </section>
</main>

<footer>
    <p>&copy; 2025 BUVETTE EMSI. Tous droits réservés.</p>
</footer>
</body>
</html>