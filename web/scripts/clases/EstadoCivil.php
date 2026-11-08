<?php
declare(strict_types=1);
 
/**
 * Enumeración (enum): lista cerrada de valores posibles. Un estado civil
 * solo puede ser uno de estos cuatro; no se puede inventar otro.
 *
 * ": int" la hace "con respaldo": cada caso tiene un número asociado.
 * Eso permite pasar de número a caso con EstadoCivil::from(20).
 */
enum EstadoCivil: int
{
    case Soltero = 10;
    case Casado = 20;
    case Pareja = 30;
    case Viudo = 40;
 
    /**
     * Devuelve el nombre del caso ("Soltero", "Casado"...).
     * $this->name lo crea PHP automáticamente en todos los enums.
     *
     * @return string
     */
    public function descripcion(): string
    {
        return $this->name;
    }
 
    /**
     * Devuelve el número asociado (10, 20...). $this->value también lo crea PHP.
     *
     * @return int
     */
    public function valor(): int
    {
        return $this->value;
    }
 
    /**
     * Devuelve todos los casos en un array. cases() también lo trae PHP de serie.
     *
     * @return array<self>
     */
    public static function casos(): array
    {
        return self::cases();
    }
}
