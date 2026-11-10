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
