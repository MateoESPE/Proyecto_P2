<?php
declare(strict_types=1);

namespace Class;

interface OperacionPolinomio
{
    public function evaluar(float $x): float;
    public function derivada(): array;  
    public static function sumarPolinomios(array $p1, array $p2): array; 
}
