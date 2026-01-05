
<?php
session_start();

// Si pas connecté, redirige vers login
if (!isset($_SESSION['user_id'])) {
    header('Location: log_in.php');
    exit;
}

include_once 'connect.php';

$role = $_SESSION['role_name'] ?? 'user';
?>
<?php
$message = '';
$messageType = '';

// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $role === 'user') {
    $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $sujet = isset($_POST['sujet']) ? trim($_POST['sujet']) : '';
    $message_text = isset($_POST['message']) ? trim($_POST['message']) : '';
    
    // Validation
    if (empty($nom) || empty($email) || empty($sujet) || empty($message_text)) {
        $message = 'Veuillez remplir tous les champs.';
        $messageType = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Format d\'email invalide.';
        $messageType = 'error';
    } else {
        // Save to database
        $stmt = $pdo->prepare('INSERT INTO messages (user_id, name, email, subject, message) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$_SESSION['user_id'], $nom, $email, $sujet, $message_text]);
        
        $message = "Merci $nom ! Votre message a été envoyé.";
        $messageType = 'success';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Restaurant</title>
    <link rel="stylesheet" href="contact.css?v=3">
</head>
<body>
    <header>
        <?php include 'nav.php'; ?>
    </header>
    
    <main class="contact-container">
        <?php if ($role === 'admin'): ?>
            <div class="contact-header">
                <h2>Messages Reçus</h2>
            </div>
            <?php
            $stmt = $pdo->query('SELECT m.*, u.first_name, u.last_name FROM messages m LEFT JOIN users u ON m.user_id = u.id ORDER BY m.created_at DESC');
            $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($messages): ?>
                <table class="messages-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Sujet</th>
                            <th>Message</th>
                            <th>Créé le</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $msg): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($msg['name']); ?></td>
                                <td><?php echo htmlspecialchars($msg['email']); ?></td>
                                <td><?php echo htmlspecialchars($msg['subject']); ?></td>
                                <td><?php echo htmlspecialchars($msg['message']); ?></td>
                                <td><?php echo $msg['created_at']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Aucun message trouvé.</p>
            <?php endif; ?>
        <?php else: ?>
            <div class="contact-header">
                <div class="header-decoration"></div>
                <h2>Contactez-nous</h2>
                <p>Nous sommes là pour répondre à toutes vos questions</p>
                <div class="header-line"></div>
            </div>
            
            <div class="contact-content">
                <div class="contact-info">
                    <div class="info-header">
                        <h3>Informations de contact</h3>
                        <div class="info-underline"></div>
                    </div>
                    <div class="info-items-wrapper">
                        <div class="info-item">
                            <div class="info-icon">📍</div>
                            <div class="info-content">
                                <strong>Adresse</strong>
                                <p>123 Rue de la Gastronomie<br>Ville, Pays</p>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">📞</div>
                            <div class="info-content">
                                <strong>Téléphone</strong>
                                <p><a href="tel:+33123456789">+33 1 23 45 67 89</a></p>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">✉️</div>
                            <div class="info-content">
                                <strong>Email</strong>
                                <p><a href="mailto:contact@restaurant.fr">contact@restaurant.fr</a></p>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">🕐</div>
                            <div class="info-content">
                                <strong>Heures d'ouverture</strong>
                                <p>Lundi - Dimanche<br>12h00 - 22h00</p>
                            </div>
                        </div>
                    </div>
                    <div class="info-footer">
                        <p>Nous vous accueillons avec plaisir !</p>
                    </div>
                </div>
                
                <div class="contact-form-wrapper">
                    <?php if ($message): ?>
                        <div class="message <?php echo $messageType; ?>">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="contact.php" method="post" class="contact-form">
                        <div class="form-group">
                            <label for="nom">Nom complet *</label>
                            <input type="text" id="nom" name="nom" required value="<?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($_SESSION['email']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="sujet">Sujet *</label>
                            <input type="text" id="sujet" name="sujet" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Message *</label>
                            <textarea id="message" name="message" rows="6" required></textarea>
                        </div>
                        
                        <button type="submit" class="submit-btn">Envoyer</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </main>
    
    <footer>
        <p>&copy; 2025 BUVETTE EMSI. Tous droits réservés.</p>
    </footer>
</body>
</html>

