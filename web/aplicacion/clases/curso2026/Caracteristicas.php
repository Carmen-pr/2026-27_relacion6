<?php
include_once(dirname(__FILE__) . "/cabecera.php");
//controlador 
// en el conmtrolador solo va los conroladores lo demas va abajo, en el controlador va la esructura de la pagina, el resto va en la vista

/**
 * Clase Caracteristicas.
 * Guarda las características extra de un mueble (color, plegable, baldas...).
 * No sé de antemano cuáles van a ser, así que las creo de forma dinámica
 * con los métodos mágicos __set y __get y las guardo en un array privado.
 * Implemento Iterator para poder recorrerlas con foreach en orden inverso
 * (la última que añadí sale la primera).
 */

class Caracteristicas implements Iterator
{
    // Aquí guardo las características con la forma nombre => valor.
    // Como PHP mantiene el orden de inserción, me sirve para saber cuál fue la última.
    private array $datos = [];

    // Pasa a true cuando se añade la característica ULTIMA.
    // A partir de ahí no dejo crear características nuevas.
    private bool $cerrada = false;

    // Las dos variables siguientes solo las uso para el foreach:
    // $claves tiene los nombres en orden inverso y $posicion dice por cuál voy.
    private array $claves = [];
    private int $posicion = 0;
    /**
     * Se ejecuta sola cuando hago $obj->nombre = valor con una propiedad que no existe.
     * Aquí es donde creo (o modifico) la característica.
     *
     * @throws Exception si ya se indicó ULTIMA y la característica es nueva
     */
    public function __set(string $nombre, mixed $valor): void
    {
        // Si está cerrada y la característica NO existía ya, es un intento de añadir
        // una nueva y tengo que dar error. Compruebo que no exista porque, si no,
        // tampoco podría modificar las que ya había, y el enunciado dice que sí se puede.
        if ($this->cerrada && !array_key_exists($nombre, $this->datos)) {
            throw new Exception("No se puede añadir '$nombre': ya se indicó la característica ULTIMA");
        }


        $this->datos[$nombre] = $valor;


        // Si lo que acabo de guardar es ULTIMA, cierro la clase para nuevas características.
        if ($nombre === 'ULTIMA') {
            $this->cerrada = true;
        }
    }

    /**
     * Se ejecuta sola cuando hago $obj->nombre y esa propiedad no existe.
     * Devuelvo el valor de la característica.
     * Elijo lanzar excepción si no existe (en vez de devolver null) para ser
     * coherente con el __set, que también da error en los usos incorrectos.
     *
     * @throws Exception si la característica no existe
     */
    public function __get(string $nombre): mixed
    {
        if (!array_key_exists($nombre, $this->datos)) {
            throw new Exception("La característica '$nombre' no existe");
        }
        return $this->datos[$nombre];
    }


    /**
     * Se ejecuta con isset($obj->nombre).
     * Me sirve para preguntar si una característica existe SIN provocar la
     * excepción del __get.
     */
    public function __isset(string $nombre): bool
    {
        return array_key_exists($nombre, $this->datos);
    }


    // ===================== MÉTODOS DE Iterator =====================
    // foreach llama a estos cinco métodos en este orden:
    // rewind() una vez, y luego valid() -> current() -> key() -> next() en cada vuelta.


    /**
     * Prepara el recorrido: saco los nombres de las características, les doy la
     * vuelta para que salgan en orden inverso y pongo el puntero al principio.
     */
    public function rewind(): void
    {
        $this->claves = array_reverse(array_keys($this->datos));
        $this->posicion = 0;
    }


    /**
     * Comprueba si todavía quedan elementos por recorrer.
     * Si la posición ya se ha salido de la lista, el foreach termina.
     */
    public function valid(): bool
    {
        return $this->posicion < count($this->claves);
    }


    /**
     * Devuelve el valor del elemento en el que estoy.
     * Busco el nombre en $claves y con él saco el valor de $datos.
     */
    public function current(): mixed
    {
        return $this->datos[$this->claves[$this->posicion]];
    }


    /**
     * Devuelve el nombre de la característica en la que estoy
     * (será el nombrede"foreach(c as $nombre => $valor)").
     */
    public function key(): mixed
    {
        return $this->claves[$this->posicion];
    }


    /**
     * Avanza el puntero al siguiente elemento.
     */
    public function next(): void
    {
        $this->posicion++;
    }


    /**
     * Convierte las características en texto con la forma
     * car1:valor1;car2:valor2;...
     * Las muestro en el orden en que las añadí.
     */
    public function __toString(): string
    {
        $partes = [];
        foreach ($this->datos as $nombre => $valor) {
            // Un true se convertiría en 1 y un false en vacío, así que
            // los paso a texto para que se vean bien.
            if (is_bool($valor)) {
                $valor = $valor ? 'true' : 'false';
            }
            $partes[] = "$nombre:$valor";
        }
        // implode une todos los trozos separándolos con ;
        return implode(';', $partes);
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
