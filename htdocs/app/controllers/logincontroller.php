<?php
date_default_timezone_set('America/Bogota');
session_start();
include('../../cn.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['password']);
    $latitud = isset($_POST['latitud']) ? $_POST['latitud'] : null;
$longitud = isset($_POST['longitud']) ? $_POST['longitud'] : null;


if ($latitud && $longitud) {
    $ubicacion = "Lat: $latitud, Lng: $longitud";
    // puedes guardarla en la tabla de asistencia, logs o sesión
    $_SESSION['ubicacion'] = $ubicacion;
}else{
    $ubicacion='desconocido';
}
    if (empty($usuario) || empty($password)) {
        echo '<script>
            alert("Todos los campos son obligatorios.");
            window.location.href = "../../inicio.php";
        </script>';
        exit();
    }

    try {
        $sql = "SELECT * FROM trabajadores WHERE usuario = :usuario LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();
        



        if ($stmt->rowCount() > 0) {
            $trabajador = $stmt->fetch(PDO::FETCH_ASSOC);

if (password_verify($password, $trabajador['clave'])) {
    // Guardar variables de sesión
    $_SESSION['id_trabajador'] = $trabajador['id_trabajador'];
    $_SESSION['nombre'] = $trabajador['nombre'];
    $_SESSION['apellido'] = $trabajador['apellido'];
    $_SESSION['id_cargo'] = $trabajador['id_cargo'];
    $_SESSION['id_area'] = $trabajador['id_area'];
    $_SESSION['id_rol'] = $trabajador['id_rol'];
    $_SESSION['usuario'] = $trabajador['usuario'];

    // ---------------------------------------------
    //  Registro de la sesión en la base de datos
    // ---------------------------------------------
    $id_trabajador = $trabajador['id_trabajador'];
    $fecha_y_hora = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'];

    $insert = $pdo->prepare("
        INSERT INTO sesiones (id_trabajador, fecha_y_hora, locacion, ip)
        VALUES (:id_trabajador, :fecha_y_hora, :locacion, :ip)
    ");
    $insert->bindParam(':id_trabajador', $id_trabajador);
    $insert->bindParam(':fecha_y_hora', $fecha_y_hora);
    $insert->bindParam(':locacion', $ubicacion);
    $insert->bindParam(':ip', $ip);
    $insert->execute();

    // ---------------------------------------------
    // Validar tipo de rol y redirigir
    // ---------------------------------------------
    $rolStmt = $pdo->prepare("SELECT nombre_rol FROM roles WHERE id_rol = :id_rol LIMIT 1");
    $rolStmt->bindParam(':id_rol', $trabajador['id_rol']);
    $rolStmt->execute();
    $rol = $rolStmt->fetchColumn();

    if (strcasecmp($rol, 'Administrador') === 0) {
        header("Location: ../../views/admins/Dashboard.php");
        exit();
    } else {
        if ($trabajador['id_cargo'] == 3) {
            header("Location: ../../views/workers/asistencia.php");
            exit();
        } else {
            header("Location: ../../views/workers/Worker_Dashboard.php");
            exit();
        }
    }
} else {
    echo '<script>
        alert("Contraseña incorrecta.");
        window.location.href = "../../inicio.php";
    </script>';
    exit();
}

        } else {
            echo '<script>
                alert("Usuario no encontrado.");
                window.location.href = "../../inicio.php";
            </script>';
            exit();
        }
    } catch (PDOException $e) {
        echo '<script>
            alert("Error en la base de datos: ' . addslashes($e->getMessage()) . '");
            window.location.href = "../../inicio.php";
        </script>';
        exit();
    }
} else {
    header("Location: ../../inicio.php");
    exit();
}
?>
