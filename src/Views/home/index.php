<!-- 2. Hero Section -->
<section id="inicio" class="w-full px-margin-mobile md:px-gutter max-w-container-max mx-auto py-stack-lg md:py-24">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-stack-lg items-center">
        <div class="flex flex-col gap-stack-sm items-start animate-on-scroll">
            <span class="inline-flex items-center gap-2 bg-secondary-fixed text-on-secondary-fixed px-4 py-2 rounded-full font-label-md text-label-md">
                <span class="material-symbols-outlined text-base">verified</span>
                Tranquilidad para tu familia • Respaldado médicamente
            </span>
            <h1 class="font-display-lg text-display-lg text-white drop-shadow-md mt-4 font-bold text-5xl">
                El cuidado y la salud de quienes más quieres, en un solo lugar y al instante.
            </h1>
            <p class="font-body-lg text-body-lg text-on-surface-variant mt-4 max-w-xl">
                Gestiona medicamentos, mantén un historial unificado y asegura acceso rápido a una ficha de emergencia mediante código QR. Diseñado pensando en la tranquilidad de cuidadores y el bienestar de los adultos mayores.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 mt-8 w-full sm:w-auto">
                <a href="<?php echo APP_URL; ?>/login" class="bg-primary text-on-primary font-label-md text-label-md rounded-full px-8 h-[56px] shadow-md hover:shadow-none hover:translate-y-px transition-all w-full sm:w-auto flex items-center justify-center gap-2">
                    Comenzar ahora sin costo
                    <span class="material-symbols-outlined">arrow_forward</span>
                </a>
                <a href="#como-funciona" class="bg-surface-container text-primary font-label-md text-label-md rounded-full px-8 h-[56px] shadow-sm hover:shadow-none hover:translate-y-px transition-all w-full sm:w-auto flex items-center justify-center gap-2 border border-outline-variant">
                    <span class="material-symbols-outlined">play_circle</span>
                    Ver demo en vivo
                </a>
            </div>
        </div>
        <div class="relative w-full h-[500px] flex justify-center items-center animate-on-scroll" style="transition-delay: 200ms;">
            <div class="absolute inset-0 bg-primary rounded-full blur-3xl w-3/4 h-3/4 m-auto opacity-40"></div>
            <!-- Usando la imagen local descargada -->
            <img class="relative z-10 max-h-[500px] object-contain drop-shadow-2xl rounded-2xl border-4 border-white" src="<?php echo APP_URL; ?>/assets/img/hero.png" alt="App Preview"/>
            
            <!-- Floating cards -->
            <div class="absolute bottom-10 -left-10 bg-white p-4 rounded-xl shadow-xl flex items-center gap-3 animate-bounce">
                <span class="material-symbols-outlined text-green-500 text-3xl">check_circle</span>
                <div>
                    <p class="font-bold text-slate-800 text-sm">Dosis tomada</p>
                    <p class="text-xs text-slate-500">Hace 10 min</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3. Pain vs Solution -->
<section id="como-funciona" class="w-full bg-surface-container-low py-stack-lg md:py-24">
    <div class="px-margin-mobile md:px-gutter max-w-container-max mx-auto">
        <div class="text-center mb-16 animate-on-scroll">
            <h2 class="font-headline-lg text-headline-lg text-on-surface mb-4">¿Por qué usar VitalSenior?</h2>
            <p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl mx-auto">Comprende la diferencia entre depender de la memoria y contar con una plataforma diseñada para el cuidado integral.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-stack-lg">
            <!-- Column 1: Pain -->
            <div class="bg-surface-variant rounded-2xl p-stack-md flex flex-col gap-6 shadow-sm border border-outline-variant/30 animate-on-scroll" style="transition-delay: 100ms;">
                <h3 class="font-headline-md text-headline-md text-on-surface border-b border-outline-variant/50 pb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-error">warning</span>
                    El Riesgo Diario
                </h3>
                <ul class="flex flex-col gap-4 font-body-md text-body-md text-on-surface-variant">
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-error mt-1">cancel</span>
                        <span>Olvido o duplicación en la toma de medicamentos recetados.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-error mt-1">cancel</span>
                        <span>Historial médico fragmentado entre distintos especialistas.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-error mt-1">cancel</span>
                        <span>Pérdida de tiempo crítico intentando contactar familiares en emergencias.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-error mt-1">cancel</span>
                        <span>Resultados clínicos extraviados o desorganizados.</span>
                    </li>
                </ul>
            </div>
            <!-- Column 2: Solution -->
            <div class="bg-surface-container-lowest rounded-2xl p-stack-md flex flex-col gap-6 shadow-lg shadow-primary/10 border-2 border-primary-container relative overflow-hidden animate-on-scroll" style="transition-delay: 200ms;">
                <div class="absolute top-0 right-0 w-32 h-32 bg-primary-fixed rounded-bl-full -z-10 opacity-50"></div>
                <h3 class="font-headline-md text-headline-md text-primary border-b border-primary-container/20 pb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">check_circle</span>
                    Con VitalSenior
                </h3>
                <ul class="flex flex-col gap-4 font-body-md text-body-md text-on-surface">
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary mt-1">task_alt</span>
                        <span>Control estricto de medicamentos con recordatorios inteligentes y registro de dosis.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary mt-1">task_alt</span>
                        <span>Expediente único y unificado, accesible desde cualquier dispositivo.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary mt-1">task_alt</span>
                        <span>Ficha de emergencia con QR para acceso inmediato a contactos vitales por primeros respondientes.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary mt-1">task_alt</span>
                        <span>Bóveda digital segura para almacenar y organizar todos los resultados clínicos.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Additional Sections for Anchors -->
