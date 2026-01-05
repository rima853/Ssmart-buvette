
<?php

session_start();

// Si pas connecté, redirige vers login
if (!isset($_SESSION['user_id'])) {
    header('Location: log_in.php');
    exit;
}

include_once 'connect.php';

$role = $_SESSION['role_name'] ?? 'user';

// Menu arrays based on menu.php
$entrees = [
    ['name' => 'Salade César', 'description' => '', 'price' => 12],
    ['name' => 'Soupe à l\'oignon', 'description' => '', 'price' => 9],
    ['name' => 'Plateau de charcuterie', 'description' => '', 'price' => 16],
    ['name' => 'Foie gras maison', 'description' => '', 'price' => 18],
];

$plats = [
    ['name' => 'Steak frites', 'description' => '', 'price' => 24],
    ['name' => 'Saumon grillé', 'description' => '', 'price' => 22],
    ['name' => 'Poulet rôti', 'description' => '', 'price' => 20],
    ['name' => 'Risotto aux champignons', 'description' => '', 'price' => 19],
    ['name' => 'Bouillabaisse', 'description' => '', 'price' => 28],
];

$desserts = [
    ['name' => 'Tiramisu', 'description' => '', 'price' => 8],
    ['name' => 'Crème brûlée', 'description' => '', 'price' => 7],
    ['name' => 'Tarte aux pommes', 'description' => '', 'price' => 8],
    ['name' => 'Assortiment de sorbets', 'description' => '', 'price' => 7],
];

$boissons = [
    ['name' => 'Vin rouge (verre)', 'description' => '', 'price' => 6],
    ['name' => 'Vin blanc (verre)', 'description' => '', 'price' => 6],
    ['name' => 'Eau minérale', 'description' => '', 'price' => 3],
    ['name' => 'Café', 'description' => '', 'price' => 2.50],
];

// Create a flat array of all dishes with prices for easy lookup
$allDishesPrices = [];
foreach ($entrees as $dish) $allDishesPrices[$dish['name']] = $dish['price'];
foreach ($plats as $dish) $allDishesPrices[$dish['name']] = $dish['price'];
foreach ($desserts as $dish) $allDishesPrices[$dish['name']] = $dish['price'];
foreach ($boissons as $dish) $allDishesPrices[$dish['name']] = $dish['price'];
?>
<?php
$message = '';
$messageType = '';

// Check for success parameter from redirect
if (isset($_GET['success']) && $_GET['success'] == '1') {
    $message = "Réservation reçue";
    $messageType = 'success';
}

