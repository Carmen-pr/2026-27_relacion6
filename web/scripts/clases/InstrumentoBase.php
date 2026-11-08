<?php
// Activa el tipado estricto: si una función pide un int y le pasas "5" (texto),
// da error en vez de convertirlo solo. Obliga a ser más preciso.
declare(strict_types=1);
 
/**
 * Clase base de todos los instrumentos.
 *
 * Es ABSTRACTA: no se puede hacer new InstrumentoBase(). Sirve de molde
 * para otras clases (como InstrumentoViento) que heredan de ella y
 * rellenan lo que aquí queda sin implementar (sonido y afinar).
 */
abstract class InstrumentoBase
{
    // private: solo se puede usar dentro de ESTA clase. Ni las hijas lo ven.
    // Por eso hay getDescripcion/setDescripcion para tocarla desde fuera.
    private string $_descripcion;
 
    // protected: se ve dentro de la clase y de sus hijas, pero no desde fuera.
    // Como no hay setEdad, desde fuera la edad solo se puede LEER (getEdad).
    protected int $_edad = 10;
 
    // Número que tiene cada instrumento según el orden en que se creó.
    protected int $_ordenCreacion = 0;
 
    // static: esta variable es UNA sola para toda la clase, no una por objeto.
    // Cuenta cuántos instrumentos se han creado en total.
    protected static int $_totalInstrumentos = 0;
 
    /**
     * Constructor: se ejecuta solo al hacer new. Deja el objeto listo.
     *
     * @param string $descripcion Texto que describe el instrumento (no puede estar vacío).
     * @param int $edad Años del instrumento (por defecto 10, no puede ser negativa).
     * @throws InvalidArgumentException Si la descripción está vacía o la edad es negativa.
     */
    public function __construct(string $descripcion, int $edad = 10)
    {
        // Reutilizo el setter para no repetir la validación de la descripción.
        $this->setDescripcion($descripcion);
 
        if ($edad < 0) {
            throw new InvalidArgumentException('La edad no puede ser negativa.');
        }
 
        // $this = "este objeto". Guardo la edad en SU propiedad.
        $this->_edad = $edad;
        $this->asignarOrdenCreacion();
    }
 
    /**
     * Getter: devuelve la descripción (así se lee una propiedad private).
     *
     * @return string
     */
    public function getDescripcion(): string
    {
        return $this->_descripcion;
    }
 
    /**
     * Setter: cambia la descripción comprobando antes que sea válida.
     * Esa es la ventaja de getter/setter frente a una propiedad pública.
     *
     * @param string $descripcion Nueva descripción.
     * @return void
     * @throws InvalidArgumentException Si queda vacía (también con solo espacios).
     */
    public function setDescripcion(string $descripcion): void
    {
        // trim quita espacios al principio y al final; mb_strlen cuenta caracteres
        // (con tildes y ñ también bien).
        if (mb_strlen(trim($descripcion)) === 0) {
            throw new InvalidArgumentException('La descripción no puede estar vacía.');
        }
 
        $this->_descripcion = trim($descripcion);
    }
 
    /**
     * Devuelve la edad. Solo lectura desde fuera.
     *
     * @return int
     */
    public function getEdad(): int
    {
        return $this->_edad;
    }
 
    /**
     * Método abstracto: aquí NO hay código, solo la firma.
     * Cada clase hija está obligada a escribirlo.
     *
     * @return string
     */
    abstract public function sonido(): string;
 
    /**
     * Método abstracto: cada hija decide cómo se afina.
     *
     * @return string
     */
    abstract public function afinar(): string;
 
    /**
     * Sube un año la edad de ESTE instrumento.
     *
     * @return void
     */
    public function envejecer(): void
    {
        $this->_edad++; // ++ suma 1
    }
 
    /**
     * Se llama sola cuando el objeto se usa como texto (echo $obj o (string) $obj).
     *
     * @return string
     */
    public function __toString(): string
    {
        // self::$variable accede a una propiedad static de la clase.
        // get_class($this) devuelve el nombre real de la clase del objeto (ej. Flauta).
        return "instrumento con descripción {$this->_descripcion}, instancia {$this->_ordenCreacion}"
            . ' de un total de ' . self::$_totalInstrumentos
            . ". Tiene {$this->_edad} años. La clase es " . get_class($this);
    }
 
    // Los cuatro métodos mágicos siguientes se disparan cuando alguien usa una
    // propiedad que NO existe. Aquí se usan para prohibirlo: sin ellos, PHP
    // crearía la propiedad nueva sin avisar.
 
    /**
     * Se llama al LEER una propiedad inexistente ($obj->x).
     *
     * @param string $nombre Nombre que se intentó leer.
     * @return never Siempre lanza excepción (never = nunca termina con normalidad).
     * @throws Exception
     */
    public function __get(string $nombre): never
    {
        throw new Exception("No existe la propiedad $nombre");
    }
 
    /**
     * Se llama al ASIGNAR una propiedad inexistente ($obj->x = 1).
     *
     * @param string $nombre Nombre de la propiedad.
     * @param mixed $valor Valor que se intentó asignar.
     * @return void
     * @throws Exception
     */
    public function __set(string $nombre, mixed $valor): void
    {
        throw new Exception("No existe la propiedad $nombre");
    }
 
    /**
     * Se llama con isset($obj->x) sobre una propiedad inexistente.
     *
     * @param string $nombre
     * @return bool Siempre false: "no existe".
     */
    public function __isset(string $nombre): bool
    {
        return false;
    }
 
    /**
     * Se llama con unset($obj->x) sobre una propiedad inexistente.
     *
     * @param string $nombre
     * @return void
     * @throws Exception
     */
    public function __unset(string $nombre): void
    {
        throw new Exception("No existe la propiedad $nombre");
    }
 
    /**
     * Da a este instrumento su número de orden y suma uno al total.
     * Es protected para que Flauta pueda llamarlo también al clonar.
     *
     * @return void
     */
    protected function asignarOrdenCreacion(): void
    {
        self::$_totalInstrumentos++;
        $this->_ordenCreacion = self::$_totalInstrumentos;
    }
}
 