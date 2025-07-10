<?php
declare(strict_types=1);

namespace Class;

require_once "ejercicio3/PolinomioAbstracto.php";

class Polinomio extends PolinomioAbstracto
{
    // Evalúa el polinomio para un valor x
    public function evaluar(float $x): float
    {
        $resultado = 0.0;
        foreach ($this->terminos as $grado => $coeficiente) {
            $resultado += $coeficiente * pow($x, (int)$grado);
        }
        return $resultado;
    }

    // Devuelve la derivada del polinomio como array asociativo
    public function derivada(): array
    {
        $derivada = [];
        foreach ($this->terminos as $grado => $coeficiente) {
            $gradoInt = (int)$grado;
            if ($gradoInt > 0) {
                $derivada[$gradoInt - 1] = $coeficiente * $gradoInt;
            }
        }
        return $derivada;
    }

    // Suma dos polinomios (arrays asociativos) y devuelve el resultado
    public static function sumarPolinomios(array $p1, array $p2): array
    {
        $resultado = $p1;

        foreach ($p2 as $grado => $coeficiente) {
            if (isset($resultado[$grado])) {
                $resultado[$grado] += $coeficiente;
            } else {
                $resultado[$grado] = $coeficiente;
            }
        }
        // Eliminar coeficientes cero
        foreach ($resultado as $grado => $coef) {
            if (abs($coef) < 1e-10) {
                unset($resultado[$grado]);
            }
        }

        krsort($resultado); // Ordenar de mayor a menor grado
        return $resultado;
    }

    // Función auxiliar para convertir array asociativo a string para mostrar
    public static function polinomioToString(array $polinomio): string
    {
        if (empty($polinomio)) return "0";

        krsort($polinomio);
        $partes = [];

        foreach ($polinomio as $grado => $coeficiente) {
            if ($coeficiente == 0) continue;

            $signo = ($coeficiente > 0) ? "+" : "-";
            $absCoef = abs($coeficiente);

            if ($grado == 0) {
                $parte = "{$absCoef}";
            } elseif ($grado == 1) {
                $parte = ($absCoef == 1) ? "x" : "{$absCoef}x";
            } else {
                $parte = ($absCoef == 1) ? "x^{$grado}" : "{$absCoef}x^{$grado}";
            }

            $partes[] = $signo . " " . $parte;
        }

        $polinomioStr = implode(" ", $partes);

        // Quitar signo + inicial si existe
        if (substr($polinomioStr, 0, 2) === "+ ") {
            $polinomioStr = substr($polinomioStr, 2);
        }

        return $polinomioStr;
    }
}
