// Amar Cell Service - Main JavaScript File

// Global configuration
const CONFIG = {
    baseUrl: window.location.origin + '/amarcell/',
    apiUrl: window.location.origin + '/amarcell/api/',
    timeout: 5000
};

// Utility functions
const Utils = {
    // Format currency to Indonesian Rupiah
    formatRupiah: function(angka) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(angka);
    },

    // Format date to Indonesian format
    formatDate: function(date) {
        return new Intl.DateTimeFormat('id-ID', {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        }).format(new Date(date));
    },

    // Debounce function
    debounce: function(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    // Show loading spinner
    showLoading: function(element) {
        element.innerHTML = '<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
    },

    // Show error message
    showError: function(message, container = null) {
        const errorHtml = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        if (container) {
            container.innerHTML = errorHtml;
        } else {
            const alertContainer = document.getElementById('alertContainer') || document.body;
            alertContainer.insertAdjacentHTML('afterbegin', errorHtml);
        }
    },

    // Show success message
    showSuccess: function(message, container = null) {
        const successHtml = `
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        if (container) {
            container.innerHTML = successHtml;
        } else {
            const alertContainer = document.getElementById('alertContainer') || document.body;
            alertContainer.insertAdjacentHTML('afterbegin', successHtml);
        }
    },

    // Auto-hide alerts after 5 seconds
    autoHideAlerts: function() {
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    }
};

// Form validation
const FormValidator = {
    // Validate email format
    isValidEmail: function(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    },

    // Validate phone number (Indonesian format)
    isValidPhone: function(phone) {
        const phoneRegex = /^08[0-9]{8,11}$/;
        return phoneRegex.test(phone);
    },

    // Validate required fields
    validateRequired: function(fields) {
        let isValid = true;
        fields.forEach(field => {
            const element = document.getElementById(field);
            if (!element.value.trim()) {
                this.showFieldError(element, 'Field ini wajib diisi');
                isValid = false;
            } else {
                this.clearFieldError(element);
            }
        });
        return isValid;
    },

    // Show field error
    showFieldError: function(element, message) {
        element.classList.add('is-invalid');
        
        // Remove existing error message
        const existingError = element.parentNode.querySelector('.invalid-feedback');
        if (existingError) {
            existingError.remove();
        }
        
        // Add error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = message;
        element.parentNode.appendChild(errorDiv);
    },

    // Clear field error
    clearFieldError: function(element) {
        element.classList.remove('is-invalid');
        const errorMessage = element.parentNode.querySelector('.invalid-feedback');
        if (errorMessage) {
            errorMessage.remove();
        }
    }
};

// API Helper
const API = {
    // Make API request
    request: async function(endpoint, options = {}) {
        const defaultOptions = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            timeout: CONFIG.timeout
        };

        const config = { ...defaultOptions, ...options };
        
        try {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), config.timeout);
            
            const response = await fetch(CONFIG.apiUrl + endpoint, {
                ...config,
                signal: controller.signal
            });
            
            clearTimeout(timeoutId);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return await response.json();
        } catch (error) {
            console.error('API request failed:', error);
            throw error;
        }
    },

    // POST request
    post: function(endpoint, data) {
        return this.request(endpoint, {
            method: 'POST',
            body: JSON.stringify(data)
        });
    },

    // GET request
    get: function(endpoint, params = {}) {
        const url = new URL(CONFIG.apiUrl + endpoint);
        Object.keys(params).forEach(key => url.searchParams.append(key, params[key]));
        return this.request(url.toString().replace(CONFIG.apiUrl, ''));
    }
};

// Animation helpers
const Animation = {
    // Fade in element
    fadeIn: function(element, duration = 300) {
        element.style.opacity = '0';
        element.style.display = 'block';
        
        let opacity = 0;
        const interval = 10;
        const increment = interval / duration;
        
        const timer = setInterval(() => {
            opacity += increment;
            element.style.opacity = opacity;
            
            if (opacity >= 1) {
                clearInterval(timer);
                element.style.opacity = '1';
            }
        }, interval);
    },

    // Fade out element
    fadeOut: function(element, duration = 300) {
        let opacity = 1;
        const interval = 10;
        const decrement = interval / duration;
        
        const timer = setInterval(() => {
            opacity -= decrement;
            element.style.opacity = opacity;
            
            if (opacity <= 0) {
                clearInterval(timer);
                element.style.display = 'none';
                element.style.opacity = '1';
            }
        }, interval);
    },

    // Slide up element
    slideUp: function(element, duration = 300) {
        const height = element.offsetHeight;
        let currentHeight = height;
        const interval = 10;
        const decrement = (height / (duration / interval));
        
        element.style.overflow = 'hidden';
        
        const timer = setInterval(() => {
            currentHeight -= decrement;
            element.style.height = currentHeight + 'px';
            
            if (currentHeight <= 0) {
                clearInterval(timer);
                element.style.display = 'none';
                element.style.height = 'auto';
                element.style.overflow = 'visible';
            }
        }, interval);
    },

    // Slide down element
    slideDown: function(element, duration = 300) {
        element.style.display = 'block';
        const height = element.offsetHeight;
        element.style.height = '0px';
        element.style.overflow = 'hidden';
        
        let currentHeight = 0;
        const interval = 10;
        const increment = (height / (duration / interval));
        
        const timer = setInterval(() => {
            currentHeight += increment;
            element.style.height = currentHeight + 'px';
            
            if (currentHeight >= height) {
                clearInterval(timer);
                element.style.height = 'auto';
                element.style.overflow = 'visible';
            }
        }, interval);
    }
};

// Local Storage helper
const Storage = {
    // Set item with expiration
    setItem: function(key, value, expirationMinutes = null) {
        const item = {
            value: value,
            timestamp: new Date().getTime()
        };
        
        if (expirationMinutes) {
            item.expiration = expirationMinutes * 60 * 1000;
        }
        
        localStorage.setItem(key, JSON.stringify(item));
    },

    // Get item with expiration check
    getItem: function(key) {
        const itemStr = localStorage.getItem(key);
        if (!itemStr) return null;
        
        try {
            const item = JSON.parse(itemStr);
            
            // Check expiration
            if (item.expiration && (new Date().getTime() - item.timestamp) > item.expiration) {
                localStorage.removeItem(key);
                return null;
            }
            
            return item.value;
        } catch (e) {
            localStorage.removeItem(key);
            return null;
        }
    },

    // Remove item
    removeItem: function(key) {
        localStorage.removeItem(key);
    },

    // Clear all storage
    clear: function() {
        localStorage.clear();
    }
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts
    Utils.autoHideAlerts();
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // Smooth scroll for anchor links
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
    
    // Add loading state to forms
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Loading...';
            }
        });
    });
});

// Export utilities for global use
window.Utils = Utils;
window.FormValidator = FormValidator;
window.API = API;
window.Animation = Animation;
window.Storage = Storage;