document.addEventListener('DOMContentLoaded', () => {
    const productForm = document.getElementById('productForm');
    const productIdInput = document.getElementById('productId');
    const productsTableBody = document.getElementById('productsTableBody');
    const addProductBtn = document.getElementById('addProductBtn');
    const productFormSection = document.getElementById('productFormSection');
    const cancelEditBtn = document.getElementById('cancelEditBtn');

    // Theme Toggle (reusing logic from main site if styles.css is linked)
    const themeToggle = document.getElementById('themeToggle');
    const themeToggleMobile = document.getElementById('themeToggleMobile');
    const body = document.body;

    function setTheme(theme) {
        body.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);
        updateThemeToggleIcons(theme);
    }

    function updateThemeToggleIcons(theme) {
        const iconClass = theme === 'dark' ? 'fa-sun' : 'fa-moon';
        if (themeToggle) themeToggle.querySelector('i').className = `fas ${iconClass}`;
        if (themeToggleMobile) themeToggleMobile.querySelector('i').className = `fas ${iconClass}`;
    }

    const savedTheme = localStorage.getItem('theme') || 'light';
    setTheme(savedTheme);

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const currentTheme = body.getAttribute('data-theme');
            setTheme(currentTheme === 'light' ? 'dark' : 'light');
        });
    }

    if (themeToggleMobile) {
        themeToggleMobile.addEventListener('click', () => {
            const currentTheme = body.getAttribute('data-theme');
            setTheme(currentTheme === 'light' ? 'dark' : 'light');
        });
    }

    // Navigation Toggle (for mobile responsiveness)
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.querySelector('.nav-menu');

    if (navToggle && navMenu) {
        navToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            navToggle.querySelector('i').classList.toggle('fa-bars');
            navToggle.querySelector('i').classList.toggle('fa-times');
        });
    }

    // Hide form by default
    productFormSection.style.display = 'none';

    addProductBtn.addEventListener('click', () => {
        productFormSection.style.display = 'block';
        productForm.reset(); // Clear form for new product
        productIdInput.value = ''; // Ensure no ID for new product
        document.querySelector('.product-form-section h2').textContent = 'Ajouter un Nouveau Produit';
        window.scrollTo({ top: productFormSection.offsetTop - 70, behavior: 'smooth' });
    });

    cancelEditBtn.addEventListener('click', () => {
        productFormSection.style.display = 'none';
        productForm.reset();
        productIdInput.value = '';
    });

    // Function to fetch and display products
    async function fetchProducts() {
        try {
            const response = await fetch('get_products.php');
            const products = await response.json();
            productsTableBody.innerHTML = ''; // Clear existing products
            products.forEach(product => {
                const row = productsTableBody.insertRow();
                row.innerHTML = `
                    <td>${product.id}</td>
                    <td>${product.title}</td>
                    <td>${product.category}</td>
                    <td>${product.rating} <i class="fas fa-star" style="color: gold;"></i></td>
                    <td class="action-buttons">
                        <button class="btn-icon btn-edit" data-id="${product.id}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-icon btn-delete" data-id="${product.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
            });
        } catch (error) {
            console.error('Erreur lors de la récupération des produits:', error);
            alert('Impossible de charger les produits. Vérifiez la connexion à la base de données.');
        }
    }

    // Handle form submission (add/edit product)
    productForm.addEventListener('submit', async (event) => {
        event.preventDefault(); // Empêche la soumission traditionnelle du formulaire
        
        // Récupérer les données du formulaire
        const formData = new FormData(productForm);
        const productData = {};
        
        // Convertir FormData en objet
        for (let [key, value] of formData.entries()) {
            productData[key] = value;
        }

        // Convertir features string en array
        if (productData.features) {
            productData.features = productData.features.split(',').map(f => f.trim());
        }

        try {
            const response = await fetch('add_product.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(productData)
            });

            const result = await response.json();
            
            if (result.success) {
                alert(result.message);
                productForm.reset();
                productIdInput.value = '';
                productFormSection.style.display = 'none';
                fetchProducts(); // Rafraîchir la liste
            } else {
                alert('Erreur: ' + result.message);
            }
        } catch (error) {
            console.error('Erreur lors de l\'envoi du formulaire:', error);
            alert('Une erreur est survenue lors de l\'enregistrement du produit.');
        }
    });

    // Handle edit and delete buttons using event delegation
    productsTableBody.addEventListener('click', async (event) => {
        if (event.target.closest('.btn-edit')) {
            const productId = event.target.closest('.btn-edit').dataset.id;
            // Find product in the fetched list (or refetch if needed)
            try {
                const response = await fetch('get_products.php');
                const products = await response.json();
                const productToEdit = products.find(p => p.id == productId); // Use == for type coercion

                if (productToEdit) {
                    productIdInput.value = productToEdit.id;
                    document.getElementById('title').value = productToEdit.title;
                    document.getElementById('description').value = productToEdit.description;
                    document.getElementById('category').value = productToEdit.category;
                    document.getElementById('icon').value = productToEdit.icon;
                    document.getElementById('features').value = productToEdit.features.join(', ');
                    document.getElementById('rating').value = productToEdit.rating;
                    document.getElementById('amazon_link').value = productToEdit.amazon_link;

                    productFormSection.style.display = 'block';
                    document.querySelector('.product-form-section h2').textContent = 'Modifier le Produit';
                    window.scrollTo({ top: productFormSection.offsetTop - 70, behavior: 'smooth' });
                } else {
                    alert('Produit non trouvé.');
                }
            } catch (error) {
                console.error('Erreur lors de la récupération du produit pour modification:', error);
                alert('Impossible de récupérer les détails du produit.');
            }
        } else if (event.target.closest('.btn-delete')) {
            const productId = event.target.closest('.btn-delete').dataset.id;
            if (confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')) {
                try {
                    const response = await fetch('delete_product.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ id: productId })
                    });
                    const result = await response.json();
                    if (result.success) {
                        alert(result.message);
                        fetchProducts(); // Refresh the list
                    } else {
                        alert('Erreur: ' + result.message);
                    }
                } catch (error) {
                    console.error('Erreur lors de la suppression du produit:', error);
                    alert('Une erreur est survenue lors de la suppression du produit.');
                }
            }
        }
    });

    // Initial fetch of products when the page loads
    fetchProducts();
}); 