<?php
declare(strict_types=1);

namespace Class;

// Interfaz que define operaciones estadísticas básicas
interface OperacionEstadistica
{
    // Calcula la media de un conjunto de datos
    public function calcularMedia(array $datos): float;

    // Calcula la mediana de un conjunto de datos
    public function calcularMediana(array $datos): float;

    // Calcula la moda de un conjunto de datos
    public function calcularModa(array $datos): array;

    // Genera un informe con los resultados
    public function generarInforme(array $datos): string;
}
