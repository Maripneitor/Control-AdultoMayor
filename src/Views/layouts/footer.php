</main>
<!-- End Main Content Area -->

<!-- 7. Footer -->
<footer class="bg-on-secondary-fixed dark:bg-on-background text-secondary-fixed dark:text-secondary-fixed-dim full-width pt-16 mt-stack-lg">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-stack-lg w-full px-margin-mobile md:px-gutter py-stack-lg max-w-container-max mx-auto">
        <!-- Brand -->
        <div class="flex flex-col gap-4 md:col-span-1">
            <span class="text-headline-md font-headline-md font-bold text-white flex items-center gap-2">VitalSenior</span>
            <p class="font-body-md text-body-md opacity-80 mt-2">
                Tu aliado en el cuidado integral de los que más importan.
            </p>
        </div>
        <!-- Links -->
        <div class="flex flex-col gap-4">
            <h4 class="font-label-md text-label-md text-white mb-2">Legal</h4>
            <a class="font-body-md text-body-md text-secondary-fixed/80 hover:text-white transition-colors" href="#">Privacidad</a>
            <a class="font-body-md text-body-md text-secondary-fixed/80 hover:text-white transition-colors" href="#">HIPAA</a>
            <a class="font-body-md text-body-md text-secondary-fixed/80 hover:text-white transition-colors" href="#">Términos</a>
        </div>
        <div class="flex flex-col gap-4">
            <h4 class="font-label-md text-label-md text-white mb-2">Soporte</h4>
            <a class="font-body-md text-body-md text-secondary-fixed/80 hover:text-white transition-colors" href="#">Contacto</a>
            <a class="font-body-md text-body-md text-secondary-fixed/80 hover:text-white transition-colors" href="#">Centro de Ayuda</a>
        </div>
    </div>
    <!-- Copyright -->
    <div class="border-t border-secondary-fixed/20 mt-8 py-6 text-center">
        <p class="font-body-md text-body-md opacity-80 px-4">
            © 2026 VitalSenior. Todos los derechos reservados. Cumplimiento con HIPAA y Privacidad.
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
        
        // Inicializar el body en estado visible si tiene la transición global
        const body = document.getElementById('body-content');
        if(body) {
            body.style.opacity = 1;
        }
    });
</script>
</body>
</html>
