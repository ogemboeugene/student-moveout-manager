// Modern Professional JavaScript for Student Move-Out Manager

// Enhanced CSRF Token Management
function getCSRFToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

// Advanced AJAX Helper with better error handling
class AjaxHelper {
    static async post(url, data = {}) {
        data.csrf_token = getCSRFToken();
        
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(data)
            });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const result = await response.json();
            return result;
        } catch (error) {
            console.error('AJAX POST Error:', error);
            throw error;
        }
    }
    
    static async get(url, params = {}) {
        const urlParams = new URLSearchParams(params);
        const fullUrl = urlParams.toString() ? `${url}?${urlParams}` : url;
        
        try {
            const response = await fetch(fullUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            return await response.json();
        } catch (error) {
            console.error('AJAX GET Error:', error);
            throw error;
        }
    }
}

// Professional Toast Notification System
class NotificationManager {
    static container = null;
    
    static init() {
        if (!this.container) {
            this.container = document.createElement('div');
            this.container.className = 'toast-container';
            this.container.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                max-width: 400px;
            `;
            document.body.appendChild(this.container);
        }
    }
    
    static show(message, type = 'info', duration = 5000, title = null) {
        this.init();
        
        const toast = document.createElement('div');
        const toastId = 'toast-' + Date.now();
        
        const icons = {
            success: '✅',
            error: '❌',
            warning: '⚠️',
            info: 'ℹ️'
        };
        
        toast.className = `alert alert-${type === 'error' ? 'danger' : type} toast-notification fade-in`;
        toast.id = toastId;
        toast.style.cssText = `
            margin-bottom: 10px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
            border: none;
            border-left: 4px solid var(--${type === 'error' ? 'danger' : type}-color);
            backdrop-filter: blur(20px);
            animation: slideInRight 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        `;
        
        toast.innerHTML = `
            <div class="d-flex align-items-start">
                <div class="me-3" style="font-size: 1.2rem;">${icons[type] || icons.info}</div>
                <div class="flex-grow-1">
                    ${title ? `<div class="fw-bold mb-1">${title}</div>` : ''}
                    <div>${message}</div>
                </div>
                <button type="button" class="btn-close ms-2" onclick="NotificationManager.remove('${toastId}')"></button>
            </div>
        `;
        
        this.container.appendChild(toast);
        
        // Auto-remove after duration
        if (duration > 0) {
            setTimeout(() => this.remove(toastId), duration);
        }
        
        // Add hover to pause auto-removal
        let autoRemoveTimer;
        if (duration > 0) {
            const startTimer = () => {
                autoRemoveTimer = setTimeout(() => this.remove(toastId), duration);
            };
            
            toast.addEventListener('mouseenter', () => clearTimeout(autoRemoveTimer));
            toast.addEventListener('mouseleave', startTimer);
            startTimer();
        }
    }
    
    static remove(toastId) {
        const toast = document.getElementById(toastId);
        if (toast) {
            toast.style.animation = 'slideOutRight 0.3s ease-in';
            setTimeout(() => toast.remove(), 300);
        }
    }
    
    static clear() {
        if (this.container) {
            this.container.innerHTML = '';
        }
    }
    
    static success(message, title = 'Success', duration = 4000) {
        this.show(message, 'success', duration, title);
    }
    
    static error(message, title = 'Error', duration = 7000) {
        this.show(message, 'error', duration, title);
    }
    
    static warning(message, title = 'Warning', duration = 6000) {
        this.show(message, 'warning', duration, title);
    }
    
    static info(message, title = null, duration = 5000) {
        this.show(message, 'info', duration, title);
    }
}

// Advanced Form Validation with Real-time Feedback
class FormValidator {
    static rules = {
        required: (value, params) => {
            return value && value.trim() !== '' ? null : `This field is required.`;
        },
        minLength: (value, params) => {
            const min = parseInt(params);
            return value && value.length >= min ? null : `Must be at least ${min} characters.`;
        },
        maxLength: (value, params) => {
            const max = parseInt(params);
            return value && value.length <= max ? null : `Must not exceed ${max} characters.`;
        },
        email: (value) => {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(value) ? null : 'Please enter a valid email address.';
        },
        numeric: (value) => {
            return /^\d+$/.test(value) ? null : 'Please enter numbers only.';
        },
        alpha: (value) => {
            return /^[a-zA-Z\s]+$/.test(value) ? null : 'Please enter letters only.';
        }
    };
    
    static validateField(input) {
        const errors = [];
        const value = input.value;
        const validationRules = input.dataset.validate ? input.dataset.validate.split('|') : [];
        
        // Check required first
        if (input.hasAttribute('required') && (!value || value.trim() === '')) {
            errors.push('This field is required.');
            this.showFieldError(input, errors[0]);
            return false;
        }
        
        // If field is empty and not required, skip other validations
        if (!value || value.trim() === '') {
            this.clearFieldError(input);
            return true;
        }
        
        // Apply validation rules
        validationRules.forEach(rule => {
            const [ruleName, params] = rule.split(':');
            if (this.rules[ruleName]) {
                const error = this.rules[ruleName](value, params);
                if (error) errors.push(error);
            }
        });
        
        if (errors.length > 0) {
            this.showFieldError(input, errors[0]);
            return false;
        } else {
            this.showFieldSuccess(input);
            return true;
        }
    }
    
    static showFieldError(input, message) {
        this.clearFieldFeedback(input);
        
        input.classList.add('is-invalid');
        input.classList.remove('is-valid');
        
        const feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        feedback.textContent = message;
        feedback.style.display = 'block';
        
        input.parentNode.appendChild(feedback);
    }
    
    static showFieldSuccess(input) {
        this.clearFieldFeedback(input);
        
        input.classList.add('is-valid');
        input.classList.remove('is-invalid');
    }
    
    static clearFieldError(input) {
        input.classList.remove('is-invalid', 'is-valid');
        this.clearFieldFeedback(input);
    }
    
    static clearFieldFeedback(input) {
        const feedback = input.parentNode.querySelector('.invalid-feedback, .valid-feedback');
        if (feedback) feedback.remove();
    }
    
    static validateForm(form) {
        const inputs = form.querySelectorAll('input, select, textarea');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });
        
        return isValid;
    }
}

// Enhanced Loading Manager with Different States
class LoadingManager {
    static show(element, options = {}) {
        const {
            text = 'Loading...',
            spinner = true,
            disable = true,
            size = 'normal'
        } = options;
        
        if (typeof element === 'string') {
            element = document.querySelector(element);
        }
        
        if (!element) return;
        
        // Store original state
        element.dataset.originalContent = element.innerHTML;
        element.dataset.originalDisabled = element.disabled;
        
        if (disable) element.disabled = true;
        
        const spinnerSize = size === 'small' ? '16' : '20';
        const spinnerHtml = spinner ? `
            <svg class="spinner me-2" width="${spinnerSize}" height="${spinnerSize}" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none" 
                        stroke-dasharray="32" stroke-dashoffset="32" opacity="0.3"/>
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none" 
                        stroke-dasharray="32" stroke-dashoffset="24" 
                        style="animation: spin 1s linear infinite;"/>
            </svg>
        ` : '';
        
        element.innerHTML = `${spinnerHtml}${text}`;
    }
    
    static hide(element) {
        if (typeof element === 'string') {
            element = document.querySelector(element);
        }
        
        if (!element) return;
        
        // Restore original state
        if (element.dataset.originalContent) {
            element.innerHTML = element.dataset.originalContent;
            delete element.dataset.originalContent;
        }
        
        if (element.dataset.originalDisabled !== undefined) {
            element.disabled = element.dataset.originalDisabled === 'true';
            delete element.dataset.originalDisabled;
        }
    }
    
    static showOverlay(message = 'Loading...') {
        const overlay = document.createElement('div');
        overlay.id = 'loading-overlay';
        overlay.className = 'loading-overlay fade-in';
        overlay.innerHTML = `
            <div class="text-center">
                <div class="spinner-large mb-3"></div>
                <div class="h5 text-white">${message}</div>
            </div>
        `;
        
        document.body.appendChild(overlay);
        document.body.style.overflow = 'hidden';
    }
    
    static hideOverlay() {
        const overlay = document.getElementById('loading-overlay');
        if (overlay) {
            overlay.remove();
            document.body.style.overflow = '';
        }
    }
}

// Modern Confirmation Dialog
class ConfirmDialog {
    static show(options = {}) {
        const {
            title = 'Confirm Action',
            message = 'Are you sure?',
            confirmText = 'Confirm',
            cancelText = 'Cancel',
            type = 'danger',
            onConfirm = () => {},
            onCancel = () => {}
        } = options;
        
        return new Promise((resolve) => {
            const modal = document.createElement('div');
            modal.className = 'modal fade';
            modal.id = 'confirmModal';
            modal.innerHTML = `
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header border-0">
                            <h5 class="modal-title fw-bold">${title}</h5>
                        </div>
                        <div class="modal-body py-4">
                            <p class="mb-0">${message}</p>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                ${cancelText}
                            </button>
                            <button type="button" class="btn btn-${type} confirm-btn">
                                ${confirmText}
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            
            const confirmBtn = modal.querySelector('.confirm-btn');
            const modalInstance = new bootstrap.Modal(modal);
            
            confirmBtn.addEventListener('click', () => {
                onConfirm();
                resolve(true);
                modalInstance.hide();
            });
            
            modal.addEventListener('hidden.bs.modal', () => {
                if (!modal.dataset.confirmed) {
                    onCancel();
                    resolve(false);
                }
                modal.remove();
            });
            
            modalInstance.show();
        });
    }
}

