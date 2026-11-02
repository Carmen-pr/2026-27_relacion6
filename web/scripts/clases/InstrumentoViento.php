<?php
declare(strict_types=1);

abstract class InstrumentoViento extends InstrumentoBase
{
    // El material se conserva protegido para las clases concretas, sin getter ni setter.
    protected string $_material = 'madera';

    public function __construct(string $material = 'madera', int $edad = 15)
    {
        if (mb_strlen(trim($material)) === 0) {
            throw new InvalidArgumentException('El material no puede estar vacío.');
        }

        // Todos los instrumentos de viento comparten la misma descripción base.
        parent::__construct('instrumento de viento', $edad);
        $this->_material = trim($material);
    }

    // La afinación es común a todos los instrumentos de viento.
    final public function afinar(): string
    {
        return 'El instrumento de viento está afinado.';
    }

    // Se añade el material al texto descriptivo heredado.
    public function __toString(): string
    {
        return parent::__toString() . " Instrumento de material {$this->_material}";
    }

    // Se mantienen bloqueados los accesos dinámicos en esta clase intermedia.
    public function __get(string $nombre): never
    {
        throw new Exception("No existe la propiedad $nombre");
    }

    public function __set(string $nombre, mixed $valor): void
    {
        throw new Exception("No existe la propiedad $nombre");
    }

    public function __isset(string $nombre): bool
    {
        return false;
    }

    public function __unset(string $nombre): void
    {
        throw new Exception("No existe la propiedad $nombre");
    }
}