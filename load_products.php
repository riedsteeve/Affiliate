<?php
header('Content-Type: application/json');
include 'db_connect.php';

try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
    $products = $stmt->fetchAll();
    
    // Convertir les features JSON en tableau pour chaque produit
    foreach ($products as &$product) {
        if (isset($product['features'])) {
            $product['features'] = json_decode($product['features'], true);
        }
    }
    
    echo json_encode($products);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erreur lors de la récupération des produits: ' . $e->getMessage()]);
}
?> 