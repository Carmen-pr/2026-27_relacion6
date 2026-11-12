<?php
include_once(dirname(__FILE__) . "/cabecera.php");
//controlador 
// en el conmtrolador solo va los conroladores lo demas va abajo, en el controlador va la esructura de la pagina, el resto va en la vista


/**
 * Clase MuebleReciclado.
 * Es un mueble normal (hereda de MuebleBase) que además guarda el porcentaje
 * de material reciclado que tiene.
 * La pongo final porque el enunciado dice que no se pueden crear subclases de ella.
 */
final class MuebleReciclado extends MuebleBase
{
    // Propiedad que añade esta clase. La pongo privada, igual que en MuebleBase,
    // para que solo se pueda tocar con el set y el get.
    private int $porcentajeReciclado;

    /**
     * Constructor.
     * Recibe primero los datos de MuebleBase (en el mismo orden que allí)
     * y al final el porcentaje, que es lo único propio de esta clase.
     * Todos tienen valor por defecto menos el nombre, que es obligatorio.
     *
     * @throws Exception si falla el nombre o se supera el máximo de muebles
     *                   (esas excepciones las lanza el constructor del padre).
     */
    public function __construct(
        string $nombre,
        string $fabricante = 'FaMu:',
        string $pais = 'ESPAÑA',
        int $anio = 2020,
        string $fechaIni = '01/01/2020',
        string $fechaFin = '31/12/2040',
        float $precio = 30,
        int $porcentaje = 10
    ) {
        // Lo primero es llamar al constructor del padre, que se encarga de
        // todos los datos básicos (comprobar el máximo, validar el nombre,
        // poner valores por defecto y contar el mueble).
        parent::__construct($nombre, $fabricante, $pais, $anio, $fechaIni, $fechaFin, $precio);

        // Después asigno lo mío llamando al set, como pide el enunciado.
        // Si el set devuelve false (porcentaje no válido), pongo el valor por defecto, que es 10.
        if (!$this->setPorcentajeReciclado($porcentaje)) {
            $this->setPorcentajeReciclado(10);
        }
    }

    /**
     * Set del porcentaje reciclado.
     * Tiene que ser un entero entre 0 y 100, ambos incluidos.
     * Si es correcto lo guardo y devuelvo true; si no, devuelvo false
     * y no toco la propiedad.
     *
     * @param mixed $porcentaje Valor que quiero asignar (pongo mixed para que
     *                          no explote si me llega algo que no es un número)
     * @return bool true si se ha asignado, false si no es válido
     */
    public function setPorcentajeReciclado(mixed $porcentaje): bool
    {
        // Uso mi función de validaciones. Como es estricta con los tipos,
        // la meto en un try/catch: si salta un TypeError, el valor no vale
        // y devuelvo false en lugar de dejar que la página se rompa.
        try {
            // El último parámetro es el valor por defecto, pero aquí no lo
            // aprovecho: si falla simplemente salgo con false.
            if (!validaEntero($porcentaje, 0, 100, 10)) {
                return false;
            }
        } catch (TypeError $e) {
            return false;
        }

        // Solo llego aquí si el valor es correcto, así que ya puedo guardarlo.
        $this->porcentajeReciclado = $porcentaje;
        return true;
    }

    /**
     * Get del porcentaje reciclado: solo devuelve el valor, sin validar nada.
     */
    public function getPorcentajeReciclado(): int
    {
        return $this->porcentajeReciclado;
    }

    /**
     * Redefino el método del padre (allí devolvía un array vacío).
     * Devuelvo los nombres de las propiedades que esta clase añade.
     * Los nombres tienen que coincidir con los getters (getPorcentajeReciclado),
     * porque luego los uso en el __toString y en index.php para llamar a
     * 'get' + nombre y así no tengo que hacer un if por cada tipo de mueble.
     *
     * @return array nombres de las propiedades adicionales
     */
    public function damePropiedadesAdicionales(): array
    {
        return ['PorcentajeReciclado'];
    }

    // No redefino __toString: el del padre ya recorre damePropiedadesAdicionales()
    // y añade el porcentaje a la cadena, así no repito código.
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
