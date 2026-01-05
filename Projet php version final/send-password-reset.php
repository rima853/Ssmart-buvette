<?php

require __DIR__ . "/connect.php";

$email = $_POST['email'] ?? null;
$message = '';
$messageType = 'success';

if (!$email) {
    $message = "Email manquant";
    $messageType = 'error';
} else {
    $token = bin2hex(random_bytes(16));
    $token_hash = hash('sha256', $token);
    $expiry = date("Y-m-d H:i:s", time() + 1800);

    $sql = "UPDATE users
            SET reset_token_hash = :hash,
                reset_token_expires_at = :expiry
            WHERE email = :email";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'hash'   => $token_hash,
        'expiry'=> $expiry,
        'email' => $email
    ]);

    if ($stmt->rowCount() === 0) {
        $message = "Aucun utilisateur trouvé avec cet email";
        $messageType = 'error';
    } else {
        $mail = require __DIR__ . "/mailer.php";

        $mail->setFrom("someoneoutthere.01@gmail.com");
        $mail->addAddress($email);
        $mail->Subject = "Réinitialisation du mot de passe";
        $mail->Body = "
        Cliquez sur ce lien pour réinitialiser votre mot de passe :
        <br>
        <a href='http://localhost/php_wamp/finally_im_gonna_kms%20-%20(i%20lied)/reset_password.php?token=" . urlencode($token) . "'>
        Réinitialiser
        </a>
        ";

        try {
            $mail->send();
            $message = "Message envoyé ! Vérifiez votre boîte mail.";
        } catch (Exception $e) {
            $message = "Erreur lors de l'envoi de l'email : " . $mail->ErrorInfo;
            $messageType = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Réinitialisation du mot de passe</title>
    <link rel="stylesheet" href="reset_password.css" />
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

            <div class="back-link" style="margin-top: 30px;">
                <a href="log_in.php" class="back-btn">Retour à la connexion</a>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 BUVETTE EMSI. Tous droits réservés.</p>
    </footer>
</body>
</html>