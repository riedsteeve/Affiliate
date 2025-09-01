<?php
require_once 'auth.php';
require_once 'db_connect.php';

// Vérifier si l'utilisateur est connecté et est un admin
requireLogin();
// Optionnel : vérifier le rôle si vous avez des rôles différents
// if ($_SESSION['user_role'] !== 'admin') {
//     http_response_code(403);
//     echo json_encode(['error' => 'Accès non autorisé']);
//     exit;
// }

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT id, username, role, created_at FROM users ORDER BY id DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($users);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la récupération des utilisateurs: ' . $e->getMessage()]);
} 