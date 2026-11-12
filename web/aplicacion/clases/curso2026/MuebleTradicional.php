<?php
include_once(dirname(__FILE__) . "/cabecera.php");
//controlador 
// en el conmtrolador solo va los conroladores lo demas va abajo, en el controlador va la esructura de la pagina, el resto va en la vista

/**
 * Clase MuebleTradicional.
 * Es un mueble normal (hereda de MuebleBase) que además guarda su peso y su serie.
 * La pongo final porque el enunciado dice que no se pueden crear subclases de ella.
 */
final class MuebleTradicional extends MuebleBase
{
    // Propiedades que añade esta clase. Privadas, como en MuebleBase.
    private float $peso;
    private string $serie;

    /**
     * Constructor.
     * Primero van los datos de MuebleBase (en el mismo orden que allí) y al final
     * los dos propios: peso y serie. Todos tienen valor por defecto menos el nombre.
     *
     * @throws Exception si falla el nombre o se supera el máximo de muebles
     *                   (las lanza el constructor del padre)
     */
    public function __construct(
        string $nombre,
        string $fabricante = 'FaMu:',
        string $pais = 'ESPAÑA',
        int $anio = 2020,
        string $fechaIni = '01/01/2020',
        string $fechaFin = '31/12/2040',
        float $precio = 30,
        float $peso = 15,
        string $serie = 'S01'
    ) {
        // El constructor del padre se ocupa de todo lo básico: comprobar el máximo,
        // validar el nombre, poner valores por defecto y contar el mueble.
        parent::__construct($nombre, $fabricante, $pais, $anio, $fechaIni, $fechaFin, $precio);

        // Mis propiedades las asigno con mis set; si fallan, valor por defecto
        // (15 para el peso y S01 para la serie, como pide el enunciado).
        if (!$this->setPeso($peso))   $this->setPeso(15);
        if (!$this->setSerie($serie)) $this->setSerie('S01');
    }
}










$barra = [
    [
        "TEXTO" => "inicio",
        "ENLACE" => "/index.php",
        "ADICIONAL" => ""
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
