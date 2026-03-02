<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Baramovil - Productos</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
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
                            50: 'color-mix(in srgb, var(--color-primary, #ea580c) 10%, white)',
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
        body { font-family: 'Inter', sans-serif; background-color: #f9fafb; }
        .drawer-overlay {
            transition: opacity 0.3s ease;
            background-color: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(2px);
        }
        .drawer-content {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card { transition: all 0.2s ease; }
        .card:active { transform: scale(0.98); }
        /* Hide scrollbar */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
  </head>
  <body class="text-gray-900 overflow-x-hidden">
    
    <!-- Top Navigation -->
    <header class="sticky top-0 z-40 w-full bg-white/80 backdrop-blur-md border-b border-gray-100 shadow-sm">
        <div class="px-4 h-16 flex items-center justify-between gap-3">
            <button id="burgerBtn" class="p-2 -ml-2 rounded-xl text-gray-600 hover:bg-gray-100 active:bg-gray-200 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            
            <div class="flex-1 flex items-center bg-gray-100 rounded-2xl px-3 py-2 transition-within:ring-2 ring-primary/20">
                <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input id="searchInput" type="text" placeholder="Buscar..." 
                       class="bg-transparent border-none outline-none text-sm w-full font-medium placeholder-gray-400">
            </div>

            <div class="flex items-center gap-1">
                <button id="btnOpenCart" class="relative p-2 rounded-xl text-gray-600 hover:bg-gray-100 transition-colors disabled:opacity-30">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span id="cartBadge" class="absolute top-1 right-1 bg-primary text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full min-w-[18px] hidden border-2 border-white">0</span>
                </button>
                <button id="btnLogout" class="p-2 rounded-xl text-red-500 hover:bg-red-50 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Client Status Bar -->
        <div id="clientStatusBar" class="px-4 py-2 bg-primary/5 border-t border-primary/10 flex items-center justify-between overflow-hidden">
            <div class="flex items-center gap-2 overflow-hidden">
                <div class="w-2 h-2 rounded-full bg-primary animate-pulse shrink-0"></div>
                <span id="clientLabel" class="text-xs font-semibold text-primary truncate max-w-xs">Sin cliente seleccionado</span>
            </div>
            <span class="text-[10px] uppercase tracking-wider font-bold text-primary/40">Baramovil Live</span>
        </div>
    </header>

    <!-- Sidebar / Drawer -->
    <div id="sidebarOverlay" class="fixed inset-0 z-50 hidden bg-black/40 backdrop-blur-sm">
        <aside id="sidebar" class="fixed top-0 left-0 h-full w-80 max-w-[85vw] bg-white shadow-2xl z-50 transform -translate-x-full transition-transform duration-300 ease-in-out">
            <div class="flex flex-col h-full uppercase">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-xs font-bold text-gray-400 tracking-widest">MENU PRINCIPAL</h2>
                        <img id="appLogo" src="img/Logo.jpg" class="h-8 mt-2 opacity-80" alt="Logo">
                    </div>
                    <button id="closeSidebar" class="p-2 rounded-lg text-gray-400 hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="flex-1 overflow-y-auto no-scrollbar py-4 px-2 space-y-4">
                    <!-- SECCIÓN PEDIDO -->
                    <div id="menuOperaciones">
                        <div class="px-4 mb-2 text-[10px] font-black text-gray-300 tracking-[0.2em]">OPERACIONES</div>
                        <div class="space-y-1">
                            <div class="menu-section">
                                <button class="menu-title w-full flex items-center justify-between p-3 rounded-xl text-gray-700 font-semibold hover:bg-gray-50 transition-colors">
                                    <span class="flex items-center gap-3">
                                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                        Clientes
                                    </span>
                                    <svg class="w-4 h-4 text-gray-400 arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </button>
                                <div class="submenu overflow-hidden max-h-0 transition-all duration-300 bg-gray-50/50 rounded-xl mt-1">
                                    <button id="btnBuscarCliente" class="w-full text-left p-3 pl-11 text-sm font-medium text-gray-600 hover:text-primary transition-colors">🔍 Buscar Cliente</button>
                                    <button id="btnCrearCliente" class="w-full text-left p-3 pl-11 text-sm font-medium text-gray-600 hover:text-primary transition-colors">➕ Crear Cliente</button>
                                </div>
                            </div>
                            <button id="btnPedidos" class="w-full flex items-center gap-3 p-3 rounded-xl text-gray-700 font-semibold hover:bg-gray-50 transition-colors">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                                Historial Hoy
                            </button>
                        </div>
                    </div>

                    <!-- SECCIÓN INVENTARIO -->
                    <div>
                        <div class="px-4 mb-2 text-[10px] font-black text-gray-300 tracking-[0.2em]">CATÁLOGO</div>
                        <div class="space-y-1">
                            <button id="btnAdmin" class="w-full hidden items-center gap-3 p-3 rounded-xl text-gray-700 font-semibold hover:bg-gray-50 transition-colors">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                Administración
                            </button>
                            <button id="showAllProducts" class="w-full flex items-center gap-3 p-3 rounded-xl text-gray-700 font-semibold hover:bg-gray-50 transition-colors">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                                Todos los Productos
                            </button>
                            <div class="menu-section">
                                <button class="menu-title w-full flex items-center justify-between p-3 rounded-xl text-gray-700 font-semibold hover:bg-gray-50 transition-colors">
                                    <span class="flex items-center gap-3">
                                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
                                        Departamentos
                                    </span>
                                    <svg class="w-4 h-4 text-gray-400 arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </button>
                                <div id="departmentsMenu" class="submenu overflow-hidden max-h-0 transition-all duration-300 bg-gray-50/50 rounded-xl mt-1"></div>
                            </div>
                            <div class="menu-section">
                                <button class="menu-title w-full flex items-center justify-between p-3 rounded-xl text-gray-700 font-semibold hover:bg-gray-50 transition-colors">
                                    <span class="flex items-center gap-3">
                                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 11h.01M7 15h.01M11 7h.01M11 11h.01M11 15h.01M15 7h.01M15 11h.01M15 15h.01" /></svg>
                                        Categorías
                                    </span>
                                    <svg class="w-4 h-4 text-gray-400 arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </button>
                                <div id="categoriesMenu" class="submenu overflow-hidden max-h-0 transition-all duration-300 bg-gray-50/50 rounded-xl mt-1"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="ratesContainer" class="px-4 pt-3 pb-1">
                    <div class="bg-gray-50 border border-gray-100 rounded-2xl p-4 flex flex-col gap-2">
                        <p class="text-[9px] text-gray-400 font-black uppercase tracking-widest mb-1">Tasas del Día</p>
                        <div id="rateBcvContainer" class="flex items-center justify-between">
                            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Tasa BCV</span>
                            <span id="rateBCV" class="text-sm font-black text-primary">—</span>
                        </div>
                        <div id="rateCopContainer" class="flex items-center justify-between">
                            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Tasa COP</span>
                            <span id="rateCOP" class="text-sm font-black text-gray-700">—</span>
                        </div>
                    </div>
                </div>

                <div class="p-4 border-t border-gray-100 italic lowercase">
                    <div class="bg-primary/5 rounded-2xl p-4">
                        <p class="text-[10px] text-primary/60 font-bold uppercase tracking-widest mb-1">Usuario Logeado</p>
                        <p class="text-sm font-extrabold text-gray-800"><?php echo $_SESSION['user_name']; ?></p>
                    </div>
                </div>
            </div>
        </aside>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 py-6 lowercase">
        <div id="filtersResume" class="text-xs font-bold text-gray-400 mb-6 uppercase tracking-wider">Mostrando todos los productos</div>
        <div id="cardsContainer" class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-2 sm:gap-5">
            <!-- Cards will be injected here -->
        </div>
        
        <!-- Loading Indicator for Infinite Scroll -->
        <div id="scrollLoading" class="hidden py-8 flex justify-center">
            <div class="flex items-center gap-2 text-primary font-bold animate-pulse text-sm">
                <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>CARGANDO MÁS PRODUCTOS...</span>
            </div>
        </div>
    </main>

    <!-- MODAL AGREGAR UNIDADES -->
    <div id="modalAgregarUnidades" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/40 backdrop-blur-sm transition-opacity duration-300">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden transform transition-all scale-95 opacity-0 duration-300">
            <div class="bg-primary p-6 text-white text-center relative">
                <div class="absolute -bottom-6 left-1/2 -translate-x-1/2 bg-white rounded-full p-2 shadow-lg">
                    <div class="bg-primary/10 rounded-full p-2">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                </div>
                <h3 class="text-xl font-bold uppercase tracking-widest">Añadir Unidades</h3>
            </div>
            <div class="p-8 pt-10 text-center">
                <div class="flex items-center justify-center gap-4 mb-8">
                    <button onclick="document.getElementById('inputUnidades').stepDown()" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" /></svg>
                    </button>
                    <input type="number" id="inputUnidades" value="1" min="1" 
                           class="w-24 text-4xl font-black text-center text-gray-900 bg-transparent border-none outline-none">
                    <button onclick="document.getElementById('inputUnidades').stepUp()" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    </button>
                </div>
                <div class="flex gap-3 mt-4">
                    <button id="btnCancelarUnidades" class="flex-1 px-6 py-4 bg-gray-100 hover:bg-gray-200 text-gray-500 font-bold rounded-2xl transition-all">CANCELAR</button>
                    <button id="btnConfirmarUnidades" class="flex-2 px-8 py-4 bg-primary hover:bg-primary-dark text-white font-bold rounded-2xl shadow-lg shadow-primary/30 transition-all">AÑADIR</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL PARA CLIENTES -->
    <div id="modalClientes" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/40 backdrop-blur-sm transition-opacity duration-300">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all scale-95 opacity-0 duration-300 max-h-[90vh] flex flex-col">
            <header class="p-6 border-b border-gray-100 flex items-center justify-between">
                <h3 id="modalTitle" class="text-xl font-bold text-gray-900 tracking-tight uppercase">Gestión de Cliente</h3>
                <button id="closeModal" class="p-2 rounded-xl text-gray-400 hover:bg-gray-100 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </header>
            
            <div class="flex-1 overflow-y-auto no-scrollbar p-6">
                <!-- Vista Buscar -->
                <div id="viewBuscarCliente">
                    <div class="relative group mb-4">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-primary">
                            <svg class="h-5 w-5 text-gray-400 group-focus-within:text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </div>
                        <input type="text" id="searchClientName" 
                               class="block w-full pl-11 pr-4 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-gray-900 text-sm ring-primary/10 focus:ring-4 focus:bg-white focus:border-primary transition-all outline-none" 
                               placeholder="Buscar por nombre o RIF...">
                    </div>
                    <div id="clientSearchResults" class="space-y-2">
                        <!-- Items dynamicos -->
                    </div>
                </div>

                <!-- Vista Crear -->
                <form id="formCrearCliente" class="hidden space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-gray-400 px-1 uppercase letter-spacing-widest">Código</label>
                            <input type="text" id="newClientCode" readonly class="w-full px-4 py-3 bg-gray-100 rounded-xl text-gray-500 font-bold border-none outline-none">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-gray-400 px-1 uppercase letter-spacing-widest">RIF</label>
                            <input type="text" id="newClientRif" required class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 ring-primary/20 outline-none">
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-gray-400 px-1 uppercase letter-spacing-widest">Nombre</label>
                        <input type="text" id="newClientName" required class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 ring-primary/20 outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-gray-400 px-1 uppercase letter-spacing-widest">Apellido</label>
                        <input type="text" id="newClientLastName" required class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 ring-primary/20 outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-gray-400 px-1 uppercase letter-spacing-widest">Dirección</label>
                        <input type="text" id="newClientDir" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 ring-primary/20 outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-gray-400 px-1 uppercase letter-spacing-widest">Teléfono</label>
                        <input type="text" id="newClientTel" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 ring-primary/20 outline-none">
                    </div>
                    <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-4 rounded-2xl shadow-lg transition-all active:scale-[0.98]">
                        GUARDAR Y SELECCIONAR
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL CARRITO -->
    <div id="modalCarrito" class="fixed inset-0 z-50 hidden items-end sm:items-center justify-center p-0 sm:p-4 bg-black/40 backdrop-blur-sm transition-opacity duration-300">
        <div class="bg-white rounded-t-3xl sm:rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden transform transition-all translate-y-full sm:translate-y-0 sm:scale-95 opacity-0 duration-300 flex flex-col max-h-[90vh]">
            <header class="p-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 tracking-tight uppercase">🛒 Mi Pedido</h3>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Revisa tus productos seleccionados</p>
                </div>
                <button id="closeCart" class="p-2 rounded-xl text-gray-400 hover:bg-gray-100 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </header>
            
            <div class="flex-1 overflow-y-auto no-scrollbar">
                <!-- Summary Cards -->
                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-3 bg-gray-50/50 uppercase italic">
                    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-col items-center">
                        <span class="text-[9px] text-gray-400 font-black tracking-widest mb-1">TOTAL BOLÍVARES</span>
                        <span id="bsTotal" class="text-lg font-black text-gray-900 leading-none">0.00</span>
                    </div>
                    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-col items-center">
                        <span class="text-[9px] text-gray-400 font-black tracking-widest mb-1">TOTAL PESOS</span>
                        <span id="copTotal" class="text-lg font-black text-gray-900 leading-none">0.00</span>
                    </div>
                    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-col items-center">
                        <span class="text-[9px] text-gray-400 font-black tracking-widest mb-1">TOTAL DÓLARES</span>
                        <span id="usdTotal" class="text-lg font-black text-primary leading-none">0.00</span>
                    </div>
                </div>

                <div class="px-6 py-2 flex items-center justify-between text-[10px] font-black text-gray-300 tracking-[0.2em] bg-white sticky top-0 uppercase italic">
                    <span>PRODUCTOS EN CARRITO (<span id="summaryCount">0</span>)</span>
                    <span>CANTIDADES: <span id="summaryQty">0</span></span>
                </div>

                <div id="cartItemsList" class="p-6 space-y-4">
                    <!-- Items dinámicos -->
                </div>
                
                <div id="cartCommentContainer" class="px-6 py-4 border-t border-gray-100 hidden">
                    <label for="cartComment" class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Comentario Adicional</label>
                    <textarea id="cartComment" rows="2" maxlength="200" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all placeholder-gray-400" placeholder="Escribe un comentario o instrucción especial..."></textarea>
                </div>
            </div>

            <footer class="p-6 bg-white border-t border-gray-100">
                <button id="btnFinalizarPedido" class="w-full bg-primary hover:bg-primary-dark text-white font-black py-5 rounded-2xl shadow-xl shadow-primary/30 transition-all transform hover:-translate-y-1 active:scale-[0.98] flex items-center justify-center gap-3 uppercase italic">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" /></svg>
                    ENVIAR PEDIDO AHORA
                </button>
            </footer>
        </div>
    </div>

    <!-- MODAL LISTADO DE PEDIDOS -->
    <div id="modalListadoPedidos" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/40 backdrop-blur-sm transition-opacity duration-300">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-4xl overflow-hidden transform transition-all scale-95 opacity-0 duration-300 flex flex-col max-h-[85vh]">
            <header class="p-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 tracking-tight uppercase tracking-widest">📋 Pedidos del Día</h3>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Listado de ventas procesadas hoy</p>
                </div>
                <button id="closeOrders" class="p-2 rounded-xl text-gray-400 hover:bg-gray-100 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </header>
            
            <div class="flex-1 overflow-y-auto no-scrollbar lowercase italic">
                <div id="ordersTableContainer" class="hidden overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 text-[10px] font-black text-gray-400 uppercase tracking-widest sticky top-0">
                            <tr>
                                <th class="px-6 py-4">NÚMERO</th>
                                <th class="px-6 py-4">CLIENTE</th>
                                <th class="px-6 py-4 text-right">TOTAL</th>
                                <th class="px-6 py-4">HORA</th>
                            </tr>
                        </thead>
                        <tbody id="ordersTableBody" class="divide-y divide-gray-50">
                            <!-- Items -->
                        </tbody>
                    </table>
                </div>
                <div id="noOrdersMessage" class="hidden py-20 text-center uppercase tracking-widest text-gray-400 font-bold">
                    <div class="bg-gray-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    </div>
                    No hay pedidos registrados hoy
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL ADMINISTRACIÓN -->
    <div id="modalAdmin" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/40 backdrop-blur-sm transition-opacity duration-300">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all scale-95 opacity-0 duration-300 flex flex-col max-h-[85vh]">
            <header class="p-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 tracking-tight uppercase tracking-widest">⚙️ Administración</h3>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Configuración global de la App</p>
                </div>
                <button id="closeAdmin" class="p-2 rounded-xl text-gray-400 hover:bg-gray-100 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </header>
            
            <div class="flex-1 overflow-y-auto p-6 space-y-5">
                <!-- Color Base -->
                <div class="flex items-center justify-between bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <div>
                        <h4 class="text-sm font-bold text-gray-800 uppercase">Color Principal</h4>
                        <p class="text-xs text-gray-500">Define el color de los botones principales</p>
                    </div>
                    <input type="color" id="cfgColor" class="w-10 h-10 p-1 bg-white border border-gray-200 rounded-lg cursor-pointer">
                </div>

                <!-- Ver Logo -->
                <label class="flex items-center justify-between bg-gray-50 p-4 rounded-xl border border-gray-100 cursor-pointer">
                    <div>
                        <h4 class="text-sm font-bold text-gray-800 uppercase">Ver Logo</h4>
                        <p class="text-xs text-gray-500">Muestra el logo en el Menú Principal</p>
                    </div>
                    <input type="checkbox" id="cfgLogo" class="w-5 h-5 text-primary rounded border-gray-300 focus:ring-primary">
                </label>
                
                <!-- Monedas -->
                <label class="flex items-center justify-between bg-gray-50 p-4 rounded-xl border border-gray-100 cursor-pointer">
                    <div>
                        <h4 class="text-sm font-bold text-gray-800 uppercase">Ver Moneda USD</h4>
                        <p class="text-xs text-gray-500">Muestra los valores y tasa en Dólares</p>
                    </div>
                    <input type="checkbox" id="cfgUsd" class="w-5 h-5 text-primary rounded border-gray-300 focus:ring-primary">
                </label>
                <label class="flex items-center justify-between bg-gray-50 p-4 rounded-xl border border-gray-100 cursor-pointer">
                    <div>
                        <h4 class="text-sm font-bold text-gray-800 uppercase">Ver Moneda COP</h4>
                        <p class="text-xs text-gray-500">Muestra los valores y tasa en Pesos</p>
                    </div>
                    <input type="checkbox" id="cfgCop" class="w-5 h-5 text-primary rounded border-gray-300 focus:ring-primary">
                </label>

                <!-- Ver Img Producto -->
                <label class="flex items-center justify-between bg-gray-50 p-4 rounded-xl border border-gray-100 cursor-pointer">
                    <div>
                        <h4 class="text-sm font-bold text-gray-800 uppercase">Ver Imagen de Producto</h4>
                        <p class="text-xs text-gray-500">Si se deshabilita, se ocultan las fotos en el catálogo</p>
                    </div>
                    <input type="checkbox" id="cfgImg" class="w-5 h-5 text-primary rounded border-gray-300 focus:ring-primary">
                </label>

                <!-- Hab Comentario -->
                <label class="flex items-center justify-between bg-gray-50 p-4 rounded-xl border border-gray-100 cursor-pointer">
                    <div>
                        <h4 class="text-sm font-bold text-gray-800 uppercase">Habilitar Comentarios</h4>
                        <p class="text-xs text-gray-500">Permite agregar observaciones al carrito</p>
                    </div>
                    <input type="checkbox" id="cfgComen" class="w-5 h-5 text-primary rounded border-gray-300 focus:ring-primary">
                </label>
            </div>
            
            <footer class="p-6 bg-white border-t border-gray-100">
                <button id="btnSaveAdmin" class="w-full bg-primary hover:bg-primary-dark text-white font-black py-4 rounded-xl shadow-lg transition-all active:scale-[0.98] flex items-center justify-center gap-2 uppercase tracking-tight">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                    Guardar Configuración
                </button>
            </footer>
        </div>
    </div>

    <!-- MODAL MENSAJES (GENÉRICO) -->
    <div id="modalMessage" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-black/40 backdrop-blur-sm transition-opacity duration-300">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden transform transition-all scale-95 opacity-0 duration-300">
            <div class="bg-primary p-6 text-white text-center relative">
                <div class="absolute -bottom-8 left-1/2 -translate-x-1/2 bg-white rounded-full p-2 shadow-lg">
                    <div class="bg-primary/10 rounded-full p-2">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                </div>
                <h3 id="modalMessageTitle" class="text-xl font-bold Capitalize italic">Mensaje</h3>
            </div>
            <div class="p-8 pt-10 text-center italic Capitalize">
                <p id="modalMessageBody" class="text-gray-600 font-medium leading-relaxed mb-8 flex flex-col gap-1"></p>
                <div class="flex gap-3">
                    <button id="btnMessageCancel" class="hidden flex-1 px-6 py-4 bg-gray-100 hover:bg-gray-200 text-gray-500 font-bold rounded-2xl transition-all">CANCELAR</button>
                    <button id="btnMessageOk" class="flex-1 px-6 py-4 bg-primary hover:bg-primary-dark text-white font-black rounded-2xl shadow-lg transition-all active:scale-[0.98]">ACEPTAR</button>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script>
      const state = {
        config: null,
        department: '',
        departmentName: '',
        category: '',
        categoryName: '',
        search: '',
        selectedClient: null,
        vendedorId: '<?php echo $_SESSION['vendedor_id'] ?? ''; ?>',
        cart: [],
        currentProduct: null, // Para el modal de unidades
        offset: 0,
        limit: 20,
        loading: false,
        hasMore: true
      };

      const elements = {
        sidebar: document.getElementById('sidebar'),
        sidebarOverlay: document.getElementById('sidebarOverlay'),
        burgerBtn: document.getElementById('burgerBtn'),
        closeSidebar: document.getElementById('closeSidebar'),
        departmentsMenu: document.getElementById('departmentsMenu'),
        categoriesMenu: document.getElementById('categoriesMenu'),
        cardsContainer: document.getElementById('cardsContainer'),
        showAllProducts: document.getElementById('showAllProducts'),
        filtersResume: document.getElementById('filtersResume'),
        searchInput: document.getElementById('searchInput'),
        clientLabel: document.getElementById('clientLabel'),
        clientStatusBar: document.getElementById('clientStatusBar'),
        menuOperaciones: document.getElementById('menuOperaciones'),
        scrollLoading: document.getElementById('scrollLoading'),
        appLogo: document.getElementById('appLogo'),
        ratesContainer: document.getElementById('ratesContainer'),
        rateBcvContainer: document.getElementById('rateBcvContainer'),
        rateCopContainer: document.getElementById('rateCopContainer'),
        
        // Modales
        modalClientes: document.getElementById('modalClientes'),
        modalCarrito: document.getElementById('modalCarrito'),
        btnOpenCart: document.getElementById('btnOpenCart'),
        cartCommentContainer: document.getElementById('cartCommentContainer'),
        cartComment: document.getElementById('cartComment'),
        btnAdmin: document.getElementById('btnAdmin'),
        modalAdmin: document.getElementById('modalAdmin'),
        closeAdmin: document.getElementById('closeAdmin'),
        btnSaveAdmin: document.getElementById('btnSaveAdmin'),
        cfgColor: document.getElementById('cfgColor'),
        cfgLogo: document.getElementById('cfgLogo'),
        cfgUsd: document.getElementById('cfgUsd'),
        cfgCop: document.getElementById('cfgCop'),
        cfgImg: document.getElementById('cfgImg'),
        cfgComen: document.getElementById('cfgComen'),
        
        // Modal Mensajes
        modalMessage: document.getElementById('modalMessage'),
        modalMessageTitle: document.getElementById('modalMessageTitle'),
        modalMessageBody: document.getElementById('modalMessageBody'),
        btnMessageOk: document.getElementById('btnMessageOk'),
        btnMessageCancel: document.getElementById('btnMessageCancel'),
        
        // Clientes
        modalTitle: document.getElementById('modalTitle'),
        closeModal: document.getElementById('closeModal'),
        btnBuscarCliente: document.getElementById('btnBuscarCliente'),
        btnCrearCliente: document.getElementById('btnCrearCliente'),
        viewBuscarCliente: document.getElementById('viewBuscarCliente'),
        formCrearCliente: document.getElementById('formCrearCliente'),
        searchClientName: document.getElementById('searchClientName'),
        clientSearchResults: document.getElementById('clientSearchResults'),
        newClientCode: document.getElementById('newClientCode'),
        
        // Carrito
        closeCart: document.getElementById('closeCart'),
        cartBadge: document.getElementById('cartBadge'),
        cartItemsList: document.getElementById('cartItemsList'),
        summaryCount: document.getElementById('summaryCount'),
        summaryQty: document.getElementById('summaryQty'),
        
        // Totales Carrito
        bsTotal: document.getElementById('bsTotal'),
        copTotal: document.getElementById('copTotal'),
        usdTotal: document.getElementById('usdTotal'),
        
        // Modal Unidades
        modalAgregarUnidades: document.getElementById('modalAgregarUnidades'),
        inputUnidades: document.getElementById('inputUnidades'),
        btnCancelarUnidades: document.getElementById('btnCancelarUnidades'),
        btnConfirmarUnidades: document.getElementById('btnConfirmarUnidades'),
        
        // Modal Pedidos
        modalListadoPedidos: document.getElementById('modalListadoPedidos'),
        ordersTableBody: document.getElementById('ordersTableBody'),
        noOrdersMessage: document.getElementById('noOrdersMessage'),
        ordersTableContainer: document.getElementById('ordersTableContainer'),
        closeOrders: document.getElementById('closeOrders'),
        btnPedidos: document.getElementById('btnPedidos')
      };

      // --- UTILITARIOS ---
      async function getJson(url) {
        try {
          const response = await fetch(url);
          if (!response.ok) throw new Error(`HTTP ${response.status}`);
          const data = await response.json();
          if (data.error) {
            if (data.error.includes('Sesión no activa') || data.error.includes('Sesión expirada')) {
              window.location.href = 'login.php';
              return;
            }
            throw new Error(data.error);
          }
          return data;
        } catch (e) {
          console.error("API Error:", e);
          throw e;
        }
      }

      function formatMoney(value) {
        return new Intl.NumberFormat('es-VE', {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        }).format(Number(value || 0));
      }

      // Rastreamos timers pendientes para evitar que un cierre anterior tape un modal nuevo
      const _modalTimers = new WeakMap();

      function showModal(modalEl) {
          // Cancelar si este modal estaba en proceso de cerrarse
          const pending = _modalTimers.get(modalEl);
          if (pending) { clearTimeout(pending); _modalTimers.delete(modalEl); }

          const content = modalEl.querySelector('div.bg-white');
          modalEl.classList.remove('hidden');
          modalEl.classList.add('flex', 'open');

          if (modalEl.id === 'modalCarrito' && window.innerWidth < 640) {
              setTimeout(() => {
                content.classList.remove('translate-y-full', 'opacity-0');
                content.classList.add('translate-y-0', 'opacity-100');
              }, 10);
          } else {
              setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
              }, 10);
          }
      }

      // Devuelve una Promesa que se resuelve cuando la animación de cierre termina (300ms)
      function hideModal(modalEl) {
          return new Promise(resolve => {
              const content = modalEl.querySelector('div.bg-white');

              if (modalEl.id === 'modalCarrito' && window.innerWidth < 640) {
                  content.classList.add('translate-y-full', 'opacity-0');
                  content.classList.remove('translate-y-0', 'opacity-100');
              } else {
                  content.classList.add('scale-95', 'opacity-0');
                  content.classList.remove('scale-100', 'opacity-100');
              }

              const timer = setTimeout(() => {
                  _modalTimers.delete(modalEl);
                  modalEl.classList.remove('flex', 'open');
                  modalEl.classList.add('hidden');
                  resolve();
              }, 300);
              _modalTimers.set(modalEl, timer);
          });
      }

      function showModalMessage(title, message, isConfirm = false) {
        return new Promise((resolve) => {
          elements.modalMessageTitle.textContent = title;
          elements.modalMessageBody.innerHTML = message.replace(/\n/g, '<br>');
          elements.btnMessageCancel.style.display = isConfirm ? 'block' : 'none';

          showModal(elements.modalMessage);

          elements.btnMessageOk.onclick = async () => {
            await hideModal(elements.modalMessage);
            resolve(true);
          };

          elements.btnMessageCancel.onclick = async () => {
            await hideModal(elements.modalMessage);
            resolve(false);
          };
        });
      }

      // --- GESTIÓN DE CLIENTES ---
      function openClientModal(view) {
        showModal(elements.modalClientes);
        if (view === 'buscar') {
          elements.modalTitle.textContent = 'Buscar Cliente';
          elements.viewBuscarCliente.classList.remove('hidden');
          elements.formCrearCliente.classList.add('hidden');
          elements.searchClientName.focus();
        } else {
          elements.modalTitle.textContent = 'Crear Nuevo Cliente';
          elements.viewBuscarCliente.classList.add('hidden');
          elements.formCrearCliente.classList.remove('hidden');
          loadNextClientCode();
        }
      }

      function closeClientModal() {
        hideModal(elements.modalClientes);
      }

      function hideSidebar() {
          elements.sidebar.classList.add('-translate-x-full');
          elements.sidebarOverlay.classList.add('hidden');
          elements.sidebarOverlay.classList.remove('flex');
      }

      async function selectClient(client) {
        state.selectedClient = client;
        elements.clientLabel.textContent = `Cliente: ${client.CLT_NOMBRE}`;
        elements.btnOpenCart.disabled = false;
        closeClientModal();
        hideSidebar();
        
        await showModalMessage('Cliente Seleccionado', `El cliente ${client.CLT_NOMBRE} ha sido seleccionado correctamente.`);
        loadProducts();
      }

      async function loadNextClientCode() {
        const data = await getJson('../api.php?action=get_next_client_code');
        elements.newClientCode.value = data.next_code;
      }

      async function searchClients(query) {
        if (!query) {
          elements.clientSearchResults.innerHTML = '';
          return;
        }
        try {
          const clients = await getJson(`../api.php?action=search_clients&search=${query}`);
          elements.clientSearchResults.innerHTML = '';
          if (clients.length === 0) {
            elements.clientSearchResults.innerHTML = '<div class="p-4 text-center text-gray-400 font-bold uppercase tracking-widest text-xs">No se encontraron clientes</div>';
            return;
          }
          clients.forEach(c => {
            const div = document.createElement('div');
            div.className = 'p-4 bg-gray-50 hover:bg-primary/10 rounded-2xl cursor-pointer transition-colors border border-gray-100 flex items-center justify-between group text-sm';
            div.innerHTML = `
                <div class="Capitalize">
                    <p class="font-bold text-gray-900 group-hover:text-primary transition-colors">${c.CLT_NOMBRE}</p>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-0.5">${c.CLT_CODIGO} &middot; RIF: ${c.CLT_RIF}</p>
                </div>
                <svg class="w-5 h-5 text-gray-300 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            `;
            div.onclick = () => selectClient(c);
            elements.clientSearchResults.appendChild(div);
          });
        } catch (e) {
          elements.clientSearchResults.innerHTML = `<div class="p-4 text-red-500 text-xs font-bold uppercase tracking-widest">Error: ${e.message}</div>`;
        }
      }

      // --- GESTIÓN DEL CARRITO ---
      function addToCart(product, qty) {
        if (!state.selectedClient) {
          alert('Por favor, selecciona un cliente primero.');
          openClientModal('buscar');
          return;
        }
        const existing = state.cart.find(i => i.codigo === product.codigo);
        if (existing) {
          existing.qty = qty; // Modificar la cantidad en lugar de sumarla
        } else {
          state.cart.push({ ...product, qty });
        }
        updateCartUI();
        loadProducts(); // Recargar productos para que aparezca el check
      }

      function updateCartUI() {
        const totalQty = state.cart.reduce((acc, i) => acc + i.qty, 0);
        elements.cartBadge.textContent = totalQty;
        elements.cartBadge.style.display = totalQty > 0 ? 'block' : 'none';

        if (elements.modalCarrito.classList.contains('open')) {
          renderCartItems();
          updateCartSummary();
        }
      }

      function renderCartItems() {
        elements.cartItemsList.innerHTML = '';
        state.cart.forEach(item => {
          const row = document.createElement('div');
          row.className = 'flex items-center gap-4 bg-gray-50 p-4 rounded-2xl border border-gray-100 group transition-all hover:bg-white hover:shadow-md';
          
          const showImg = state.config?.WEB_IMG_PROD !== '0';
          const imgHtml = showImg ? `
            <div class="w-16 h-16 bg-white rounded-xl overflow-hidden border border-gray-100 shrink-0">
                <img src="${item.imageUrl}" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/100x100?text=Baramovil'">
            </div>
          ` : '';

          row.innerHTML = `
            ${imgHtml}
            <div class="flex-1 min-w-0 Uppercase italic">
              <h5 class="text-xs font-black text-gray-900 truncate uppercase tracking-tight">${item.descripcion}</h5>
              <div class="flex items-center gap-3 mt-1 underline">
                <p class="text-[10px] font-bold text-primary">CANT: ${item.qty}</p>
                <p class="text-[10px] text-gray-400 font-bold">${item.codigo} &middot; USD ${formatMoney(item.usd)}</p>
              </div>
            </div>
            <button onclick="window.removeItem('${item.codigo}')" class="p-3 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
            </button>
          `;
          elements.cartItemsList.appendChild(row);
        });
      }

      window.updateItemQty = (codigo, qty) => {
        const item = state.cart.find(i => i.codigo === codigo);
        if (item) {
          item.qty = parseInt(qty) || 1;
          updateCartUI();
        }
      };

      window.removeItem = (codigo) => {
        state.cart = state.cart.filter(i => i.codigo !== codigo);
        updateCartUI();
        loadProducts(); // Refrescar para desmarcar el check si el item se eliminó
      };

      function updateCartSummary() {
        let bsSubtotal = 0, copSubtotal = 0, usdSubtotal = 0;
        let bsIvaTotal = 0, copIvaTotal = 0, usdIvaTotal = 0;
        let totalQty = 0;

        state.cart.forEach(item => {
          const qty = item.qty;
          const ivaRate = (parseFloat(item.iva_rate) || 0) / 100;

          const bsLine = item.bs * qty;
          const copLine = item.cop * qty;
          const usdLine = item.usd * qty;

          bsSubtotal += bsLine;
          copSubtotal += copLine;
          usdSubtotal += usdLine;

          bsIvaTotal += bsLine * ivaRate;
          copIvaTotal += copLine * ivaRate;
          usdIvaTotal += usdLine * ivaRate;

          totalQty += qty;
        });

        elements.summaryCount.textContent = state.cart.length;
        elements.summaryQty.textContent = totalQty;
        elements.bsTotal.textContent = formatMoney(bsSubtotal);
        elements.copTotal.textContent = formatMoney(copSubtotal);
        elements.usdTotal.textContent = formatMoney(usdSubtotal);
      }

      // --- PRODUCTOS ---
      function openUnitsModal(product) {
        if (!state.selectedClient) {
          if (localStorage.getItem('ope_web') === '1') {
            showModalMessage('Selección de Cliente', 'Para ver detalles o agregar productos, primero debe seleccionar un cliente.');
            openClientModal('buscar');
          } else {
            showModalMessage('Acceso Restringido', 'No tiene permisos para realizar pedidos.');
          }
          return;
        }
        state.currentProduct = product;
        
        const existingItem = state.cart.find(i => i.codigo === product.codigo);
        elements.inputUnidades.value = existingItem ? existingItem.qty : 1;
        
        showModal(elements.modalAgregarUnidades);
        setTimeout(() => elements.inputUnidades.focus(), 100);
      }

      function createCard(product) {
        const article = document.createElement('article');
        const opeWeb = localStorage.getItem('ope_web');
        
        if (opeWeb === '1') {
            // Tarjeta interactiva para usuarios con permiso
            article.className = 'group bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 flex flex-col cursor-pointer transform hover:-translate-y-1 active:scale-[0.98]';
            article.onclick = async () => {
              if (!state.selectedClient) {
                await showModalMessage('Selección de Cliente', 'Selecciona un cliente para gestionar productos.');
                openClientModal('buscar');
                return;
              }
              openUnitsModal(product);
            };
        } else {
            // Tarjeta estática para modo consulta
            article.className = 'bg-white rounded-3xl overflow-hidden shadow-sm border border-gray-100 flex flex-col';
        }

        const isInCart = state.cart.some(item => item.codigo === product.codigo);
        const checkHtml = isInCart ? '<div class="absolute top-3 right-3 bg-primary text-white w-7 h-7 rounded-full flex items-center justify-center shadow-lg border-2 border-white z-10 animate-bounce-short"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg></div>' : '';

        const showImg = state.config?.WEB_IMG_PROD !== '0';
        const showCop = state.config?.WEB_VER_COP !== '0';
        const showUsd = state.config?.WEB_VER_USD !== '0';

        const imgHtml = showImg ? `
          <div class="relative overflow-hidden aspect-square flex items-center justify-center p-4 bg-gray-50/50 border-2 border-primary/30 rounded-2xl m-3 group-hover:border-primary transition-colors duration-300">
              ${checkHtml}
              <img src="${product.imageUrl}" class="w-full h-full object-contain mix-blend-multiply group-hover:scale-110 transition-transform duration-500 rounded-xl" onerror="this.src='https://placehold.co/400x400?text=Sin+Imagen'" loading="lazy">
              <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-gray-900/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
          </div>
        ` : `<div class="px-5 pt-5 relative">${checkHtml}</div>`;

        const priceBsHtml = `<div class="bg-primary/5 text-primary px-3 py-1.5 rounded-xl uppercase border border-primary/10">BS ${formatMoney(product.bs)}</div>`;
        const priceCopHtml = showCop ? `<div class="bg-gray-100 text-gray-600 px-3 py-1.5 rounded-xl uppercase">COP ${formatMoney(product.cop)}</div>` : '';
        const priceUsdHtml = showUsd ? `<div class="bg-gray-100 text-gray-600 px-3 py-1.5 rounded-xl uppercase">USD ${formatMoney(product.usd)}</div>` : '';

        article.innerHTML = `
          ${imgHtml}
          <div class="p-5 flex-1 flex flex-col lowercase italic">
            <h4 class="text-sm font-black text-gray-800 line-clamp-2 leading-relaxed mb-2 uppercase tracking-tight">${product.descripcion}</h4>
            <p class="text-[10px] font-black text-gray-400 bg-gray-100 self-start px-2 py-0.5 rounded-full uppercase tracking-widest mb-4">Cód: ${product.codigo}</p>
			${Number(product.exis) <= 0
              ? `<p class="text-[10px] font-black text-red-500 bg-red-50 self-start px-2 py-0.5 rounded-full uppercase tracking-widest mb-4 border border-red-200">⚠ Sin Stock</p>`
              : `<p class="text-[10px] font-black text-green-600 bg-green-50 self-start px-2 py-0.5 rounded-full uppercase tracking-widest mb-4 border border-green-200">✓ Exist: ${Number(product.exis).toLocaleString('es-VE', {minimumFractionDigits:0, maximumFractionDigits:2})}</p>`
            }
            <div class="mt-auto pt-3 border-t border-gray-100/50 flex flex-wrap gap-2 text-[10px] font-bold">
                ${priceBsHtml}
                ${priceCopHtml}
                ${priceUsdHtml}
            </div>
          </div>
        `;
        return article;
      }

      function renderCards(products, append = false) {
        if (!append) {
          elements.cardsContainer.innerHTML = '';
          if (products.length === 0) {
            elements.cardsContainer.innerHTML = '<p>No hay productos para los filtros seleccionados.</p>';
            return;
          }
        }
        
        const fragment = document.createDocumentFragment();
        products.forEach(p => fragment.appendChild(createCard(p)));
        elements.cardsContainer.appendChild(fragment);
      }

      async function loadProducts(append = false) {
        if (state.loading) return;
        if (!append) {
          state.offset = 0;
          state.hasMore = true;
        }
        if (!state.hasMore) return;

        state.loading = true;
        const query = new URLSearchParams();
        if (state.department) query.set('department', state.department);
        if (state.category) query.set('category', state.category);
        if (state.search) query.set('search', state.search);
        query.set('limit', state.limit);
        query.set('offset', state.offset);

        try {
          const products = await getJson(`../api.php?action=products&${query.toString()}`);
          renderCards(products, append);
          
          if (products.length < state.limit) {
            state.hasMore = false;
          } else {
            state.offset += state.limit;
          }
        } catch (e) {
          console.error("Error cargando productos:", e);
        } finally {
          state.loading = false;
          updateFiltersResume();
        }
      }

      function updateFiltersResume() {
        const parts = [];
        if (state.department) parts.push(`Departamento: ${state.departmentName}`);
        if (state.category) parts.push(`Categoría: ${state.categoryName}`);
        if (state.search) parts.push(`Búsqueda: ${state.search}`);
        elements.filtersResume.textContent = parts.length ? `Filtrando por: ${parts.join(' | ')}` : 'Mostrando todos los productos';
      }

      // --- EVENT LISTENERS ---
      elements.burgerBtn.onclick = () => elements.sidebar.classList.toggle('open');
      // --- EVENT LISTENERS ---
      elements.burgerBtn.onclick = () => {
          elements.sidebar.classList.remove('-translate-x-full');
          elements.sidebarOverlay.classList.remove('hidden');
          elements.sidebarOverlay.classList.add('flex');
      };
      
      elements.sidebarOverlay.onclick = elements.closeSidebar.onclick = hideSidebar;
      elements.sidebar.onclick = (e) => e.stopPropagation();

      elements.btnOpenCart.onclick = async () => { 
        if (!state.selectedClient) {
          await showModalMessage('Sin Cliente', 'Por favor, selecciona un cliente primero.');
          openClientModal('buscar');
          return;
        }
        showModal(elements.modalCarrito);
        updateCartUI(); 
      };
      
      elements.closeCart.onclick = () => hideModal(elements.modalCarrito);
      elements.btnBuscarCliente.onclick = () => openClientModal('buscar');
      elements.btnCrearCliente.onclick = () => openClientModal('crear');
      elements.closeModal.onclick = closeClientModal;
      
      elements.showAllProducts.onclick = () => { 
        state.department = state.category = state.search = ''; 
        elements.searchInput.value = '';
        hideSidebar();
        loadProducts(); 
      };

      let timer = null;
      elements.searchInput.oninput = () => {
        clearTimeout(timer);
        timer = setTimeout(() => { 
          state.search = elements.searchInput.value.trim(); 
          state.department = state.category = '';
          loadProducts(); 
        }, 300);
      };

      elements.searchClientName.oninput = () => {
        clearTimeout(timer);
        timer = setTimeout(() => searchClients(elements.searchClientName.value), 300);
      };

      elements.formCrearCliente.onsubmit = async (e) => {
        e.preventDefault();
        const clientData = {
          codigo: document.getElementById('newClientCode').value,
          nombre: document.getElementById('newClientName').value,
          apellido: document.getElementById('newClientLastName').value,
          rif: document.getElementById('newClientRif').value,
          direccion: document.getElementById('newClientDir').value,
          telefono: document.getElementById('newClientTel').value
        };
        const res = await fetch('../api.php?action=create_client', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(clientData)
        }).then(r => r.json());

        if (res.success) {
          selectClient({ 
            CLT_CODIGO: clientData.codigo, 
            CLT_NOMBRE: (clientData.nombre + ' ' + clientData.apellido).trim(), 
            CLT_RIF: clientData.rif 
          });
        } else await showModalMessage('Error', 'Error al crear cliente: ' + res.error);
      };

      document.getElementById('btnFinalizarPedido').onclick = async () => {
        if (state.cart.length === 0) return await showModalMessage('Carrito Vacío', 'El carrito está vacío.');
        
        const invalidPrices = state.cart.some(item => (parseFloat(item.bs) < 0 || parseFloat(item.usd) < 0));
        if (invalidPrices) {
          return await showModalMessage('Precios Inválidos', 'Existen productos con precios inválidos (menores a cero).');
        }

        try {
          const pseudoId = `BARA-${Date.now()}-${Math.floor(Math.random() * 1000)}`;
          const jData = state.cart.map(item => ({
            codigo: item.codigo,
            lista: 'A',
            precio_bs: item.bs,
            qty: item.qty,
            pseudoId: pseudoId
          }));

          const cCliente = state.selectedClient.CLT_CODIGO;
          const bodyData = { cCliente, jData, pseudoId, comentario: elements.cartComment.value.trim() };

          const proceed = await showModalMessage('Confirmación', `¿Deseas enviar este pedido de ${state.cart.length} items?`, true);
          if (!proceed) return;

          const btn = document.getElementById('btnFinalizarPedido');
          btn.disabled = true;
          btn.innerHTML = `
              <svg class="animate-spin h-5 w-5 mr-3" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              ENVIANDO...
          `;

          const res = await fetch('../api.php?action=submit_order', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(bodyData)
          }).then(r => r.json());

          if (res.success) {
            await showModalMessage('Éxito', `✅ Pedido procesado exitosamente.<br>Número: <span class="font-black text-primary">${res.pedido}</span>`);
            state.cart = [];
            state.selectedClient = null;
            elements.clientLabel.textContent = 'Ningún cliente seleccionado';
            elements.btnOpenCart.disabled = true;
            elements.cartComment.value = '';
            updateCartUI();
            await hideModal(elements.modalCarrito);
            loadProducts();
          } else {
            if (res.error.includes('Sesión')) window.location.href = 'login.php';
            else await showModalMessage('Error', 'Error: ' + (res.error || 'Desconocido'));
          }
        } catch (error) {
          await showModalMessage('Error de Red', 'Error: ' + error.message);
        } finally {
          const btn = document.getElementById('btnFinalizarPedido');
          btn.disabled = false;
          btn.innerHTML = `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" /></svg> ENVIAR PEDIDO AHORA`;
        }
      };

      elements.btnCancelarUnidades.onclick = () => {
        hideModal(elements.modalAgregarUnidades);
        state.currentProduct = null;
      };

      elements.btnConfirmarUnidades.onclick = async () => {
        const qty = parseInt(elements.inputUnidades.value) || 0;
        if (qty <= 0) {
          await showModalMessage('Cuidado', 'La cantidad debe ser mayor a cero.');
          elements.inputUnidades.focus();
          return;
        }
        if (state.currentProduct) {
          addToCart(state.currentProduct, qty);
          hideModal(elements.modalAgregarUnidades);
          state.currentProduct = null;
        }
      };

      document.getElementById('btnLogout').onclick = async () => {
        const confirmLogout = await showModalMessage('Cerrar Sesión', '¿Estás seguro que deseas salir del sistema?', true);
        if (confirmLogout) {
          await fetch('../api.php?action=logout');
          window.location.href = 'login.php';
        }
      };

      // --- GESTIÓN DE PEDIDOS DEL DÍA ---
      elements.btnPedidos.onclick = async () => {
        hideSidebar();
        try {
          const orders = await getJson('../api.php?action=get_orders');
          renderOrders(orders);
          showModal(elements.modalListadoPedidos);
        } catch (e) {
          await showModalMessage('Error', 'No se pudieron cargar los pedidos: ' + e.message);
        }
      };

      elements.closeOrders.onclick = () => {
        hideModal(elements.modalListadoPedidos);
      };

      function renderOrders(orders) {
        elements.ordersTableBody.innerHTML = '';
        if (orders.length === 0) {
          elements.ordersTableContainer.classList.add('hidden');
          elements.noOrdersMessage.classList.remove('hidden');
          return;
        }

        elements.ordersTableContainer.classList.remove('hidden');
        elements.noOrdersMessage.classList.add('hidden');

        orders.forEach(order => {
          const total = Number(order.DCL_NETO);

          // --- Fila principal del pedido ---
          const tr = document.createElement('tr');
          tr.className = 'cursor-pointer hover:bg-orange-50/40 transition-colors border-b border-gray-100';
          tr.innerHTML = `
            <td class="px-4 py-3 font-black text-gray-900 uppercase tracking-tighter text-xs">
              <span class="toggle-icon inline-block mr-1 transition-transform duration-200">▶</span>${order.DCL_NUMERO}
            </td>
            <td class="px-4 py-3">
              <div class="font-bold text-gray-800 uppercase text-xs">${order.CLT_NOMBRE}</div>
              <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-0.5">${order.DCL_CLT_CODIGO}</div>
            </td>
            <td class="px-4 py-3 text-right font-black text-primary text-xs">${formatMoney(total)}</td>
            <td class="px-4 py-3 text-[10px] font-black text-gray-400 uppercase tracking-tighter text-center">${order.DCL_HORA}</td>
          `;

          // --- Fila de detalle (oculta por defecto) ---
          const trDetail = document.createElement('tr');
          trDetail.className = 'hidden';
          trDetail.innerHTML = `
            <td colspan="4" class="px-0 pb-0 pt-0 bg-orange-50/30">
              <div class="detail-content px-4 py-3"></div>
            </td>
          `;

          // --- Lógica de expansión ---
          let loaded = false;
          let expanded = false;

          tr.onclick = async () => {
            expanded = !expanded;
            const icon = tr.querySelector('.toggle-icon');
            const detailContent = trDetail.querySelector('.detail-content');

            if (expanded) {
              icon.style.transform = 'rotate(90deg)';
              trDetail.classList.remove('hidden');

              if (!loaded) {
                detailContent.innerHTML = `
                  <div class="flex items-center gap-2 text-xs text-gray-400 py-2">
                    <svg class="animate-spin w-4 h-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Cargando items...
                  </div>`;

                try {
                  const items = await getJson(`../api.php?action=get_order_detail&numero=${order.DCL_NUMERO}`);
                  loaded = true;

                  if (!items || items.length === 0) {
                    detailContent.innerHTML = '<p class="text-xs text-gray-400 py-2">Sin movimientos registrados.</p>';
                  } else {
                    detailContent.innerHTML = `
                      <table class="w-full text-[11px]">
                        <thead>
                          <tr class="text-[9px] font-black text-gray-400 uppercase tracking-widest border-b border-orange-200">
                            <th class="py-1 text-left">Código</th>
                            <th class="py-1 text-left">Descripción</th>
                            <th class="py-1 text-right">Cant.</th>
                            <th class="py-1 text-right">Base</th>
                            <th class="py-1 text-center">IVA</th>
                          </tr>
                        </thead>
                        <tbody>
                          ${items.map(i => `
                            <tr class="border-b border-orange-100/50 last:border-0">
                              <td class="py-1.5 font-mono font-black text-gray-500">${i.CODIGO}</td>
                              <td class="py-1.5 text-gray-700 pr-2">${i.DESCRIPCION}</td>
                              <td class="py-1.5 text-right font-bold text-gray-800">${formatMoney(i.CANTIDAD)}</td>
                              <td class="py-1.5 text-right font-bold text-primary">${formatMoney(i.BASE)}</td>
                              <td class="py-1.5 text-center font-black uppercase text-gray-400">${i.IVA}</td>
                            </tr>`).join('')}
                        </tbody>
                      </table>`;
                  }
                } catch (e) {
                  detailContent.innerHTML = `<p class="text-xs text-red-500 py-2">Error: ${e.message}</p>`;
                }
              }
            } else {
              icon.style.transform = 'rotate(0deg)';
              trDetail.classList.add('hidden');
            }
          };

          elements.ordersTableBody.appendChild(tr);
          elements.ordersTableBody.appendChild(trDetail);
        });
      }

      // --- ADMINISTRACIÓN ---
      function openAdminModal() {
          if (!state.config) return;
          elements.cfgColor.value = state.config.WEB_COLOR || '#ea580c';
          elements.cfgLogo.checked = state.config.WEB_LOGO === '1';
          elements.cfgUsd.checked = state.config.WEB_VER_USD === '1';
          elements.cfgCop.checked = state.config.WEB_VER_COP === '1';
          elements.cfgImg.checked = state.config.WEB_IMG_PROD === '1';
          elements.cfgComen.checked = state.config.WEB_HAB_COMEN === '1';
          showModal(elements.modalAdmin);
      }
      
      elements.btnAdmin.onclick = () => {
          openAdminModal();
      };
      
      elements.closeAdmin.onclick = () => {
          hideModal(elements.modalAdmin);
      };
      
      elements.btnSaveAdmin.onclick = async () => {
          const payload = {
              WEB_COLOR: elements.cfgColor.value,
              WEB_LOGO: elements.cfgLogo.checked ? '1' : '0',
              WEB_VER_USD: elements.cfgUsd.checked ? '1' : '0',
              WEB_VER_COP: elements.cfgCop.checked ? '1' : '0',
              WEB_IMG_PROD: elements.cfgImg.checked ? '1' : '0',
              WEB_HAB_COMEN: elements.cfgComen.checked ? '1' : '0'
          };
          
          elements.btnSaveAdmin.disabled = true;
          elements.btnSaveAdmin.innerHTML = '<span class="animate-spin mr-2">⏳</span> Guardando...';
          
          try {
              const res = await fetch('../api.php?action=save_config', {
                  method: 'POST',
                  headers: { 'Content-Type': 'application/json' },
                  body: JSON.stringify(payload)
              }).then(r => r.json());
              
              if (res.success) {
                  await showModalMessage('Configuración Guardada', 'Se actualizará la página para aplicar los cambios.');
                  window.location.reload();
              } else {
                  await showModalMessage('Error', 'No se pudo guardar la configuración: ' + (res.error || ''));
              }
          } catch (e) {
              await showModalMessage('Error', 'Error de red: ' + e.message);
          } finally {
              elements.btnSaveAdmin.disabled = false;
              elements.btnSaveAdmin.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg> Guardar Configuración';
          }
      };

      // --- INICIALIZACIÓN ---
      async function init() {
        // Verificar permisos OPE_WEB
        const opeWeb = localStorage.getItem('ope_web');
        if (opeWeb !== '1') {
          if (elements.menuOperaciones) elements.menuOperaciones.style.display = 'none';
          if (elements.clientStatusBar) elements.clientStatusBar.style.display = 'none';
          if (elements.btnOpenCart) elements.btnOpenCart.style.display = 'none';
        } else {
          // Mostrar botón de administración si es ope_web = 1
          if (elements.btnAdmin) elements.btnAdmin.style.display = 'flex';
        }

        try {
          const [configData, deps, cats, rates] = await Promise.all([
            getJson('../api.php?action=get_config'),
            getJson('../api.php?action=departments'),
            getJson('../api.php?action=categories'),
            getJson('../api.php?action=get_rates')
          ]);

          if (configData && configData.success) {
            state.config = configData.config;
            
            // Aplicar configuración global
            document.documentElement.style.setProperty('--color-primary', state.config.WEB_COLOR);
            
            if (state.config.WEB_LOGO === '0') {
              elements.appLogo.style.display = 'none';
            }
            if (state.config.WEB_VER_USD === '0' && state.config.WEB_VER_COP === '0') {
              elements.ratesContainer.style.display = 'none';
            } else {
              if (state.config.WEB_VER_USD === '0') elements.rateBcvContainer.style.display = 'none';
              if (state.config.WEB_VER_COP === '0') elements.rateCopContainer.style.display = 'none';
            }
            if (state.config.WEB_HAB_COMEN === '1') {
              elements.cartCommentContainer.classList.remove('hidden');
            }
          }

          // Mostrar tasas en el sidebar
          if (rates && !rates.error) {
            const fmtRate = v => Number(v).toLocaleString('es-VE', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('rateBCV').textContent = 'Bs. ' + fmtRate(rates.bcv);
            document.getElementById('rateCOP').textContent = '$ ' + fmtRate(rates.cop);
          }

          elements.departmentsMenu.innerHTML = '';
          if (Array.isArray(deps)) {
            deps.forEach(d => {
              const btn = document.createElement('button');
              btn.className = 'w-full text-left p-3 rounded-2xl text-xs font-bold text-gray-400 hover:text-primary hover:bg-primary/5 transition-all uppercase tracking-widest';
              btn.textContent = `${d.DEP_CODIGO} - ${d.DEP_DESCRIPCION}`;
              btn.onclick = () => { 
                  state.department = d.DEP_CODIGO; 
                  state.departmentName = d.DEP_DESCRIPCION; 
                  state.category = state.search = ''; 
                  hideSidebar(); 
                  loadProducts(); 
              };
              const li = document.createElement('li'); li.appendChild(btn); elements.departmentsMenu.appendChild(li);
            });
          }

          elements.categoriesMenu.innerHTML = '';
          if (Array.isArray(cats)) {
            cats.forEach(c => {
              const btn = document.createElement('button');
              btn.className = 'w-full text-left p-3 rounded-2xl text-xs font-bold text-gray-400 hover:text-primary hover:bg-primary/5 transition-all uppercase tracking-widest';
              btn.textContent = `${c.CAT_CODIGO} - ${c.CAT_DESCRIPCION}`;
              btn.onclick = () => { 
                  state.category = c.CAT_CODIGO; 
                  state.categoryName = c.CAT_DESCRIPCION; 
                  state.department = state.search = ''; 
                  hideSidebar(); 
                  loadProducts(); 
              };
              const li = document.createElement('li'); li.appendChild(btn); elements.categoriesMenu.appendChild(li);
            });
          }

          // Lógica de Acordeón Moderna (Corregida)
          document.querySelectorAll('.menu-title').forEach(title => {
              title.onclick = () => {
                  const content = title.nextElementSibling;
                  const icon = title.querySelector('svg.arrow');
                  const isOpen = !content.classList.contains('max-h-0');
                  
                  // Cerrar otros menús si se desea (acordeón estricto)
                  document.querySelectorAll('.submenu').forEach(s => {
                      if (s !== content) {
                          s.classList.add('max-h-0');
                          s.previousElementSibling.querySelector('svg.arrow')?.classList.remove('rotate-180');
                      }
                  });

                  if (isOpen) {
                      content.classList.add('max-h-0');
                      content.classList.remove('max-h-[500px]');
                      icon?.classList.remove('rotate-180');
                  } else {
                      content.classList.remove('max-h-0');
                      content.classList.add('max-h-[500px]');
                      icon?.classList.add('rotate-180');
                  }
              };
          });

          await loadProducts();
          

        } catch (e) {
          elements.cardsContainer.innerHTML = `<div class="p-8 text-center text-red-500 font-bold uppercase tracking-widest text-xs">Error de carga: ${e.message}</div>`;
        }
      }

      init();

      // --- INFINITE SCROLL ---
      window.onscroll = () => {
        if (state.loading || !state.hasMore) return;
        
        // Detectar si faltan 20px para el final
        const scrollHeight = document.documentElement.scrollHeight;
        const scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
        const clientHeight = document.documentElement.clientHeight;

        if (scrollTop + clientHeight >= scrollHeight - 20) {
          loadProducts(true);
        }
      };
    </script>
  </body>
</html>
