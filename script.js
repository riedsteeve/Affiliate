document.addEventListener('DOMContentLoaded', () => {

    // ----------------------------------------------------
    // GESTION DU MENU DE NAVIGATION MOBILE
    // ----------------------------------------------------
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.querySelector('.nav-menu');

    if (navToggle && navMenu) {
        navToggle.addEventListener('click', () => {
            navMenu.classList.toggle('open');
            // Change l'icône du bouton
            const icon = navToggle.querySelector('i');
            if (navMenu.classList.contains('open')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });

        // Ferme le menu lorsque l'on clique sur un lien (pour la navigation sur la même page)
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                navMenu.classList.remove('open');
                navToggle.querySelector('i').classList.remove('fa-times');
                navToggle.querySelector('i').classList.add('fa-bars');
            });
        });
    }

    // ----------------------------------------------------
    // GESTION DU THÈME CLAIR/SOMBRE
    // ----------------------------------------------------
    const themeToggle = document.getElementById('themeToggle');
    const themeToggleMobile = document.getElementById('themeToggleMobile');
    const body = document.body;
    const currentTheme = localStorage.getItem('theme') || 'light';

    // Applique le thème sauvegardé au chargement de la page
    body.setAttribute('data-theme', currentTheme);
    updateThemeIcon(currentTheme);

    function updateThemeIcon(theme) {
        if (theme === 'dark') {
            themeToggle.querySelector('i').classList.replace('fa-moon', 'fa-sun');
            themeToggleMobile.querySelector('i').classList.replace('fa-moon', 'fa-sun');
        } else {
            themeToggle.querySelector('i').classList.replace('fa-sun', 'fa-moon');
            themeToggleMobile.querySelector('i').classList.replace('fa-sun', 'fa-moon');
        }
    }

    function toggleTheme() {
        let newTheme = body.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        body.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateThemeIcon(newTheme);
    }

    if (themeToggle) {
        themeToggle.addEventListener('click', toggleTheme);
    }
    if (themeToggleMobile) {
        themeToggleMobile.addEventListener('click', toggleTheme);
    }

    // ----------------------------------------------------
    // GESTION DU BOUTON "RETOUR EN HAUT"
    // ----------------------------------------------------
    const backToTopButton = document.getElementById('backToTop');

    if (backToTopButton) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) { // Affiche le bouton après avoir défilé de 300px
                backToTopButton.classList.add('show');
            } else {
                backToTopButton.classList.remove('show');
            }
        });

        backToTopButton.addEventListener('click', (e) => {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // ----------------------------------------------------
    // GESTION DE LA BARRE DE RECHERCHE
    // ----------------------------------------------------
    const searchInput = document.getElementById('productSearch');
    const productsGrid = document.getElementById('productsGrid');

    if (searchInput && productsGrid) {
        searchInput.addEventListener('keyup', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            const productCards = productsGrid.querySelectorAll('.product-card');
            
            productCards.forEach(card => {
                const title = card.querySelector('.product-title').textContent.toLowerCase();
                const description = card.querySelector('.product-description').textContent.toLowerCase();
                const features = card.querySelectorAll('.feature-tag');
                let featuresText = '';
                features.forEach(tag => {
                    featuresText += tag.textContent.toLowerCase() + ' ';
                });

                // Si le terme de recherche se trouve dans le titre, la description ou les fonctionnalités
                if (title.includes(searchTerm) || description.includes(searchTerm) || featuresText.includes(searchTerm)) {
                    card.style.display = 'flex'; // Affiche la carte
                } else {
                    card.style.display = 'none'; // Cache la carte
                }
            });
        });
    }

    // ----------------------------------------------------
    // GESTION DE L'AFFICHAGE DU MESSAGE DE BIENVENUE
    // ----------------------------------------------------
    const greetingElement = document.getElementById('greeting');
    if (greetingElement) {
        const hour = new Date().getHours();
        let greeting;
        if (hour < 12) {
            greeting = 'Bonjour et bienvenue !';
        } else if (hour < 18) {
            greeting = 'Bon après-midi et bienvenue !';
        } else {
            greeting = 'Bonsoir et bienvenue !';
        }
        greetingElement.textContent = greeting;
    }

    // ----------------------------------------------------
    // MISE À JOUR DE L'ANNÉE DANS LE FOOTER
    // ----------------------------------------------------
    const currentYearElement = document.getElementById('current-year');
    if (currentYearElement) {
        currentYearElement.textContent = new Date().getFullYear();
    }
});