// Enhanced Time Formatting
class TimeFormatter {
    static formatElapsed(seconds) {
        if (seconds < 60) {
            return `${seconds}s ago`;
        }
        
        const minutes = Math.floor(seconds / 60);
        if (minutes < 60) {
            return `${minutes}m ago`;
        }
        
        const hours = Math.floor(minutes / 60);
        if (hours < 24) {
            return `${hours}h ago`;
        }
        
        const days = Math.floor(hours / 24);
        return `${days}d ago`;
    }
    
    static formatDuration(seconds) {
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = seconds % 60;
        return `${minutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`;
    }
    
    static formatTimestamp(timestamp, options = {}) {
        const date = new Date(timestamp);
        const {
            dateStyle = 'medium',
            timeStyle = 'short',
            relative = false
        } = options;
        
        if (relative) {
            const now = new Date();
            const diffMs = now - date;
            const diffSeconds = Math.floor(diffMs / 1000);
            
            if (diffSeconds < 60) return 'Just now';
            if (diffSeconds < 3600) return this.formatElapsed(diffSeconds);
            if (diffSeconds < 86400) return this.formatElapsed(diffSeconds);
        }
        
        return date.toLocaleString(undefined, { dateStyle, timeStyle });
    }
    
    static getRemainingTime(startTime, durationMinutes = 5) {
        const start = new Date(startTime);
        const now = new Date();
        const elapsed = Math.floor((now - start) / 1000);
        const total = durationMinutes * 60;
        const remaining = total - elapsed;
        
        return Math.max(0, remaining);
    }
    
