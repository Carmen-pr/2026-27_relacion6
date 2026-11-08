<?php
declare(strict_types=1);
 
/**
 * Flauta: clase concreta (ya se puede crear).
 *
 * - "final": nadie puede heredar de ella.
 * - "extends InstrumentoViento": hereda todo lo anterior.
 * - "implements IFabricable": se compromete a tener metodoFabricacion() y metodoReciclado().
 */
final class Flauta extends InstrumentoViento implements IFabricable
{
    /**
     * Constructor PRIVADO: fuera de la clase NO se puede hacer new Flauta().
     * Obliga a usar CrearDesdeArray(), que valida los datos antes de crearla.
     *
     * @param string $material
     * @param int $edad
     */
    private function __construct(string $material = 'madera', int $edad = 15)
    {
        parent::__construct($material, $edad);
    }
 
    /**
     * Método de fábrica: la única forma de obtener una Flauta.
     * Se llama así: Flauta::CrearDesdeArray([...]) (static = se llama con la clase, sin objeto).
     *
     * @param array $datos Puede traer 'material' (string) y 'edad' (int).
     * @return self Una Flauta nueva (self = esta misma clase).
     * @throws InvalidArgumentException Si el material no es texto o la edad no es un entero >= 0.
     */
    public static function CrearDesdeArray(array $datos): self
    {
        // ?? significa "usa el de la izquierda; si no existe o es null, el de la derecha".
        $material = $datos['material'] ?? 'madera';
        $edad = $datos['edad'] ?? 15;
 
        if (!is_string($material)) {
            throw new InvalidArgumentException('El material debe ser una cadena.');
        }
        if (!is_int($edad) || $edad < 0) {
            throw new InvalidArgumentException('La edad debe ser un entero no negativo.');
        }
 
        // Aquí sí se puede usar new, porque estamos DENTRO de la clase.
        return new self($material, $edad);
    }
 
    /**
     * Cumple el método abstracto sonido() de InstrumentoBase.
     *
     * @return string
     */
    public function sonido(): string
    {
        return 'La flauta suena: fiiii.';
    }
 
    /**
     * Requerido por IFabricable.
     *
     * @return string
     */
    public function metodoFabricacion(): string
    {
        return 'La flauta se fabrica dando forma al material y añadiendo sus llaves.';
    }
 
    /**
     * Requerido por IFabricable.
     *
     * @return string
     */
    public function metodoReciclado(): string
    {
        return 'La flauta se desmonta y se separan sus materiales para reciclarlos.';
    }
 
    /**
     * Se ejecuta automáticamente al hacer "clone $flauta", sobre la COPIA.
     * PHP copia todas las propiedades; aquí se retocan las que no deben copiarse tal cual.
     *
     * @return void
     */
    public function __clone()
    {
        $this->_edad = 0;                 // la copia es nueva: tiene 0 años
        $this->asignarOrdenCreacion();    // y recibe su propio número de orden
    }
 
    // Igual que en las clases padre: se prohíben propiedades que no existen.
 
    /**
     * @param string $nombre
     * @return never
     * @throws Exception
     */
    public function __get(string $nombre): never
    {
        throw new Exception("No existe la propiedad $nombre");
    }
 
    /**
     * @param string $nombre
     * @param mixed $valor
     * @return void
     * @throws Exception
     */
    public function __set(string $nombre, mixed $valor): void
    {
        throw new Exception("No existe la propiedad $nombre");
    }
 
    /**
     * @param string $nombre
     * @return bool
     */
    public function __isset(string $nombre): bool
    {
        return false;
    }
 
    /**
     * @param string $nombre
     * @return void
     * @throws Exception
     */
    public function __unset(string $nombre): void
    {
        throw new Exception("No existe la propiedad $nombre");
    }
}
