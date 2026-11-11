<?php
include_once(dirname(__FILE__) . "/cabecera.php");
//controlador 
// en el conmtrolador solo va los conroladores lo demas va abajo, en el controlador va la esructura de la pagina, el resto va en la vista

/**
 * Clase abstracta base para todos los muebles de la tienda.
 * Las propiedades son privadas: solo se tocan con set/get.
 */

abstract class MuebleBase 
{
    const MAXIMO_MUEBLES = 20;               // debe ser > 5 (el array + pruebas + auxiliares)
    private static int $mueblesCreados = 0;  // instancias creadas hasta ahora

    private string $nombre;
    private string $fabricante;
    private string $pais;
    private int $anio;
    private string $fechaIniVenta;
    private string $fechaFinVenta;
    private float $precio;
    private Caracteristicas $caracteristicas;

    
    /**
     * Constructor. Solo el nombre es obligatorio.
     * @throws Exception si se superó el máximo o el nombre no es válido.
     */

    public function __construct(
        string $nombre,
        string $fabricante = 'FaMu:',
        string $pais = 'ESPAÑA',
        int $anio = 2020,
        string $fechaIni = '01/01/2020',
        string $fechaFin = '31/12/2040',
        float $precio = 30
    ) {
        // 1) ¿Se pueden crear más?
        if (!self::puedeCrear($quedan)) {
            throw new Exception("Se ha superado el máximo de muebles (" . self::MAXIMO_MUEBLES . ")");
        }

        // 2) Nombre obligatorio: si falla, error
        if (!$this->setNombre($nombre)) {
            throw new Exception("El nombre del mueble no es válido");
        }

        // 3) Resto de campos: si el set falla, valor por defecto.
        //    OJO al orden: anio antes que las fechas, e ini antes que fin.
        if (!$this->setFabricante($fabricante)) $this->setFabricante('FaMu:');
        if (!$this->setPais($pais))             $this->setPais('ESPAÑA');
        if (!$this->setAnio($anio))             $this->setAnio(2020);
        if (!$this->setFechaIniVenta($fechaIni)) $this->setFechaIniVenta('01/01/' . $this->anio);
        if (!$this->setFechaFinVenta($fechaFin)) $this->setFechaFinVenta('31/12/2040');
        if (!$this->setPrecio($precio))         $this->setPrecio(30);

        $this->caracteristicas = new Caracteristicas();

        // 4) Solo al final, cuando todo ha ido bien, se cuenta el mueble
        self::$mueblesCreados++;
    }

// ===================== SET =====================

    public function setNombre(mixed $nombre): bool
    {
        if (!is_string($nombre)) return false;
        $nombre = trim($nombre);
        if ($nombre === '') return false;
        try {
            if (!validaCadena($nombre, 40, '')) return false;
        } catch (TypeError $e) {
            return false;
        }
        $this->nombre = mb_strtoupper($nombre);
        return true;
    }

     public function setFabricante(mixed $fabricante): bool
    {
        if (!is_string($fabricante)) return false;
        // Siempre empieza por FaMu:
        if (!str_starts_with($fabricante, 'FaMu:')) {
            $fabricante = 'FaMu:' . $fabricante;
        }
        // Se recorta a 30 caracteres
        $fabricante = mb_substr($fabricante, 0, 30);
        try {
            if (!validaCadena($fabricante, 30, 'FaMu:')) return false;
        } catch (TypeError $e) {
            return false;
        }
        $this->fabricante = $fabricante;
        return true;
    }

    public function setPais(mixed $pais): bool 
    {
        if (!is_string($pais)) return false;
        try {
            if (!validaCadena($pais, 20, 'ESPAÑA')) return false;
        } catch (TypeError $e) {
            return false;
        }
        $this->pais = mb_strtoupper($pais);
        return true;
    }
    
    public function setAnio(mixed $anio): bool
    {
        try {
            if (!validaEntero($anio, 2020, (int) date('Y'), 2020)) return false;

        } catch (TypeError $e) {
            return false;
        $this->anio = $anio;
        return true;
        }
    }

    public function setFechaIniVenta(mixed $fecha): bool
    {
        if (!is_string($fecha)) return false;
        try {
            if (!validaFecha($fecha, '')) return false; // $fecha queda normalizada dd/mm/aaaa
        } catch (TypeError $e) {
            return false;
        }
        // No puede ser anterior al 1 de enero del año de fabricación
        if ($this->aFecha($fecha) < $this->aFecha('01/01/' . $this->anio)) return false;
        $this->fechaIniVenta = $fecha;
        return true;
    }

