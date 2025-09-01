<?php
require_once 'auth.php';
require_once 'db_connect.php';

// Vérifier si l'utilisateur est connecté et sa session valide
requireLogin();
checkSession();

// Récupérer les données pour le tableau de bord de manière sécurisée
$totalProducts = 0;
$totalUsers = 0;
$latestProducts = [];
$latestUsers = [];

try {
    // Total produits
    $stmt = $pdo->query("SELECT COUNT(*) FROM products");
    $totalProducts = $stmt->fetchColumn();

    // Total utilisateurs
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $totalUsers = $stmt->fetchColumn();

    // Derniers produits
    $stmt = $pdo->query("SELECT title, created_at FROM products ORDER BY created_at DESC LIMIT 5");
    $latestProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Derniers utilisateurs
    $stmt = $pdo->query("SELECT username, created_at FROM users ORDER BY created_at DESC LIMIT 5");
    $latestUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Ne pas afficher les erreurs de la BDD à l'utilisateur
    error_log("Erreur de base de données dans admin.php: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Affiliate</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Styles spécifiques pour le tableau des produits */
        .products-table td:nth-child(2) img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }
    </style>
</head>
<body data-theme="light">
    <div class="admin-wrapper">
        <aside class="sidebar">
            <h2>Admin Panel</h2>
            <ul>
                <li><a href="#dashboard" class="active"><i class="fas fa-tachometer-alt"></i> Tableau de Bord</a></li>
                <li><a href="#products" id="productsLink"><i class="fas fa-box"></i> Gestion des Produits</a></li>
                <li><a href="#users" id="usersLink"><i class="fas fa-users"></i> Gestion des Utilisateurs</a></li>
                <li><a href="#settings"><i class="fas fa-cog"></i> Paramètres</a></li>
            </ul>
            <a href="logout.php" class="btn logout-btn" style="width: 100%; text-align: center; margin-top: auto;">Déconnexion</a>
        </aside>

        <main class="main-content">
            <header class="admin-header">
                <h1 class="admin-title" id="mainAdminTitle">Tableau de Bord</h1>
                <div class="admin-actions">
                    <a href="index.php" class="btn">Retour au site</a>
                </div>
            </header>

            <section id="dashboard" class="content-section active-section">
                <h2>Bienvenue dans l'Administration</h2>
                <div class="dashboard-grid">
                    <div class="dashboard-card">
                        <h3>Produits Totaux</h3>
                        <div class="value"><?php echo htmlspecialchars($totalProducts); ?></div>
                    </div>
                    <div class="dashboard-card">
                        <h3>Utilisateurs Totaux</h3>
                        <div class="value"><?php echo htmlspecialchars($totalUsers); ?></div>
                    </div>
                    <div class="dashboard-card latest-list">
                        <h3>Derniers Produits</h3>
                        <ul>
                            <?php if (empty($latestProducts)): ?>
                                <li>Aucun produit récent.</li>
                            <?php else: ?>
                                <?php foreach ($latestProducts as $product): ?>
                                    <li>
                                        <span><?php echo htmlspecialchars($product['title']); ?></span>
                                        <span class="date"><?php echo htmlspecialchars((new DateTime($product['created_at']))->format('d/m/Y')); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="dashboard-card latest-list">
                        <h3>Derniers Utilisateurs</h3>
                        <ul>
                            <?php if (empty($latestUsers)): ?>
                                <li>Aucun utilisateur récent.</li>
                            <?php else: ?>
                                <?php foreach ($latestUsers as $user): ?>
                                    <li>
                                        <span><?php echo htmlspecialchars($user['username']); ?></span>
                                        <span class="date"><?php echo htmlspecialchars((new DateTime($user['created_at']))->format('d/m/Y')); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </section>

            <section id="products" class="content-section" style="display:none;">
                <h2>Gestion des Produits</h2>
                <section class="product-form-section">
                    <h3>Ajouter/Modifier un Produit</h3>
                    <form id="productForm" action="add_product.php" method="POST" class="product-form">
                        <input type="hidden" id="productId" name="id">
                        
                        <div class="form-group">
                            <label for="title">Titre du Produit</label>
                            <input type="text" id="title" name="title" required>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="category">Catégorie</label>
                            <select id="category" name="category" required>
                                <option value="">Sélectionner une catégorie</option>
                                <option value="tech">Tech</option>
                                <option value="storage">Stockage</option>
                                <option value="audio">Audio</option>
                                <option value="accessories">Accessoires</option>
                                <option value="network">Réseau</option>
                                <option value="smart">Smart Home</option>
                                <option value="peripherals">Périphériques</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="image_url">URL de l'Image du Produit</label>
                            <input type="url" id="image_url" name="image_url" placeholder="Ex: https://exemple.com/image.jpg" required>
                        </div>

                        <div class="form-group">
                            <label for="features">Caractéristiques (séparées par des virgules)</label>
                            <input type="text" id="features" name="features" placeholder="Ex: USB 3.1, Haute Vitesse, Fiable">
                        </div>

                        <div class="form-group">
                            <label for="rating">Note (0.0 à 5.0)</label>
                            <input type="number" id="rating" name="rating" step="0.1" min="0" max="5" value="0.0" required>
                        </div>

                        <div class="form-group">
                            <label for="amazon_link">Lien Amazon</label>
                            <input type="url" id="amazon_link" name="amazon_link" placeholder="Ex: https://amzn.to/..." required>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-save"></i> Enregistrer
                            </button>
                            <button type="button" id="cancelEditBtn" class="btn btn-secondary">
                                <i class="fa-solid fa-times"></i> Annuler
                            </button>
                        </div>
                    </form>
                </section>

                <section class="products-list">
                    <h3>Liste des Produits</h3>
                    <div class="table-responsive">
                        <table class="products-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Titre</th>
                                    <th>Catégorie</th>
                                    <th>Note</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="productsTableBody">
                            </tbody>
                        </table>
                    </div>
                </section>
            </section>

            <section id="users" class="content-section" style="display:none;">
                <h2>Gestion des Utilisateurs</h2>
                <section class="user-form-section content-section">
                    <h3>Ajouter/Modifier un Utilisateur</h3>
                    <form id="userForm" class="product-form">
                        <input type="hidden" id="userId" name="id">
                        
                        <div class="form-group">
                            <label for="username">Nom d'utilisateur</label>
                            <input type="text" id="username" name="username" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Mot de passe (laisser vide pour ne pas changer)</label>
                            <input type="password" id="password" name="password">
                        </div>

                        <div class="form-group">
                            <label for="role">Rôle</label>
                            <select id="role" name="role" required>
                                <option value="user">Utilisateur</option>
                                <option value="admin">Administrateur</option>
                            </select>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-save"></i> Enregistrer
                            </button>
                            <button type="button" id="cancelUserEditBtn" class="btn btn-secondary">
                                <i class="fa-solid fa-times"></i> Annuler
                            </button>
                        </div>
                    </form>
                </section>

                <section class="users-list content-section">
                    <h3>Liste des Utilisateurs</h3>
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nom d'utilisateur</th>
                                    <th>Rôle</th>
                                    <th>Date d'inscription</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="usersTableBody">
                            </tbody>
                        </table>
                    </div>
                </section>
            </section>

            <section id="settings" class="content-section" style="display:none;">
                <h2>Paramètres du Site</h2>
                <p>Cette section est en cours de développement.</p>
            </section>

        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const initialSection = window.location.hash.substring(1) || 'dashboard';
            const activeLink = document.querySelector(`.sidebar ul li a[href="#${initialSection}"]`);
            const activeSection = document.getElementById(initialSection);
            const mainAdminTitle = document.getElementById('mainAdminTitle');

            if (activeLink && activeSection) {
                document.querySelectorAll('.sidebar ul li a').forEach(nav => nav.classList.remove('active'));
                activeLink.classList.add('active');

                document.querySelectorAll('.content-section').forEach(section => {
                    section.style.display = 'none';
                });
                activeSection.style.display = 'block';

                if (initialSection === 'dashboard') {
                    mainAdminTitle.textContent = 'Tableau de Bord';
                } else if (initialSection === 'products') {
                    mainAdminTitle.textContent = 'Gestion des Produits';
                    loadProducts();
                } else if (initialSection === 'users') {
                    mainAdminTitle.textContent = 'Gestion des Utilisateurs';
                    loadUsers();
                } else if (initialSection === 'settings') {
                    mainAdminTitle.textContent = 'Paramètres du Site';
                }
            }
        });

        document.querySelectorAll('.sidebar ul li a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.sidebar ul li a').forEach(nav => nav.classList.remove('active'));
                this.classList.add('active');

                const targetId = this.getAttribute('href').substring(1);
                document.querySelectorAll('.content-section').forEach(section => {
                    section.style.display = 'none';
                });
                document.getElementById(targetId).style.display = 'block';

                const mainAdminTitle = document.getElementById('mainAdminTitle');
                if (targetId === 'dashboard') {
                    mainAdminTitle.textContent = 'Tableau de Bord';
                } else if (targetId === 'products') {
                    mainAdminTitle.textContent = 'Gestion des Produits';
                    loadProducts();
                } else if (targetId === 'users') {
                    mainAdminTitle.textContent = 'Gestion des Utilisateurs';
                    loadUsers();
                } else if (targetId === 'settings') {
                    mainAdminTitle.textContent = 'Paramètres du Site';
                }
            });
        });

        // ------------------ FONCTIONS DE GESTION DES PRODUITS ------------------

        async function loadProducts() {
            const productsTableBody = document.getElementById('productsTableBody');
            productsTableBody.innerHTML = '';
            
            try {
                const response = await fetch('get_products.php');
                const products = await response.json();

                if (products.error) {
                    productsTableBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">${products.error}</td></tr>`;
                    return;
                }

                if (products.length === 0) {
                    productsTableBody.innerHTML = `<tr><td colspan="6" class="text-center text-info">Aucun produit trouvé.</td></tr>`;
                    return;
                }

                products.forEach(product => {
                    const tr = document.createElement('tr');
                    tr.dataset.productId = product.id;

                    const idCell = document.createElement('td');
                    idCell.textContent = product.id;
                    tr.appendChild(idCell);

                    const imageCell = document.createElement('td');
                    const img = document.createElement('img');
                    
                    let safeImageUrl = product.image_url;
                    if (safeImageUrl) {
                        try {
                            new URL(safeImageUrl);
                            img.src = safeImageUrl;
                        } catch (e) {
                            img.src = 'assets/images/placeholder.png'; 
                        }
                    } else {
                        img.src = 'assets/images/placeholder.png';
                    }
                    img.alt = product.title ? product.title : 'Image du produit';
                    imageCell.appendChild(img);
                    tr.appendChild(imageCell);
                    
                    const titleCell = document.createElement('td');
                    titleCell.textContent = product.title;
                    tr.appendChild(titleCell);

                    const categoryCell = document.createElement('td');
                    categoryCell.textContent = product.category;
                    tr.appendChild(categoryCell);

                    const ratingCell = document.createElement('td');
                    ratingCell.textContent = product.rating;
                    tr.appendChild(ratingCell);

                    const actionsCell = document.createElement('td');
                    actionsCell.className = 'action-buttons';
                    
                    const editBtn = document.createElement('button');
                    editBtn.className = 'action-btn edit-btn';
                    editBtn.innerHTML = '<i class="fa-solid fa-pen-to-square"></i>';
                    editBtn.title = 'Modifier';
                    editBtn.addEventListener('click', () => editProduct(product.id));
                    
                    const deleteBtn = document.createElement('button');
                    deleteBtn.className = 'action-btn delete-btn';
                    deleteBtn.innerHTML = '<i class="fa-solid fa-trash-alt"></i>';
                    deleteBtn.title = 'Supprimer';
                    deleteBtn.addEventListener('click', () => deleteProduct(product.id));

                    actionsCell.appendChild(editBtn);
                    actionsCell.appendChild(deleteBtn);
                    tr.appendChild(actionsCell);

                    productsTableBody.appendChild(tr);
                });
            } catch (error) {
                console.error('Erreur lors du chargement des produits:', error);
                productsTableBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">Impossible de charger les produits. Veuillez réessayer.</td></tr>`;
            }
        }
        
        async function editProduct(id) {
            try {
                const response = await fetch(`get_product.php?id=${id}`);
                const product = await response.json();

                if (product.error) {
                    alert(product.error);
                    return;
                }
                
                document.getElementById('productId').value = product.id;
                document.getElementById('title').value = product.title;
                document.getElementById('description').value = product.description;
                document.getElementById('category').value = product.category;
                document.getElementById('image_url').value = product.image_url;
                document.getElementById('features').value = product.features.join(', ');
                document.getElementById('rating').value = product.rating;
                document.getElementById('amazon_link').value = product.amazon_link;

            } catch (error) {
                console.error('Erreur lors de l\'édition du produit:', error);
                alert('Une erreur est survenue lors de l\'édition.');
            }
        }

        async function deleteProduct(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')) {
                try {
                    const response = await fetch('delete_product.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ id: id })
                    });
                    const data = await response.json();

                    if (data.success) {
                        alert('Produit supprimé avec succès!');
                        loadProducts();
                    } else {
                        alert('Erreur lors de la suppression du produit: ' + data.message);
                    }
                } catch (error) {
                    console.error('Erreur lors de la suppression du produit:', error);
                    alert('Une erreur est survenue lors de la suppression.');
                }
            }
        }

        document.getElementById('cancelEditBtn').addEventListener('click', () => {
            document.getElementById('productForm').reset();
            document.getElementById('productId').value = '';
        });

        document.getElementById('productForm').addEventListener('submit', async (event) => {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const productData = {};
            for (let [key, value] of formData.entries()) {
                productData[key] = value;
            }
            if (productData.features) {
                productData.features = productData.features.split(',').map(f => f.trim());
            } else {
                productData.features = [];
            }
            try {
                const response = await fetch('add_product.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(productData)
                });
                const result = await response.json();
                if (result.success) {
                    alert('Produit enregistré avec succès!');
                    form.reset();
                    document.getElementById('productId').value = '';
                    loadProducts();
                } else {
                    alert('Erreur lors de l\'enregistrement: ' + result.message);
                }
            } catch (error) {
                console.error('Erreur lors de la requête:', error);
                alert('Une erreur est survenue lors de la communication avec le serveur.');
            }
        });

        // ------------------ FONCTIONS DE GESTION DES UTILISATEURS ------------------

        async function loadUsers() {
            const usersTableBody = document.getElementById('usersTableBody');
            usersTableBody.innerHTML = '';
            
            try {
                const response = await fetch('get_users.php');
                const users = await response.json();

                if (users.error) {
                    usersTableBody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">${users.error}</td></tr>`;
                    return;
                }

                if (users.length === 0) {
                    usersTableBody.innerHTML = `<tr><td colspan="5" class="text-center text-info">Aucun utilisateur trouvé.</td></tr>`;
                    return;
                }

                users.forEach(user => {
                    const tr = document.createElement('tr');
                    tr.dataset.userId = user.id;

                    const idCell = document.createElement('td');
                    idCell.textContent = user.id;
                    tr.appendChild(idCell);

                    const usernameCell = document.createElement('td');
                    usernameCell.textContent = user.username;
                    tr.appendChild(usernameCell);

                    const roleCell = document.createElement('td');
                    roleCell.textContent = user.role;
                    tr.appendChild(roleCell);

                    const dateCell = document.createElement('td');
                    dateCell.textContent = new Date(user.created_at).toLocaleDateString();
                    tr.appendChild(dateCell);

                    const actionsCell = document.createElement('td');
                    actionsCell.className = 'action-buttons';
                    
                    const editBtn = document.createElement('button');
                    editBtn.className = 'action-btn edit-btn';
                    editBtn.innerHTML = '<i class="fa-solid fa-pen-to-square"></i>';
                    editBtn.title = 'Modifier';
                    editBtn.addEventListener('click', () => editUser(user.id));
                    
                    const deleteBtn = document.createElement('button');
                    deleteBtn.className = 'action-btn delete-btn';
                    deleteBtn.innerHTML = '<i class="fa-solid fa-trash-alt"></i>';
                    deleteBtn.title = 'Supprimer';
                    deleteBtn.addEventListener('click', () => deleteUser(user.id));

                    actionsCell.appendChild(editBtn);
                    actionsCell.appendChild(deleteBtn);
                    tr.appendChild(actionsCell);

                    usersTableBody.appendChild(tr);
                });
            } catch (error) {
                console.error('Erreur lors du chargement des utilisateurs:', error);
                usersTableBody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">Impossible de charger les utilisateurs. Veuillez réessayer.</td></tr>`;
            }
        }

        async function editUser(id) {
            try {
                const response = await fetch(`get_user.php?id=${id}`);
                const user = await response.json();
                
                if (user.error) {
                    alert(user.error);
                    return;
                }

                document.getElementById('userId').value = user.id;
                document.getElementById('username').value = user.username;
                document.getElementById('role').value = user.role;
                document.getElementById('password').value = ''; 
                document.getElementById('password').removeAttribute('required');
            } catch (error) {
                console.error('Erreur lors de l\'édition de l\'utilisateur:', error);
                alert('Une erreur est survenue lors de l\'édition.');
            }
        }

        async function deleteUser(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
                try {
                    const response = await fetch('delete_user.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: id })
                    });
                    const data = await response.json();
                    if (data.success) {
                        alert('Utilisateur supprimé avec succès!');
                        loadUsers();
                    } else {
                        alert('Erreur lors de la suppression de l\'utilisateur: ' + data.message);
                    }
                } catch (error) {
                    console.error('Erreur lors de la suppression de l\'utilisateur:', error);
                    alert('Une erreur est survenue lors de la suppression.');
                }
            }
        }

        document.getElementById('cancelUserEditBtn').addEventListener('click', () => {
            document.getElementById('userForm').reset();
            document.getElementById('userId').value = '';
            document.getElementById('password').removeAttribute('required');
        });

        document.getElementById('userForm').addEventListener('submit', async (event) => {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const userData = {};
            for (let [key, value] of formData.entries()) {
                if (key === 'password' && value === '' && document.getElementById('userId').value !== '') {
                    continue;
                }
                userData[key] = value;
            }
            try {
                const response = await fetch('add_user.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(userData)
                });
                const result = await response.json();
                if (result.success) {
                    alert('Utilisateur enregistré avec succès!');
                    form.reset();
                    document.getElementById('userId').value = '';
                    document.getElementById('password').removeAttribute('required');
                    loadUsers();
                } else {
                    alert('Erreur lors de l\'enregistrement: ' + result.message);
                }
            } catch (error) {
                console.error('Erreur lors de la requête:', error);
                alert('Une erreur est survenue lors de la communication avec le serveur.');
            }
        });
    </script>
</body>
</html>