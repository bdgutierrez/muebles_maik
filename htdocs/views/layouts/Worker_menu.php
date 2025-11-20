<?php
$id_trabajador = $_SESSION['id_trabajador'];
$id_cargo = $_SESSION['id_cargo'];

// 🧩 Consultar datos del trabajador
$stmt = $pdo->prepare("SELECT nombre, usuario, id_area FROM trabajadores WHERE usuario = :usuario LIMIT 1");
$stmt->bindParam(':usuario', $_SESSION['usuario']);
$stmt->execute();
$trabajador = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$trabajador) {
    echo "<p class='text-center text-red-600 mt-10 font-semibold text-lg'>⚠️ Error: Trabajador no encontrado.</p>";
    exit();
}
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Trabajador</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            box-sizing: border-box;
        }

        /* Animación suave menú móvil */
        #mobile-menu {
            transition: all 0.3s ease-in-out;
            max-height: 0;
            overflow: hidden;
        }

        #mobile-menu.active {
            max-height: 600px;
        }

        /* Icono hamburguesa animado */
        .hamburger-line {
            transition: all 0.3s ease-in-out;
        }

        .hamburger.active .line-1 {
            transform: rotate(45deg) translate(6px, 6px);
        }

        .hamburger.active .line-2 {
            opacity: 0;
        }

        .hamburger.active .line-3 {
            transform: rotate(-45deg) translate(6px, -6px);
        }

        /* Hover de enlaces */
        .nav-link {
            position: relative;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6);
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .nav-link:hover::before {
            transform: translateX(0);
        }

        /* Badge notificaciones */
        .notification-badge {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
</head>

<body class="bg-gray-50">

<header class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 text-white shadow-lg sticky top-0 z-50">
    <div class="container mx-auto px-6 sm:px-8">
        <div class="flex justify-between items-center h-20 sm:h-24">
            <!-- Logo -->
            <div class="flex items-center space-x-4">
                <div class="bg-gradient-to-br from-blue-500 to-purple-600 p-3 rounded-lg shadow-md">
                    <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">Panel del Trabajador</h1>
                    <p class="text-sm text-gray-400 hidden sm:block">Sistema de Gestión</p>
                </div>
            </div>

            <!-- Menú Desktop -->
            <nav class="hidden lg:flex items-center space-x-6 text-lg">
                <div class="flex items-center space-x-3 bg-gray-800 px-5 py-3 rounded-lg border border-gray-700">
                    <div class="bg-gradient-to-br from-blue-400 to-purple-500 p-2 rounded-full">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                  clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="text-left">
                        <p class="text-base font-semibold"><?= htmlspecialchars($trabajador['nombre']) ?></p>
                        <p class="text-sm text-gray-400">@<?= htmlspecialchars($trabajador['usuario']) ?></p>
                    </div>
                </div>

                <!-- Salir -->
                <a href="../../app/controllers/logout.php"
                   class="flex items-center space-x-2 bg-gradient-to-r from-red-600 to-red-700 px-6 py-3 rounded-lg hover:from-red-700 hover:to-red-800 transition-all shadow-md hover:shadow-lg transform hover:scale-105 text-base font-semibold">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span>Salir</span>
                </a>
            </nav>

            <!-- Botón hamburguesa -->
            <button id="menu-toggle" class="lg:hidden p-3 rounded-lg hover:bg-gray-800 transition-colors focus:outline-none hamburger">
                <div class="w-7 h-6 flex flex-col justify-between">
                    <span class="hamburger-line line-1 w-full h-1 bg-white rounded"></span>
                    <span class="hamburger-line line-2 w-full h-1 bg-white rounded"></span>
                    <span class="hamburger-line line-3 w-full h-1 bg-white rounded"></span>
                </div>
            </button>
        </div>
    </div>

    <!-- Menú móvil -->
    <div id="mobile-menu" class="lg:hidden bg-gray-800 border-t border-gray-700">
        <div class="container mx-auto px-6 py-6 space-y-5 text-lg">
            <div class="flex items-center space-x-4 bg-gray-900 p-4 rounded-lg border border-gray-700">
                <div class="bg-gradient-to-br from-blue-400 to-purple-500 p-3 rounded-full">
                    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                              d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                              clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-xl"><?= htmlspecialchars($trabajador['nombre']) ?></p>
                    <p class="text-base text-gray-400">@<?= htmlspecialchars($trabajador['usuario']) ?></p>
                </div>
            </div>

            <a href="../../app/controllers/logout.php"
               class="flex items-center justify-center space-x-3 w-full bg-gradient-to-r from-red-600 to-red-700 px-5 py-4 rounded-lg hover:from-red-700 hover:to-red-800 transition-all shadow-md text-lg font-semibold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span>Cerrar Sesión</span>
            </a>
        </div>
    </div>
</header>

<script>
    const toggle = document.getElementById('menu-toggle');
    const menu = document.getElementById('mobile-menu');
    const hamburger = document.querySelector('.hamburger');

    toggle.addEventListener('click', () => {
        menu.classList.toggle('active');
        hamburger.classList.toggle('active');
    });

    // Cerrar menú al hacer clic fuera
    document.addEventListener('click', (e) => {
        if (!toggle.contains(e.target) && !menu.contains(e.target)) {
            menu.classList.remove('active');
            hamburger.classList.remove('active');
        }
    });

    // Cerrar menú al redimensionar
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            menu.classList.remove('active');
            hamburger.classList.remove('active');
        }
    });
</script>

</body>
</html>
