<?php
require_once 'db_connect.php';

try {
    // Vérifier si la table products existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'products'");
    $tableExists = $stmt->rowCount() > 0;

    if (!$tableExists) {
        // Créer la table products
        $pdo->exec("CREATE TABLE products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            category VARCHAR(50) NOT NULL,
            icon VARCHAR(100) NOT NULL,
            features JSON,
            rating DECIMAL(2,1) NOT NULL DEFAULT 0.0,
            amazon_link VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        echo "Table 'products' créée avec succès.<br>";
    } else {
        echo "La table 'products' existe déjà.<br>";
    }

    // Vérifier si la table users existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    $usersTableExists = $stmt->rowCount() > 0;

    if (!$usersTableExists) {
        // Créer la table users
        $pdo->exec("CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role VARCHAR(20) NOT NULL DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        // Ajouter un utilisateur admin par défaut
        $default_admin_username = ADMIN_USERNAME;
        $default_admin_password_hash = password_hash(ADMIN_PASSWORD, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'admin')");
        $stmt->execute([$default_admin_username, $default_admin_password_hash]);

        echo "Table 'users' créée avec succès et utilisateur admin par défaut ajouté.<br>";
    } else {
        echo "La table 'users' existe déjà.<br>";
    }

    // Afficher la structure de la table products
    $stmt = $pdo->query("DESCRIBE products");
    echo "<h3>Structure de la table 'products' :</h3>";
    echo "<pre>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
    echo "</pre>";

    // Afficher la structure de la table users
    $stmt = $pdo->query("DESCRIBE users");
    echo "<h3>Structure de la table 'users' :</h3>";
    echo "<pre>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
    echo "</pre>";

} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?> 