<?php
declare(strict_types=1);

final class Flauta extends InstrumentoViento implements IFabricable
{
    // El constructor privado obliga a crear flautas mediante la fábrica estática.
    private function __construct(string $material = 'madera', int $edad = 15)
    {
        parent::__construct($material, $edad);
    }

    // Lee las opciones permitidas y aplica sus valores por defecto.
    public static function CrearDesdeArray(array $datos): self
    {
        $material = $datos['material'] ?? 'madera';
        $edad = $datos['edad'] ?? 15;

        if (!is_string($material)) {
            throw new InvalidArgumentException('El material debe ser una cadena.');
        }
        if (!is_int($edad) || $edad < 0) {
            throw new InvalidArgumentException('La edad debe ser un entero no negativo.');
        }

        return new self($material, $edad);
    }

    // Implementa el sonido propio de la flauta.
    public function sonido(): string
    {
        return 'La flauta suena: fiiii.';
    }

    // Implementaciones requeridas por IFabricable.
    public function metodoFabricacion(): string
    {
        return 'La flauta se fabrica dando forma al material y añadiendo sus llaves.';
    }

    public function metodoReciclado(): string
    {
        return 'La flauta se desmonta y se separan sus materiales para reciclarlos.';
    }

    // La copia empieza con edad cero y recibe un nuevo orden de creación.
    public function __clone()
    {
        $this->_edad = 0;
        $this->asignarOrdenCreacion();
    }

    // La flauta tampoco permite acceder a propiedades no declaradas.
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
