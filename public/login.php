<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baramovil - Acceso</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: 'var(--color-primary, #ea580c)',
                            dark: 'color-mix(in srgb, var(--color-primary, #ea580c) 80%, black)',
                            light: 'color-mix(in srgb, var(--color-primary, #ea580c) 80%, white)',
                            100: 'color-mix(in srgb, var(--color-primary, #ea580c) 10%, white)',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background: radial-gradient(circle at top right, #fff5e6, #ffffff);
            font-family: 'Inter', sans-serif;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        <!-- Card de Login -->
        <div class="glass-card shadow-2xl rounded-3xl p-8 sm:p-10 transition-all duration-300 hover:shadow-primary/10">
            <div class="flex flex-col items-center mb-8">
                <div class="bg-primary/10 p-4 rounded-2xl mb-4">
                    <img id="loginLogo" src="img/Logo.jpg" alt="Baramovil" class="w-20 h-20 object-contain rounded-xl shadow-sm">
                </div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Bienvenido</h1>
                <p class="text-gray-500 mt-2 font-medium">Gestiona tus pedidos con facilidad</p>
            </div>

            <form id="loginForm" class="space-y-6">
                <!-- Usuario -->
                <div class="space-y-2">
                    <label for="username" class="text-sm font-semibold text-gray-700 ml-1">Usuario</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-primary">
                            <svg class="h-5 w-5 text-gray-400 group-focus-within:text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <input type="text" id="username" name="username" required 
                               class="block w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl text-gray-900 text-sm ring-primary/20 focus:ring-4 focus:border-primary focus:bg-white transition-all outline-none" 
                               placeholder="Identificación de usuario">
                    </div>
                </div>

                <!-- Contraseña -->
                <div class="space-y-2">
                    <div class="flex justify-between items-center px-1">
                        <label for="password" class="text-sm font-semibold text-gray-700">Contraseña</label>
                    </div>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 group-focus-within:text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input type="password" id="password" name="password" required 
                               class="block w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl text-gray-900 text-sm ring-primary/20 focus:ring-4 focus:border-primary focus:bg-white transition-all outline-none" 
                               placeholder="••••••••">
                    </div>
                </div>

                <!-- Botón Login -->
                <button type="submit" id="btnLogin"
                        class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-4 rounded-2xl shadow-lg shadow-primary/30 transition-all duration-300 transform hover:-translate-y-1 active:scale-[0.98] flex items-center justify-center gap-2">
                    <span>Iniciar Sesión</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </button>
            </form>

            <footer class="mt-10 text-center text-sm text-gray-400 font-medium">
                &copy; 2026 Baramovil &middot; <span class="text-primary/60">Sales Force Edition</span>
            </footer>
        </div>
    </div>

    <!-- MODAL MENSAJES -->
    <div id="modalMessage" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/40 backdrop-blur-sm transition-opacity duration-300">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden transform transition-all scale-95 opacity-0 duration-300 zoom-in">
            <div class="bg-primary p-6 text-white text-center relative">
                <div class="absolute -bottom-6 left-1/2 -translate-x-1/2 bg-white rounded-full p-2 shadow-lg">
                    <div class="bg-primary/10 rounded-full p-2">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <h3 id="modalTitle" class="text-xl font-bold">Aviso</h3>
            </div>
            <div class="p-8 pt-10 text-center">
                <p id="modalBody" class="text-gray-600 font-medium leading-relaxed mb-6"></p>
                <button onclick="hideModal()" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-3.5 rounded-2xl transition-all duration-200">
                    Entendido
                </button>
            </div>
        </div>
    </div>

    <script>
        const loginForm = document.getElementById('loginForm');
        const btnLogin = document.getElementById('btnLogin');
        const modal = document.getElementById('modalMessage');
        const mTitle = document.getElementById('modalTitle');
        const mBody = document.getElementById('modalBody');
        const mContent = modal.querySelector('div.bg-white');

        function showMessage(title, text) {
            mTitle.textContent = title;
            mBody.textContent = text;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            // Animación
            setTimeout(() => {
                mContent.classList.remove('scale-95', 'opacity-0');
                mContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function hideModal() {
            mContent.classList.add('scale-95', 'opacity-0');
            mContent.classList.remove('scale-100', 'opacity-100');
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 300);
        }

        loginForm.onsubmit = async (e) => {
            e.preventDefault();
            
            const originalText = btnLogin.innerHTML;
            btnLogin.disabled = true;
            btnLogin.innerHTML = `
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Verificando...</span>
            `;

            const payload = {
                username: document.getElementById('username').value.trim(),
                password: document.getElementById('password').value
            };

            try {
                const res = await fetch('../api.php?action=login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                }).then(r => r.json());

                if (res.success) {
                    localStorage.setItem('ope_web', res.ope_web || 0);
                    window.location.href = 'index.php';
                } else {
                    showMessage('Acceso Denegado', res.error || 'Credenciales incorrectas');
                    btnLogin.disabled = false;
                    btnLogin.innerHTML = originalText;
                }
            } catch (error) {
                showMessage('Error', 'No se pudo conectar con el servidor.');
                btnLogin.disabled = false;
                btnLogin.innerHTML = originalText;
            }
        };

        // Fetch config on load
        window.addEventListener('DOMContentLoaded', async () => {
            try {
                const res = await fetch('../api.php?action=get_config').then(r => r.json());
                if (res.success && res.config) {
                    document.documentElement.style.setProperty('--color-primary', res.config.WEB_COLOR);
                    if (res.config.WEB_LOGO === '0') {
                        document.getElementById('loginLogo').parentElement.style.display = 'none';
                    }
                }
            } catch (e) {
                console.error("No se pudo cargar la configuración", e);
            }
        });
    </script>
</body>
</html>
