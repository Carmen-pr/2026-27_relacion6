<?php
declare(strict_types=1);

class ClaseMisPropiedades implements IteratorAggregate
{
    // Aquí se guardan las propiedades dinámicas creadas mediante __set().
    private array $_propiedades = [];

    // Estas dos propiedades se añaden al final del recorrido del objeto.
    public mixed $propPublica = null;
    private mixed $_propPrivada = 25;

    // Guarda valores dinámicos y evita que se modifique la propiedad privada.
    public function __set(string $nombre, mixed $valor): void
    {
        if ($nombre === '_propPrivada') {
            throw new Exception("No se puede asignar la propiedad $nombre");
        }

        if ($nombre === '' || $nombre === 'propPublica') {
            throw new Exception("No existe la propiedad dinámica $nombre");
        }

        $this->_propiedades[$nombre] = $valor;
    }

    // Devuelve una propiedad dinámica o lanza una excepción si no está asignada.
    public function __get(string $nombre): mixed
    {
        if ($nombre === '_propPrivada') {
            throw new Exception("No se puede acceder a la propiedad $nombre");
        }

        if (!array_key_exists($nombre, $this->_propiedades)) {
            throw new Exception("No existe la propiedad $nombre");
        }

        return $this->_propiedades[$nombre];
    }

    // Comprueba las propiedades dinámicas sin generar errores si no existen.
    public function __isset(string $nombre): bool
    {
        if ($nombre === '_propPrivada') {
            return false;
        }

        return isset($this->_propiedades[$nombre]);
    }

    // Elimina solo propiedades dinámicas; la propiedad privada queda protegida.
    public function __unset(string $nombre): void
    {
        if ($nombre === '_propPrivada') {
            throw new Exception("No se puede eliminar la propiedad $nombre");
        }

        unset($this->_propiedades[$nombre]);
    }

    // Devuelve las propiedades dinámicas con prefijo y, al final, las dos declaradas.
    public function getIterator(): Traversable
    {
        $elementos = [];
        foreach ($this->_propiedades as $nombre => $valor) {
            $elementos['oi_' . $nombre] = $valor;
        }

        $elementos['oi_propPublica'] = $this->propPublica;
        $elementos['oi__propPrivada'] = $this->_propPrivada;

        return new ArrayIterator($elementos);
    }
}