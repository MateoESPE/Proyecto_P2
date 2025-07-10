<?php
declare(strict_types=1);
session_start();

require_once "ejercicio3/OperacionPolinomio.php";
require_once "ejercicio3/PolinomioAbstracto.php";
require_once "ejercicio3/Polinomio.php";

use Class\Polinomio;

$mensaje = "";
$resultadoEvaluacion = "";
$resultadoDerivada = "";
$resultadoSuma = "";

$polinomiosGuardados = $_SESSION['polinomios'] ?? [];

function obtenerPolinomioDesdePost(): array {
    $terminos = [];
    if (isset($_POST['coef']) && is_array($_POST['coef'])) {
        foreach ($_POST['coef'] as $grado => $coef) {
            $gradoInt = (int)$grado;
            $coefFloat = (float)$coef;
            if ($coefFloat != 0) { // guardamos solo coeficientes != 0 para evitar términos nulos
                $terminos[$gradoInt] = $coefFloat;
            }
        }
    }
    return $terminos;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accion = $_POST['accion'] ?? "";

    if ($accion === "agregar") {
        $nombre = trim($_POST['nombre'] ?? "");
        $terminos = obtenerPolinomioDesdePost();

        if ($nombre === "") {
            $mensaje = "Debe ingresar un nombre para el polinomio.";
        } elseif (empty($terminos)) {
            $mensaje = "Debe ingresar al menos un coeficiente distinto de cero.";
        } else {
            $polinomiosGuardados[$nombre] = $terminos;
            $_SESSION['polinomios'] = $polinomiosGuardados;
            $mensaje = "Polinomio agregado correctamente.";
        }
    }

    if ($accion === "evaluar") {
        $nombre = $_POST['nombreEvaluar'] ?? "";
        $x = floatval($_POST['valorX'] ?? 0);
        if (!isset($polinomiosGuardados[$nombre])) {
            $mensaje = "Polinomio no encontrado.";
        } else {
            $p = new Polinomio($polinomiosGuardados[$nombre]);
            $res = $p->evaluar($x);
            $resultadoEvaluacion = "P($x) = $res";
        }
    }

    if ($accion === "derivar") {
        $nombre = $_POST['nombreDerivar'] ?? "";
        if (!isset($polinomiosGuardados[$nombre])) {
            $mensaje = "Polinomio no encontrado.";
        } else {
            $p = new Polinomio($polinomiosGuardados[$nombre]);
            $der = $p->derivada();
            $resultadoDerivada = "P'(x) = " . Polinomio::polinomioToString($der);
        }
    }

    if ($accion === "sumar") {
        $nombre1 = $_POST['nombre1'] ?? "";
        $nombre2 = $_POST['nombre2'] ?? "";
        if (!isset($polinomiosGuardados[$nombre1]) || !isset($polinomiosGuardados[$nombre2])) {
            $mensaje = "Uno o ambos polinomios no encontrados.";
        } else {
            $resultado = Polinomio::sumarPolinomios($polinomiosGuardados[$nombre1], $polinomiosGuardados[$nombre2]);
            $resultadoSuma = Polinomio::polinomioToString($resultado);
        }
    }

    if ($accion === "limpiar") {
        $_SESSION['polinomios'] = [];
        $polinomiosGuardados = [];
        $mensaje = "Datos limpiados.";
        $resultadoEvaluacion = "";
        $resultadoDerivada = "";
        $resultadoSuma = "";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Manejo de Polinomios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script>
        function mostrarCampos() {
            const grado = parseInt(document.getElementById('grado').value);
            const contenedor = document.getElementById('camposCoeficientes');
            contenedor.innerHTML = "";

            if (isNaN(grado) || grado < 1) return;

            const rowDiv = document.createElement('div');
            rowDiv.className = "row gx-3";

            for (let i = grado; i >= 0; i--) {
                const colDiv = document.createElement('div');
                colDiv.className = "col-auto text-center";

                const label = document.createElement('label');
                label.className = "form-label d-block";
                label.style.fontWeight = "bold";
                label.style.fontSize = "1.1rem";
                label.textContent = (i > 1) ? `x^${i}` : (i === 1) ? `x` : "Const.";

                const input = document.createElement('input');
                input.type = "number";
                input.step = "any";
                input.name = `coef[${i}]`;
                input.className = "form-control text-center";
                input.style.maxWidth = "80px";
                input.value = "0";

                colDiv.appendChild(label);
                colDiv.appendChild(input);
                rowDiv.appendChild(colDiv);
            }

            contenedor.appendChild(rowDiv);
        }
        window.onload = function() {
            mostrarCampos();
        };
    </script>
</head>
<body class="container py-4">
    <h1 class="mb-4">Manejo de Polinomios</h1>

    <?php if($mensaje): ?>
        <div class="alert alert-info"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <!-- Formulario para agregar polinomio -->
    <form method="post" class="mb-4">
        <h5>Agregar polinomio</h5>
         <div class="mb-3">
            <label for="nombre" class="form-label">Nombre del polinomio</label>
            <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Ejemplo: P1" required />
        </div>
        <div class="mb-3">
            <label for="grado" class="form-label">Seleccione grado del polinomio</label>
            <select id="grado" name="grado" class="form-select" onchange="mostrarCampos()" required>
                <option value="" disabled selected>Seleccione grado</option>
                <?php for ($i = 1; $i <= 10; $i++): ?>
                    <option value="<?= $i ?>"><?= $i ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <div id="camposCoeficientes" class="mb-3"></div>


        <button type="submit" name="accion" value="agregar" class="btn btn-primary">Agregar Polinomio</button>
    </form>

    <?php if(!empty($polinomiosGuardados)): ?>
        <h5>Polinomios guardados:</h5>
        <ul class="list-group mb-3">
            <?php foreach($polinomiosGuardados as $nombre => $terminos): ?>
                <li class="list-group-item">
                    <strong><?= htmlspecialchars($nombre) ?>:</strong> <?= Polinomio::polinomioToString($terminos) ?>
                </li>
            <?php endforeach; ?>
        </ul>

        <!-- Evaluar polinomio -->
        <form method="post" class="mb-3">
            <h5>Evaluar polinomio</h5>
            <div class="row g-2 align-items-center">
                <div class="col-md-4">
                    <select name="nombreEvaluar" class="form-select" required>
                        <option value="" disabled selected>Seleccione polinomio</option>
                        <?php foreach($polinomiosGuardados as $nombre => $_): ?>
                            <option value="<?= htmlspecialchars($nombre) ?>"><?= htmlspecialchars($nombre) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="number" step="any" name="valorX" class="form-control" placeholder="Valor de x" required />
                </div>
                <div class="col-md-4">
                    <button type="submit" name="accion" value="evaluar" class="btn btn-success w-100">Evaluar</button>
                </div>
            </div>
            <?php if ($resultadoEvaluacion): ?>
                <div class="mt-2 alert alert-secondary"><?= $resultadoEvaluacion ?></div>
            <?php endif; ?>
        </form>

        <!-- Derivar polinomio -->
        <form method="post" class="mb-3">
            <h5>Derivar polinomio</h5>
            <div class="row g-2 align-items-center">
                <div class="col-md-8">
                    <select name="nombreDerivar" class="form-select" required>
                        <option value="" disabled selected>Seleccione polinomio</option>
                        <?php foreach($polinomiosGuardados as $nombre => $_): ?>
                            <option value="<?= htmlspecialchars($nombre) ?>"><?= htmlspecialchars($nombre) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" name="accion" value="derivar" class="btn btn-warning w-100">Derivar</button>
                </div>
            </div>
            <?php if ($resultadoDerivada): ?>
                <div class="mt-2 alert alert-secondary"><?= $resultadoDerivada ?></div>
            <?php endif; ?>
        </form>

        <!-- Sumar polinomios -->
        <form method="post" class="mb-3">
            <h5>Sumar polinomios</h5>
            <div class="row g-2 align-items-center">
                <div class="col-md-5">
                    <select name="nombre1" class="form-select" required>
                        <option value="" disabled selected>Polinomio 1</option>
                        <?php foreach($polinomiosGuardados as $nombre => $_): ?>
                            <option value="<?= htmlspecialchars($nombre) ?>"><?= htmlspecialchars($nombre) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-5">
                    <select name="nombre2" class="form-select" required>
                        <option value="" disabled selected>Polinomio 2</option>
                        <?php foreach($polinomiosGuardados as $nombre => $_): ?>
                            <option value="<?= htmlspecialchars($nombre) ?>"><?= htmlspecialchars($nombre) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" name="accion" value="sumar" class="btn btn-info w-100">Sumar</button>
                </div>
            </div>
            <?php if ($resultadoSuma): ?>
                <div class="mt-2 alert alert-secondary"><?= $resultadoSuma ?></div>
            <?php endif; ?>
        </form>

        <!-- Limpiar todo -->
        <form method="post" class="mb-3">
            <button type="submit" name="accion" value="limpiar" class="btn btn-danger w-100">Limpiar Todo</button>
        </form>
    <?php else: ?>
        <div class="alert alert-warning">No hay polinomios agregados aún.</div>
    <?php endif; ?>
</body>
</html>
