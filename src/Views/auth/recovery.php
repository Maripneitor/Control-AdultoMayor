<!DOCTYPE html>

<html lang="es"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>VitalSenior - Recuperar Contraseña</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            "colors": {
                    "secondary-fixed-dim": "#b6c4ff",
                    "secondary-container": "#8fa7fe",
                    "error": "#ba1a1a",
                    "tertiary-fixed-dim": "#b7c9d5",
                    "on-primary-fixed-variant": "#003ea8",
                    "on-tertiary": "#ffffff",
                    "surface-container-low": "#f2f4f6",
                    "on-error-container": "#93000a",
                    "on-background": "#191c1e",
                    "tertiary-container": "#5f707a",
                    "on-primary": "#ffffff",
                    "inverse-primary": "#b4c5ff",
                    "surface": "#f7f9fb",
                    "primary-fixed-dim": "#b4c5ff",
                    "surface-dim": "#d8dadc",
                    "inverse-surface": "#2d3133",
                    "primary-fixed": "#dbe1ff",
                    "on-surface": "#191c1e",
                    "outline-variant": "#c3c6d7",
                    "surface-container-high": "#e6e8ea",
                    "background": "#f7f9fb",
                    "surface-bright": "#f7f9fb",
                    "tertiary": "#475861",
                    "on-tertiary-fixed-variant": "#384953",
                    "surface-container": "#eceef0",
                    "on-tertiary-container": "#e2f4ff",
                    "surface-container-lowest": "#ffffff",
                    "inverse-on-surface": "#eff1f3",
                    "on-secondary-container": "#1d3989",
                    "on-primary-container": "#eeefff",
                    "on-primary-fixed": "#00174b",
                    "primary": "#004ac6",
                    "error-container": "#ffdad6",
                    "on-error": "#ffffff",
                    "surface-variant": "#e0e3e5",
                    "on-secondary-fixed": "#00164e",
                    "outline": "#737686",
                    "surface-container-highest": "#e0e3e5",
                    "on-secondary": "#ffffff",
                    "secondary-fixed": "#dce1ff",
                    "on-surface-variant": "#434655",
                    "on-tertiary-fixed": "#0c1e26",
                    "primary-container": "#2563eb",
                    "tertiary-fixed": "#d3e5f1",
                    "secondary": "#4059aa",
                    "surface-tint": "#0053db",
                    "on-secondary-fixed-variant": "#264191"
            },
            "borderRadius": {
                    "DEFAULT": "0.25rem",
                    "lg": "0.5rem",
                    "xl": "0.75rem",
                    "full": "9999px"
            },
            "spacing": {
                    "margin-mobile": "20px",
                    "stack-sm": "12px",
                    "container-max": "1280px",
                    "stack-lg": "48px",
                    "stack-md": "24px",
                    "unit": "8px",
                    "gutter": "24px"
            },
            "fontFamily": {
                    "headline-lg": ["Outfit"],
                    "headline-md": ["Outfit"],
                    "body-lg": ["Outfit"],
                    "label-md": ["Outfit"],
                    "body-md": ["Outfit"],
                    "display-lg": ["Outfit"],
                    "headline-lg-mobile": ["Outfit"]
            },
            "fontSize": {
                    "headline-lg": ["32px", {"lineHeight": "40px", "fontWeight": "600"}],
                    "headline-md": ["24px", {"lineHeight": "32px", "fontWeight": "600"}],
                    "body-lg": ["20px", {"lineHeight": "30px", "fontWeight": "400"}],
                    "label-md": ["16px", {"lineHeight": "24px", "fontWeight": "600"}],
                    "body-md": ["18px", {"lineHeight": "28px", "fontWeight": "400"}],
                    "display-lg": ["48px", {"lineHeight": "56px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                    "headline-lg-mobile": ["28px", {"lineHeight": "36px", "fontWeight": "600"}]
            }
          },
        },
      }
    </script>
<style>
        body {
            background-color: #f8fafc; /* Fallback */
            background-color: theme('colors.surface-container-low');
        }
        /* Custom Scrollbar for better UI */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: theme('colors.surface-variant');
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: theme('colors.outline');
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-margin-mobile md:p-gutter antialiased text-on-surface">
<main class="w-full max-w-[1024px] bg-surface-container-lowest rounded-2xl shadow-[0_8px_30px_rgb(0,74,198,0.15)] overflow-hidden flex flex-col md:flex-row min-h-[600px]">
<!-- Left Column: Brand Showcase -->
<section class="hidden md:flex md:w-1/2 bg-primary relative flex-col justify-between p-stack-lg overflow-hidden group">
<!-- Decorative Elements -->
<div class="absolute inset-0 opacity-20 pointer-events-none">
<svg height="100%" width="100%" xmlns="http://www.w3.org/2000/svg">
<defs>
<pattern height="40" id="grid" patternunits="userSpaceOnUse" width="40">
<path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="0.5"></path>
</pattern>
</defs>
<rect fill="url(#grid)" height="100%" width="100%"></rect>
</svg>
</div>
<div class="relative z-10">
<div class="flex items-center gap-2 mb-stack-lg">
<span class="material-symbols-outlined text-on-primary text-[32px]">medical_services</span>
<h1 class="font-headline-lg text-headline-lg text-on-primary">VitalSenior</h1>
</div>
<h2 class="font-headline-md text-headline-md text-on-primary mb-stack-md leading-tight">
                    Cuidamos de tu salud. Gestión sencilla para ti y tu familia.
                </h2>
