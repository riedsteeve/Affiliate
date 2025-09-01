<?php
require_once 'auth.php';
require_once 'db_connect.php';

// Vérifier si l'utilisateur est connecté
requireLogin();

// Vérifier si l'ID est fourni
if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID du produit non fourni']);
    exit;
}

$id = (int)$_GET['id'];

try {
    // Préparer et exécuter la requête
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        http_response_code(404);
        echo json_encode(['error' => 'Produit non trouvé']);
        exit;
    }

    // Convertir les caractéristiques JSON en tableau
    if (isset($product['features'])) {
        $product['features'] = json_decode($product['features'], true);
    }

    // Renvoyer le produit en JSON
    header('Content-Type: application/json');
    echo json_encode($product);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la récupération du produit']);
} 