<?php
declare(strict_types=1);

namespace Class;

require_once "ejercicio2/Estadistica.php";

// Clase concreta que implementa los métodos estadísticos
class EstadisticaBasica extends Estadistica
{
    // Calcula la media de un conjunto de datos
    public function calcularMedia(array $datos): float {
        if (count($datos) === 0) return 0;
        return array_sum($datos) / count($datos);
    }

    // Calcula la mediana de un conjunto de datos
    public function calcularMediana(array $datos): float {
        sort($datos);
        $n = count($datos);
        if ($n === 0) return 0;

        $mid = (int)($n / 2);

        if ($n % 2 === 0) {
            return ($datos[$mid - 1] + $datos[$mid]) / 2;
        } else {
            return $datos[$mid];
        }
    }

    // Calcula la moda de un conjunto de datos
    public function calcularModa(array $datos): array {
        if (count($datos) === 0) return [];

        // Convertir a string para usar array_count_values sin errores
        $datosComoStrings = array_map('strval', $datos);
        $frecuencias = array_count_values($datosComoStrings);

        if (empty($frecuencias)) return [];

        $max = max($frecuencias);

        // Filtrar y devolver los valores más frecuentes como float
        return array_map('floatval', array_keys(array_filter($frecuencias, fn($v) => $v === $max)));
    }

    // Genera un informe HTML con media, mediana y moda para cada conjunto
    public function generarInforme(array $datos): string {
        $informe = "";

        foreach ($datos as $clave => $valores) {
            sort($valores);
            $media = $this->calcularMedia($valores);
            $mediana = $this->calcularMediana($valores);
            $moda = $this->calcularModa($valores);

            $informe .= "<strong>Conjunto: {$clave}</strong><br>";
            $informe .= "Media: " . number_format($media, 2) . "<br>";
            $informe .= "Mediana: " . number_format($mediana, 2) . "<br>";
            $informe .= "Moda: " . implode(", ", $moda) . "<br><hr>";
        }

        return $informe;
    }
}
