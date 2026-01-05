<?php
require __DIR__ . "/connect.php";

$error = '';
$token = $_GET['token'] ?? null;

if (!$token) {
    $error = "Token manquant";
} else {
    $token_hash = hash('sha256', $token);

    $sql = "SELECT * FROM users
            WHERE reset_token_hash = :hash
              AND reset_token_expires_at > NOW()";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['hash' => $token_hash]);

    $user = $stmt->fetch();

    if (!$user) {
        $error = "Token invalide ou expiré";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialiser le mot de passe - Restaurant</title>
    <link rel="stylesheet" href="reset_password.css">
</head>
<body>
    <header>
        <nav>
            <h1>BUVETTE EMSI</h1>
            <div class="logo">
                <ul>
                    <li><a href="log_in.php">Connexion</a></li>
                    <li><a href="register.php">Inscription</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main class="forgot-container">
        <div class="forgot-box">
            <h2>Réinitialiser le mot de passe</h2>
            <p class="subtitle">Entrez votre nouveau mot de passe</p>

            <?php if ($error): ?>
                <div class="message error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if (!$error): ?>
                <form action="process-reset-password.php" method="post" class="reset-form">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                    <div class="form-group">
                        <label for="password">Nouveau mot de passe</label>
                        <input type="password" id="password" name="password" placeholder="••••••••" required>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirmer le mot de passe</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="••••••••" required>
                    </div>

                    <button type="submit" class="reset-btn">Réinitialiser le mot de passe</button>

                    <div class="back-link">
                        <p><a href="log_in.php">Retour à la connexion</a></p>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 BUVETTE EMSI. Tous droits réservés.</p>
    </footer>
</body>
</html>