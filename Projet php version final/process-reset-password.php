<?php
require 'connect.php';

$message = '';
$messageType = 'success';
$showTryAgain = false;
$token = $_POST['token'] ?? '';

$token = $_POST['token'] ?? '';
$password = $_POST['password'] ?? '';
$confirm = $_POST['password_confirmation'] ?? '';

if (!$token) {
    $message = "Token manquant.";
    $messageType = 'error';
} elseif (strlen($password) < 8) {
    $message = "Le mot de passe doit contenir au moins 8 caractères.";
    $messageType = 'error';
    $showTryAgain = true;
} elseif (!preg_match("/[a-z]/i", $password)) {
    $message = "Le mot de passe doit contenir au moins une lettre.";
    $messageType = 'error';
    $showTryAgain = true;
} elseif (!preg_match("/[0-9]/", $password)) {
    $message = "Le mot de passe doit contenir au moins un chiffre.";
    $messageType = 'error';
    $showTryAgain = true;
} elseif ($password !== $confirm) {
    $message = "Les mots de passe ne correspondent pas.";
    $messageType = 'error';
    $showTryAgain = true;
} else {
    // Hash the token to match what's stored in the database
    $token_hash = hash('sha256', $token);

    // Find the user with this token hash and valid expiration
    $sql = "SELECT * FROM users
            WHERE reset_token_hash = :hash
              AND reset_token_expires_at > NOW()";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['hash' => $token_hash]);

    $user = $stmt->fetch();

    if (!$user) {
        $message = "Token invalide ou expiré.";
        $messageType = 'error';
    } else {
        // Update the password
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            UPDATE users
            SET password_hash = ?,
                reset_token_hash = NULL,
                reset_token_expires_at = NULL
            WHERE id = ?
        ");
        $stmt->execute([$hash, $user['id']]);

        $message = "Mot de passe mis à jour avec succès ! Vous pouvez maintenant vous connecter.";
        $messageType = 'success';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du mot de passe - Restaurant</title>
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
            <h2>Réinitialisation du mot de passe</h2>

            <?php if ($message): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="action-buttons" style="margin-top: 30px;">
                <?php if ($showTryAgain && $token): ?>
                    <div class="try-again-section" style="margin-bottom: 15px;">
                        <a href="reset_password.php?token=<?php echo urlencode($token); ?>" class="reset-btn">Réessayer</a>
                    </div>
                <?php endif; ?>

                <div class="back-link">
                    <p><a href="log_in.php" class="back-btn">Aller à la connexion</a></p>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 BUVETTE EMSI. Tous droits réservés.</p>
    </footer>
</body>
</html>