    static formatCountdown(seconds) {
        if (seconds <= 0) return '00:00';
        
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = seconds % 60;
        
        return `${minutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`;
    }
}

// Smart Auto-refresh with Visibility API
class AutoRefresh {
    constructor(callback, interval = 10000) {
        this.callback = callback;
        this.interval = interval;
        this.timer = null;
        this.isActive = false;
        this.failureCount = 0;
        this.maxFailures = 3;
        
        // Handle visibility changes
        document.addEventListener('visibilitychange', () => {
            if (this.isActive) {
                if (document.hidden) {
                    this.pause();
                } else {
                    this.resume();
                }
            }
        });
    }
    
    async start() {
        if (this.isActive) return;
        
        this.isActive = true;
        this.failureCount = 0;
        
        const runCallback = async () => {
            if (!this.isActive) return;
            
            try {
                await this.callback();
                this.failureCount = 0;
                
                // Schedule next run
                this.timer = setTimeout(runCallback, this.interval);
            } catch (error) {
                console.error('AutoRefresh callback error:', error);
                this.failureCount++;
                
                if (this.failureCount >= this.maxFailures) {
                    NotificationManager.warning(
                        'Auto-refresh temporarily paused due to connection issues.',
                        'Connection Warning'
                    );
                    this.stop();
                } else {
                    // Retry with exponential backoff
                    const backoffDelay = this.interval * Math.pow(2, this.failureCount - 1);
                    this.timer = setTimeout(runCallback, backoffDelay);
                }
            }
        };
        
        // Run immediately
        await runCallback();
    }
    
