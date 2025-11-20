<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Muebles Maik</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: 'Poppins', sans-serif; }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-amber-200 to-amber-500">

  <div class="bg-white/80 backdrop-blur-lg p-8 rounded-2xl shadow-2xl w-full max-w-md text-center">
    <h1 class="text-3xl font-bold text-amber-800 mb-6">MUEBLES MAIK</h1>
    <h2 class="text-lg text-gray-600 mb-4">Inicia sesión para continuar</h2>

    <?php if (!empty($error)): ?>
      <div class="bg-red-100 text-red-700 py-2 px-4 rounded mb-4 text-sm">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form action="app/controllers/logincontroller.php" method="POST" class="space-y-5">
      <input type="hidden" name="latitud" id="latitud">
<input type="hidden" name="longitud" id="longitud">

      <div>
        <input type="text" name="usuario" placeholder="Usuario" required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:outline-none">
      </div>

        <!-- Campo de Contraseña con Toggle (Contenedor RELATIVO) -->
        <div class="relative mb-4">
            <!-- El input original del usuario, con un ID añadido -->
            <input type="password" id="passwordInput" name="password" placeholder="Contraseña" required
                class="w-full pl-4 pr-12 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:outline-none transition duration-150">

            <!-- Botón del Ojo (Posicionado ABSOLUTO) -->
            <button type="button" id="togglePassword"
                class="absolute right-3 top-1/2 transform -translate-y-1/2 p-1 text-gray-500 hover:text-amber-600 focus:outline-none transition duration-150"
                aria-label="Mostrar/Ocultar Contraseña">
                <!-- Icono inicial: Ojo cerrado (eye-off) -->
                <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-10-7-10-7a1.86 1.86 0 0 1 1.63-1.57M16.6 9.4A3 3 0 0 0 14 12c0 .7-.16 1.34-.43 1.9L2 2L22 22M15 12a3 3 0 0 0-3-3m-3 0a3 3 0 0 0-3 3"/>
                </svg>
            </button>
        </div>

      <button type="submit"
        class="w-full bg-amber-600 hover:bg-amber-700 text-white font-semibold py-2 rounded-lg transition">
        Iniciar sesión
      </button>
    </form>

    <p class="mt-5 text-gray-600 text-sm">© <?= date("Y") ?> Muebles Maik. Todos los derechos reservados.</p>
  </div>


    <!-- Lógica JavaScript para el toggle -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const passwordInput = document.getElementById('passwordInput');
            const toggleButton = document.getElementById('togglePassword');
            const eyeIcon = document.getElementById('eyeIcon');

            /**
             * Alterna la visibilidad de la contraseña y cambia el icono.
             */
            function togglePasswordVisibility() {
                // 1. Alternar el atributo 'type'
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';

                // 2. Alternar el icono SVG
                // Ojo abierto (eye) para mostrar el texto
                const iconOpen = '<path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>';
                // Ojo tachado (eye-off) para ocultar el texto
                const iconClosed = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-10-7-10-7a1.86 1.86 0 0 1 1.63-1.57M16.6 9.4A3 3 0 0 0 14 12c0 .7-.16 1.34-.43 1.9L2 2L22 22M15 12a3 3 0 0 0-3-3m-3 0a3 3 0 0 0-3 3"/>';
                
                eyeIcon.innerHTML = isPassword
                    ? iconOpen // Si era 'password', ahora es 'text', mostramos el ojo abierto
                    : iconClosed; // Si era 'text', ahora es 'password', mostramos el ojo cerrado
            }

            // Asignar el listener al botón
            if (toggleButton) {
                toggleButton.addEventListener('click', togglePasswordVisibility);
            }
        });
    </script>

    <script>
document.addEventListener('DOMContentLoaded', () => {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (position) => {
        // Si se obtiene correctamente la ubicación
        document.getElementById('latitud').value = position.coords.latitude;
        document.getElementById('longitud').value = position.coords.longitude;
        console.log("Ubicación obtenida:", position.coords.latitude, position.coords.longitude);
      },
      (error) => {
        console.error("Error obteniendo la ubicación:", error.message);
        alert("No pudimos obtener tu ubicación. Activa el GPS o permisos de ubicación.");
      }
    );
  } else {
    alert("Tu navegador no soporta la geolocalización.");
  }
});
</script>

</body>
</html>
