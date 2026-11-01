<?php
declare(strict_types=1);

class ClaseMisPropiedades implements IteratorAggregate
{
    private array $_propiedades = [];
    public mixed $propPublica = null;
    private mixed $_propPrivada = 25;

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

    public function __get(string $nombre): mixed
    {
        if ($nombre === '_propPrivada') {
            return $this->_propPrivada;
        }

        if (!array_key_exists($nombre, $this->_propiedades)) {
            throw new Exception("No existe la propiedad $nombre");
        }

        return $this->_propiedades[$nombre];
    }

    public function __isset(string $nombre): bool
    {
        if ($nombre === '_propPrivada') {
            return isset($this->_propPrivada);
        }

        return isset($this->_propiedades[$nombre]);
    }

    public function __unset(string $nombre): void
    {
        if ($nombre === '_propPrivada') {
            throw new Exception("No se puede eliminar la propiedad $nombre");
        }

        unset($this->_propiedades[$nombre]);
    }

    public function getIterator(): Traversable
    {
        $elementos = [];
        foreach ($this->_propiedades as $nombre => $valor) {
            $elementos['oi_' . $nombre] = $valor;
        }

        $elementos['propPublica'] = $this->propPublica;
        $elementos['_propPrivada'] = $this->_propPrivada;

        return new ArrayIterator($elementos);
    }
}