    public function setFechaFinVenta(mixed $fecha): bool
    {
        if (!is_string($fecha)) return false;
        try {
            if (!validaFecha($fecha, '')) return false; // $fecha queda normalizada dd/mm/aaaa
        } catch (TypeError $e) {
            return false;
        }
        // No puede ser anterior a la fecha de inicio de venta
        if ($this->aFecha($fecha) < $this->aFecha($this->fechaIniVenta)) return false;
        $this->fechaFinVenta = $fecha;
        return true;
    }

    public function setPrecio(mixed $precio): bool
    {   
        try {
            if (!validaReal($precio, 30, PHP_FLOAT_MAX, 30)) return false;
        } catch (TypeError $e) {
            return false;
        }
       $this->precio = $precio;
       return true;
    }

// ===================== GET =====================

    public function getNombre(): string {return $this->nombre;}
    public function getFabricante(): string {return $this->fabricante;}
    public function getPais(): string {return $this->pais;}
    public function getAnio(): string {return $this->anio;}
    public function getFechaIniVenta(): string {return $this->fechaIniVenta;}
    public function getFechaFinVenta(): string {return $this->fechaFinVenta;}
    public function getPrecio(): string {return $this->precio;}

// ============ SIN SOBRECARGA DINÁMICA ============
// Estos métodos solo se ejecutan si alguien intenta tocar una propiedad
// que no existe o que es privada desde fuera.

    public function __set(string $nombre, mixed $valor): void
    {
        throw new Exception("No se puede asignar la propiedad '$nombre' directamente. Usa los métodos set");
    }

    public function __get(string $nombre): mixed
    {
        throw new Exception("No se puede leer la propiedad '$nombre' directamente. Usa los métodos get");
    }

// ===================== OTROS =====================

    /** Las clases hijas lo redefinen: nombres de sus propiedades adicionales. */
    public function damePropiedadesAdicionales(): array
    {
        return [];
    }

    /**
     * Método de clase: ¿se pueden crear más muebles?
     * @param int &$numero Se rellena con los muebles que aún se pueden crear.
     */
    public static function puedeCrear(&$numero): bool
    {
        $numero = self::MAXIMO_MUEBLES - self::$mueblesCreados;
        return $numero > 0;
    }

    /** Añade pares (nombre, valor). Si sobra un argumento (número impar), se descarta. */
    public function anadirCaracteristicas(mixed ...$args): void
    {
        for ($i = 0; $i + 1 < count($args); $i += 2) {
            $nombre = (string) $args[$i];
            $this->caracteristicas->$nombre = $args[$i + 1];   // dispara __set de Caracteristicas
        }
    }

    public function getCaracteristicas(): Caracteristicas
    {
        return $this->caracteristicas;
    }

    public function __toString(): string
    {
        $texto = "MUEBLE de clase " . get_class($this)
            . " con nombre {$this->nombre}, fabricante {$this->fabricante}"
            . ", fabricado en {$this->pais} a partir del año {$this->anio}"
            . ", vendido desde {$this->fechaIniVenta} hasta {$this->fechaFinVenta}"
            . ", precio {$this->precio}";

        // Propiedades de la clase hija (funciona para cualquier hija)
        foreach ($this->damePropiedadesAdicionales() as $propiedad) {
            $get = 'get' . $propiedad;
            $texto .= ", $propiedad " . $this->$get();
        }
        return $texto . ". Características: " . $this->caracteristicas;
    }

    // Convierte "dd/mm/aaaa" en un objeto fecha para poder comparar
    private function aFecha(string $fecha): DateTime
    {
        return DateTime::createFromFormat('!d/m/Y', $fecha);
    }

}





$barra=[
    [
        "TEXTO"=> "inicio", 
        "ENLACE" => "/index.php",
        "ADICIONAL" =>""
    ],
];

//dibuja la plantilla de la vista
inicioCabecera("tienda");
cabecera();
finCabecera();
inicioCuerpo("TIENDA", $barra);
cuerpo();  //llamo a la vista
finCuerpo();
// **********************************************************

//vista
function cabecera() 
{
    ?>
    <?php
}

//vista
function cuerpo()
{
?>
<?php


}
