<?php
require_once 'auth.php';

// Déconnecter l'utilisateur
logout();

// Rediriger vers la page de connexion
header('Location: login.php');
exit; 