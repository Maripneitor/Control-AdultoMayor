const Accessibility = {
    sizes: ['text-base', 'text-lg', 'text-xl'],
    currentIdx: 1, // Tamaño predeterminado (lg para adultos mayores)

    init() {
        const savedContrast = localStorage.getItem('senior_contrast') === 'true';
        const savedSize = parseInt(localStorage.getItem('senior_font_size'), 10) || 1;
        this.currentIdx = savedSize;

        if (savedContrast) document.documentElement.classList.add('high-contrast');
        this.applyFontSize();
    },

    changeFontSize(step) {
        if (!document.getElementById('accessible-content')) return;
        this.currentIdx = Math.max(0, Math.min(this.sizes.length - 1, this.currentIdx + step));
        localStorage.setItem('senior_font_size', this.currentIdx);
        this.applyFontSize();
    },

    applyFontSize() {
        const mainContent = document.getElementById('accessible-content');
        if (!mainContent) return;
        this.sizes.forEach(cls => mainContent.classList.remove(cls));
        mainContent.classList.add(this.sizes[this.currentIdx]);
    },

    toggleHighContrast() {
        const isHigh = document.documentElement.classList.toggle('high-contrast');
        localStorage.setItem('senior_contrast', isHigh);
    }
};

document.addEventListener('DOMContentLoaded', () => Accessibility.init());
