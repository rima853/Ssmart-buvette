<?php
// Config BD
$db_host = '127.0.0.1:3306';
$db_name = 'db'; // adapter si nécessaire
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Set time zone to UTC to match PHP
    $pdo->exec("SET time_zone = '+00:00'");
} catch (Exception $e) {
    die('Erreur de connexion BD' . $e->getMessage());
}



/*
<?php
$dsn = "mysql:host=localhost;port=3307;dbname=test;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur connexion : " . $e->getMessage());
}
?>
*/