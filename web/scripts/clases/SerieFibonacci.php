<?php
declare(strict_types=1);

class SerieFibonacci implements Iterator
{
    private int $_limite;
    private int $_claveActual = 0;

    public function __construct(int $limite)
    {
        if ($limite < 0) {
            throw new InvalidArgumentException('El límite no puede ser negativo.');
        }

        $this->_limite = $limite;
    }

    public function current(): int
    {
        return self::calcularFibonacci($this->_claveActual);
    }

    public function key(): mixed
    {
        return $this->_claveActual;
    }

    public function next(): void
    {
        $this->_claveActual++;
    }

    public function rewind(): void
    {
        $this->_claveActual = 0;
    }

    public function valid(): bool
    {
        return $this->_claveActual <= $this->_limite;
    }

    public static function fFibonacci(int $ultimo): Generator
    {
        if ($ultimo < 0) {
            throw new InvalidArgumentException('El último índice no puede ser negativo.');
        }

        for ($indice = 0; $indice <= $ultimo; $indice++) {
            yield self::calcularFibonacci($indice);
        }
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

    private static function calcularFibonacci(int $indice): int
    {
        $raizCinco = sqrt(5);
        $phi = (1 + $raizCinco) / 2;
        $psi = (1 - $raizCinco) / 2;

        return (int) round((($phi ** $indice) - ($psi ** $indice)) / $raizCinco);
    }
}