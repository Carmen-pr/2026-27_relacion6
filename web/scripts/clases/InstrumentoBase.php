<?php
declare(strict_types=1);

abstract class InstrumentoBase
{
    private string $_descripcion;
    protected int $_edad = 10;
    protected int $_ordenCreacion = 0;
    protected static int $_totalInstrumentos = 0;

    public function __construct(string $descripcion, int $edad = 10)
    {
        $this->_descripcion = $descripcion;
        $this->_edad = $edad;
        $this->asignarOrdenCreacion();
    }

    public function getDescripcion(): string
    {
        return $this->_descripcion;
    }

    public function setDescripcion(string $descripcion): void
    {
        if (mb_strlen(trim($descripcion)) === 0) {
            throw new InvalidArgumentException('La descripción no puede estar vacía.');
        }

        $this->_descripcion = trim($descripcion);
    }

    public function getEdad(): int
    {
        return $this->_edad;
    }

    abstract public function sonido(): string;

    abstract public function afinar(): string;

    public function envejecer(): void
    {
        $this->_edad++;
    }

    public function __toString(): string
    {
        return "instrumento con descripción {$this->_descripcion}, instancia {$this->_ordenCreacion}"
            . ' de un total de ' . self::$_totalInstrumentos
            . ". Tiene {$this->_edad} años. La clase es " . get_class($this);
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

    protected function asignarOrdenCreacion(): void
    {
        self::$_totalInstrumentos++;
        $this->_ordenCreacion = self::$_totalInstrumentos;
    }
}