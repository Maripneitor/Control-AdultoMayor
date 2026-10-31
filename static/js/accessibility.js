const Accessibility = {
    sizes: ['text-base', 'text-lg', 'text-xl'],
    currentIdx: 1, // Tamaño predeterminado en memoria (Zero Trust: sin usar localStorage/sessionStorage)

    init() {
        // Zero Trust: Purgar cualquier dato residual de Web Storage del navegador
        try {
            window.localStorage.clear();
            window.sessionStorage.clear();
        } catch (_) {}
        this.applyFontSize();
    },

    changeFontSize(step) {
        if (!document.getElementById('accessible-content')) return;
        this.currentIdx = Math.max(0, Math.min(this.sizes.length - 1, this.currentIdx + step));
        this.applyFontSize();
    },

    applyFontSize() {
        const mainContent = document.getElementById('accessible-content');
        if (!mainContent) return;
        this.sizes.forEach(cls => mainContent.classList.remove(cls));
        mainContent.classList.add(this.sizes[this.currentIdx]);
    },

    toggleHighContrast() {
        document.documentElement.classList.toggle('high-contrast');
    }
};

document.addEventListener('DOMContentLoaded', () => Accessibility.init());
