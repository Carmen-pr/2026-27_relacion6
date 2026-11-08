<?php
declare(strict_types=1);
 
/**
 * Clase que permite crear propiedades "dinámicas" (que no están declaradas)
 * guardándolas en un array interno, y se puede recorrer con foreach.
 *
 * IteratorAggregate: obliga a tener getIterator(), que devuelve el objeto
 * que sabe recorrer los datos. Es más fácil que implementar Iterator entero.
 */
class ClaseMisPropiedades implements IteratorAggregate
{
    /** @var array Aquí se guardan las propiedades dinámicas: nombre => valor. */
    private array $_propiedades = [];
 
    /** @var mixed Propiedad pública normal: se lee y se asigna sin pasar por métodos mágicos. */
    public mixed $propPublica = null;
 
    /** @var mixed Propiedad privada: desde fuera no se puede tocar. */
    private mixed $_propPrivada = 25;
 
    /**
     * PHP llama a __set al asignar una propiedad que NO existe o que NO es
     * accesible desde fuera. Por eso salta con $obj->propiedad1 = 'x' (no existe)
     * y también con $obj->_propPrivada = 99 (existe pero es private).
     *
     * @param string $nombre Nombre de la propiedad.
     * @param mixed $valor Valor a guardar.
     * @return void
     * @throws Exception Si se intenta tocar la privada o el nombre no vale.
     */
    public function __set(string $nombre, mixed $valor): void
    {
        if ($nombre === '_propPrivada') {
            throw new Exception("No se puede asignar la propiedad $nombre");
        }
 
        if ($nombre === '' || $nombre === 'propPublica') {
            throw new Exception("No existe la propiedad dinámica $nombre");
        }
 
        // Se guarda en el array con el nombre como clave.
        $this->_propiedades[$nombre] = $valor;
    }
 
    /**
     * Se llama al LEER una propiedad que no es accesible ($obj->propiedad1).
     *
     * @param string $nombre
     * @return mixed El valor guardado.
     * @throws Exception Si es la privada o no se había asignado nunca.
     */
    public function __get(string $nombre): mixed
    {
        if ($nombre === '_propPrivada') {
            throw new Exception("No se puede acceder a la propiedad $nombre");
        }
 
        // array_key_exists mira si la clave existe aunque su valor sea null.
        if (!array_key_exists($nombre, $this->_propiedades)) {
            throw new Exception("No existe la propiedad $nombre");
        }
 
        return $this->_propiedades[$nombre];
    }
 
    /**
     * Se llama con isset($obj->x). Dice si existe, sin dar error si no.
     *
     * @param string $nombre
     * @return bool
     */
    public function __isset(string $nombre): bool
    {
        if ($nombre === '_propPrivada') {
            return false; // la privada "no existe" para el exterior
        }
 
        return isset($this->_propiedades[$nombre]);
    }
 
    /**
     * Se llama con unset($obj->x). Borra una propiedad dinámica.
     *
     * @param string $nombre
     * @return void
     * @throws Exception Si se intenta borrar la privada.
     */
    public function __unset(string $nombre): void
    {
        if ($nombre === '_propPrivada') {
            throw new Exception("No se puede eliminar la propiedad $nombre");
        }
 
        unset($this->_propiedades[$nombre]);
    }
 
    /**
     * Lo llama foreach al recorrer el objeto. Prepara un array con todo lo que
     * se quiere mostrar y devuelve un ArrayIterator (iterador listo para arrays).
     * Las dinámicas llevan el prefijo "oi_" y al final van la pública y la privada.
     *
     * @return Traversable
     */
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