<section id="modulos" class="w-full px-margin-mobile md:px-gutter max-w-container-max mx-auto py-stack-lg md:py-24">
    <div class="text-center mb-16 animate-on-scroll">
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-4">Nuestros Módulos</h2>
        <p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl mx-auto">Herramientas completas para simplificar el cuidado diario.</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm border border-outline-variant/30 text-center animate-on-scroll" style="transition-delay: 100ms;">
            <div class="w-16 h-16 bg-primary/10 text-primary rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-3xl">medication</span>
            </div>
            <h3 class="font-headline-md text-headline-md text-on-surface mb-2">Control de Medicación</h3>
            <p class="text-on-surface-variant">Registros precisos y seguimiento de tomas con alertas inteligentes.</p>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm border border-outline-variant/30 text-center animate-on-scroll" style="transition-delay: 200ms;">
            <div class="w-16 h-16 bg-primary/10 text-primary rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-3xl">calendar_month</span>
            </div>
            <h3 class="font-headline-md text-headline-md text-on-surface mb-2">Agenda Médica</h3>
            <p class="text-on-surface-variant">No olvides nunca una cita. Controla visitas al doctor y laboratorios en un solo calendario.</p>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm border border-outline-variant/30 text-center animate-on-scroll" style="transition-delay: 300ms;">
            <div class="w-16 h-16 bg-primary/10 text-primary rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-3xl">vital_signs</span>
            </div>
            <h3 class="font-headline-md text-headline-md text-on-surface mb-2">Historial Clínico</h3>
            <p class="text-on-surface-variant">Archiva diagnósticos y evolución de la salud centralizados y ordenados.</p>
        </div>
    </div>
</section>

