<?php
$host = 'mes-recoms-bdynk-mysql.mes-recoms-bdynk.svc.cluster.local';
$db   = 'mes-recoms';
$user = 'steeve';
$pass = 'TON_MOT_DE_PASSE_ICI';

try {
    $pdo = new PDO(
        "mysql:host=$host;port=3306;dbname=$db;charset=utf8",
        $user,
        $pass,
        [
            PDO::ATTR_PERSISTENT => true, // connexion persistante
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]
    );
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
