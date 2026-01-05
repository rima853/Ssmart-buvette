<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once'connect.php';
session_start();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // Validation
    if ($first_name === '' || $last_name === '' || $email === '' || $password === '') {
        $error = 'Les champs obligatoires doivent être remplis.';
    } elseif ($password !== $password_confirm) {
        $error = 'Les mots de passe ne correspondent pas.';
    } elseif (strlen($password) < 8) {
        $error = 'Le mot de passe doit contenir au moins 8 caractères.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email invalide.';
    } else {
        // Vérifier si l'email existe déjà
        $check = $pdo->prepare('SELECT id FROM users WHERE email = :email');
        $check->execute([':email' => $email]);
        if ($check->rowCount() > 0) {
            $error = 'Cet email est déjà utilisé.';
        } else {
            // Insérer nouvel utilisateur avec hachage de mot de passe sécurisé
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            try {
                // Insérer dans users (tous les champs)
                $stmt = $pdo->prepare('INSERT INTO users (email, password_hash, first_name, last_name, phone, is_active) VALUES (:email, :password_hash, :first_name, :last_name, :phone, 1)');
                $stmt->execute([
                    ':email' => $email,
                    ':password_hash' => $password_hash,
                    ':first_name' => $first_name,
                    ':last_name' => $last_name,
                    ':phone' => $phone
                ]);

                $success = 'Inscription réussie ! Redirection en cours...';
                echo '<meta http-equiv="refresh" content="2; url=log_in.php">';
            } catch (PDOException $e) {
                $error = 'Erreur BD : ' . $e->getMessage();
                error_log('Erreur inscription : ' . $e->getMessage());
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Inscription - Restaurant</title>
  <link rel="stylesheet" href="register.css" />
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

  <main class="register-container">
    <div class="register-header">
      <h2>Créer un compte</h2>
      <p>Rejoignez-nous pour réserver votre table</p>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form action="register.php" method="post" class="register-form" novalidate>
        <div class="form-group">
          <label for="first_name">Prénom</label>
          <input id="first_name" name="first_name" type="text" value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>" required />
        </div>

        <div class="form-group">
          <label for="last_name">Nom</label>
          <input id="last_name" name="last_name" type="text" value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>" required />
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <input id="email" name="email" type="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required />
        </div>

        <div class="form-group">
          <label for="phone">Téléphone (optionnel)</label>
          <input id="phone" name="phone" type="tel" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" />
        </div>

        <div class="form-group full-width">
          <label for="password">Mot de passe</label>
          <input id="password" name="password" type="password" minlength="8" required />
        </div>

        <div class="form-group full-width">
          <label for="password_confirm">Confirmer le mot de passe</label>
          <input id="password_confirm" name="password_confirm" type="password" minlength="8" required />
        </div>

        <button type="submit" class="register-submit-btn">S'inscrire</button>
      </form>

      <p>Vous avez déjà un compte ? <a href="log_in.php">Se connecter</a></p>
  </main>

  <footer>
    <p>&copy; 2025 BUVETTE EMSI. Tous droits réservés.</p>
  </footer>
</body>
</html>
