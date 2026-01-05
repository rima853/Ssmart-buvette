<?php
// This file should be included after session_start() in pages that need navigation
if (!isset($_SESSION['user_id'])) {
    header('Location: log_in.php');
    exit;
}

$role = $_SESSION['role_name'] ?? 'user'; // Default to user if not set
?>

<nav>
  <h1>BUVETTE EMSI</h1>
  <div class="logo">
    <ul>
      <li><a href="index.php">Accueil</a></li>
      <?php if ($role === 'user'): ?>
        <li><a href="menu.php">Menu</a></li>
      <?php endif; ?>
      <li><a href="reservation.php">Réservation</a></li>
      <li><a href="contact.php">Contact</a></li>
      <li><a href="logout.php">Déconnexion</a></li>
    </ul>
  </div>
</nav>