// Handle reservation form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $role === 'user') {
    $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $telephone = isset($_POST['telephone']) ? trim($_POST['telephone']) : '';
    $nb_personnes = isset($_POST['nb_personnes']) ? $_POST['nb_personnes'] : '';
    $date = isset($_POST['date']) ? $_POST['date'] : '';
    $heure = isset($_POST['heure']) ? $_POST['heure'] : '';
    $preferences = isset($_POST['preferences']) ? trim($_POST['preferences']) : '';

    // Collect selected dishes and quantities
    $selectedDishes = [];
    $categories = ['entrees', 'plats', 'desserts', 'boissons'];
    foreach ($categories as $category) {
        if (isset($_POST['dishes'][$category]) && is_array($_POST['dishes'][$category])) {
            foreach ($_POST['dishes'][$category] as $dishName) {
                $quantity = isset($_POST['quantities'][$category][$dishName]) ? (int)$_POST['quantities'][$category][$dishName] : 1;
                $selectedDishes[] = [
                    'category' => $category,
                    'name' => $dishName,
                    'quantity' => $quantity
                ];
            }
        }
    }

    // Combine preferences with selected dishes as JSON
    $fullPreferences = $preferences;
    if (!empty($selectedDishes)) {
        $dishesJson = json_encode($selectedDishes);
        $fullPreferences .= ($fullPreferences ? "\n\n" : "") . "Plats sélectionnés: " . $dishesJson;
    }

    $current_date = date('Y-m-d');
    $current_time = date('H:i');
    // Validation
    if (empty($nom) || empty($email) || empty($telephone) || empty($nb_personnes) || empty($date) || empty($heure)) {
        $message = 'Veuillez remplir tous les champs obligatoires.';
        $messageType = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || $date < $current_date || ($date === $current_date && $heure <= $current_time)) {
        $message = 'Format d\'email, date ou heure invalide.';
        $messageType = 'error';
    } else {
        // Save to database
        $stmt = $pdo->prepare('INSERT INTO reservations (user_id, name, email, phone, nb_personnes, date, heure, preferences) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$_SESSION['user_id'], $nom, $email, $telephone, $nb_personnes, $date, $heure, $fullPreferences]);

        // Redirect to prevent form resubmission on refresh
        header('Location: reservation.php?success=1');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation - Restaurant</title>
    <link rel="stylesheet" href="reservation.css?v=3">

</head>
<body>
    <header>
        <?php include 'nav.php'; ?>
    </header>
    
    <main class="reservation-container">
        <?php if ($role === 'admin'): ?>
            <div class="reservation-header">
                <h2>Liste des Réservations</h2>
            </div>
            <?php
            $stmt = $pdo->query('SELECT r.*, u.first_name, u.last_name FROM reservations r JOIN users u ON r.user_id = u.id ORDER BY r.created_at DESC');
            $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($reservations): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Personnes</th>
                            <th>Date</th>
                            <th>Heure</th>
                            <th>Plats</th>
                            <th>Total</th>
                            <th>Préférences</th>
                            <th>Créé le</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservations as $res):
                            // Parse dishes from preferences for admin view
                            $preferences = $res['preferences'] ?? '';
                            $dishesDisplay = '';
                            $otherPreferences = '';

                            if (strpos($preferences, 'Plats sélectionnés:') !== false) {
                                $parts = explode('Plats sélectionnés:', $preferences, 2);
                                $otherPreferences = trim($parts[0]);
                                $dishesJson = trim($parts[1]);

                                if (!empty($dishesJson)) {
                                    $selectedDishes = json_decode($dishesJson, true);
                                    if (is_array($selectedDishes)) {
                                        $dishesList = [];
                                        $total = 0;
                                        foreach ($selectedDishes as $dish) {
                                            $dishesList[] = $dish['name'] . ' (x' . $dish['quantity'] . ')';
                                            $price = $allDishesPrices[$dish['name']] ?? 0;
                                            $total += $price * $dish['quantity'];
                                        }
                                        $dishesDisplay = implode(', ', $dishesList);
                                    }
                                }
                            } else {
                                $otherPreferences = $preferences;
                                $total = 0;
                            }
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($res['name']); ?></td>
                                <td><?php echo htmlspecialchars($res['email']); ?></td>
                                <td><?php echo htmlspecialchars($res['phone']); ?></td>
                                <td><?php echo $res['nb_personnes']; ?></td>
                                <td><?php echo $res['date']; ?></td>
                                <td><?php echo $res['heure']; ?></td>
                                <td><?php echo htmlspecialchars($dishesDisplay); ?></td>
                                <td><?php echo number_format($total, 2); ?>€</td>
                                <td><?php echo htmlspecialchars($otherPreferences); ?></td>
                                <td><?php echo $res['created_at']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Aucune réservation trouvée.</p>
            <?php endif; ?>
        <?php else: ?>
            <div class="reservation-header">
                <h2>Réservez votre table</h2>
                <p>Remplissez le formulaire ci-dessous pour réserver votre table</p>
            </div>
            
            <?php if ($message): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <form action="reservation.php" method="post" class="reservation-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nom">Nom complet *</label>
                        <input type="text" id="nom" name="nom" placeholder="Votre nom" required value="<?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" placeholder="votre@email.com" required value="<?php echo htmlspecialchars($_SESSION['email']); ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="telephone">Téléphone *</label>
                        <input type="tel" id="telephone" name="telephone" placeholder="06 12 34 56 78" required value="<?php echo htmlspecialchars($_SESSION['phone'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="nb_personnes">Nombre de personnes *</label>
                        <select id="nb_personnes" name="nb_personnes" required>
                            <option value="">Sélectionnez</option>
                            <option value="1">1 personne</option>
                            <option value="2">2 personnes</option>
                            <option value="3">3 personnes</option>
                            <option value="4">4 personnes</option>
                            <option value="5">5 personnes</option>
                            <option value="6">6 personnes</option>
                            <option value="7">7 personnes</option>
                            <option value="8">8 personnes</option>
                            <option value="9">9 personnes</option>
                            <option value="10">10+ personnes</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="date">Date *</label>
                        <input type="date" id="date" name="date" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="heure">Heure *</label>
                        <select id="heure" name="heure" required>
                            <option value="">Sélectionnez</option>
                            <option value="12:00">12:00</option>
                            <option value="12:30">12:30</option>
                            <option value="13:00">13:00</option>
                            <option value="13:30">13:30</option>
                            <option value="19:00">19:00</option>
                            <option value="19:30">19:30</option>
                            <option value="20:00">20:00</option>
                            <option value="20:30">20:30</option>
                            <option value="21:00">21:00</option>
                            <option value="21:30">21:30</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Choisissez vos plats (optionnel)</label>

                    <table class="menu-table">
                        <thead>
                            <tr>
                                <th>Sélection</th>
                                <th>Catégorie</th>
                                <th>Plat</th>
                                <th>Prix</th>
                                <th>Quantité</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $allDishes = [
                                'entrees' => ['name' => 'Entrées', 'dishes' => $entrees],
                                'plats' => ['name' => 'Plats Principaux', 'dishes' => $plats],
                                'desserts' => ['name' => 'Desserts', 'dishes' => $desserts],
                                'boissons' => ['name' => 'Boissons', 'dishes' => $boissons]
                            ];

                            foreach ($allDishes as $categoryKey => $categoryData): ?>
                                <?php foreach ($categoryData['dishes'] as $dish): ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="dishes[<?php echo $categoryKey; ?>][]" value="<?php echo htmlspecialchars($dish['name']); ?>">
                                        </td>
                                        <td><?php echo htmlspecialchars($categoryData['name']); ?></td>
                                        <td><?php echo htmlspecialchars($dish['name']); ?></td>
                                        <td><?php echo $dish['price']; ?>€</td>
                                        <td>
                                            <input type="number" name="quantities[<?php echo $categoryKey; ?>][<?php echo htmlspecialchars($dish['name']); ?>]" min="1" value="1" disabled style="width: 60px;">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="form-group">
                    <label for="preferences">Préférences spéciales</label>
                    <textarea id="preferences" name="preferences" rows="4" placeholder="Allergies, préférences alimentaires, demande spéciale..."></textarea>
                </div>
                
                <div class="form-group checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="confirmation" required>
                        <span>Je confirme que les informations fournies sont correctes *</span>
                    </label>
                </div>
                
                <div class="form-buttons">
                    <button type="submit" class="submit-btn">Réserver</button>
                    <button type="reset" class="reset-btn">Réinitialiser</button>
                </div>
            </form>
            
            <div class="past-reservations">
                <h1 style="text-align: center;">Vos Réservations Passées</h1>
                <?php
                $stmt = $pdo->prepare('SELECT * FROM reservations WHERE user_id = ? ORDER BY created_at DESC');
                $stmt->execute([$_SESSION['user_id']]);
                $userReservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ($userReservations): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Heure</th>
                                <th>Personnes</th>
                                <th>Plats</th>
                                <th>Total</th>
                                <th>Préférences</th>
                                <th>Créé le</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($userReservations as $res):
                                // Parse dishes from preferences
                                $preferences = $res['preferences'] ?? '';
                                $dishesDisplay = '';
                                $otherPreferences = '';

                                if (strpos($preferences, 'Plats sélectionnés:') !== false) {
                                    $parts = explode('Plats sélectionnés:', $preferences, 2);
                                    $otherPreferences = trim($parts[0]);
                                    $dishesJson = trim($parts[1]);

                                    if (!empty($dishesJson)) {
                                        $selectedDishes = json_decode($dishesJson, true);
                                        if (is_array($selectedDishes)) {
                                            $dishesList = [];
                                            $total = 0;
                                            foreach ($selectedDishes as $dish) {
                                                $dishesList[] = $dish['name'] . ' (x' . $dish['quantity'] . ')';
                                                $price = $allDishesPrices[$dish['name']] ?? 0;
                                                $total += $price * $dish['quantity'];
                                            }
                                            $dishesDisplay = implode(', ', $dishesList);
                                        }
                                    }
                                } else {
                                    $otherPreferences = $preferences;
                                    $total = 0;
                                }
                            ?>
                                <tr>
                                    <td><?php echo $res['date']; ?></td>
                                    <td><?php echo $res['heure']; ?></td>
                                    <td><?php echo $res['nb_personnes']; ?></td>
                                    <td><?php echo htmlspecialchars($dishesDisplay); ?></td>
                                    <td><?php echo number_format($total, 2); ?>€</td>
                                    <td><?php echo htmlspecialchars($otherPreferences); ?></td>
                                    <td><?php echo $res['created_at']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>Aucune réservation trouvée.</p>
                <?php endif; ?>
            </div>
            
            <div class="reservation-info">
                <h3>Informations importantes</h3>
                <ul>
                    <li>Les réservations sont confirmées par email</li>
                    <li>Merci d'arriver à l'heure prévue</li>
                    <li>Pour annuler, contactez-nous au moins 24h à l'avance</li>
                    <li>Heures d'ouverture : Lundi - Dimanche : 12h00 - 22h00</li>
                </ul>
            </div>
        <?php endif; ?>
    </main>
    
    <footer>
        <p>&copy; 2025 BUVETTE EMSI. Tous droits réservés.</p>
    </footer>
    
    <script>
        // Set minimum date to today
        const dateInput = document.getElementById('date');
        const today = new Date().toISOString().split('T')[0];
        dateInput.setAttribute('min', today);

        // Toggle quantity inputs when checkboxes are checked
        document.querySelectorAll('input[type="checkbox"][name^="dishes"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const dishName = this.value;
                const category = this.name.match(/dishes\[(\w+)\]\[\]/)[1];
                const quantityInput = document.querySelector(`input[name="quantities[${category}][${dishName.replace(/'/g, "\\'")}]"]`);
                if (quantityInput) {
                    quantityInput.disabled = !this.checked;
                }
            });
        });
    </script>
</body>
</html>

