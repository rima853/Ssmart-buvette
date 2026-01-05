<?php
session_start();
$error = '';
$success = '';

include_once'connect.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $success = 0;

    if ($email !== '' && $password !== '') {
        // Récupérer l'utilisateur
        $stmt = $pdo->prepare('
            SELECT u.id, u.password_hash, u.is_active, u.first_name, u.last_name, u.phone, r.name as role_name
            FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE u.email = :email 
            LIMIT 1
        ');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Vérification mot de passe avec hachage sécurisé
        if ($user && (int)$user['is_active'] === 1 && password_verify($password, $user['password_hash'])) {
            $success = 1;
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $email;
            $_SESSION['first_name'] = $user['first_name'] ?? '';
            $_SESSION['last_name'] = $user['last_name'] ?? '';
            $_SESSION['phone'] = $user['phone'] ?? '';
            $_SESSION['role_name'] = $user['role_name'] ?? 'user';
            header('Location: index.php');
            exit;
        }
    }

    // Enregistrer tentative
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    $log = $pdo->prepare('INSERT INTO login_attempts (user_id, email_attempted, ip, successful) VALUES (:user_id, :email, :ip, :successful)');
    $log->execute([
        ':user_id' => $user['id'] ?? null,
        ':email' => $email,
        ':ip' => $ip,
        ':successful' => $success
    ]);

    $error = 'Identifiants invalides';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Restaurant</title>
    <link rel="stylesheet" href="log_in.css">
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
    
    <main class="login-container">
        <div class="login-box">
            <h2>Connexion</h2>
            <p class="subtitle">Connectez-vous à votre compte</p>
            
            <?php if ($error): ?>
                <div class="message error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="message success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form action="log_in.php" method="post" class="login-form">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="votre@email.com" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                </div>
                
                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember">
                        <span>Se souvenir de moi</span>
                    </label>
                    <a href="forgot_password.php" class="forgot-password">Mot de passe oublié ?</a>
                </div>
                
                <button type="submit" class="login-btn">Se connecter</button>
                
                <div class="signup-link">
                    <p>Pas encore de compte ? <a href="register.php">Créer un compte</a></p>

                </div>
            </form>
        </div>
    </main>
    
    <footer>
        <p>&copy; 2025 BUVETTE EMSI. Tous droits réservés.</p>
    </footer>
</body>
</html>

