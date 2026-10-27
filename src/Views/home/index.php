<!-- 2. Hero Section -->
<section id="inicio" class="w-full bg-[#0a192f] text-white py-16 md:py-24 relative overflow-hidden">
    <!-- Decorative Ambient Glow Effects -->
    <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 right-10 w-96 h-96 bg-indigo-600/15 rounded-full blur-3xl pointer-events-none"></div>

    <div class="px-4 md:px-8 max-w-7xl mx-auto relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
            
            <!-- Left Text Content (Col 7) -->
            <div class="lg:col-span-7 flex flex-col items-start animate-on-scroll">
                <div class="inline-flex items-center gap-2 bg-blue-500/15 border border-blue-400/30 text-blue-200 px-4 py-2 rounded-full text-xs sm:text-sm font-bold mb-6 backdrop-blur-sm">
                    <svg class="w-4 h-4 text-blue-300 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span>Tranquilidad Familiar • Respaldado Médicamente</span>
                </div>

                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-[52px] font-black text-white tracking-tight leading-[1.15] mb-6">
                    El cuidado y la salud de quienes más quieres, en un solo lugar y al instante.
                </h1>

                <p class="text-base sm:text-lg md:text-xl text-slate-200 font-normal leading-relaxed mb-8 max-w-2xl">
                    Gestiona medicamentos, mantén un historial unificado y asegura acceso rápido a una ficha de emergencia mediante código QR. Diseñado pensando en la tranquilidad de cuidadores y el bienestar del adulto mayor.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row items-center gap-4 w-full sm:w-auto">
                    <a href="<?php echo APP_URL; ?>/login" 
                       class="w-full sm:w-auto bg-blue-600 hover:bg-blue-500 text-white font-bold text-base sm:text-lg px-8 py-4 rounded-2xl shadow-xl shadow-blue-950/40 hover:-translate-y-0.5 active:scale-[0.98] transition-all flex items-center justify-center gap-2.5 min-h-[48px] focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400">
                        <span>Comenzar ahora sin costo</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                    
                    <a href="#como-funciona" 
                       class="w-full sm:w-auto bg-slate-800/80 hover:bg-slate-800 text-white border border-slate-700/80 font-bold text-base sm:text-lg px-8 py-4 rounded-2xl backdrop-blur-sm active:scale-[0.98] transition-all flex items-center justify-center gap-2.5 min-h-[48px] focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400">
                        <svg class="w-5 h-5 text-blue-300" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                        </svg>
                        <span>Ver cómo funciona</span>
                    </a>
                </div>

                <!-- Trust Points -->
                <div class="flex flex-wrap items-center gap-6 mt-10 pt-8 border-t border-slate-800/80 text-sm text-slate-300 font-medium">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>Sin tarjeta de crédito requerida</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span>Datos clínicos 100% cifrados</span>
                    </div>
                </div>
            </div>

            <!-- Right Visual (Col 5) - Imagen Institucional -->
            <div class="lg:col-span-5 relative flex items-center justify-center animate-on-scroll" style="transition-delay: 150ms;">
                <div class="relative w-full max-w-lg mx-auto">
                    <!-- Sutil resplandor ambiental decorativo -->
                    <div class="absolute -inset-2 bg-gradient-to-tr from-blue-600/20 via-blue-500/15 to-indigo-500/15 rounded-3xl blur-2xl opacity-75 pointer-events-none"></div>

                    <img src="<?= APP_URL ?>/assets/img/hero.png" 
                         alt="Plataforma de cuidado y monitoreo VitalSenior" 
                         loading="eager"
                         class="relative rounded-3xl shadow-2xl shadow-blue-950/20 ring-1 ring-white/10 border border-slate-700/60 object-cover w-full max-w-lg mx-auto aspect-[4/3] lg:aspect-auto">
                </div>
            </div>

        </div>
    </div>
</section>

