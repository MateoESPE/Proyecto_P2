<?php
declare(strict_types=1);

namespace Class;

require_once "ejercicio3/OperacionPolinomio.php";

abstract class PolinomioAbstracto implements OperacionPolinomio
{
    protected array $terminos; // Array asociativo grado => coeficiente

    public function __construct(array $terminos = [])
    {
        $this->terminos = $terminos;
    }
}
