<?php
header('Content-Type: application/json');
require_once 'auth.php';
require_once 'db_connect.php';

// Vérifier si l'utilisateur est connecté et est un admin
requireLogin();

$response = ['success' => false, 'message' => ''];

// Récupérer le corps de la requête (JSON)
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Vérifier les données requises
if (!isset($data['username']) || !isset($data['role'])) {
    $response['message'] = 'Le nom d\'utilisateur et le rôle sont obligatoires.';
    echo json_encode($response);
    exit;
}

$id = $data['id'] ?? null;
$username = sanitizeInput($data['username']);
$role = sanitizeInput($data['role']);
$password = $data['password'] ?? null;

try {
    $hashed_password = null;
    if ($password) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    }

    if ($id) {
        // Mode modification
        if ($hashed_password) {
            $stmt = $pdo->prepare("UPDATE users SET username = ?, password = ?, role = ? WHERE id = ?");
            $stmt->execute([$username, $hashed_password, $role, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
            $stmt->execute([$username, $role, $id]);
        }
        $response['message'] = 'Utilisateur modifié avec succès.';
    } else {
        // Mode ajout
        if (!$password) {
            $response['message'] = 'Le mot de passe est obligatoire pour un nouvel utilisateur.';
            echo json_encode($response);
            exit;
        }
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$username, $hashed_password, $role]);
        $response['message'] = 'Utilisateur ajouté avec succès.';
    }

    $response['success'] = true;

} catch (PDOException $e) {
    $response['message'] = 'Erreur de base de données: ' . $e->getMessage();
    if ($e->getCode() == '23000') { // Code d'erreur pour entrée dupliquée (unique constraint)
        $response['message'] = 'Le nom d\'utilisateur existe déjà.';
    }
} catch (Exception $e) {
    $response['message'] = 'Erreur: ' . $e->getMessage();
}

echo json_encode($response);
?> 