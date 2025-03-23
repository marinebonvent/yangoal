const cartToggle = document.getElementById('cart-toggle');
const sidebarCart = document.getElementById('sidebar-cart');
const cartClose = document.getElementById('cart-close');
const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
const mobileMenuClose = document.getElementById('mobile-menu-close');
const mobileMenu = document.getElementById('mobile-menu');

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