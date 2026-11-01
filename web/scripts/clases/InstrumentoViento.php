<?php
declare(strict_types=1);

abstract class InstrumentoViento extends InstrumentoBase
{
    protected string $_material = 'madera';

    public function __construct(string $material = 'madera', int $edad = 15)
    {
        if (mb_strlen(trim($material)) === 0) {
            throw new InvalidArgumentException('El material no puede estar vacío.');
        }

        parent::__construct('instrumento de viento', $edad);
        $this->_material = trim($material);
    }

    final public function afinar(): string
    {
        return 'El instrumento de viento está afinado.';
    }

    public function __toString(): string
    {
        return parent::__toString() . " Instrumento de material {$this->_material}";
    }

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