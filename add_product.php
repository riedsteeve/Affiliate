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

if (!$data) {
    $response['message'] = 'Données JSON invalides.';
    echo json_encode($response);
    exit;
}

// Vérifier les champs obligatoires
$required = ['title', 'description', 'category', 'image_url', 'rating', 'amazon_link'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        $response['message'] = "Le champ '$field' est obligatoire.";
        echo json_encode($response);
        exit;
    }
}

$id = $data['id'] ?? null;
$features = isset($data['features']) ? json_encode($data['features']) : '[]';

try {
    if ($id) {
        // Modification
        $stmt = $pdo->prepare("
            UPDATE products
            SET title = :title,
                description = :description,
                category = :category,
                image_url = :image_url,
                features = :features,
                rating = :rating,
                amazon_link = :amazon_link
            WHERE id = :id
        ");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    } else {
        // Ajout
        $stmt = $pdo->prepare("
            INSERT INTO products (title, description, category, image_url, features, rating, amazon_link)
            VALUES (:title, :description, :category, :image_url, :features, :rating, :amazon_link)
        ");
    }

    // Lier les paramètres
    $stmt->bindParam(':title', $data['title']);
    $stmt->bindParam(':description', $data['description']);
    $stmt->bindParam(':category', $data['category']);
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
        error_log("Erreur d'exécution add_product.php : " . json_encode($stmt->errorInfo()));
    }

} catch (PDOException $e) {
    $response['message'] = 'Erreur de base de données: ' . $e->getMessage();
    error_log("Erreur PDO add_product.php : " . $e->getMessage());
}

echo json_encode($response);
?>