<!-- 3. Pain vs Solution -->
<section id="como-funciona" class="w-full bg-slate-50 py-16 md:py-24 border-b border-slate-200/80">
    <div class="px-4 md:px-8 max-w-7xl mx-auto">
        <div class="text-center mb-16 animate-on-scroll">
            <span class="text-blue-700 bg-blue-50 border border-blue-200/60 font-bold text-xs uppercase tracking-wider px-3.5 py-1.5 rounded-full inline-block mb-3">Comparativa de Cuidado</span>
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight mb-4">¿Por qué usar VitalSenior?</h2>
            <p class="text-base sm:text-lg text-slate-600 max-w-2xl mx-auto leading-relaxed">Comprende la diferencia entre depender de la memoria y contar con una plataforma integral diseñada para la salud familiar.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Column 1: Pain -->
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-200 flex flex-col gap-6 animate-on-scroll" style="transition-delay: 100ms;">
                <h3 class="text-2xl font-bold text-slate-900 border-b border-slate-100 pb-4 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <span>El Riesgo Diario</span>
                </h3>
                <ul class="flex flex-col gap-4 text-base text-slate-700">
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        <span>Olvido o duplicación accidental en la toma de medicamentos recetados.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        <span>Historial médico fragmentado y recetas dispersas entre distintos especialistas.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        <span>Pérdida de tiempo crítico intentando contactar familiares durante emergencias médicas.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        <span>Resultados clínicos y estudios de laboratorio extraviados o sin seguimiento.</span>
                    </li>
                </ul>
            </div>

            <!-- Column 2: Solution -->
            <div class="bg-white rounded-3xl p-8 shadow-xl shadow-blue-900/5 border-2 border-blue-600/30 flex flex-col gap-6 relative overflow-hidden animate-on-scroll" style="transition-delay: 200ms;">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-bl-full -z-10 opacity-70"></div>
                <h3 class="text-2xl font-bold text-blue-900 border-b border-blue-100 pb-4 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span>Con VitalSenior</span>
                </h3>
                <ul class="flex flex-col gap-4 text-base text-slate-800 font-medium">
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-blue-50 text-blue-700 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span>Control estricto de medicamentos con recordatorios inteligentes y registro de dosis.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-blue-50 text-blue-700 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span>Expediente único y unificado, accesible de forma segura desde cualquier dispositivo.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-blue-50 text-blue-700 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span>Ficha de emergencia QR para acceso inmediato a contactos vitales por primeros respondientes.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-blue-50 text-blue-700 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span>Bóveda digital segura para almacenar y organizar todos los resultados clínicos familiares.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- 4. Carrusel Interactivo de Funcionalidades Clave -->
<section id="modulos" class="w-full px-4 md:px-8 max-w-7xl mx-auto py-16 md:py-24 overflow-hidden">
    <div class="text-center mb-12 animate-on-scroll">
        <span class="text-blue-700 bg-blue-50 border border-blue-200/60 font-bold text-xs uppercase tracking-wider px-3.5 py-1.5 rounded-full inline-block mb-3">Ecosistema Integral VitalSenior</span>
        <h2 class="text-3xl md:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight mb-4">Funcionalidades Diseñadas para la Salud Familiar</h2>
        <p class="text-base sm:text-lg md:text-xl text-slate-600 max-w-2xl mx-auto leading-relaxed">Explora las herramientas clínicas y de asistencia preventiva desarrolladas para acompañar cada momento del día.</p>
    </div>

    <!-- Contenedor Principal del Carrusel -->
    <div id="vital-carousel" 
         class="relative bg-white rounded-3xl border border-slate-200 shadow-xl shadow-slate-200/50 overflow-hidden focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600" 
         tabindex="0"
         role="region" 
         aria-roledescription="carrusel" 
         aria-label="Funcionalidades principales del programa VitalSenior"
         aria-live="polite">

        <!-- Track de Diapositivas -->
        <div id="carousel-track" 
             class="flex min-w-full will-change-transform" 
             style="transform: translateX(0%); transition: transform 500ms cubic-bezier(0.16, 1, 0.3, 1);">

            <!-- Diapositiva 1: Signos Vitales -->
            <div class="min-w-full w-full flex-shrink-0 p-6 sm:p-10 md:p-14 grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center" 
                 role="group" 
                 aria-roledescription="diapositiva" 
                 aria-label="1 de 4: Monitoreo de Signos Vitales">
                <div class="lg:col-span-7 flex flex-col items-start">
                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-blue-50 text-blue-800 border border-blue-200/80 mb-3">
                        <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                        Salud Diaria en Tiempo Real
                    </span>
                    <h3 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 tracking-tight mb-4">
                        Monitoreo Continuo de Signos Vitales
                    </h3>
                    <p class="text-slate-700 text-base sm:text-lg leading-relaxed mb-6">
                        Registra y visualiza la evolución de presión arterial (sistólica/diastólica), glucosa en ayunas o postprandial, pulso y estado de ánimo. Cuenta con un semáforo clínico que categoriza lecturas normales, precaución y alertas de riesgo inmediato.
                    </p>
                    <ul class="space-y-3 mb-8 text-sm sm:text-base text-slate-800 font-medium">
                        <li class="flex items-center gap-2.5">
                            <div class="w-5 h-5 rounded-full bg-blue-100 text-blue-800 flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <span>Evaluación y semaforización clínica instantánea</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <div class="w-5 h-5 rounded-full bg-blue-100 text-blue-800 flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <span>Cálculo de promedios semanales para la consulta médica</span>
                        </li>
                    </ul>
                    <a href="<?= APP_URL ?>/login" class="inline-flex items-center gap-2 px-7 py-3.5 min-h-[48px] bg-blue-600 hover:bg-blue-700 text-white text-sm sm:text-base font-bold rounded-2xl shadow-md shadow-blue-600/20 hover:shadow-lg transition-all active:scale-[0.98] cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600">
                        <span>Registrar Signos Vitales</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>

                <div class="lg:col-span-5 flex justify-center">
                    <div class="w-full max-w-sm bg-slate-50 border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-inner space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                            <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Panel Clínico</span>
                            <span class="text-xs font-extrabold text-blue-800 bg-blue-100/90 px-3 py-1 rounded-full">Óptimo</span>
                        </div>
                        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex justify-between items-center">
                            <div>
                                <p class="text-xs text-slate-600 font-semibold">Presión Arterial</p>
                                <p class="text-xl font-black text-slate-900">120 / 80 <span class="text-xs font-medium text-slate-500">mmHg</span></p>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center font-bold text-xs">PA</div>
                        </div>
                        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex justify-between items-center">
                            <div>
                                <p class="text-xs text-slate-600 font-semibold">Glucosa en Ayunas</p>
                                <p class="text-xl font-black text-slate-900">95 <span class="text-xs font-medium text-slate-500">mg/dL</span></p>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center font-bold text-xs">GLU</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Diapositiva 2: Medicación -->
            <div class="min-w-full w-full flex-shrink-0 p-6 sm:p-10 md:p-14 grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center" 
                 role="group" 
                 aria-roledescription="diapositiva" 
                 aria-label="2 de 4: Control de Tomas Farmacológicas">
                <div class="lg:col-span-7 flex flex-col items-start">
                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-blue-50 text-blue-800 border border-blue-200/80 mb-3">
                        <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                        Farmacovigilancia y Adherencia
                    </span>
                    <h3 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 tracking-tight mb-4">
                        Control y Adherencia de Medicación
                    </h3>
                    <p class="text-slate-700 text-base sm:text-lg leading-relaxed mb-6">
                        Planificador estructurado de horarios, dosis y recordatorios de fármacos prescritos. Permite marcar tomas con 1 toque táctil, posponer tomas temporales y emitir alertas automáticas si un medicamento prioritario no se registra a tiempo.
                    </p>
                    <ul class="space-y-3 mb-8 text-sm sm:text-base text-slate-800 font-medium">
                        <li class="flex items-center gap-2.5">
                            <div class="w-5 h-5 rounded-full bg-blue-100 text-blue-800 flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <span>Horarios adaptados a la rutina matutina, tarde y noche</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <div class="w-5 h-5 rounded-full bg-blue-100 text-blue-800 flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <span>Control de inventario con aviso de recetas próximas a agotarse</span>
                        </li>
                    </ul>
                    <a href="<?= APP_URL ?>/login" class="inline-flex items-center gap-2 px-7 py-3.5 min-h-[48px] bg-blue-600 hover:bg-blue-700 text-white text-sm sm:text-base font-bold rounded-2xl shadow-md shadow-blue-600/20 hover:shadow-lg transition-all active:scale-[0.98] cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600">
                        <span>Organizar Medicamentos</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>

                <div class="lg:col-span-5 flex justify-center">
                    <div class="w-full max-w-sm bg-slate-50 border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-inner space-y-3">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                            <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Toma Programada</span>
                            <span class="text-xs font-bold text-blue-800 bg-blue-100 px-3 py-1 rounded-full">08:00 AM</span>
                        </div>
                        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex justify-between items-center">
                            <div>
                                <p class="text-sm font-bold text-slate-900">Losartán Potásico</p>
                                <p class="text-xs text-slate-600 font-medium">50mg • 1 tableta con desayuno</p>
                            </div>
                            <span class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center shadow-xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </span>
                        </div>
                        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex justify-between items-center opacity-90">
                            <div>
                                <p class="text-sm font-bold text-slate-900">Metformina</p>
                                <p class="text-xs text-slate-600 font-medium">850mg • 1 tableta con comida</p>
                            </div>
                            <span class="text-xs font-bold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-md">2:00 PM</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Diapositiva 3: Ficha QR SOS -->
            <div class="min-w-full w-full flex-shrink-0 p-6 sm:p-10 md:p-14 grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center" 
                 role="group" 
                 aria-roledescription="diapositiva" 
                 aria-label="3 de 4: Ficha Médica de Emergencia SOS con QR">
                <div class="lg:col-span-7 flex flex-col items-start">
                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-rose-50 text-rose-800 border border-rose-200/80 mb-3">
                        <span class="w-2 h-2 rounded-full bg-rose-600 animate-pulse"></span>
                        Respuesta y Auxilio Inmediato
                    </span>
                    <h3 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 tracking-tight mb-4">
                        Ficha Médica de Emergencia SOS con QR
                    </h3>
                    <p class="text-slate-700 text-base sm:text-lg leading-relaxed mb-6">
                        Un perfil de auxilio público y accesible al instante con cualquier cámara de smartphone. Proporciona a socorristas y médicos datos críticos: tipo de sangre, alergias severas, condiciones crónicas y botón directo para llamar a la red de cuidadores.
                    </p>
                    <ul class="space-y-3 mb-8 text-sm sm:text-base text-slate-800 font-medium">
                        <li class="flex items-center gap-2.5">
                            <div class="w-5 h-5 rounded-full bg-rose-100 text-rose-800 flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <span>Acceso sin contraseña exclusivo para emergencias médicas</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <div class="w-5 h-5 rounded-full bg-rose-100 text-rose-800 flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <span>Enlace telefónico directo con los familiares prioritarios</span>
                        </li>
                    </ul>
                    <a href="<?= APP_URL ?>/login" class="inline-flex items-center gap-2 px-7 py-3.5 min-h-[48px] bg-rose-600 hover:bg-rose-700 text-white text-sm sm:text-base font-bold rounded-2xl shadow-md shadow-rose-600/20 hover:shadow-lg transition-all active:scale-[0.98] cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-600">
                        <span>Configurar Código QR SOS</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>

                <div class="lg:col-span-5 flex justify-center">
                    <div class="w-full max-w-sm bg-white border-2 border-rose-200/80 rounded-3xl p-6 sm:p-8 shadow-xl flex flex-col items-center gap-4 text-center">
                        <div class="w-36 h-36 bg-slate-900 rounded-2xl p-3 flex items-center justify-center shadow-inner">
                            <svg class="w-full h-full text-white" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M2 2h8v8H2V2zm2 2v4h4V4H4zm10-2h8v8h-8V2zm2 2v4h4V4h-4zM2 14h8v8H2v-8zm2 2v4h4v-4H4zm14 0h4v4h-4v-4zm-4 4h4v4h-4v-4zm0-4h4v-4h-4v4zm4-4h4v-4h-4v4zM5 5h2v2H5V5zm12 0h2v2h-2V5zM5 17h2v2H5v-2z" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-xs font-black text-rose-700 uppercase tracking-wider">Ficha de Emergencia Activa</span>
                            <p class="text-xs text-slate-600 font-medium mt-0.5">Compatible con pulseras, tarjetas y llaveros QR</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Diapositiva 4: Expediente y PDF -->
            <div class="min-w-full w-full flex-shrink-0 p-6 sm:p-10 md:p-14 grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center" 
                 role="group" 
                 aria-roledescription="diapositiva" 
                 aria-label="4 de 4: Expediente Familiar y Reportes PDF">
                <div class="lg:col-span-7 flex flex-col items-start">
                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-blue-50 text-blue-800 border border-blue-200/80 mb-3">
                        <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                        Historial y Documentación Médica
                    </span>
                    <h3 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 tracking-tight mb-4">
                        Expediente Familiar y Reportes PDF
                    </h3>
                    <p class="text-slate-700 text-base sm:text-lg leading-relaxed mb-6">
                        Centraliza bitácoras de visitas post-consulta, recetas adjuntas, catálogo de especialistas médicos y genera en 1 clic reportes clínicos oficiales en PDF con formato estructurado listos para imprimir o compartir con el médico.
                    </p>
                    <ul class="space-y-3 mb-8 text-sm sm:text-base text-slate-800 font-medium">
                        <li class="flex items-center gap-2.5">
                            <div class="w-5 h-5 rounded-full bg-blue-100 text-blue-800 flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <span>Exportación formal de historial clínico en PDF con diseño limpio</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <div class="w-5 h-5 rounded-full bg-blue-100 text-blue-800 flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <span>Acceso multitenant para familiares administradores y cuidadores</span>
                        </li>
                    </ul>
                    <a href="<?= APP_URL ?>/login" class="inline-flex items-center gap-2 px-7 py-3.5 min-h-[48px] bg-blue-600 hover:bg-blue-700 text-white text-sm sm:text-base font-bold rounded-2xl shadow-md shadow-blue-600/20 hover:shadow-lg transition-all active:scale-[0.98] cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600">
                        <span>Ver Expediente Digital</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>

                <div class="lg:col-span-5 flex justify-center">
                    <div class="w-full max-w-sm bg-slate-50 border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-inner space-y-3">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                            <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Documentación</span>
                            <span class="text-xs font-bold text-blue-800 bg-blue-100 px-3 py-1 rounded-full">PDF Listo</span>
                        </div>
                        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center font-bold text-xs shrink-0">
                                PDF
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-bold text-slate-900 truncate">Resumen Clínico Integral</p>
                                <p class="text-[11px] text-slate-600 font-medium">Historial de signos y recetas</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Barra Inferior de Navegación y Controles -->
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex flex-wrap items-center justify-between gap-4">
            
            <!-- Botón Anterior -->
            <button type="button" 
                    id="carousel-btn-prev" 
                    aria-label="Diapositiva anterior"
                    class="tap-bounce touch-min min-h-[48px] min-w-[48px] px-4 py-2.5 rounded-2xl bg-white hover:bg-slate-100 text-slate-800 border border-slate-300 shadow-xs inline-flex items-center justify-center gap-1.5 text-sm font-bold transition-all duration-150 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                <span class="hidden sm:inline">Anterior</span>
            </button>

            <!-- Indicadores de Posición (Dots) -->
            <div id="carousel-dots" class="flex items-center gap-2.5" role="tablist" aria-label="Selector de diapositiva">
                <button type="button" data-slide-index="0" role="tab" aria-selected="true" aria-label="Ir a diapositiva 1: Signos Vitales" class="carousel-dot h-3 rounded-full transition-all duration-300 w-9 bg-blue-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 cursor-pointer hover:scale-110"></button>
                <button type="button" data-slide-index="1" role="tab" aria-selected="false" aria-label="Ir a diapositiva 2: Control de Medicación" class="carousel-dot h-3 rounded-full transition-all duration-300 w-3 bg-slate-300 hover:bg-slate-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 cursor-pointer hover:scale-110"></button>
                <button type="button" data-slide-index="2" role="tab" aria-selected="false" aria-label="Ir a diapositiva 3: Ficha QR SOS" class="carousel-dot h-3 rounded-full transition-all duration-300 w-3 bg-slate-300 hover:bg-slate-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 cursor-pointer hover:scale-110"></button>
                <button type="button" data-slide-index="3" role="tab" aria-selected="false" aria-label="Ir a diapositiva 4: Expediente PDF" class="carousel-dot h-3 rounded-full transition-all duration-300 w-3 bg-slate-300 hover:bg-slate-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 cursor-pointer hover:scale-110"></button>
            </div>

            <!-- Botón Siguiente -->
            <button type="button" 
                    id="carousel-btn-next" 
                    aria-label="Siguiente diapositiva"
                    class="tap-bounce touch-min min-h-[48px] min-w-[48px] px-4 py-2.5 rounded-2xl bg-white hover:bg-slate-100 text-slate-800 border border-slate-300 shadow-xs inline-flex items-center justify-center gap-1.5 text-sm font-bold transition-all duration-150 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 cursor-pointer">
                <span class="hidden sm:inline">Siguiente</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </button>

        </div>
    </div>
