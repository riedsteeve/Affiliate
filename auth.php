<?php
session_start();

// Configuration
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_TIMEOUT', 900); // 15 minutes en secondes
define('SESSION_TIMEOUT', 1800); // 30 minutes en secondes

// Identifiants de connexion (à changer en production)
define('ADMIN_USERNAME', 'affwebsite');
define('ADMIN_PASSWORD', 'Steeve@20056789');

// Fonction pour vérifier si l'utilisateur est connecté
function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Fonction pour vérifier les tentatives de connexion
function checkLoginAttempts() {
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['last_attempt'] = time();
        return true;
    }

    if ($_SESSION['login_attempts'] >= MAX_LOGIN_ATTEMPTS) {
        if (time() - $_SESSION['last_attempt'] < LOGIN_TIMEOUT) {
            return false;
        }
        // Réinitialiser les tentatives après le timeout
        $_SESSION['login_attempts'] = 0;
    }
    return true;
}

// Fonction pour enregistrer une tentative de connexion
function recordLoginAttempt($success) {
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
    }
    
    if (!$success) {
        $_SESSION['login_attempts']++;
        $_SESSION['last_attempt'] = time();
    } else {
        $_SESSION['login_attempts'] = 0;
    }
}

// Fonction pour sécuriser les entrées
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Fonction pour vérifier les identifiants
function verifyCredentials($username, $password) {
    return $username === ADMIN_USERNAME && $password === ADMIN_PASSWORD;
}

// Fonction pour déconnecter l'utilisateur
function logout() {
    session_unset();
    session_destroy();
}

// Fonction pour vérifier la session
function checkSession() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }

    // Vérifier le timeout de session
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
        logout();
        header('Location: login.php?timeout=1');
        exit;
    }

    // Mettre à jour le timestamp de dernière activité
    $_SESSION['last_activity'] = time();
}

// Fonction pour protéger une page
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}
 