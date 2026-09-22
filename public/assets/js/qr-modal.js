/**
 * VitalSenior - Generación Client-Side de Código QR y Manejo de Modal
 * Renderiza el QR en un elemento <canvas> mediante JavaScript para ahorrar CPU en backend.
 */

// Carga asíncrona de qrcode.min.js si no se encuentra cargada previamente
function asegurarLibreriaQRCode() {
    return new Promise((resolve, reject) => {
        if (typeof QRCode !== 'undefined') {
            resolve();
            return;
        }
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js';
        script.onload = () => resolve();
        script.onerror = () => reject(new Error('No se pudo cargar la librería QRCode.'));
        document.head.appendChild(script);
    });
}

/**
 * Abre el Modal y renderiza el QR apuntando a la Ficha de Emergencia (Vista 6)
 * @param {string} uuidPublico Identificador UUIDv4 del paciente
 * @param {string} nombrePaciente Nombre completo para la tarjeta impresa
 */
async function abrirModalQR(uuidPublico, nombrePaciente = 'Paciente VitalSenior') {
    const modal = document.getElementById('modal-qr-emergencia');
    const canvas = document.getElementById('qr-canvas');
    const labelNombre = document.getElementById('qr-paciente-nombre');

    if (!modal || !canvas) {
        console.error('Elementos del modal de QR no localizados en el DOM.');
        return;
    }

    try {
        await asegurarLibreriaQRCode();

        // 1. Construir URL pública con UUIDv4 (Vista 6)
        const baseUrl = window.location.origin + '/Control-AdultoMayor/public';
        const urlEmergencia = `${baseUrl}/emergencia?id=${encodeURIComponent(uuidPublico)}`;

        // 2. Renderizar sobre el elemento <canvas> con alto contraste
        await QRCode.toCanvas(canvas, urlEmergencia, {
            width: 240,
            margin: 2,
            color: {
                dark: '#0f172a',  // Slate 900
                light: '#ffffff'  // Fondo blanco puro
            },
            errorCorrectionLevel: 'H' // Alta tolerancia a errores por deterioro físico del papel
        });

        if (labelNombre) {
            labelNombre.textContent = nombrePaciente;
        }

        // 3. Mostrar modal con clases Tailwind
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    } catch (err) {
        console.error('[Error generación QR]', err);
        alert('Ocurrió un error al generar el código QR en el navegador.');
    }
}

/**
 * Cierra el modal de QR
 */
function cerrarModalQR() {
    const modal = document.getElementById('modal-qr-emergencia');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

/**
 * Invoca el diálogo nativo de impresión para el carnet médico
 */
function imprimirCarnetQR() {
    // Al invocar print(), los estilos de impresión aislarán el carnet
    window.print();
}
