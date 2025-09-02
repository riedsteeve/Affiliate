<?php
require_once 'auth.php';

// Si déjà connecté, rediriger vers admin.php
if (isLoggedIn()) {
    header('Location: admin.php');
    exit;
}

$error = '';

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!checkLoginAttempts()) {
        $error = "Trop de tentatives de connexion. Veuillez réessayer dans 15 minutes.";
    } else {
        $username = sanitizeInput($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (verifyCredentials($username, $password)) {
            recordLoginAttempt(true);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['last_activity'] = time();
            header('Location: admin.php');
            exit;
        } else {
            recordLoginAttempt(false);
            $error = "Identifiants incorrects";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Connexion</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/png" href="images/favs.png">
    <style>
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 2rem;
            background: var(--card-bg);
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .login-title {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--text-color);
        }
        
        .login-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .form-group label {
            color: var(--text-color);
            font-weight: 500;
        }
        
        .form-group input {
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: var(--bg-color);
            color: var(--text-color);
        }
        
        .login-btn {
            background: var(--primary-color);
            color: white;
            padding: 0.75rem;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        
        .error-message {
            color: #dc3545;
            text-align: center;
            margin-bottom: 1rem;
            padding: 0.5rem;
            background: rgba(220, 53, 69, 0.1);
            border-radius: 8px;
        }

        .attempts-warning {
            color: #ffc107;
            text-align: center;
            margin-top: 1rem;
            font-size: 0.9rem;
        }
    </style>
</head>
<body data-theme="light">
    <div class="login-container">
        <h1 class="login-title">Administration</h1>
        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form class="login-form" method="POST" autocomplete="off">
            <div class="form-group">
                <label for="username">Nom d'utilisateur</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="login-btn">Se connecter</button>
        </form>
        <?php if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] > 0): ?>
            <div class="attempts-warning">
                Tentatives restantes : <?php echo MAX_LOGIN_ATTEMPTS - $_SESSION['login_attempts']; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 