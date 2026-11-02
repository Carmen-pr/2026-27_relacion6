<?php
declare(strict_types=1);

// Antes de hacerla abstracta, se puede probar con una subclase concreta que implemente sonido() y afinar().
abstract class InstrumentoBase
{
    // La descripción es privada y la edad solo puede consultarse desde fuera.
    private string $_descripcion;
    protected int $_edad = 10;

    // El contador compartido asigna a cada instrumento su orden de creación.
    protected int $_ordenCreacion = 0;
    protected static int $_totalInstrumentos = 0;

    public function __construct(string $descripcion, int $edad = 10)
    {
        // Se reutiliza el setter para validar y normalizar la descripción inicial.
        $this->setDescripcion($descripcion);

        if ($edad < 0) {
            throw new InvalidArgumentException('La edad no puede ser negativa.');
        }

        $this->_edad = $edad;
        $this->asignarOrdenCreacion();
    }

    // Los métodos públicos permiten consultar y cambiar únicamente la descripción.
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

    // La edad se expone en modo lectura; envejecer() es quien la modifica.
    public function getEdad(): int
    {
        return $this->_edad;
    }

    // Cada instrumento concreto define su sonido y su forma de afinación.
    abstract public function sonido(): string;

    abstract public function afinar(): string;

    // Incrementa en un año la edad de la instancia.
    public function envejecer(): void
    {
        $this->_edad++;
    }

    // Describe la instancia e indica su posición y el total de instrumentos creados.
    public function __toString(): string
    {
        return "instrumento con descripción {$this->_descripcion}, instancia {$this->_ordenCreacion}"
            . ' de un total de ' . self::$_totalInstrumentos
            . ". Tiene {$this->_edad} años. La clase es " . get_class($this);
    }

    // Se bloquea el acceso mágico para que solo se usen las propiedades definidas.
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

    // Asigna un nuevo número de instancia tanto al crear como al clonar.
    protected function asignarOrdenCreacion(): void
    {
        self::$_totalInstrumentos++;
        $this->_ordenCreacion = self::$_totalInstrumentos;
    }
}