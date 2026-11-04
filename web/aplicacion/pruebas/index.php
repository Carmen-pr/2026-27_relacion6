<?php
include_once(dirname(__FILE__) . "/../../cabecera.php");
//controlador

$barra = [
    [
        "TEXTO" => "inicio",
        "ENLACE" => "/index.php",
    ],
    [
        "TEXTO" => "pruebas"
    ]
];


//dibuja la plantilla de la vista
inicioCabecera("APLICACION PRIMER TRIMESTRE");
cabecera();
finCabecera();
inicioCuerpo("2DAW APLICACION", $barra);
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
        <h2 class="subtitulo-seccion">Elemento de pruebas</h2>

        <div class="lista-ejercicios">
            <a href="basicas.php">Funcionamiento básico</a>
            <a href="pasopar.php">Trabajo comunicacion controlador-vista</a>
            <a href="array.php">Arrays</a>
            <a href="cadenas.php">Cadenas</a>
            <a href="expresiones_regulares.php">Expresiones Regulares</a>
            <a href="index_funciones.php">Funciones</a>
            <a href="clases.php">Clases</a>
            <a href="./pagformulario.php">Paginas Formularios</a>
        </div>
    </div>
<?php
}
