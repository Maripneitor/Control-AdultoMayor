</main>
<!-- End Main Content Area -->

<!-- Barra de Navegación Móvil Fija -->
<?php require_once dirname(__DIR__) . '/components/bottom_nav.php'; ?>

<!-- 7. Footer -->
<footer class="bg-[#0b1b3d] text-slate-300 border-t border-blue-900/60 pt-12 pb-24 md:pb-12">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8 md:gap-10 w-full px-4 sm:px-6 md:px-8 max-w-7xl mx-auto">
        <!-- Brand -->
        <div class="flex flex-col gap-3 md:col-span-2">
            <a class="flex items-center gap-2.5 text-xl sm:text-2xl font-black text-white tracking-tight" href="<?php echo APP_URL; ?>/">
                <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center text-white text-lg font-black">
                    ✚
                </div>
                <span>Vital<span class="text-blue-400">Senior</span></span>
            </a>
            <p class="text-xs sm:text-sm text-slate-400 max-w-md leading-relaxed">
                Plataforma integral para la gestión de salud y cuidado de adultos mayores. Tu aliado de confianza para coordinar medicamentos, citas médicas y emergencias familiares.
            </p>
        </div>
        <!-- Links -->
        <div class="flex flex-col gap-2.5">
            <h4 class="font-bold text-white text-xs uppercase tracking-wider mb-1">Legal y Seguridad</h4>
            <a class="text-xs text-slate-400 hover:text-white transition-colors" href="#">Política de Privacidad</a>
            <a class="text-xs text-slate-400 hover:text-white transition-colors" href="#">Protección de Datos Médicos</a>
            <a class="text-xs text-slate-400 hover:text-white transition-colors" href="#">Términos de Servicio</a>
        </div>
        <div class="flex flex-col gap-2.5">
            <h4 class="font-bold text-white text-xs uppercase tracking-wider mb-1">Soporte y Contacto</h4>
            <a class="text-xs text-slate-400 hover:text-white transition-colors" href="#">Centro de Ayuda</a>
            <a class="text-xs text-slate-400 hover:text-white transition-colors" href="#">Atención a Cuidadores</a>
            <a class="text-xs text-slate-400 hover:text-white transition-colors" href="tel:911">Línea de Emergencia (911)</a>
        </div>
    </div>
    <!-- Copyright -->
    <div class="border-t border-slate-800/80 mt-10 py-6 text-center text-[11px] text-slate-500">
        <p class="px-4">
            © <?php echo date('Y'); ?> VitalSenior • Diseñado para la seguridad y el bienestar del adulto mayor.
        </p>
    </div>
</footer>

</body>
</html>

