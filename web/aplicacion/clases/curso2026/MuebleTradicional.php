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

    /**
     * Set del peso: real entre 15 y 300, ambos incluidos.
     * Si es válido lo guardo y devuelvo true; si no, devuelvo false y no toco nada.
     */
    public function setPeso(mixed $peso): bool
    {
        // Mi función de validaciones es estricta con los tipos, así que la meto
        // en un try/catch: si salta un TypeError, el valor no sirve y devuelvo false.
        try {
            if (!validaReal($peso, 15, 300, 15)) return false;
        } catch (TypeError $e) {
            return false;
        }

        // Solo llego aquí si el peso es correcto
        $this->peso = $peso;
        return true;
    }

    /**
     * Set de la serie: cadena, no puede estar vacía.
     * El enunciado no da más restricciones, así que solo compruebo eso.
     * Uso trim para que una serie de solo espacios cuente como vacía.
     */
    public function setSerie(mixed $serie): bool
    {
        if (!is_string($serie) || trim($serie) === '') return false;

        $this->serie = $serie;
        return true;
    }

    // Getters: solo devuelven el valor, sin validar nada.
    public function getPeso(): float
    {
        return $this->peso;
    }
    public function getSerie(): string
    {
        return $this->serie;
    }

    /**
     * Redefino el método del padre (allí devolvía un array vacío).
     * Devuelvo los nombres de mis propiedades extra. Tienen que coincidir con mis
     * getters (getPeso, getSerie) porque el __toString del padre y index.php
     * construyen la llamada como 'get' + nombre.
     */
    public function damePropiedadesAdicionales(): array
    {
        return ['Peso', 'Serie'];
    }

    // No redefino __toString: el del padre ya recorre damePropiedadesAdicionales()
    // y añade peso y serie a la cadena, así no repito código.
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