</section>

<!-- 5. Emergency QR Section -->
<section id="emergencia" class="w-full bg-rose-50/60 py-16 md:py-24 border-y border-rose-100">
    <div class="px-4 md:px-8 max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <div class="lg:col-span-7 text-center lg:text-left animate-on-scroll">
                <div class="inline-flex items-center gap-2 bg-rose-100 text-rose-700 px-4 py-1.5 rounded-full text-sm font-bold mb-4">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <span>Respuesta Inmediata</span>
                </div>
                
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 mb-4">Ficha de Emergencia QR</h2>
                <p class="text-lg text-slate-600 max-w-2xl mx-auto lg:mx-0 mb-8 leading-relaxed">
                    Un perfil vital accesible al instante a través de un código QR, listo para primeros auxilios o personal médico de respuesta rápida. Incluye tipo de sangre, alergias crónicas, padecimientos y contactos prioritarios.
                </p>
                
                <a href="<?php echo APP_URL; ?>/login" class="inline-flex items-center gap-2.5 bg-rose-600 hover:bg-rose-700 text-white font-semibold text-base rounded-full px-8 py-4 shadow-lg shadow-rose-600/30 hover:shadow-xl hover:shadow-rose-600/40 hover:-translate-y-0.5 transition-all">
                    <span>Configurar Ficha de Emergencia</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>

            <div class="lg:col-span-5 relative flex justify-center items-center animate-on-scroll" style="transition-delay: 200ms;">
                <div class="bg-white p-8 rounded-3xl shadow-2xl border-4 border-rose-200/60 flex flex-col items-center gap-5 relative z-10 max-w-sm w-full transform hover:scale-105 transition-transform duration-300">
                    <div class="w-48 h-48 bg-slate-900 rounded-2xl p-4 flex items-center justify-center shadow-inner">
                        <svg class="w-full h-full text-white" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M2 2h8v8H2V2zm2 2v4h4V4H4zm10-2h8v8h-8V2zm2 2v4h4V4h-4zM2 14h8v8H2v-8zm2 2v4h4v-4H4zm14 0h4v4h-4v-4zm-4 4h4v4h-4v-4zm0-4h4v-4h-4v4zm4-4h4v-4h-4v4zM5 5h2v2H5V5zm12 0h2v2h-2V5zM5 17h2v2H5v-2z" />
                        </svg>
                    </div>
                    <div class="text-center">
                        <p class="font-extrabold text-sm text-rose-600 tracking-widest uppercase">Escanear en Emergencia</p>
                        <p class="text-xs text-slate-500 mt-1">Compatible con cualquier cámara de smartphone</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- 6. Testimonios Section -->
