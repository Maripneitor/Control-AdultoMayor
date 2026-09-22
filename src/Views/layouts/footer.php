</main>
<!-- End Main Content Area -->

<!-- 7. Footer -->
<footer class="bg-[#0b1b3d] text-slate-300 border-t border-blue-900/60 pt-16">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-10 w-full px-4 md:px-8 max-w-7xl mx-auto">
        <!-- Brand -->
        <div class="flex flex-col gap-4 md:col-span-2">
            <a class="flex items-center gap-2.5 text-2xl font-bold text-white tracking-tight" href="<?php echo APP_URL; ?>/">
                <div class="w-9 h-9 rounded-xl bg-[#004ac6] flex items-center justify-center text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </div>
                <span>Vital<span class="text-blue-400">Senior</span></span>
            </a>
            <p class="text-sm text-slate-400 max-w-md leading-relaxed mt-1">
                Plataforma integral para la gestión de salud y cuidado de adultos mayores. Tu aliado de confianza para coordinar medicamentos, citas médicas y emergencias familiares.
            </p>
        </div>
        <!-- Links -->
        <div class="flex flex-col gap-3">
            <h4 class="font-bold text-white text-sm uppercase tracking-wider mb-1">Legal y Seguridad</h4>
            <a class="text-sm text-slate-400 hover:text-white transition-colors" href="#">Política de Privacidad</a>
            <a class="text-sm text-slate-400 hover:text-white transition-colors" href="#">Protección de Datos Médicos</a>
            <a class="text-sm text-slate-400 hover:text-white transition-colors" href="#">Términos y Condiciones</a>
        </div>
        <div class="flex flex-col gap-3">
            <h4 class="font-bold text-white text-sm uppercase tracking-wider mb-1">Soporte y Contacto</h4>
            <a class="text-sm text-slate-400 hover:text-white transition-colors" href="#">Centro de Ayuda</a>
            <a class="text-sm text-slate-400 hover:text-white transition-colors" href="#">Atención a Cuidadores</a>
            <a class="text-sm text-slate-400 hover:text-white transition-colors" href="#">Línea de Asistencia</a>
        </div>
    </div>
    <!-- Copyright -->
    <div class="border-t border-slate-800/80 mt-12 py-6 text-center text-xs text-slate-500">
        <p class="px-4">
            © <?php echo date('Y'); ?> VitalSenior. Todos los derechos reservados. Diseñado para la seguridad y el bienestar de los adultos mayores.
        </p>
    </div>
</footer>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const observerOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.15
        };

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        document.querySelectorAll('.animate-on-scroll').forEach(element => {
            observer.observe(element);
        });
    });
</script>
</body>
</html>

