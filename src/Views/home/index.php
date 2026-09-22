<!-- 2. Hero Section -->
<section id="inicio" class="w-full bg-[#0b1b3d] text-white py-16 md:py-24 relative overflow-hidden">
    <!-- Decorative Glow Effects -->
    <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 right-10 w-96 h-96 bg-indigo-500/15 rounded-full blur-3xl pointer-events-none"></div>

    <div class="px-4 md:px-8 max-w-7xl mx-auto relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
            
            <!-- Left Text Content (Col 7) -->
            <div class="lg:col-span-7 flex flex-col items-start animate-on-scroll">
                <div class="inline-flex items-center gap-2 bg-blue-500/10 border border-blue-400/30 text-blue-200 px-4 py-2 rounded-full text-sm font-semibold mb-6 backdrop-blur-sm">
                    <svg class="w-4 h-4 text-blue-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span>Tranquilidad para tu familia • Respaldado médicamente</span>
                </div>

                <h1 class="text-4xl md:text-5xl lg:text-[52px] font-extrabold text-white tracking-tight leading-[1.15] mb-6">
                    El cuidado y la salud de quienes más quieres, en un solo lugar y al instante.
                </h1>

                <p class="text-lg md:text-xl text-blue-100/90 font-normal leading-relaxed mb-8 max-w-2xl">
                    Gestiona medicamentos, mantén un historial unificado y asegura acceso rápido a una ficha de emergencia mediante código QR. Diseñado pensando en la tranquilidad de cuidadores y el bienestar de los adultos mayores.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row items-center gap-4 w-full sm:w-auto">
                    <a href="<?php echo APP_URL; ?>/login" class="w-full sm:w-auto bg-[#004ac6] hover:bg-[#003ea8] text-white font-semibold text-base px-8 py-4 rounded-full shadow-lg shadow-blue-600/30 hover:shadow-xl hover:shadow-blue-600/40 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2.5">
                        <span>Comenzar ahora sin costo</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                    
                    <a href="#como-funciona" class="w-full sm:w-auto bg-white/10 hover:bg-white/20 text-white border border-white/20 font-semibold text-base px-8 py-4 rounded-full backdrop-blur-sm transition-all flex items-center justify-center gap-2.5">
                        <svg class="w-5 h-5 text-blue-300" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                        </svg>
                        <span>Ver demo en vivo</span>
                    </a>
                </div>

                <!-- Trust Points -->
                <div class="flex items-center gap-6 mt-10 pt-8 border-t border-blue-900/60 text-sm text-blue-200">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Sin tarjeta requerida</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span>Datos 100% cifrados</span>
                    </div>
                </div>
            </div>

            <!-- Right Visual Mockup (Col 5) -->
            <div class="lg:col-span-5 relative flex items-center justify-center animate-on-scroll" style="transition-delay: 150ms;">
                <div class="relative w-full max-w-[420px]">
                    
                    <!-- Smartphone SaaS Mockup (Spanish UI) -->
                    <div class="w-full bg-slate-900 rounded-[40px] p-3 shadow-2xl border-4 border-slate-700/80 ring-1 ring-white/20">
                        <!-- Phone Screen Content -->
                        <div class="bg-slate-50 text-slate-900 rounded-[32px] overflow-hidden p-5 flex flex-col gap-4 font-sans text-left">
                            
                            <!-- Phone Top Bar -->
                            <div class="flex justify-between items-center pb-2 border-b border-slate-200">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-[#004ac6] flex items-center justify-center text-white">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/></svg>
                                    </div>
                                    <span class="font-bold text-sm text-[#004ac6]">VitalSenior</span>
                                </div>
                                <span class="text-xs bg-emerald-100 text-emerald-800 font-semibold px-2 py-0.5 rounded-full">En línea</span>
                            </div>

                            <!-- Patient Profile Tag -->
                            <div class="flex items-center justify-between bg-blue-50 p-3 rounded-xl border border-blue-100">
                                <div>
                                    <p class="text-xs text-slate-500 font-medium">Expediente de:</p>
                                    <p class="text-sm font-bold text-slate-800">Roberto Gómez S. (74 años)</p>
                                </div>
                                <span class="text-xs font-bold text-blue-700 bg-white px-2.5 py-1 rounded-lg shadow-sm border border-blue-100">Cuidador Activo</span>
                            </div>

                            <!-- Medication Card (Próxima Toma) -->
                            <div class="bg-white p-3.5 rounded-xl shadow-sm border border-slate-200">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Próxima toma</span>
                                    <span class="text-xs font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded">08:00 AM</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-bold text-slate-900 text-sm">Losartán Potásico</p>
                                        <p class="text-xs text-slate-500">50 mg • 1 tableta con agua</p>
                                    </div>
                                    <div class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center shadow-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Health Vitals (Signos Vitales) -->
                            <div class="grid grid-cols-2 gap-2.5">
                                <div class="bg-white p-3 rounded-xl shadow-sm border border-slate-200">
                                    <p class="text-[11px] text-slate-500 font-medium">Presión Arterial</p>
                                    <p class="text-base font-extrabold text-slate-900 mt-0.5">120/80 <span class="text-[10px] font-normal text-slate-400">mmHg</span></p>
                                    <span class="text-[10px] font-semibold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded">Óptima</span>
                                </div>
                                <div class="bg-white p-3 rounded-xl shadow-sm border border-slate-200">
                                    <p class="text-[11px] text-slate-500 font-medium">Glucosa en Ayunas</p>
                                    <p class="text-base font-extrabold text-slate-900 mt-0.5">95 <span class="text-[10px] font-normal text-slate-400">mg/dL</span></p>
                                    <span class="text-[10px] font-semibold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded">Controlada</span>
                                </div>
                            </div>

                            <!-- Next Appointment (Agenda) -->
                            <div class="bg-slate-100/80 p-3 rounded-xl border border-slate-200 flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-[#004ac6]/10 text-[#004ac6] flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-bold text-slate-900 truncate">Cita: Dr. Javier Martínez</p>
                                    <p class="text-[11px] text-slate-500">Cardiología • Jueves 4:00 PM</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Floating Physical QR Emergency Card Overlay -->
                    <div class="absolute -bottom-6 -right-6 md:-right-8 bg-white text-slate-900 p-4 rounded-2xl shadow-2xl border-2 border-red-500/30 w-64 backdrop-blur-md animate-on-scroll" style="transition-delay: 300ms;">
                        <div class="flex items-center justify-between pb-2 mb-2 border-b border-slate-100">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-red-500 animate-pulse"></span>
                                <span class="text-xs font-bold text-red-600 uppercase tracking-wider">Ficha de Emergencia</span>
                            </div>
                            <span class="text-[10px] font-extrabold bg-red-50 text-red-700 px-1.5 py-0.5 rounded">QR SOS</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <!-- QR Pattern Vector -->
                            <div class="w-16 h-16 bg-slate-900 rounded-lg p-1.5 flex items-center justify-center flex-shrink-0 shadow-inner">
                                <svg class="w-full h-full text-white" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M2 2h8v8H2V2zm2 2v4h4V4H4zm10-2h8v8h-8V2zm2 2v4h4V4h-4zM2 14h8v8H2v-8zm2 2v4h4v-4H4zm14 0h4v4h-4v-4zm-4 4h4v4h-4v-4zm0-4h4v-4h-4v4zm4-4h4v-4h-4v4zM5 5h2v2H5V5zm12 0h2v2h-2V5zM5 17h2v2H5v-2z" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-900">Sangre: <span class="text-red-600">O+</span></p>
                                <p class="text-[11px] text-slate-600 truncate">Alergias: Penicilina</p>
                                <p class="text-[11px] text-slate-500 font-semibold">SOS: Red Directa Familiar</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

<!-- 3. Pain vs Solution -->
<section id="como-funciona" class="w-full bg-slate-50 py-16 md:py-24 border-b border-slate-200/80">
    <div class="px-4 md:px-8 max-w-7xl mx-auto">
        <div class="text-center mb-16 animate-on-scroll">
            <span class="text-[#004ac6] font-bold text-sm uppercase tracking-wider">Comparativa de Cuidado</span>
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 mt-2 mb-4">¿Por qué usar VitalSenior?</h2>
            <p class="text-lg text-slate-600 max-w-2xl mx-auto">Comprende la diferencia entre depender de la memoria y contar con una plataforma integral diseñada para la salud familiar.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Column 1: Pain -->
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-200 flex flex-col gap-6 animate-on-scroll" style="transition-delay: 100ms;">
                <h3 class="text-2xl font-bold text-slate-900 border-b border-slate-100 pb-4 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <span>El Riesgo Diario</span>
                </h3>
                <ul class="flex flex-col gap-4 text-base text-slate-600">
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        <span>Olvido o duplicación accidental en la toma de medicamentos recetados.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        <span>Historial médico fragmentado y recetas dispersas entre distintos especialistas.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        <span>Pérdida de tiempo crítico intentando contactar familiares durante emergencias médicas.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        <span>Resultados clínicos y estudios de laboratorio extraviados o sin seguimiento.</span>
                    </li>
                </ul>
            </div>

            <!-- Column 2: Solution -->
            <div class="bg-white rounded-3xl p-8 shadow-xl shadow-blue-900/5 border-2 border-blue-600/30 flex flex-col gap-6 relative overflow-hidden animate-on-scroll" style="transition-delay: 200ms;">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-bl-full -z-10 opacity-70"></div>
                <h3 class="text-2xl font-bold text-[#004ac6] border-b border-blue-100 pb-4 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-100 text-[#004ac6] flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span>Con VitalSenior</span>
                </h3>
                <ul class="flex flex-col gap-4 text-base text-slate-800 font-medium">
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-blue-50 text-[#004ac6] flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span>Control estricto de medicamentos con recordatorios inteligentes y registro de dosis.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-blue-50 text-[#004ac6] flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span>Expediente único y unificado, accesible de forma segura desde cualquier dispositivo.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-blue-50 text-[#004ac6] flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span>Ficha de emergencia QR para acceso inmediato a contactos vitales por primeros respondientes.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-blue-50 text-[#004ac6] flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span>Bóveda digital segura para almacenar y organizar todos los resultados clínicos familiares.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- 4. Modules Section -->
<section id="modulos" class="w-full px-4 md:px-8 max-w-7xl mx-auto py-16 md:py-24">
    <div class="text-center mb-16 animate-on-scroll">
        <span class="text-[#004ac6] font-bold text-sm uppercase tracking-wider">Funcionalidades Principales</span>
        <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 mt-2 mb-4">Nuestros Módulos</h2>
        <p class="text-lg text-slate-600 max-w-2xl mx-auto">Herramientas completas diseñadas con altos estándares de accesibilidad para simplificar el cuidado diario.</p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Module 1 -->
        <div class="bg-white p-8 rounded-3xl shadow-sm hover:shadow-md transition-shadow border border-slate-200 text-center animate-on-scroll" style="transition-delay: 100ms;">
            <div class="w-16 h-16 bg-blue-50 text-[#004ac6] rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-sm">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-3">Control de Medicación</h3>
            <p class="text-slate-600 leading-relaxed">Registros precisos, horarios de dosificación y alertas para no omitir ningún tratamiento prescrito.</p>
        </div>

        <!-- Module 2 -->
        <div class="bg-white p-8 rounded-3xl shadow-sm hover:shadow-md transition-shadow border border-slate-200 text-center animate-on-scroll" style="transition-delay: 200ms;">
            <div class="w-16 h-16 bg-blue-50 text-[#004ac6] rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-sm">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-3">Agenda Médica</h3>
            <p class="text-slate-600 leading-relaxed">Centraliza consultas con especialistas, análisis clínicos y revisiones periódicas en un solo calendario familiar.</p>
        </div>

        <!-- Module 3 -->
        <div class="bg-white p-8 rounded-3xl shadow-sm hover:shadow-md transition-shadow border border-slate-200 text-center animate-on-scroll" style="transition-delay: 300ms;">
            <div class="w-16 h-16 bg-blue-50 text-[#004ac6] rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-sm">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-3">Historial Clínico</h3>
            <p class="text-slate-600 leading-relaxed">Monitorea signos vitales, alergias, diagnósticos y la evolución de la salud con reportes claros y ordenados.</p>
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

