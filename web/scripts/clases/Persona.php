<?php
declare(strict_types=1);
 
/**
 * Representa a una persona. Todos sus datos son privados y se tocan
 * solo a través de getters y setters, que validan lo que entra.
 * El constructor es privado: se crea con Persona::registrarPersona().
 */
class Persona
{
    // Valores por defecto: sin ellos, PHP daría error al leer una propiedad
    // tipada que todavía no se ha asignado.
    private string $_nombre = 'prueba';
    private string $_fecha_nacimiento = '01/01/2000';
    private string $_domicilio = 'carrera 12';
    private string $_localidad = 'antequera';
    private EstadoCivil $_estado = EstadoCivil::Soltero; // el tipo es el enum, no un número
 
    /**
     * Constructor privado y sin parámetros: solo la propia clase puede crear personas.
     * Rellena las propiedades con los valores por defecto del enunciado.
     */
    private function __construct()
    {
        $this->_nombre = 'prueba';
        $this->_fecha_nacimiento = '01/01/2000';
        $this->_domicilio = 'carrera 12';
        $this->_localidad = 'antequera';
        $this->_estado = EstadoCivil::Soltero;
    }
 
    /**
     * Método de fábrica: crea la persona y rellena sus datos usando los setters,
     * así todos los datos pasan por validación.
     *
     * @param string $nombre
     * @param string $fechaNacimiento Formato dd/mm/aaaa.
     * @param string $domicilio
     * @param string $localidad
     * @param EstadoCivil $estado Un caso del enum.
     * @return self
     * @throws InvalidArgumentException Si algún dato no es válido.
     */
    public static function registrarPersona(
        string $nombre,
        string $fechaNacimiento,
        string $domicilio,
        string $localidad,
        EstadoCivil $estado
    ): self {
        $persona = new self(); // se puede usar new porque estamos dentro de la clase
        $persona->setNombre($nombre);
        $persona->setFechaNacimiento($fechaNacimiento);
        $persona->setDomicilio($domicilio);
        $persona->setLocalidad($localidad);
        $persona->setEstado($estado);
 
        return $persona;
    }
 
    /**
     * @return string
     */
    public function getNombre(): string
    {
        return $this->_nombre;
    }
 
    /**
     * @param string $nombre
     * @return void
     * @throws InvalidArgumentException Si está vacío.
     */
    public function setNombre(string $nombre): void
    {
        $this->_nombre = $this->validarTexto($nombre, 'nombre');
    }
 
    /**
     * @return string Fecha como texto dd/mm/aaaa.
     */
    public function getFechaNacimiento(): string
    {
        return $this->_fecha_nacimiento;
    }
 
    /**
     * Valida que la fecha exista de verdad y tenga formato dd/mm/aaaa.
     *
     * @param string $fechaNacimiento
     * @return void
     * @throws InvalidArgumentException Si la fecha no es válida (ej. 31/02/2000).
     */
    public function setFechaNacimiento(string $fechaNacimiento): void
    {
        $fechaNacimiento = trim($fechaNacimiento);
 
        // Intenta convertir el texto a fecha con el formato d/m/Y.
        // El "!" hace que lo que no se indica (hora...) quede a cero y no a la hora actual.
        $fecha = DateTimeImmutable::createFromFormat('!d/m/Y', $fechaNacimiento);
 
        // PHP no falla con 31/02: lo "corrige" a marzo y deja un aviso (warning).
        // getLastErrors() permite ver esos avisos.
        $errores = DateTimeImmutable::getLastErrors();
 
        if (
            $fecha === false                                   // no se pudo convertir
            || ($errores !== false && ($errores['warning_count'] > 0 || $errores['error_count'] > 0))
            || $fecha->format('d/m/Y') !== $fechaNacimiento    // la fecha resultante no coincide con la escrita
        ) {
            throw new InvalidArgumentException('La fecha debe ser válida y tener el formato dd/mm/aaaa.');
        }
 
        $this->_fecha_nacimiento = $fechaNacimiento;
    }
 
    /**
     * @return string
     */
    public function getDomicilio(): string
    {
        return $this->_domicilio;
    }
 
    /**
     * @param string $domicilio
     * @return void
     * @throws InvalidArgumentException Si está vacío.
     */
    public function setDomicilio(string $domicilio): void
    {
        $this->_domicilio = $this->validarTexto($domicilio, 'domicilio');
    }
 
    /**
     * @return string
     */
    public function getLocalidad(): string
    {
        return $this->_localidad;
    }
 
    /**
     * @param string $localidad
     * @return void
     * @throws InvalidArgumentException Si está vacía.
     */
    public function setLocalidad(string $localidad): void
    {
        $this->_localidad = $this->validarTexto($localidad, 'localidad');
    }
 
    /**
     * @return EstadoCivil
     */
    public function getEstado(): EstadoCivil
    {
        return $this->_estado;
    }
 
    /**
     * Al pedir el tipo EstadoCivil, PHP rechaza cualquier valor que no sea del enum.
     *
     * @param EstadoCivil $estado
     * @return void
     */
    public function setEstado(EstadoCivil $estado): void
    {
        $this->_estado = $estado;
    }
 
    /**
     * Texto con los datos de la persona.
     *
     * @return string
     */
    public function toString(): string
    {
        return "{$this->_nombre} es una persona {$this->_estado->descripcion()} "
            . "nacida en {$this->_fecha_nacimiento} y que vive en {$this->_localidad}";
    }
 
    // Métodos mágicos: prohíben usar propiedades que no existen (ver InstrumentoBase).
 
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
 
    /**
     * Comprueba que un texto no esté vacío y lo devuelve sin espacios sobrantes.
     * Es private: solo la usa la propia clase. mb_strlen cuenta bien tildes y ñ.
     *
     * @param string $valor Texto a comprobar.
     * @param string $campo Nombre del campo, para el mensaje de error.
     * @return string El texto limpio.
     * @throws InvalidArgumentException Si queda vacío.
     */
    private function validarTexto(string $valor, string $campo): string
    {
        $valor = trim($valor);
        if (mb_strlen($valor) === 0) {
            throw new InvalidArgumentException("El campo $campo no puede estar vacío.");
        }
 
        return $valor;
    }
}