    stop() {
        if (this.timer) {
            clearTimeout(this.timer);
            this.timer = null;
        }
        this.isActive = false;
    }
    
    pause() {
        if (this.timer) {
            clearTimeout(this.timer);
            this.timer = null;
        }
    }
    
    resume() {
        if (this.isActive && !this.timer) {
            this.start();
        }
    }
    
    restart() {
        this.stop();
        this.start();
    }
}

// Enhanced Theme Manager
class ThemeManager {
    static themes = {
        light: { name: 'Light', icon: '🌙' },
        dark: { name: 'Dark', icon: '☀️' },
        auto: { name: 'Auto', icon: '🌓' }
    };
    
    static init() {
        const savedTheme = localStorage.getItem('theme') || 'auto';
        this.setTheme(savedTheme);
        this.addToggleButton();
        
        // Listen for system theme changes
        if (window.matchMedia) {
            window.matchMedia('(prefers-color-scheme: dark)').addListener(() => {
                if (this.getCurrentTheme() === 'auto') {
                    this.applyTheme();
                }
            });
        }
    }
    
    static getCurrentTheme() {
        return localStorage.getItem('theme') || 'auto';
    }
    
    static setTheme(theme) {
        localStorage.setItem('theme', theme);
        this.applyTheme();
        this.updateToggleButton();
    }
    
    static applyTheme() {
        const theme = this.getCurrentTheme();
        const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        const shouldBeDark = theme === 'dark' || (theme === 'auto' && prefersDark);
        
        document.body.classList.toggle('dark-mode', shouldBeDark);
    }
    
    static cycleTheme() {
        const themes = Object.keys(this.themes);
        const currentTheme = this.getCurrentTheme();
        const currentIndex = themes.indexOf(currentTheme);
        const nextIndex = (currentIndex + 1) % themes.length;
        
        this.setTheme(themes[nextIndex]);
    }
    
    static addToggleButton() {
        const button = document.createElement('button');
        button.className = 'theme-toggle';
        button.title = 'Toggle Theme';
        button.addEventListener('click', () => this.cycleTheme());
        
        document.body.appendChild(button);
        this.updateToggleButton();
    }
    
    static updateToggleButton() {
        const button = document.querySelector('.theme-toggle');
        if (button) {
            const currentTheme = this.getCurrentTheme();
            button.innerHTML = this.themes[currentTheme].icon;
            button.title = `Current: ${this.themes[currentTheme].name} - Click to change`;
        }
    }
}

// Advanced Search with Debouncing and Highlighting
class SearchManager {
    constructor(searchInput, resultsContainer, searchFunction, options = {}) {
        this.searchInput = typeof searchInput === 'string' ? document.querySelector(searchInput) : searchInput;
        this.resultsContainer = typeof resultsContainer === 'string' ? document.querySelector(resultsContainer) : resultsContainer;
        this.searchFunction = searchFunction;
        
        this.options = {
            debounceDelay: 300,
            minChars: 2,
            showNoResults: true,
            noResultsMessage: 'No results found',
            ...options
        };
        
        this.debounceTimer = null;
        this.isSearching = false;
        
        this.init();
    }
    
    init() {
        if (!this.searchInput) return;
        
        this.searchInput.addEventListener('input', (e) => {
            this.handleSearch(e.target.value);
        });
        
        this.searchInput.addEventListener('focus', () => {
            this.searchInput.classList.add('searching');
        });
        
        this.searchInput.addEventListener('blur', () => {
            setTimeout(() => {
                this.searchInput.classList.remove('searching');
            }, 200);
        });
    }
    
