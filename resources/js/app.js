import Alpine from 'alpinejs';
import { CountUp } from 'countup.js';

window.Alpine = Alpine;
window.CountUp = CountUp;

// ===== DARK MODE STORE =====
Alpine.store('darkMode', {
    on: localStorage.getItem('darkMode') === 'true',
    toggle() {
        this.on = !this.on;
        localStorage.setItem('darkMode', this.on);
        document.documentElement.setAttribute('data-theme', this.on ? 'dark' : 'light');
        if (this.on) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    },
    init() {
        document.documentElement.setAttribute('data-theme', this.on ? 'dark' : 'light');
        if (this.on) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }
});

// ===== SIDEBAR STORE =====
Alpine.store('sidebar', {
    open: window.innerWidth >= 1024,
    collapsed: false,
    toggle() {
        if (window.innerWidth < 1024) {
            this.open = !this.open;
        } else {
            this.collapsed = !this.collapsed;
        }
    },
    close() {
        if (window.innerWidth < 1024) {
            this.open = false;
        }
    }
});

// ===== TOAST COMPONENT =====
Alpine.data('toast', () => ({
    toasts: [],
    show(message, type = 'success', duration = 4000) {
        const id = Date.now();
        this.toasts.push({ id, message, type });
        setTimeout(() => {
            this.toasts = this.toasts.filter(t => t.id !== id);
        }, duration);
    },
    dismiss(id) {
        this.toasts = this.toasts.filter(t => t.id !== id);
    }
}));

// ===== DROPDOWN COMPONENT =====
Alpine.data('dropdown', () => ({
    open: false,
    toggle() { this.open = !this.open; },
    close() { this.open = false; }
}));

// ===== MODAL COMPONENT =====
Alpine.data('modal', () => ({
    open: false,
    show() { this.open = true; document.body.style.overflow = 'hidden'; },
    hide() { this.open = false; document.body.style.overflow = ''; }
}));

// ===== COUNTUP ANIMATION =====
Alpine.directive('countup', (el, { expression }) => {
    const target = parseFloat(expression) || 0;
    const options = {
        duration: 2,
        separator: '.',
        decimal: ',',
        prefix: el.dataset.prefix || '',
        suffix: el.dataset.suffix || '',
    };
    const counter = new CountUp(el, target, options);
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                counter.start();
                observer.unobserve(el);
            }
        });
    });
    observer.observe(el);
});

// ===== SEARCH DEBOUNCE =====
Alpine.data('searchDebounce', (callback) => ({
    query: '',
    timeout: null,
    search() {
        clearTimeout(this.timeout);
        this.timeout = setTimeout(() => {
            if (typeof callback === 'function') callback(this.query);
        }, 300);
    }
}));

// ===== INIT =====
Alpine.start();