<section id="testimonios" class="w-full px-4 md:px-8 max-w-7xl mx-auto py-16 md:py-24">
    <div class="text-center mb-16 animate-on-scroll">
        <span class="text-[#004ac6] font-bold text-sm uppercase tracking-wider">Historias Reales</span>
        <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 mt-2 mb-4">Lo que dicen nuestras familias</h2>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Testimonial 1 -->
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200 flex flex-col justify-between animate-on-scroll" style="transition-delay: 100ms;">
            <div>
                <svg class="w-10 h-10 text-blue-200 mb-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                </svg>
                <p class="text-slate-700 italic leading-relaxed mb-6">
                    "VitalSenior cambió la forma en la que organizo la salud de mi madre. Ya no pierdo recetas ni dudo de qué medicamento le toca. ¡Recomendado 100%!"
                </p>
            </div>
            <div class="pt-4 border-t border-slate-100">
                <h4 class="font-bold text-slate-900">María Fernanda C.</h4>
                <p class="text-xs text-slate-500">Cuidadora Principal</p>
            </div>
        </div>

        <!-- Testimonial 2 -->
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200 flex flex-col justify-between animate-on-scroll" style="transition-delay: 200ms;">
            <div>
                <svg class="w-10 h-10 text-blue-200 mb-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                </svg>
                <p class="text-slate-700 italic leading-relaxed mb-6">
                    "La ficha de emergencia QR me da una tranquilidad inmensa cuando mi abuelo sale a caminar. Sé que si algo pasa, mi teléfono está a un scan de distancia."
                </p>
            </div>
            <div class="pt-4 border-t border-slate-100">
                <h4 class="font-bold text-slate-900">Jorge Luis P.</h4>
                <p class="text-xs text-slate-500">Familiar Responsable</p>
            </div>
        </div>

        <!-- Testimonial 3 -->
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200 flex flex-col justify-between animate-on-scroll" style="transition-delay: 300ms;">
            <div>
                <svg class="w-10 h-10 text-blue-200 mb-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                </svg>
                <p class="text-slate-700 italic leading-relaxed mb-6">
                    "Al fin tengo todos los estudios y la agenda médica de mi padre en un solo lugar. La app es súper intuitiva incluso para mí que no soy muy tecnológico."
                </p>
            </div>
            <div class="pt-4 border-t border-slate-100">
                <h4 class="font-bold text-slate-900">Carlos R.</h4>
                <p class="text-xs text-slate-500">Hijo y Cuidador</p>
            </div>
        </div>
    </div>
