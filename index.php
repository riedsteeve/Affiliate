<?php
// Inclure le fichier de connexion à la base de données
include 'db_connect.php';

// Récupérer les produits depuis la base de données
try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des produits: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Recommandations Tech - Ried Steeve</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Onest:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <meta name="description" content="Découvrez Mes Recommandations Tech : une sélection de produits testés et approuvés, alliant fiabilité, performance et meilleur rapport qualité-prix.">
    <link rel="icon" type="images/fav.png" href="favicon.png">
</head>

<body data-theme="light">
     

    <nav class="navbar">
    <div class="nav-container">
        <div class="nav-brand">
            <i class="fas fa-microchip"></i>
            <span>Recos Tech Steeve</span>
        </div>
        <div class="nav-menu">
            <a href="#home" class="nav-link active">Accueil</a>
            <a href="#products" class="nav-link">Produits</a>
            <a href="#about" class="nav-link">À propos</a>
            <a href="login.php" class="admin-link">Admin</a>

            <button class="theme-toggle" id="themeToggle" title="Changer de thème">
                <i class="fas fa-moon"></i>
            </button>
        </div>
        <div class="nav-actions">
            <button class="theme-toggle" id="themeToggleMobile" title="Changer de thème">
                <i class="fas fa-moon"></i>
            </button>
            <button class="nav-toggle" id="navToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>
</nav>

    <section id="home" class="hero-section">
        <div class="hero-content">
            <div class="hero-badge">
                <i class="fas fa-star"></i>
                <span>Recommandations Premium</span>
            </div>
            <h1 class="hero-title">
                <span class="title-line">Recos Tech</span>
                <span class="title-line gradient-text">Ried Steeve</span>
            </h1>
            <p id="greeting" class="hero-greeting">Bonjour et bienvenue !</p>
            <p class="hero-description">
                Bienvenue sur ma sélection personnelle ! Fort de ma passion dévorante pour la tech et de mes années de tests, je vous partage ici mes
                <span class="highlight">dernières découvertes et mes recommandations</span>
                de produits Amazon qui ont <b>véritablement transformé mon quotidien</b>. Que vous soyez un professionnel averti, un gamer passionné ou un simple curieux à la recherche de <b>gadgets utiles et performants</b>, vous découvrirez des pépites triées sur le volet pour leur innovation, leur fiabilité et leur excellent rapport qualité-prix. Laissez-vous guider et équipez-vous malin !
            </p>
            <div class="hero-actions">
                <button class="btn-primary" onclick="scrollToSection('products')">
                    <span>Découvrir mes recos</span>
                    <i class="fas fa-arrow-down"></i>
                </button>
                <a href="#about" class="btn-secondary">
                    <span>En savoir plus sur moi</span>
                    <i class="fas fa-info-circle"></i>
                </a>
            </div>
        </div>
        <div class="hero-visual">
            <div class="floating-cards">
                <div class="floating-card card-1">
                    <i class="fas fa-laptop"></i> </div>
                <div class="floating-card card-2">
                    <i class="fas fa-headphones"></i> </div>
                <div class="floating-card card-3">
                    <i class="fas fa-smartphone"></i> </div>
                <div class="floating-card card-4">
                    <i class="fas fa-mouse"></i> </div>
                <div class="floating-card card-5">
                    <i class="fas fa-robot"></i> </div>
                <div class="floating-card card-6">
                    <i class="fas fa-gamepad"></i> </div>
            </div>
        </div>
    </section>

    <section id="products" class="products-section">
        <div class="section-header">
            <div class="section-badge">
                <i class="fas fa-star"></i>
                <span>Sélection Premium</span>
            </div>
            <h2 class="section-title">Mes Indispensables Tech</h2>
            <p class="section-description">
                Plongez dans ma collection personnelle : une sélection rigoureuse de produits tech que j'utilise et <b>approuve au quotidien</b> pour leur qualité, performance et innovation.
            </p>
            <div class="product-search-bar">
                <input type="text" id="productSearch" placeholder="Rechercher un produit..." aria-label="Rechercher des produits">
                <button id="searchButton" aria-label="Lancer la recherche"><i class="fas fa-search"></i></button>
            </div>
        </div>

        <div class="products-grid" id="productsGrid">
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php else: ?>
                <?php foreach ($products as $product): 
                    // Convertir les features JSON en tableau
                    $features = json_decode($product['features'], true) ?? [];
                    
                    // Générer les étoiles
                    $rating = floatval($product['rating']);
                    $fullStars = floor($rating);
                    $hasHalfStar = ($rating - $fullStars) >= 0.5;
                ?>
                    <div class="product-card" data-category="<?php echo htmlspecialchars($product['category']); ?>">
                        <div class="product-image">
    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>">
