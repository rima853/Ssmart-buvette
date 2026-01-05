

<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: log_in.php');
    exit;
}

$role = $_SESSION['role_name'] ?? 'user';
if ($role !== 'user') {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Restaurant</title>
    <link rel="stylesheet" href="menu.css?v=3">
</head>
<body>
    <header>
        <?php include 'nav.php'; ?>
    </header>
    
    <main class="menu-container">
        <div class="menu-header">
            <h2>Notre Menu</h2>
            <p>Découvrez nos délicieuses spécialités</p>
        </div>
        
        <section class="menu-section">
            <h3 class="section-title">Entrées</h3>
            <div class="menu-items">
                <div class="menu-item">
                    <div class="item-info">
                        <h4>Salade César</h4>
                        <p>Laitue romaine, croûtons, parmesan, sauce césar</p>
                    </div>
                    <span class="price">12€</span>
                </div>
                <div class="menu-item">
                    <div class="item-info">
                        <h4>Soupe à l'oignon</h4>
                        <p>Soupe traditionnelle française gratinée</p>
                    </div>
                    <span class="price">9€</span>
                </div>
                <div class="menu-item">
                    <div class="item-info">
                        <h4>Plateau de charcuterie</h4>
                        <p>Sélection de charcuteries et fromages locaux</p>
                    </div>
                    <span class="price">16€</span>
                </div>
                <div class="menu-item">
                    <div class="item-info">
                        <h4>Foie gras maison</h4>
                        <p>Accompagné de pain brioché et confiture d'oignons</p>
                    </div>
                    <span class="price">18€</span>
                </div>
            </div>
        </section>
        
        <section class="menu-section">
            <h3 class="section-title">Plats Principaux</h3>
            <div class="menu-items">
                <div class="menu-item">
                    <div class="item-info">
                        <h4>Steak frites</h4>
                        <p>Entrecôte de bœuf, frites maison, sauce au poivre</p>
                    </div>
                    <span class="price">24€</span>
                </div>
                <div class="menu-item">
                    <div class="item-info">
                        <h4>Saumon grillé</h4>
                        <p>Filet de saumon, légumes de saison, riz pilaf</p>
                    </div>
                    <span class="price">22€</span>
                </div>
                <div class="menu-item">
                    <div class="item-info">
                        <h4>Poulet rôti</h4>
                        <p>Poulet fermier, pommes de terre rôties, jus au thym</p>
                    </div>
                    <span class="price">20€</span>
                </div>
                <div class="menu-item">
                    <div class="item-info">
                        <h4>Risotto aux champignons</h4>
                        <p>Risotto crémeux aux champignons de saison, parmesan</p>
                    </div>
                    <span class="price">19€</span>
                </div>
                <div class="menu-item">
                    <div class="item-info">
                        <h4>Bouillabaisse</h4>
                        <p>Mélange de poissons et fruits de mer, rouille, croûtons</p>
                    </div>
                    <span class="price">28€</span>
                </div>
            </div>
        </section>
        
        <section class="menu-section">
            <h3 class="section-title">Desserts</h3>
            <div class="menu-items">
                <div class="menu-item">
                    <div class="item-info">
                        <h4>Tiramisu</h4>
                        <p>Dessert italien traditionnel au café et mascarpone</p>
                    </div>
                    <span class="price">8€</span>
                </div>
                <div class="menu-item">
                    <div class="item-info">
                        <h4>Crème brûlée</h4>
                        <p>Crème vanille caramélisée</p>
                    </div>
                    <span class="price">7€</span>
                </div>
                <div class="menu-item">
                    <div class="item-info">
                        <h4>Tarte aux pommes</h4>
                        <p>Tarte maison, pommes caramélisées, glace vanille</p>
                    </div>
                    <span class="price">8€</span>
                </div>
                <div class="menu-item">
                    <div class="item-info">
                        <h4>Assortiment de sorbets</h4>
                        <p>Trois parfums au choix</p>
                    </div>
                    <span class="price">7€</span>
                </div>
            </div>
        </section>
        
        <section class="menu-section">
            <h3 class="section-title">Boissons</h3>
            <div class="menu-items">
                <div class="menu-item">
                    <div class="item-info">
                        <h4>Vin rouge (verre)</h4>
                        <p>Sélection de vins de la région</p>
                    </div>
                    <span class="price">6€</span>
                </div>
                <div class="menu-item">
                    <div class="item-info">
                        <h4>Vin blanc (verre)</h4>
                        <p>Sélection de vins de la région</p>
                    </div>
                    <span class="price">6€</span>
                </div>
                <div class="menu-item">
                    <div class="item-info">
                        <h4>Eau minérale</h4>
                        <p>Bouteille 50cl</p>
                    </div>
                    <span class="price">3€</span>
                </div>
                <div class="menu-item">
                    <div class="item-info">
                        <h4>Café</h4>
                        <p>Expresso ou allongé</p>
                    </div>
                    <span class="price">2.50€</span>
                </div>
            </div>
        </section>
    </main>
    
    <footer>
        <p>&copy; 2025 BUVETTE EMSI. Tous droits réservés.</p>
    </footer>
</body>
</html>

