/**
 * VitalSenior - Manejador Asíncrono del Botón SOS (Vista 4 - Dashboard)
 * Ejecuta el llamado seguro en segundo plano sin recarga de página.
 */
document.addEventListener('DOMContentLoaded', () => {
    const btnSos = document.getElementById('btn-sos');
    if (!btnSos) return;

    btnSos.addEventListener('click', async (e) => {
        e.preventDefault();

        // Evitar doble clic
        if (btnSos.disabled) return;

        const pacienteId = btnSos.dataset.pacienteId || '';
        const originalContent = btnSos.innerHTML;

        // 1. Modificar estado visual con clases Tailwind y Spinner
        btnSos.disabled = true;
        btnSos.classList.add('opacity-75', 'cursor-not-allowed', 'pointer-events-none');
        btnSos.innerHTML = `
            <span class="inline-flex items-center gap-3">
                <svg class="animate-spin h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="font-bold tracking-wide">Enviando alerta...</span>
            </span>
        `;

        try {
            // 2. Despachar petición asíncrona mediante Fetch API
            const response = await fetch('/Control-AdultoMayor/public/api/sos', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    paciente_id: pacienteId
                })
            });

            const data = await response.json();

            // 3. Mostrar feedback visual (Toast) sin recargar la página
            if (response.ok && data.success) {
                mostrarToast('success', '🚨 Alerta SOS enviada. Tus contactos de emergencia han sido notificados por WhatsApp.');
            } else {
                const errorMsg = data.error || 'No se pudo enviar la alerta. Por favor llama directamente a tus contactos de emergencia.';
                mostrarToast('error', errorMsg);
            }
        } catch (error) {
            console.error('[SOS Error]', error);
            mostrarToast('error', 'Fallo de conexión. Llama directamente a tus contactos de emergencia.');
        } finally {
            // 4. Restaurar estado del botón tras una breve pausa de seguridad
            setTimeout(() => {
                btnSos.disabled = false;
                btnSos.classList.remove('opacity-75', 'cursor-not-allowed', 'pointer-events-none');
                btnSos.innerHTML = originalContent;
            }, 3000);
        }
    });
});

/**
 * Componente Toast flotante de Tailwind CSS accesible
 */
function mostrarToast(tipo, mensaje) {
    // Eliminar toasters previos si existieran
    const toastsPrevios = document.querySelectorAll('.toast-alerta-vitalsenior');
    toastsPrevios.forEach(t => t.remove());

    const toast = document.createElement('div');
    toast.className = `toast-alerta-vitalsenior fixed bottom-6 right-6 z-50 max-w-md w-full p-4 rounded-2xl shadow-2xl border flex items-start gap-3 transition-all duration-300 transform translate-y-4 opacity-0 ${
        tipo === 'success' ? 'bg-emerald-600 text-white border-emerald-500' : 'bg-rose-600 text-white border-rose-500'
    }`;

    const icono = tipo === 'success' ? '✅' : '⚠️';

    toast.innerHTML = `
        <span class="text-2xl shrink-0">${icono}</span>
        <div class="flex-1 text-sm font-semibold leading-snug">${mensaje}</div>
        <button onclick="this.parentElement.remove()" class="text-white/80 hover:text-white text-xl font-bold ml-2 leading-none">&times;</button>
    `;

    document.body.appendChild(toast);

    // Animación de entrada
    requestAnimationFrame(() => {
        toast.classList.remove('translate-y-4', 'opacity-0');
        toast.classList.add('translate-y-0', 'opacity-100');
    });

    // Auto-cierre tras 6 segundos
    setTimeout(() => {
        toast.classList.remove('opacity-100');
        toast.classList.add('opacity-0', 'translate-y-4');
        setTimeout(() => toast.remove(), 300);
    }, 6000);
}
