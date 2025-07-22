/**
 * Club Dadvice - Main JavaScript Application
 * Handles core functionality and progressive enhancement
 */

class DadviceApp {
    constructor() {
        this.init();
    }
    
    init() {
        this.setupEventListeners();
        this.initializeComponents();
        this.checkForUpdates();
    }
    
    setupEventListeners() {
        // Mobile menu toggle (when navigation is implemented)
        document.addEventListener('click', (e) => {
            if (e.target.matches('.mobile-menu-toggle')) {
                this.toggleMobileMenu();
            }
        });
        
        // Search functionality enhancement
        const searchInput = document.querySelector('#search-input');
        if (searchInput) {
            searchInput.addEventListener('input', this.debounce(this.handleSearch.bind(this), 300));
        }
        
        // Bookmark functionality (when user system is implemented)
        document.addEventListener('click', (e) => {
            if (e.target.matches('.bookmark-btn')) {
                e.preventDefault();
                this.toggleBookmark(e.target);
            }
        });
        
        // Share functionality
        document.addEventListener('click', (e) => {
            if (e.target.matches('.share-btn')) {
                e.preventDefault();
                this.shareArticle(e.target);
            }
        });
    }
    
    initializeComponents() {
        // Initialize any components that need JavaScript
        this.initLazyLoading();
        this.initSmoothScrolling();
        this.initFormValidation();
    }
    
    // Utility function for debouncing
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // Mobile menu toggle
    toggleMobileMenu() {
        const nav = document.querySelector('.main-nav');
        if (nav) {
            nav.classList.toggle('mobile-open');
        }
    }
    
    // Enhanced search with suggestions
    async handleSearch(event) {
        const query = event.target.value.trim();
        if (query.length < 2) {
            this.hideSuggestions();
            return;
        }
        
        try {
            // This will be implemented when search system is built
            // const suggestions = await this.fetchSearchSuggestions(query);
            // this.showSuggestions(suggestions);
        } catch (error) {
            console.error('Search error:', error);
        }
    }
    
    // Bookmark functionality
    async toggleBookmark(button) {
        if (!this.isLoggedIn()) {
            this.showLoginPrompt();
            return;
        }
        
        const articleId = button.dataset.articleId;
        const isBookmarked = button.classList.contains('bookmarked');
        
        try {
            const response = await fetch('api/bookmark.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': this.getCSRFToken()
                },
                body: JSON.stringify({
                    article_id: articleId,
                    action: isBookmarked ? 'remove' : 'add'
                })
            });
            
            if (response.ok) {
                button.classList.toggle('bookmarked');
                button.textContent = isBookmarked ? 'Bookmark' : 'Bookmarked';
                this.showNotification(isBookmarked ? 'Bookmark removed' : 'Article bookmarked');
            }
        } catch (error) {
            console.error('Bookmark error:', error);
            this.showNotification('Error updating bookmark', 'error');
        }
    }
    
    // Share functionality
    async shareArticle(button) {
        const articleTitle = button.dataset.title;
        const articleUrl = button.dataset.url || window.location.href;
        
        if (navigator.share) {
            try {
                await navigator.share({
                    title: articleTitle,
                    url: articleUrl
                });
            } catch (error) {
                if (error.name !== 'AbortError') {
                    this.fallbackShare(articleUrl);
                }
            }
        } else {
            this.fallbackShare(articleUrl);
        }
    }
    
    // Fallback share functionality
    fallbackShare(url) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(url).then(() => {
                this.showNotification('Link copied to clipboard');
            });
        } else {
            // Create temporary input for older browsers
            const tempInput = document.createElement('input');
            tempInput.value = url;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);
            this.showNotification('Link copied to clipboard');
        }
    }
    
    // Lazy loading for images
    initLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        observer.unobserve(img);
                    }
                });
            });
            
            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    }
    
    // Smooth scrolling for anchor links
    initSmoothScrolling() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }
    
    // Form validation enhancement
    initFormValidation() {
        const forms = document.querySelectorAll('form[data-validate]');
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                }
            });
        });
    }
    
    validateForm(form) {
        let isValid = true;
        const inputs = form.querySelectorAll('input[required], textarea[required]');
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                this.showFieldError(input, 'This field is required');
                isValid = false;
            } else {
                this.clearFieldError(input);
            }
        });
        
        return isValid;
    }
    
    showFieldError(input, message) {
        input.classList.add('error');
        let errorElement = input.parentNode.querySelector('.field-error');
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'field-error';
            input.parentNode.appendChild(errorElement);
        }
        errorElement.textContent = message;
    }
    
    clearFieldError(input) {
        input.classList.remove('error');
        const errorElement = input.parentNode.querySelector('.field-error');
        if (errorElement) {
            errorElement.remove();
        }
    }
    
    // Utility functions
    isLoggedIn() {
        // This will be implemented when user system is built
        return document.body.classList.contains('logged-in');
    }
    
    getCSRFToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }
    
    showLoginPrompt() {
        this.showNotification('Please log in to bookmark articles', 'info');
        // Could redirect to login page or show modal
    }
    
    showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => notification.classList.add('show'), 100);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
    
    // Check for app updates (PWA)
    checkForUpdates() {
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.addEventListener('controllerchange', () => {
                this.showNotification('App updated! Refresh to see changes.', 'info');
            });
        }
    }
}

// Initialize the app when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new DadviceApp();
});

// Export for use in other scripts
window.DadviceApp = DadviceApp;