<?php
$host = 'mes-recoms-bdynk-mysql.mes-recoms-bdynk.svc.cluster.local';
$db   = 'mes-recoms';
$user = 'steeve';
$pass = 'mG4_dX4-rF5-lE9-cZ4='; // Remplace par ton mot de passe

function connectPDO() {
    global $host, $db, $user, $pass;

    $options = [
        PDO::ATTR_PERSISTENT => true,           // Connexion persistante
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
    ];

    try {
        return new PDO("mysql:host=$host;port=3306;dbname=$db;charset=utf8", $user, $pass, $options);
    } catch (PDOException $e) {
        error_log("Erreur de connexion PDO : " . $e->getMessage());
        die(json_encode([
            'success' => false,
            'message' => 'Impossible de se connecter à la base de données.'
        ]));
    }
}

// Crée l'objet PDO
$pdo = connectPDO();
?>
