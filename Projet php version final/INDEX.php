<?php

session_start();

// Si pas connecté, redirige vers login
if (!isset($_SESSION['user_id'])) {
    header('Location: log_in.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Accueil - Restaurant</title>
  <link rel="stylesheet" href="index.css?v=3" />
</head>
<body class="main">
  <header>
    <?php include 'nav.php'; ?>
  </header>

  <section class="Accueil">
    <div class="Acceuil2">
      <h1>BUVETTE EMSI </h1>
    </div>
    <h2>Bienvenue, <?php echo htmlspecialchars($_SESSION['first_name'] ?? 'Client'); ?> !</h2>
    <p>Découvrez une expérience culinaire unique.</p>
  </section>

  <footer>
    <section class="footer">
      <p>&copy; 2025 BUVETTE EMSI. Tous droits réservés.</p>
    </section>
  </footer>

  <script>
    // Affiche/cache le lien inscription au clic
    document.querySelector('.logo').addEventListener('click', function() {
      const link = document.getElementById('inscriptionLink');
      link.style.display = link.style.display === 'none' ? 'block' : 'none';
    });
  </script>
</body>
</html>
