// Add content-hidden class to main content
document.addEventListener('DOMContentLoaded', function() {
    const mainContent = document.querySelector('main');
    if (mainContent) {
        mainContent.classList.add('content-hidden');
    }
});

// Wait for all resources to load
window.addEventListener('load', function() {
    const preloader = document.querySelector('.preloader');
    const mainContent = document.querySelector('main');

    // First make sure all CSS is applied
    setTimeout(function() {
        if (mainContent) {
            mainContent.classList.remove('content-hidden');
            mainContent.classList.add('content-visible');
        }
        
        // Then fade out preloader
        preloader.style.opacity = '0';
        setTimeout(function() {
            preloader.style.display = 'none';
        }, 300);
    }, 500);
}); 