</section>

<!-- 7. Final CTA Section -->
<section class="w-full bg-[#004ac6] text-white py-16 md:py-24 relative overflow-hidden">
    <div class="absolute inset-0 bg-blue-700/30 rounded-full blur-3xl w-[150%] h-[150%] -top-1/2 -left-1/4 pointer-events-none"></div>
    <div class="px-4 md:px-8 max-w-7xl mx-auto text-center relative z-10 animate-on-scroll">
        <h2 class="text-3xl md:text-5xl font-extrabold text-white mb-6 tracking-tight">
            ¿Listo para mejorar el cuidado de tu familia?
        </h2>
        <p class="text-lg md:text-xl text-blue-100 max-w-2xl mx-auto mb-10 leading-relaxed font-normal">
            Únete a VitalSenior hoy y obtén acceso inmediato a todas las herramientas que necesitas para asegurar el bienestar de tus seres queridos.
        </p>
        <a href="<?php echo APP_URL; ?>/login" class="inline-flex items-center justify-center gap-3 bg-white hover:bg-slate-100 text-[#004ac6] font-bold text-lg rounded-full px-10 py-5 shadow-2xl hover:shadow-xl hover:-translate-y-0.5 transition-all">
            <span>Crear Expediente Gratis</span>
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </a>
    </div>
