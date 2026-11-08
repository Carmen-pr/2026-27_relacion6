<?php
declare(strict_types=1);
 
/**
 * Instrumento de viento. HEREDA de InstrumentoBase ("extends"): ya tiene
 * descripción, edad, envejecer()... sin volver a escribirlos.
 * Sigue siendo abstracta porque todavía falta sonido() (lo escribe Flauta).
 */
abstract class InstrumentoViento extends InstrumentoBase
{
    // Solo visible para esta clase y sus hijas. No hay getter ni setter a propósito.
    protected string $_material = 'madera';
 
    /**
     * @param string $material Material del instrumento (no puede estar vacío).
     * @param int $edad Años del instrumento.
     * @throws InvalidArgumentException Si el material está vacío.
     */
    public function __construct(string $material = 'madera', int $edad = 15)
    {
        if (mb_strlen(trim($material)) === 0) {
            throw new InvalidArgumentException('El material no puede estar vacío.');
        }
 
        // parent:: llama a un método de la clase padre. Aquí ejecuta el constructor
        // de InstrumentoBase, que guarda la descripción y la edad.
        parent::__construct('instrumento de viento', $edad);
        $this->_material = trim($material);
    }
 
    /**
     * Implementa afinar() para TODOS los instrumentos de viento.
     * "final" impide que las clases hijas lo vuelvan a escribir.
     *
     * @return string
     */
    final public function afinar(): string
    {
        return 'El instrumento de viento está afinado.';
    }
 
    /**
     * Amplía el texto del padre: coge el suyo (parent::__toString()) y añade el material.
     *
     * @return string
     */
    public function __toString(): string
    {
        return parent::__toString() . " Instrumento de material {$this->_material}";
    }
 
    // Se repiten los métodos mágicos para dejar claro que esta clase también
    // prohíbe propiedades que no existen (ver comentarios en InstrumentoBase).
 
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
