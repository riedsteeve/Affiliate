<?php
require_once 'auth.php';
require_once 'db_connect.php';

// Vérifier si l'utilisateur est connecté et est un admin
requireLogin();

// Vérifier si l'ID est fourni
if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de l\'utilisateur non fourni']);
    exit;
}

$id = (int)$_GET['id'];

header('Content-Type: application/json');

try {
    // Préparer et exécuter la requête
    $stmt = $pdo->prepare("SELECT id, username, role FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404);
        echo json_encode(['error' => 'Utilisateur non trouvé']);
        exit;
    }

    echo json_encode($user);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la récupération de l\'utilisateur: ' . $e->getMessage()]);
} 