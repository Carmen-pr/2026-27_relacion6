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
