<?php declare(strict_types=1);

namespace Class;

require_once "ejercicio2/OperacionEstadistica.php";

// Clase abstracta base para operaciones estadísticas
abstract class Estadistica implements OperacionEstadistica
{
    // Almacena los conjuntos de datos
    protected array $datos;

    // Constructor que recibe los datos (opcional)
    public function __construct(array $datos = [])
    {
        $this->datos = $datos;
    }
}
