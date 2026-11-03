<?php
include_once(dirname(__FILE__) . "/../../cabecera.php");
//controlador

$ubicacion = [
    ["TEXTO" => "Inicio", "ENLACE" => "/index.php", "ADICIONAL" => " &gt;&gt; "],
    ["TEXTO" => "Relación 1"]
];


//dibuja la plantilla de la vista
inicioCabecera("APLICACION PRIMER TRIMESTRE");
cabecera();
finCabecera();
inicioCuerpo("2DAW APLICACION", $ubicacion);
cuerpo(); //llamo a la vista
finCuerpo();
// **********************************************************

//vista
function cabecera() {}

//vista
function cuerpo()
{
?>
    <div id="documento">
        <h2 class="subtitulo-seccion">Relación 1: arrays, fechas, libreria math</h2>

        <div class="lista-ejercicios">
            <a href="../Relacion1/ejercicio1.php">Ejercicio 1</a>
            <a href="../Relacion1/ejercicio2.php">Ejercicio 2</a>
            <a href="../Relacion1/ejercicio3.php">Ejercicio 3</a>
            <a href="../Relacion1/ejercicio4.php">Ejercicio 4</a>
            <a href="../Relacion1/ejercicio5.php">Ejercicio 5</a>   
            <a href="../Relacion1/ejercicio6.php">Ejercicio 6</a>
            <a href="../Relacion1/ejercicio7.php">Ejercicio 7</a>
        </div>
    </div>
<?php
}
