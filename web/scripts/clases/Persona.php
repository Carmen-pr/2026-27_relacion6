<?php
declare(strict_types=1);

class Persona
{
    // Los valores iniciales sirven al constructor privado sin parámetros.
    private string $_nombre = 'prueba';
    private string $_fecha_nacimiento = '01/01/2000';
    private string $_domicilio = 'carrera 12';
    private string $_localidad = 'antequera';
    private EstadoCivil $_estado = EstadoCivil::Soltero;

    private function __construct()
    {
    }

    // La fábrica crea la instancia y carga sus datos mediante setters validados.
    public static function registrarPersona(
        string $nombre,
        string $fechaNacimiento,
        string $domicilio,
        string $localidad,
        EstadoCivil $estado
    ): self {
        $persona = new self();
        $persona->setNombre($nombre);
        $persona->setFechaNacimiento($fechaNacimiento);
        $persona->setDomicilio($domicilio);
        $persona->setLocalidad($localidad);
        $persona->setEstado($estado);

        return $persona;
    }

    // Cada propiedad tiene su getter y su setter tipado.
    public function getNombre(): string
    {
        return $this->_nombre;
    }

    public function setNombre(string $nombre): void
    {
        $this->_nombre = $this->validarTexto($nombre, 'nombre');
    }

    // La fecha se almacena como texto dd/mm/aaaa tras validar que sea real.
    public function getFechaNacimiento(): string
    {
        return $this->_fecha_nacimiento;
    }

    public function setFechaNacimiento(string $fechaNacimiento): void
    {
        $fechaNacimiento = trim($fechaNacimiento);
        $fecha = DateTimeImmutable::createFromFormat('!d/m/Y', $fechaNacimiento);
        $errores = DateTimeImmutable::getLastErrors();

        if (
            $fecha === false
            || ($errores !== false && ($errores['warning_count'] > 0 || $errores['error_count'] > 0))
            || $fecha->format('d/m/Y') !== $fechaNacimiento
        ) {
            throw new InvalidArgumentException('La fecha debe ser válida y tener el formato dd/mm/aaaa.');
        }

        $this->_fecha_nacimiento = $fechaNacimiento;
    }

    // Domicilio y localidad son textos obligatorios.
    public function getDomicilio(): string
    {
        return $this->_domicilio;
    }

    public function setDomicilio(string $domicilio): void
    {
        $this->_domicilio = $this->validarTexto($domicilio, 'domicilio');
    }

    public function getLocalidad(): string
    {
        return $this->_localidad;
    }

    public function setLocalidad(string $localidad): void
    {
        $this->_localidad = $this->validarTexto($localidad, 'localidad');
    }

    public function getEstado(): EstadoCivil
    {
        return $this->_estado;
    }

    // El tipo EstadoCivil impide asignar valores que no pertenezcan al enum.
    public function setEstado(EstadoCivil $estado): void
    {
        $this->_estado = $estado;
    }

    // Representación legible de la persona según el formato solicitado.
    public function toString(): string
    {
        return "{$this->_nombre} es una persona {$this->_estado->descripcion()} "
            . "nacida en {$this->_fecha_nacimiento} y que vive en {$this->_localidad}";
    }

    // Solo se permite acceder a las propiedades a través de los métodos definidos.
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

    // Comprueba textos con mb_strlen para contar correctamente caracteres UTF-8.
    private function validarTexto(string $valor, string $campo): string
    {
        $valor = trim($valor);
        if (mb_strlen($valor) === 0) {
            throw new InvalidArgumentException("El campo $campo no puede estar vacío.");
        }

        return $valor;
    }
}
