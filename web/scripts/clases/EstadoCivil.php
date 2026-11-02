<?php
declare(strict_types=1);

enum EstadoCivil: int
{
    // Los valores enteros permiten reconstruir el caso con EstadoCivil::from().
    case Soltero = 10;
    case Casado = 20;
    case Pareja = 30;
    case Viudo = 40;

    // Devuelve el nombre declarado para el caso actual.
    public function descripcion(): string
    {
        return $this->name;
    }

    // Devuelve el valor entero asociado al caso.
    public function valor(): int
    {
        return $this->value;
    }

    // Proporciona todos los casos disponibles en la enumeración.
    /** @return array<self> */
    public static function casos(): array
    {
        return self::cases();
    }
}