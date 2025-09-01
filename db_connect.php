<?php
$host = 'mes-recoms-bdynk-mysql.mes-recoms-bdynk.svc.cluster.local';
$db   = 'mes-recoms';
$user = 'steeve';
$pass = 'mG4_dX4-rF5-lE9-cZ4='; // Remplace par ton mot de passe réel

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode([
        'success' => false,
        'message' => 'Erreur de connexion à la base : ' . $e->getMessage()
    ]));
}
?>
