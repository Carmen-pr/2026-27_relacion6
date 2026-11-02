<?php
declare(strict_types=1);

interface IFabricable
{
    // Devuelve una descripción del proceso de fabricación del objeto.
    public function metodoFabricacion(): string;

    // Devuelve una descripción de cómo se recicla el objeto.
    public function metodoReciclado(): string;
}