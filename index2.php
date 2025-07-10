<?php
declare(strict_types=1);
session_start(); // Iniciar sesión para guardar datos entre peticiones

require_once "ejercicio2/OperacionEstadistica.php";
require_once "ejercicio2/Estadistica.php";
require_once "ejercicio2/EstadisticaBasica.php";

use Class\EstadisticaBasica;

$informeHTML = "";                      // Variable para el informe generado
$datosGuardados = $_SESSION['datos'] ?? [];  // Cargar datos guardados en sesión o iniciar vacíos
$mensajeError = "";                     // Mensaje para errores
$mensajeInfo = "";                      // Mensaje para información al usuario

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accion = $_POST['accion'] ?? '';

    if ($accion === "agregar") {
        // Obtener y limpiar datos del formulario
        $clave = trim($_POST['clave']);
        $valoresRaw = trim($_POST['valores']);

        // Validar campos no vacíos
        if (empty($clave) || empty($valoresRaw)) {
            $mensajeError = "Debe completar ambos campos.";
        } else {
            // Separar valores por coma y filtrar solo números
            $valores = array_map('trim', explode(',', $valoresRaw));
            $valores = array_filter($valores, fn($v) => is_numeric($v));

            if (count($valores) === 0) {
                $mensajeError = "Ingrese al menos un número válido.";
            } else {
                // Convertir a float y guardar en sesión
                $valores = array_map('floatval', $valores);
                $datosGuardados[$clave] = $valores;
                $_SESSION['datos'] = $datosGuardados;
                $mensajeInfo = "Conjunto agregado.";
            }
        }
    }

    if ($accion === "generar") {
        // Crear instancia y generar informe con los datos guardados
        $estadistica = new EstadisticaBasica($datosGuardados);
        $informeHTML = $estadistica->generarInforme($datosGuardados);
    }

    if ($accion === "limpiar") {
        // Limpiar datos y reiniciar variables
        $_SESSION['datos'] = [];
        $datosGuardados = [];
        $informeHTML = "";
        $mensajeInfo = "Datos borrados.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Estadísticas Básicas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script>
        // Validación sencilla en el cliente para valores numéricos separados por coma
        function validarFormulario() {
            const valores = document.querySelector('input[name="valores"]').value;
            const regex = /^-?\d+(\.\d+)?(,\s*-?\d+(\.\d+)?)*$/;
            if (!regex.test(valores.trim())) {
                alert("Ingrese solo números separados por comas.");
                return false; // Evita enviar formulario
            }
            return true; // Permite enviar formulario
        }
    </script>
</head>
<body class="container py-4">
    <h1 class="mb-4">Estadísticas Básicas</h1>

    <!-- Mostrar mensaje de error si existe -->
    <?php if ($mensajeError): ?>
        <div class="alert alert-danger"><?= $mensajeError ?></div>
    <?php endif; ?>

    <!-- Mostrar mensaje informativo si existe -->
    <?php if ($mensajeInfo): ?>
        <div class="alert alert-success"><?= $mensajeInfo ?></div>
    <?php endif; ?>

    <!-- Formulario para agregar conjunto -->
    <form method="post" class="mb-4" onsubmit="return validarFormulario();">
        <div class="row">
            <div class="col-md-6">
                <label class="form-label">Nombre del conjunto:</label>
                <input type="text" name="clave" class="form-control" placeholder="Ej: conjunto1" required />
            </div>
            <div class="col-md-6">
                <label class="form-label">Valores (separados por coma):</label>
                <input type="text" name="valores" class="form-control" placeholder="Ej: 3, 5, 7, 9" required />
            </div>
        </div>
        <div class="mt-3">
            <button type="submit" name="accion" value="agregar" class="btn btn-primary w-100">
                Agregar Conjunto
            </button>
        </div>
    </form>

    <!-- Mostrar conjuntos agregados si hay -->
    <?php if (!empty($datosGuardados)): ?>
        <h3>Conjuntos agregados:</h3>
        <ul class="list-group mb-4">
            <?php foreach ($datosGuardados as $clave => $valores): ?>
                <li class="list-group-item">
                    <strong><?= htmlspecialchars($clave) ?>:</strong>
                    <?= implode(', ', $valores) ?>
                </li>
            <?php endforeach; ?>
        </ul>

        <!-- Botones para generar informe y limpiar datos -->
        <div class="d-flex justify-content-between mb-4 gap-3">
            <form method="post" class="flex-fill">
                <button type="submit" name="accion" value="generar" class="btn btn-success w-100">
                    Generar Informe
                </button>
            </form>
            <form method="post" class="flex-fill">
                <button type="submit" name="accion" value="limpiar" class="btn btn-danger w-100">
                    Limpiar Todo
                </button>
            </form>
        </div>
    <?php endif; ?>

    <!-- Mostrar informe generado si existe -->
    <?php if (!empty($informeHTML)): ?>
        <h3>Informe generado:</h3>
        <div class="alert alert-info">
            <?= $informeHTML ?>
        </div>
    <?php endif; ?>
</body>
</html>
