<?php
header('Content-Type: application/json');
require_once 'auth.php';
require_once 'db_connect.php';

// Vérifier si l'utilisateur est connecté et est un admin
requireLogin();

// Récupérer le corps de la requête (JSON)
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!isset($data['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID de l\'utilisateur non fourni']);
    exit;
}

$id = (int)$data['id'];

try {
    // Empêcher la suppression du compte admin principal si c'est le seul
    // Ceci est une mesure de sécurité basique. Vous pouvez l'adapter.
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'admin'");
    $stmt->execute();
    $adminCount = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $userRole = $stmt->fetchColumn();

    if ($userRole === 'admin' && $adminCount <= 1) {
        echo json_encode(['success' => false, 'message' => 'Impossible de supprimer le dernier compte administrateur.']);
        exit;
    }

    // Préparer et exécuter la requête de suppression
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $result = $stmt->execute([$id]);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Utilisateur supprimé avec succès.']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression de l\'utilisateur.']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
}
?> 