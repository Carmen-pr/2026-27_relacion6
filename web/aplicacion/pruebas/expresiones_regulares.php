<?php
include_once(dirname(__FILE__) . "/../../cabecera.php");
//controlador

$barra = [
    [
        "TEXTO" => "inicio",
        "ENLACE" => "/index.php",
    ],
    [
        "TEXTO" => "pruebas",
        "ENLACE" => "./index.php" // Permite volver a la carpeta pruebas
    ],
    [
        "TEXTO" => "expresiones regulares"
    ]
];

$cadena='tienes 258 34 años';

//Buscar años
$exp='/años/';
$resultado=[];

preg_match($exp,$cadena,$resultado,PREG_OFFSET_CAPTURE);

//buscar numero
$exp='/012345789/';
$exp='/[0-9]+/';
$exp='/\d+/';
$exp='/\d{1,}/';
$exp='/[[:digit:]]+/';

preg_match($exp,$cadena,$resultado,PREG_OFFSET_CAPTURE);

//busco un numero con dos cifras
$exp='/\d{2}/';
preg_match($exp,$cadena,$resultado,PREG_OFFSET_CAPTURE);


//buscar un numero real
$cadena='tienes 54 Años 258.34 años';
$exp="/\d+\.\d+/";

preg_match($exp,$cadena,$resultado,PREG_OFFSET_CAPTURE);

//Modificadores
//Distingo entre mayusculas y minusculas
$exp='/años/';
$resultado=[];

//no distingo entre mayusculas y minusculas
$exp='/años/i';
$resultado=[];

preg_match($exp,$cadena,$resultado,PREG_OFFSET_CAPTURE);

//subpatrones
//quiero encontrar en un numero real en la parte decimal.
$cadena='tienes 54 Años 258.34 años';
$exp="/\d+\.(\d+)/";

preg_match($exp,$cadena,$resultado,PREG_OFFSET_CAPTURE);

//cuantificador, como hacer perezosos
$cadena="/* primer comentario */ no comentado /* segundo comentario */”y el patrón /\*.*\*/";
$exp="/^[\]*.*?[\]/";

preg_match($exp,$cadena,$resultado,PREG_OFFSET_CAPTURE);

//dibuja la plantilla de la vista
inicioCabecera("APLICACION PRIMER TRIMESTRE");
cabecera();
finCabecera();
inicioCuerpo("Expresiones Regulares", $barra); // Cambiado para incluir el array de navegación
cuerpo(); //llamo a la vista
finCuerpo();
// **********************************************************

//vista
function cabecera() {}
//vista
function cuerpo()
{
?>
<?php 
    
}
