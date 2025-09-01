<?php
require_once 'auth.php';
require_once 'db_connect.php';

// Vérifier si l'utilisateur est connecté
requireLogin();

// Vérifier si les données sont envoyées en JSON
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!isset($data['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID du produit non fourni']);
    exit;
}

$id = (int)$data['id'];

try {
    // Préparer et exécuter la requête de suppression
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $result = $stmt->execute([$id]);

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Erreur lors de la suppression du produit']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erreur lors de la suppression du produit']);
} 