<section id="emergencia" class="w-full bg-error-container/30 py-stack-lg md:py-24">
    <div class="px-margin-mobile md:px-gutter max-w-container-max mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-stack-lg items-center text-center lg:text-left">
            <div class="animate-on-scroll">
                <span class="material-symbols-outlined text-error text-6xl mb-4">emergency</span>
                <h2 class="font-headline-lg text-headline-lg text-on-surface mb-4">Ficha de Emergencia QR</h2>
                <p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl mx-auto lg:mx-0 mb-8">Un perfil vital accesible al instante a través de un código QR, listo para primeros auxilios o personal médico de respuesta rápida. Incluye tipo de sangre, alergias crónicas y contactos prioritarios.</p>
                <a href="<?php echo APP_URL; ?>/login" class="inline-flex items-center gap-2 bg-error text-on-error font-label-md text-label-md rounded-full px-8 h-[56px] shadow-md hover:shadow-none hover:translate-y-px transition-all">
                    Configurar Ficha de Emergencia
                </a>
            </div>
            <div class="relative flex justify-center items-center animate-on-scroll" style="transition-delay: 200ms;">
                <div class="absolute inset-0 bg-error/10 rounded-full blur-3xl w-3/4 h-3/4 m-auto"></div>
                <!-- Representación visual del QR -->
                <div class="bg-white p-8 rounded-3xl shadow-2xl border-4 border-error/20 flex flex-col items-center gap-4 relative z-10 transform hover:scale-105 transition-transform duration-300">
                    <span class="material-symbols-outlined text-[150px] text-on-surface">qr_code_2</span>
                    <p class="font-label-md text-label-md text-error tracking-widest uppercase">Escanear en Emergencia</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="testimonios" class="w-full px-margin-mobile md:px-gutter max-w-container-max mx-auto py-stack-lg md:py-24">
    <div class="text-center mb-16 animate-on-scroll">
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-4">Lo que dicen nuestros usuarios</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-surface-variant p-8 rounded-2xl animate-on-scroll">
            <span class="material-symbols-outlined text-4xl text-primary/40 mb-4">format_quote</span>
            <p class="italic text-on-surface-variant mb-4 min-h-[80px]">"VitalSenior cambió la forma en la que organizo la salud de mi madre. Ya no pierdo recetas ni dudo de qué medicamento le toca. ¡Recomendado 100%!"</p>
            <h4 class="font-bold text-on-surface">— María Fernanda C.</h4>
        </div>
        <div class="bg-surface-variant p-8 rounded-2xl animate-on-scroll" style="transition-delay: 200ms;">
            <span class="material-symbols-outlined text-4xl text-primary/40 mb-4">format_quote</span>
            <p class="italic text-on-surface-variant mb-4 min-h-[80px]">"La ficha de emergencia QR me da una tranquilidad inmensa cuando mi abuelo sale a caminar. Sé que si algo pasa, mi teléfono está a un scan de distancia."</p>
            <h4 class="font-bold text-on-surface">— Jorge Luis P.</h4>
        </div>
        <div class="bg-surface-variant p-8 rounded-2xl animate-on-scroll" style="transition-delay: 400ms;">
            <span class="material-symbols-outlined text-4xl text-primary/40 mb-4">format_quote</span>
            <p class="italic text-on-surface-variant mb-4 min-h-[80px]">"Al fin tengo todos los estudios y la agenda médica de mi padre en un solo lugar. La app es súper intuitiva incluso para mí que no soy muy tecnológico."</p>
            <h4 class="font-bold text-on-surface">— Carlos R.</h4>
        </div>
    </div>
</section>

<!-- Final CTA Section -->
<section class="w-full bg-primary-fixed-dim/20 py-stack-lg md:py-24 relative overflow-hidden">
    <div class="absolute inset-0 bg-primary/5 rounded-full blur-3xl w-[150%] h-[150%] -top-1/2 -left-1/4 -z-10"></div>
    <div class="px-margin-mobile md:px-gutter max-w-container-max mx-auto text-center animate-on-scroll">
        <h2 class="font-display-lg text-display-lg text-on-surface mb-6">¿Listo para mejorar el cuidado de tu familia?</h2>
        <p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl mx-auto mb-10">Únete a VitalSenior hoy y obtén acceso inmediato a todas las herramientas que necesitas para asegurar la tranquilidad de tus seres queridos.</p>
        <a href="<?php echo APP_URL; ?>/login" class="inline-flex items-center justify-center gap-2 bg-primary text-on-primary font-label-md text-label-md rounded-full px-12 h-[64px] shadow-xl hover:shadow-none hover:translate-y-px transition-all text-lg">
            Crear Expediente Gratis
            <span class="material-symbols-outlined">arrow_forward</span>
        </a>
    </div>
</section>

<!-- Footer -->
<footer class="bg-slate-900 text-slate-300 py-12">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-8">
        <div>
            <h4 class="text-white font-bold text-lg mb-4">VitalSenior</h4>
            <p class="text-sm">El cuidado y la salud de quienes más quieres, en un solo lugar y al instante.</p>
        </div>
        <div>
            <h4 class="text-white font-bold text-lg mb-4">Contacto</h4>
            <p class="text-sm mb-2"><span class="material-symbols-outlined text-sm align-middle mr-2">mail</span> contacto@vitalsenior.com</p>
            <p class="text-sm"><span class="material-symbols-outlined text-sm align-middle mr-2">phone</span> +52 555 123 4567</p>
        </div>
        <div>
            <h4 class="text-white font-bold text-lg mb-4">Legal</h4>
            <a href="#" class="block text-sm hover:text-white mb-2">Aviso de Privacidad</a>
            <a href="#" class="block text-sm hover:text-white">Términos y Condiciones</a>
        </div>
    </div>
    <div class="text-center text-sm mt-12 border-t border-slate-800 pt-6">
        &copy; <?= date('Y') ?> VitalSenior. Todos los derechos reservados.
    </div>
</footer>
