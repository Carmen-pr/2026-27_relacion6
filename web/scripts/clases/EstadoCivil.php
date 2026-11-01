<?php
declare(strict_types=1);

enum EstadoCivil: int
{
    case Soltero = 10;
    case Casado = 20;
    case Pareja = 30;
    case Viudo = 40;

    public function descripcion(): string
    {
        return $this->name;
    }

    public function valor(): int
    {
        return $this->value;
    }

    /** @return array<self> */
    public static function casos(): array
    {
        return self::cases();
    }
}