<p class="font-body-md text-body-md text-on-primary/80">
                    Tu bienestar y tranquilidad son nuestra prioridad. Recupera el acceso a tu expediente médico de forma segura.
                </p>
</div>
<!-- Image/Illustration Area -->
<div class="relative z-10 w-full flex-grow mt-stack-lg rounded-xl overflow-hidden shadow-lg border border-white/10 bg-white/5 backdrop-blur-sm flex items-center justify-center">
<img class="absolute inset-0 w-full h-full object-cover opacity-90 transition-transform duration-700 group-hover:scale-105" data-alt="A comforting, stylized illustration in a clinical humanist style showing a caring medical professional in light blue scrubs holding a digital tablet, smiling reassuringly at a senior person. The background is a soft, minimalist gradient of sapphire blue and pure white, conveying trust, modern healthcare, and emotional support. High quality, soft lighting, clean lines." src="https://lh3.googleusercontent.com/aida-public/AB6AXuBX3lubAEqZagWUmjXd9g1DCMCipje4vGR9ej7I-HU2cZZvjF_I_hVDizcFe1C3MJWXnh_OpWgv26gbHMiBxGhaSWTrj6P6nCEVRw-AWJltiq4wB-SqxwPK6HNiKRWG1cbYQFWfQDeAaNCrQi9vM4Aw1-knGxrX92ylJDS5GSqoC-DPc7KxE6c-9yDl2ykoA9K4VLKlDj0H5EuNgUXQ_vjq9Qws0w9gJeCFikoghEq6uzqOVL2ix8zi5A"/>
<div class="absolute inset-0 bg-gradient-to-t from-primary/80 to-transparent"></div>
</div>
</section>
<!-- Right Column: Recovery Form -->
<section class="w-full md:w-1/2 p-stack-lg flex flex-col justify-center bg-surface-container-lowest">
<!-- Mobile Logo (Hidden on Desktop) -->
<div class="md:hidden flex items-center gap-2 mb-stack-lg justify-center">
<span class="material-symbols-outlined text-primary text-[32px]">medical_services</span>
<span class="font-headline-lg-mobile text-headline-lg-mobile text-primary">VitalSenior</span>
</div>
<div class="max-w-md w-full mx-auto">
<div class="mb-stack-lg text-center md:text-left">
<h2 class="font-headline-md text-headline-md text-on-surface mb-stack-sm">¿Olvidaste tu contraseña?</h2>
<p class="font-body-md text-body-md text-on-surface-variant">
                        No te preocupes, dinos tu correo electrónico y te enviaremos las instrucciones para restablecerla.
                    </p>
</div>
<form action="#" class="space-y-stack-md" method="POST">
<!-- Email Field -->
<div>
<label class="block font-label-md text-label-md text-on-surface mb-unit" for="email">Correo Electrónico</label>
<div class="relative">
<div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
<span class="material-symbols-outlined text-outline" style="font-variation-settings: 'FILL' 0;">mail</span>
</div>
<input class="block w-full pl-12 pr-4 py-4 text-[18px] bg-surface rounded-xl border-2 border-surface-variant focus:border-primary focus:ring-0 transition-colors text-on-surface placeholder-outline-variant" id="email" name="email" placeholder="tu@correo.com" required="" type="email"/>
</div>
</div>
<!-- Submit Button -->
<button class="w-full min-h-[56px] bg-primary text-on-primary font-label-md text-label-md rounded-xl shadow-sm hover:shadow-md hover:-translate-y-[1px] active:translate-y-[1px] active:shadow-none transition-all duration-200 flex items-center justify-center gap-2 mt-stack-md" type="submit">
                        ENVIAR INSTRUCCIONES
                    </button>
</form>
<!-- Back to Login Link -->
<div class="mt-stack-lg text-center">
<a class="inline-flex items-center gap-2 font-label-md text-label-md text-primary hover:text-on-primary-fixed-variant transition-colors group" href="{{DATA:SCREEN:SCREEN_2}}">
<span class="material-symbols-outlined text-[20px] group-hover:-translate-x-1 transition-transform">arrow_back</span>
                        Volver al inicio de sesión
                    </a>
</div>
</div>
</section>
</main>
</body></html>