</section>

<!-- Script Controlador del Carrusel Interactivo (Emil Kowalski Design Engineering) -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const track = document.getElementById('carousel-track');
    const container = document.getElementById('vital-carousel');
    const btnPrev = document.getElementById('carousel-btn-prev');
    const btnNext = document.getElementById('carousel-btn-next');
    const dotsContainer = document.getElementById('carousel-dots');
    
    if (!track || !container || !btnPrev || !btnNext || !dotsContainer) return;

    const slides = track.children;
    const totalSlides = slides.length;
    const dots = dotsContainer.querySelectorAll('.carousel-dot');
    let currentIndex = 0;
    let autoplayTimer = null;
    let isPaused = false;

    // Comprobar preferencia de movimiento reducido del usuario
    const mediaReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const prefersReducedMotion = mediaReducedMotion.matches;

    // Actualizar visualización y accesibilidad del carrusel
    function updateCarousel(index) {
        currentIndex = (index + totalSlides) % totalSlides;
        
        // Desplazamiento acelerado por hardware
        track.style.transform = `translateX(-${currentIndex * 100}%)`;

        // Actualizar indicadores (dots)
        dots.forEach((dot, idx) => {
            const isActive = idx === currentIndex;
            dot.setAttribute('aria-selected', isActive ? 'true' : 'false');
            if (isActive) {
                dot.className = 'carousel-dot h-3 rounded-full transition-all duration-300 w-9 bg-blue-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 cursor-pointer hover:scale-110';
            } else {
                dot.className = 'carousel-dot h-3 rounded-full transition-all duration-300 w-3 bg-slate-300 hover:bg-slate-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 cursor-pointer hover:scale-110';
            }
        });
    }

    // Navegar a una diapositiva específica
    window.goToSlide = function(index) {
        updateCarousel(Number(index));
        resetAutoplay();
    };

    function nextSlide() {
        updateCarousel(currentIndex + 1);
    }

    function prevSlide() {
        updateCarousel(currentIndex - 1);
    }

    // Listeners de botones anterior / siguiente
    btnPrev.addEventListener('click', (e) => {
        e.preventDefault();
        prevSlide();
        resetAutoplay();
    });

    btnNext.addEventListener('click', (e) => {
        e.preventDefault();
        nextSlide();
        resetAutoplay();
    });

    // Delegación de eventos para clicks en los dots indicadores
    dotsContainer.addEventListener('click', (e) => {
        const dot = e.target.closest('.carousel-dot');
        if (dot && dot.hasAttribute('data-slide-index')) {
            e.preventDefault();
            const slideIdx = parseInt(dot.getAttribute('data-slide-index'), 10);
            window.goToSlide(slideIdx);
        }
    });

    // Navegación por teclado (Flechas Izquierda / Derecha)
    container.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
            e.preventDefault();
            prevSlide();
            resetAutoplay();
        } else if (e.key === 'ArrowRight') {
            e.preventDefault();
            nextSlide();
            resetAutoplay();
        }
    });

    // Soporte táctil / Gestos de deslizamiento (Swipe)
    let touchStartX = 0;
    let touchEndX = 0;

    container.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });

    container.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        const diffX = touchStartX - touchEndX;
        if (Math.abs(diffX) > 45) { // Umbral de swipe intencional
            if (diffX > 0) {
                nextSlide();
            } else {
                prevSlide();
            }
            resetAutoplay();
        }
    }, { passive: true });

    // Gestión del ciclo de Auto-reproducción
    function startAutoplay() {
        if (prefersReducedMotion || autoplayTimer) return;
        autoplayTimer = setInterval(() => {
            if (!isPaused) {
                nextSlide();
            }
        }, 5500);
    }

    function stopAutoplay() {
        if (autoplayTimer) {
            clearInterval(autoplayTimer);
            autoplayTimer = null;
        }
    }

    function resetAutoplay() {
        stopAutoplay();
        startAutoplay();
    }

    // Pausar al hacer hover o enfocar con teclado
    container.addEventListener('mouseenter', () => { isPaused = true; });
    container.addEventListener('mouseleave', () => { isPaused = false; });
    container.addEventListener('focusin', () => { isPaused = true; });
    container.addEventListener('focusout', () => { isPaused = false; });

    // Inicializar estado del carrusel en pantalla
    updateCarousel(0);
    startAutoplay();
});
</script>

