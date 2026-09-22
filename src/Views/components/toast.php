<?php
/**
 * VitalSenior - Modern Toast & In-App Notification System
 * Inspired by Emil Kowalski's UI Polish & Anti-Slop Design Principles
 * Supports both PHP server-side flash messages and Client-side JavaScript triggers
 */
$flashData = $flash ?? ($_SESSION['flash_notification'] ?? null);
if (isset($_SESSION['flash_notification'])) {
    unset($_SESSION['flash_notification']);
}
?>

<!-- Contenedor flotante de notificaciones Toasts (Fijo arriba a la derecha con alto z-index) -->
<div id="vital-toast-container" class="fixed top-5 right-5 z-[99999] flex flex-col gap-3 pointer-events-none max-w-sm w-[calc(100vw-2.5rem)] sm:w-96" aria-live="polite" aria-atomic="true"></div>

<style>
@keyframes vitalToastSlideIn {
    0% {
        opacity: 0;
        transform: translate3d(0, -16px, 0) scale(0.95);
    }
    60% {
        transform: translate3d(0, 2px, 0) scale(1.01);
    }
    100% {
        opacity: 1;
        transform: translate3d(0, 0, 0) scale(1);
    }
}

@keyframes vitalToastSlideOut {
    0% {
        opacity: 1;
        transform: translate3d(0, 0, 0) scale(1);
        max-height: 120px;
        margin-bottom: 0.75rem;
    }
    100% {
        opacity: 0;
        transform: translate3d(20px, 0, 0) scale(0.92);
        max-height: 0;
        margin-bottom: 0;
        padding-top: 0;
        padding-bottom: 0;
    }
}

.vital-toast {
    animation: vitalToastSlideIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    will-change: transform, opacity;
}

.vital-toast.hiding {
    animation: vitalToastSlideOut 0.28s cubic-bezier(0.4, 0, 1, 1) forwards;
}

.vital-toast-progress {
    transition: width linear;
}
</style>

