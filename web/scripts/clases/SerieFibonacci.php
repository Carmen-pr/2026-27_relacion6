<?php
declare(strict_types=1);

class SerieFibonacci implements Iterator
{
    // El límite es inclusivo: con 10 se generan los índices de 0 a 10.
    private int $_limite;
    private int $_claveActual = 0;

    public function __construct(int $limite)
    {
        if ($limite < 0) {
            throw new InvalidArgumentException('El límite no puede ser negativo.');
        }

        $this->_limite = $limite;
    }

    // Iterator: calcula el valor correspondiente al índice actual.
    public function current(): int
    {
        return self::calcularFibonacci($this->_claveActual);
    }

    // Iterator: usa el índice de la serie como clave.
    public function key(): mixed
    {
        return $this->_claveActual;
    }

    // Iterator: avanza al siguiente índice.
    public function next(): void
    {
        $this->_claveActual++;
    }

    // Iterator: reinicia el recorrido para poder iterar otra vez.
    public function rewind(): void
    {
        $this->_claveActual = 0;
    }

    // El límite es inclusivo, por lo que también se genera F(límite).
    public function valid(): bool
    {
        return $this->_claveActual <= $this->_limite;
    }

    // Genera los valores desde F(0) hasta F(último) sin almacenar la serie completa.
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

    // Aplica la fórmula de Binet y redondea para compensar los errores de coma flotante.
    private static function calcularFibonacci(int $indice): int
    {
        $raizCinco = sqrt(5);
        $phi = (1 + $raizCinco) / 2;
        $psi = (1 - $raizCinco) / 2;

        return (int) round((($phi ** $indice) - ($psi ** $indice)) / $raizCinco);
    }
}