</div>
                        <div class="product-content">
                            <h3 class="product-title"><?php echo htmlspecialchars($product['title']); ?></h3>
                            <p class="product-description"><?php echo htmlspecialchars($product['description'] ?? ''); ?></p>
                            <div class="product-features">
                                <?php foreach ($features as $feature): ?>
                                    <span class="feature-tag"><?php echo htmlspecialchars($feature); ?></span>
                                <?php endforeach; ?>
                            </div>
                            <div class="product-rating">
                                <div class="stars">
                                    <?php for ($i = 0; $i < $fullStars; $i++): ?>
                                        <i class="fas fa-star"></i>
                                    <?php endfor; ?>
                                    <?php if ($hasHalfStar): ?>
                                        <i class="fas fa-star-half-alt"></i>
                                    <?php endif; ?>
                                    <?php for ($i = 0; $i < (5 - $fullStars - ($hasHalfStar ? 1 : 0)); $i++): ?>
                                        <i class="far fa-star"></i>
                                    <?php endfor; ?>
                                </div>
                                <span class="rating-text"><?php echo number_format($rating, 1); ?> sur 5</span>
                            </div>
                        </div>
                        <div class="product-action">
                            <a href="<?php echo htmlspecialchars($product['amazon_link']); ?>" target="_blank" rel="noopener noreferrer" class="btn-product">
                                <span>Voir sur Amazon</span>
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <section id="about" class="about-section">
        <div class="about-content">
            <div class="about-text">
                <div class="section-badge">
                    <i class="fas fa-user"></i>
                    <span>À propos de moi</span>
                </div>
                <h2 class="section-title">Pourquoi faire confiance aux Recos de Steeve ?</h2>
                <p class="section-description">
                    Salut, c'est Steeve ! Depuis des années, la <b>tech est ma passion</b>, mon terrain de jeu. Je ne me contente pas de lire des fiches techniques ; chaque produit que je recommande est <b>minutieusement testé dans des conditions réelles</b>. Mon objectif ? Vous faire gagner du temps et vous assurer que chaque achat est un investissement judicieux.
                </p>
                <div class="about-features">
                    <div class="feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span><b>Tests Approfondis : </b> chaque produit est mis à l'épreuve par mes soins.</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span><b>Expertise Technique :</b> Des années d'expérience et une veille constante.</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span><b>Recommandations Honnêtes :</b> Mon avis est indépendant et sans compromis.</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span><b>Rapport Qualité-Prix :</b> Je cherche toujours le meilleur pour votre budget.</span>
                    </div>
                </div>
                <!-----
                <div class="about-cta">
                    <button class="btn-primary" onclick="window.location.href='autreProduit.html'">
                        <span>Voir d'autres Produits</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
                ----->
            </div>
            <div class="about-visual">
                <div class="profile-card">
                    <div class="profile-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <h3>Ried Steeve</h3>
                    <p>Expert Tech & Testeur Passionné</p>
                </div>
            </div>
        </div>
    </section>

    <section id="contact" class="contact-section">
    <div class="section-header">
        <div class="section-badge">
            <i class="fas fa-envelope"></i>
            <span>Restons connectés</span>
        </div>
        <h2 class="section-title">Me Contacter</h2>
        <p class="section-description">
            Vous avez une question sur un produit, une suggestion de test, ou vous souhaitez simplement échanger ? N'hésitez pas à m'écrire. Je réponds à chaque message !
        </p>
    </div>
    
    <div class="contact-container">
        <form id="contactForm" class="contact-form" action="https://formspree.io/f/xqadlyjj" method="POST">
            <div class="form-group">
                <label for="name">Votre Nom</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="email">Votre Adresse e-mail</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="subject">Sujet</label>
                <input type="text" id="subject" name="subject" required>
            </div>
            
            <div class="form-group">
                <label for="message">Votre Message</label>
                <textarea id="message" name="message" rows="6" required></textarea>
            </div>
            
            <button type="submit" class="btn-primary">
                <span>Envoyer le message</span>
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
    </div>
</section>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-brand">
                <div class="footer-logo">
                    <i class="fas fa-microchip"></i>
                    <span>Recos Tech</span>
                </div>
                <p class="footer-description">
                    Votre guide de confiance pour les meilleures <b>recommandations tech</b>, sélectionnées avec passion et expertise.
                </p>
            </div>
            <div class="footer-links">
                <div class="footer-column">
                    <h4>Navigation Rapide</h4>
                    <a href="#home">Accueil</a>
                    <a href="#products">Produits</a>
                    <a href="#about">À propos</a>
                </div>
                <div class="footer-column">
                    <h4>Contact & Réseaux</h4>
                    <a href="mailto:Playtogetherwithus@outlook.com">Playtogetherwithus@outlook.com</a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>
                © <span id="current-year"></span> – Ried Steeve. Tous droits réservés.
            </p>
        </div>
    </footer>

    <button id="backToTop" class="back-to-top" title="Retour en haut">
        <i class="fas fa-arrow-up"></i>
    </button>
    <script src="script.js"></script>
    <script>
        // Fonction pour vider le formulaire
function clearContactForm() {
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        // La méthode reset() réinitialise tous les champs du formulaire
        contactForm.reset();
        
        // Optionnel : Afficher un message de succès
        alert('Votre message a été envoyé avec succès !');
    }
}

// Écoute l'événement submit du formulaire
document.addEventListener('DOMContentLoaded', () => {
    const contactForm = document.getElementById('contactForm');
    
    if (contactForm) {
        contactForm.addEventListener('submit', (e) => {
            // Empêche l'envoi par défaut du formulaire pour le gérer en JavaScript
            e.preventDefault();

            // Simule l'envoi avec Formspree
            // Vous devez remplacer 'votre_code_unique' par votre véritable code Formspree
            const formAction = 'https://formspree.io/f/xqadlyjj';
            
            fetch(formAction, {
                method: 'POST',
                body: new FormData(e.target),
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                    // Si l'envoi est un succès, on vide le formulaire
                    clearContactForm();
                } else {
                    // Si l'envoi échoue
                    alert('Une erreur est survenue lors de l\'envoi du message.');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur de connexion est survenue. Veuillez réessayer.');
            });
        });
    }
});
    </script>
</body>
</html>