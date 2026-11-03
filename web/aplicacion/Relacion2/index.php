<?php
include_once(dirname(__FILE__) . "/../../cabecera.php");
//controlador

$ubicacion = [
    ["TEXTO" => "Inicio", "ENLACE" => "/index.php", "ADICIONAL" => " &gt;&gt; "],
    ["TEXTO" => "Relación 2"]
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
        <h2 class="subtitulo-seccion">Relación 2: Gestión de Cadenas</h2>

        <div class="lista-ejercicios">
            <a href="../Relacion2/ejercicio1.php">Ejercicio 1</a>
            <a href="../Relacion2/ejercicio2.php">Ejercicio 2</a>
            <a href="../Relacion2/ejercicio3.php">Ejercicio 3</a>
            <a href="../Relacion2/ejercicio4.php">Ejercicio 4</a>
            <a href="../Relacion2/ejercicio5.php">Ejercicio 5</a>
        </div>
    </div>
<?php
}