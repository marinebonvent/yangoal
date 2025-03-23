const cartToggle = document.getElementById('cart-toggle');
const sidebarCart = document.getElementById('sidebar-cart');
const cartClose = document.getElementById('cart-close');
const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
const mobileMenuClose = document.getElementById('mobile-menu-close');
const mobileMenu = document.getElementById('mobile-menu');
console.log('wekkk');
// Cart functionality
function openCart() {
    sidebarCart.classList.remove('translate-x-full');
}

function closeCart() {
    sidebarCart.classList.add('translate-x-full');
}

cartToggle.addEventListener('click', openCart);
cartClose.addEventListener('click', closeCart);

// Close cart when clicking outside
document.addEventListener('click', (event) => {
    if (!sidebarCart.contains(event.target) && 
        !cartToggle.contains(event.target) && 
        !sidebarCart.classList.contains('translate-x-full')) {
        closeCart();
    }
});

// Mobile menu functionality
mobileMenuToggle.addEventListener('click', () => {
    mobileMenu.classList.remove('-translate-x-full');
});

mobileMenuClose.addEventListener('click', () => {
    mobileMenu.classList.add('-translate-x-full');
});

// Prevent closing when clicking inside cart
sidebarCart.addEventListener('click', (event) => {
    event.stopPropagation();
});

/// details

/**
 * Product Gallery JavaScript
 * Ce fichier gère le carrousel d'images sur la page produit
 */
document.addEventListener('DOMContentLoaded', function() {
    // Récupérer les données d'images depuis l'élément caché dans la page
    const galleryDataElement = document.getElementById('product-gallery-data');
    if (!galleryDataElement) return;
    
    // Extraire les URLs des images
    let images = [];
    try {
      images = JSON.parse(galleryDataElement.dataset.images || '[]');
    } catch (e) {
      console.error('Erreur lors du parsing des données d\'images:', e);
      return;
    }
    
    if (images.length === 0) return;
    
    // Éléments DOM
    const mainImage = document.getElementById('main-image');
    const prevButton = document.getElementById('prev-button');
    const nextButton = document.getElementById('next-button');
    const thumbnailButtons = document.querySelectorAll('.thumbnail-btn');
    
    let currentIndex = 0;
    const totalImages = images.length;
    
    /**
     * Met à jour l'image principale et les états des miniatures
     * @param {number} index - Index de l'image à afficher
     */
    function updateMainImage(index) {
      if (!mainImage || index >= images.length) return;
      
      mainImage.src = images[index];
      
      // Mettre à jour la classe active sur les miniatures
      thumbnailButtons.forEach((btn, i) => {
        if (i === index) {
          btn.classList.remove('border', 'border-gray-200');
          btn.classList.add('border-2', 'border-amber-600');
        } else {
          btn.classList.remove('border-2', 'border-amber-600');
          btn.classList.add('border', 'border-gray-200');
        }
      });
    }
    
    // Gérer les événements de clic pour les boutons précédent/suivant
    if (prevButton) {
      prevButton.addEventListener('click', function() {
        currentIndex = (currentIndex - 1 + totalImages) % totalImages;
        updateMainImage(currentIndex);
      });
    }
    
    if (nextButton) {
      nextButton.addEventListener('click', function() {
        currentIndex = (currentIndex + 1) % totalImages;
        updateMainImage(currentIndex);
      });
    }
    
    // Gérer les événements de clic pour les miniatures
    thumbnailButtons.forEach(btn => {
      btn.addEventListener('click', function() {
        const index = parseInt(this.getAttribute('data-index'));
        if (isNaN(index) || index >= images.length) return;
        
        currentIndex = index;
        updateMainImage(currentIndex);
      });
    });
    
    // Ajouter la navigation par clavier
    document.addEventListener('keydown', function(event) {
      // Seulement si nous sommes sur une page produit avec galerie
      if (!mainImage) return;
      
      if (event.key === 'ArrowLeft') {
        currentIndex = (currentIndex - 1 + totalImages) % totalImages;
        updateMainImage(currentIndex);
      } else if (event.key === 'ArrowRight') {
        currentIndex = (currentIndex + 1) % totalImages;
        updateMainImage(currentIndex);
      }
    });
    
    // Initialiser l'image principale
    updateMainImage(0);
    
    // Optionnel: Ajouter un défilement automatique
    // Décommentez le code ci-dessous si vous voulez un carrousel automatique
    /*
    const AUTO_SCROLL_INTERVAL = 5000; // 5 secondes
    
    let autoScrollInterval = setInterval(function() {
      currentIndex = (currentIndex + 1) % totalImages;
      updateMainImage(currentIndex);
    }, AUTO_SCROLL_INTERVAL);
    
    // Arrêter le défilement automatique si l'utilisateur interagit avec le carrousel
    const galleryContainer = document.getElementById('main-image-container');
    if (galleryContainer) {
      galleryContainer.addEventListener('mouseenter', function() {
        clearInterval(autoScrollInterval);
      });
      
      galleryContainer.addEventListener('mouseleave', function() {
        autoScrollInterval = setInterval(function() {
          currentIndex = (currentIndex + 1) % totalImages;
          updateMainImage(currentIndex);
        }, AUTO_SCROLL_INTERVAL);
      });
    }
    */

    function checkVoucher() {
      const voucherCode = document.getElementById('voucherInput').value;
      const resultElement = document.getElementById('resultMessage');
      
      // Remplacer 2222 par le code saisi par l'utilisateur
      resultElement.innerHTML = `Voucher ${voucherCode} not found`;
      resultElement.classList.remove('hidden');
    }  });

    //notifcation

    document.getElementById('close-notification').addEventListener('click', function() {
      document.getElementById('cart-notification').style.display = 'none';
    });