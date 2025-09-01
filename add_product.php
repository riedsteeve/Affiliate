<?php
header('Content-Type: application/json');
require_once 'auth.php';
require_once 'db_connect.php';

// Vérifier si l'utilisateur est connecté
requireLogin();

$response = ['success' => false, 'message' => ''];

// Récupérer le corps de la requête (JSON)
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Vérifier les données requises
// La correction est ici: 'icon' a été remplacé par 'image_url'
if (!isset($data['title']) || !isset($data['description']) || !isset($data['category']) || !isset($data['image_url']) || !isset($data['rating']) || !isset($data['amazon_link'])) {
    $response['message'] = 'Tous les champs obligatoires doivent être remplis.';
    echo json_encode($response);
    exit;
}

$id = $data['id'] ?? null;

try {
    // Convertir le tableau features en JSON
    $features = isset($data['features']) ? json_encode($data['features']) : '[]';

    if ($id) {
        // Mode modification
        // Et ici: la colonne 'icon' a été remplacée par 'image_url'
        $stmt = $pdo->prepare("UPDATE products SET title = :title, description = :description, category = :category, image_url = :image_url, features = :features, rating = :rating, amazon_link = :amazon_link WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    } else {
        // Mode ajout
        // Et ici: la colonne 'icon' a été remplacée par 'image_url'
        $stmt = $pdo->prepare("INSERT INTO products (title, description, category, image_url, features, rating, amazon_link) VALUES (:title, :description, :category, :image_url, :features, :rating, :amazon_link)");
    }

    // Lier les paramètres
    $stmt->bindParam(':title', $data['title']);
    $stmt->bindParam(':description', $data['description']);
    $stmt->bindParam(':category', $data['category']);
    // Et ici: le paramètre 'icon' a été remplacé par 'image_url'
    $stmt->bindParam(':image_url', $data['image_url']);
    $stmt->bindParam(':features', $features);
    $stmt->bindParam(':rating', $data['rating']);
    $stmt->bindParam(':amazon_link', $data['amazon_link']);

    // Exécuter la requête
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = $id ? 'Produit modifié avec succès.' : 'Produit ajouté avec succès.';
    } else {
        $response['message'] = $id ? 'Erreur lors de la modification du produit.' : 'Erreur lors de l\'ajout du produit.';
        error_log("Erreur d'exécution de la requête dans add_product.php: " . json_encode($stmt->errorInfo()));
    }
} catch (PDOException $e) {
    $response['message'] = 'Erreur de base de données: ' . $e->getMessage();
    error_log("Erreur de base de données dans add_product.php: " . $e->getMessage());
}

echo json_encode($response);
?>