<script>
(function() {
    // Configuración y catálogo de estilos por tipo
    const TOAST_TYPES = {
        success: {
            title: '¡Operación Exitosa!',
            badgeBg: 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20',
            barBg: 'bg-emerald-500',
            border: 'border-emerald-200/80',
            glow: 'shadow-emerald-500/10',
            icon: `<svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`
        },
        danger: {
            title: 'Atención / Error',
            badgeBg: 'bg-rose-500/10 text-rose-600 border-rose-500/20',
            barBg: 'bg-rose-500',
            border: 'border-rose-200/80',
            glow: 'shadow-rose-500/10',
            icon: `<svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`
        },
        error: {
            title: 'Atención / Error',
            badgeBg: 'bg-rose-500/10 text-rose-600 border-rose-500/20',
            barBg: 'bg-rose-500',
            border: 'border-rose-200/80',
            glow: 'shadow-rose-500/10',
            icon: `<svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`
        },
        warning: {
            title: 'Aviso Importante',
            badgeBg: 'bg-amber-500/10 text-amber-600 border-amber-500/20',
            barBg: 'bg-amber-500',
            border: 'border-amber-200/80',
            glow: 'shadow-amber-500/10',
            icon: `<svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`
        },
        info: {
            title: 'Información',
            badgeBg: 'bg-blue-500/10 text-[#004ac6] border-blue-500/20',
            barBg: 'bg-[#004ac6]',
            border: 'border-blue-200/80',
            glow: 'shadow-blue-500/10',
            icon: `<svg class="w-5 h-5 text-[#004ac6]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`
        }
    };

    window.VitalToast = {
        show: function({ type = 'info', title = '', message = '', duration = 5000 }) {
            const container = document.getElementById('vital-toast-container');
            if (!container) return;

            const safeType = TOAST_TYPES[type] ? type : 'info';
            const config = TOAST_TYPES[safeType];
            const displayTitle = title || config.title;

            // Crear elemento de Toast
            const toast = document.createElement('div');
            toast.className = `vital-toast pointer-events-auto relative overflow-hidden bg-white/95 backdrop-blur-md rounded-2xl p-4 border ${config.border} shadow-xl ${config.glow} flex items-start gap-3.5 select-none transition-all duration-200 hover:shadow-2xl hover:scale-[1.01]`;
            toast.setAttribute('role', safeType === 'danger' || safeType === 'error' ? 'alert' : 'status');

            toast.innerHTML = `
                <div class="flex-shrink-0 w-10 h-10 rounded-xl ${config.badgeBg} border flex items-center justify-center">
                    ${config.icon}
                </div>
                <div class="flex-1 pr-2 min-w-0">
                    <h4 class="text-sm font-bold text-slate-900 leading-snug tracking-tight mb-0.5">${escapeHtml(displayTitle)}</h4>
                    <p class="text-xs text-slate-600 leading-relaxed break-words font-medium">${escapeHtml(message)}</p>
                </div>
                <button type="button" class="flex-shrink-0 text-slate-400 hover:text-slate-700 hover:bg-slate-100 p-1 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-slate-300" aria-label="Cerrar notificación">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                <div class="vital-toast-progress absolute bottom-0 left-0 h-1 ${config.barBg} opacity-80" style="width: 100%;"></div>
            `;

            container.appendChild(toast);

            const closeBtn = toast.querySelector('button');
            const progressBar = toast.querySelector('.vital-toast-progress');
            
            let startTime = Date.now();
            let remaining = duration;
            let timerId = null;
            let progressInterval = null;

            function dismiss() {
                if (toast.classList.contains('hiding')) return;
                toast.classList.add('hiding');
                clearInterval(progressInterval);
                clearTimeout(timerId);
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            }

            function startTimer() {
                startTime = Date.now();
                if (duration > 0) {
                    progressBar.style.transition = `width ${remaining}ms linear`;
                    progressBar.style.width = '0%';
                    timerId = setTimeout(dismiss, remaining);
                }
            }

            function pauseTimer() {
                if (duration > 0) {
                    clearTimeout(timerId);
                    const elapsed = Date.now() - startTime;
                    remaining = Math.max(0, remaining - elapsed);
                    const computedWidth = getComputedStyle(progressBar).width;
                    progressBar.style.transition = 'none';
                    progressBar.style.width = computedWidth;
                }
            }

            toast.addEventListener('mouseenter', pauseTimer);
            toast.addEventListener('mouseleave', startTimer);
            closeBtn.addEventListener('click', dismiss);

            // Iniciar cuenta regresiva
            startTimer();
        },

        success: function(message, title = '¡Éxito!') {
            this.show({ type: 'success', title, message });
        },
        error: function(message, title = 'Error') {
            this.show({ type: 'danger', title, message });
        },
        danger: function(message, title = 'Error') {
            this.show({ type: 'danger', title, message });
        },
        warning: function(message, title = 'Aviso') {
            this.show({ type: 'warning', title, message });
        },
        info: function(message, title = 'Información') {
            this.show({ type: 'info', title, message });
        }
    };

    // Alias global para compatibilidad simple
    window.notify = function(message, type = 'info', title = '') {
        window.VitalToast.show({ message, type, title });
    };

    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // Inicializar Flash Message de PHP en caso de existir
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (!empty($flashData) && !empty($flashData['message'])): ?>
            const flashType = <?= json_encode($flashData['type'] ?? 'info'); ?>;
            const flashMsg = <?= json_encode($flashData['message']); ?>;
            const flashTitle = <?= json_encode($flashData['title'] ?? ''); ?>;
            setTimeout(() => {
                window.VitalToast.show({
                    type: flashType,
                    title: flashTitle,
                    message: flashMsg,
                    duration: 6000
                });
            }, 150);
        <?php endif; ?>
    });
})();
</script>