    handleSearch(query) {
        clearTimeout(this.debounceTimer);
        
        if (query.length < this.options.minChars) {
            this.clearResults();
            return;
        }
        
        this.showSearching();
        
        this.debounceTimer = setTimeout(async () => {
            try {
                this.isSearching = true;
                const results = await this.searchFunction(query);
                this.displayResults(results, query);
            } catch (error) {
                console.error('Search error:', error);
                this.showError('Search failed. Please try again.');
            } finally {
                this.isSearching = false;
                this.hideSearching();
            }
        }, this.options.debounceDelay);
    }
    
    showSearching() {
        if (this.resultsContainer) {
            this.resultsContainer.innerHTML = '<div class="text-center p-3"><div class="spinner"></div></div>';
        }
    }
    
    hideSearching() {
        // Results will be replaced by displayResults or clearResults
    }
    
    displayResults(results, query) {
        if (!this.resultsContainer) return;
        
        if (results.length === 0) {
            if (this.options.showNoResults) {
                this.resultsContainer.innerHTML = `
                    <div class="text-center p-4 text-muted">
                        <i class="fas fa-search fa-2x mb-2"></i>
                        <div>${this.options.noResultsMessage}</div>
                    </div>
                `;
            } else {
                this.clearResults();
            }
            return;
        }
        
        // Render results (implementation depends on specific needs)
        this.renderResults(results, query);
    }
    
    renderResults(results, query) {
        // Override this method in specific implementations
        this.resultsContainer.innerHTML = results.map(result => 
            `<div class="search-result">${this.highlightText(result.toString(), query)}</div>`
        ).join('');
    }
    
    highlightText(text, query) {
        if (!query) return text;
        
        const regex = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
        return text.replace(regex, '<mark>$1</mark>');
    }
    
    clearResults() {
        if (this.resultsContainer) {
            this.resultsContainer.innerHTML = '';
        }
    }
    
    showError(message) {
        if (this.resultsContainer) {
            this.resultsContainer.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    ${message}
                </div>
            `;
        }
    }
}

// Initialize everything on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    // Initialize theme management
    ThemeManager.init();
    
    // Initialize notification system
    NotificationManager.init();
    
    // Enhanced form validation
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        // Real-time validation
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', () => FormValidator.validateField(input));
            input.addEventListener('input', () => {
                if (input.classList.contains('is-invalid')) {
                    FormValidator.validateField(input);
                }
            });
        });
        
        // Form submission validation
        form.addEventListener('submit', (e) => {
            if (!FormValidator.validateForm(form)) {
                e.preventDefault();
                NotificationManager.error('Please correct the errors below.', 'Validation Error');
            }
        });
    });
    
    // Auto-hide temporary alerts
    const alerts = document.querySelectorAll('.alert:not(.alert-persistent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.3s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
    
    // Add smooth scrolling to anchor links
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
    
    // Add ripple effect to buttons
    document.querySelectorAll('.btn').forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                background: rgba(255, 255, 255, 0.3);
                border-radius: 50%;
                transform: scale(0);
                animation: ripple 0.6s ease-out;
                pointer-events: none;
            `;
            
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            
            setTimeout(() => ripple.remove(), 600);
        });
    });
});

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    
    @keyframes ripple {
        to { transform: scale(4); opacity: 0; }
    }
    
    .spinner-large {
        width: 3rem;
        height: 3rem;
        border: 4px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: white;
        animation: spin 1s linear infinite;
    }
`;
document.head.appendChild(style);

// Export for global use
window.AjaxHelper = AjaxHelper;
window.NotificationManager = NotificationManager;
window.FormValidator = FormValidator;
window.LoadingManager = LoadingManager;
window.ConfirmDialog = ConfirmDialog;
window.TimeFormatter = TimeFormatter;
window.AutoRefresh = AutoRefresh;
window.ThemeManager = ThemeManager;
window.SearchManager = SearchManager;
