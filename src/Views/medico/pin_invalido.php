<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Médico Denegado - VitalSenior</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-100 min-h-full flex items-center justify-center p-4 font-['Plus_Jakarta_Sans',sans-serif]">
    <div class="max-w-md w-full bg-white rounded-3xl p-8 shadow-xl border border-slate-200 text-center">
        <div class="w-16 h-16 bg-rose-100 text-rose-600 rounded-2xl flex items-center justify-center text-3xl font-black mx-auto mb-4">
            🔒
        </div>
        <h1 class="text-xl font-bold text-slate-900 mb-2">Acceso Clínico Restringido</h1>
        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-6">
            <?= htmlspecialchars($mensaje ?? 'El PIN de seguridad ha expirado, no existe o superó su límite de consultas permitidas.') ?>
        </p>
        
        <form action="<?= APP_URL ?>/medico/consulta" method="GET" class="space-y-3">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1 text-left">
                    Ingresar otro PIN de 6 dígitos:
                </label>
                <input type="text" name="pin" required maxlength="8" placeholder="Ej. 842910" 
                       class="w-full text-center tracking-widest text-lg font-mono font-bold rounded-2xl border-slate-300 px-4 py-3 bg-slate-50 focus:border-blue-600 focus:bg-white focus:ring-4 focus:ring-blue-500/10 outline-none">
            </div>
            <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-2xl shadow-md shadow-blue-500/20 transition cursor-pointer">
                Verificar PIN
            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-slate-100 text-[11px] text-slate-400">
            VitalSenior • Portal de Seguridad Médica Hospitalaria
        </div>
    </div>
</body>
</html>
