<?php
include_once(dirname(__FILE__) . "/../../cabecera.php");
//controlador 
// en el controlador solo va los conroladores lo demas va abajo, en el controlador va la esructura de la pagina, el resto va en la vista

//datos basicos
$nombre = "Carmen"; 
$edad = 20;

$basicos=[
    "nombre"=>$nombre,
    "edad"=>$edad
];


//relleno otras
$otras=rellenarOtras();


$barra=[
     [
       "TEXTO"=> "inicio",
       "ENLACE" =>"/index.php", // Cambiado LINK por ENLACE
     ],
      [ 
       "TEXTO"=> "pruebas",   
       "ENLACE" =>"./index.php", // Cambiado LINK por ENLACE y ruta relativa
     ], 
       [ 
       "TEXTO"=> "pasopar"  
     ] 
];

//dibuja la plantilla de la vista
inicioCabecera("APLICACION PRIMER TRIMESTRE");
cabecera();
finCabecera();
inicioCuerpo("2DAW APLICACION INDEX" , $barra);
cuerpo($basicos, $otras);  //llamo a la vista
finCuerpo();
// **********************************************************

//vista
function cabecera() 
{
    ?>
    <!-- esto va en el head -->  
     <?php


}

//vista
function cuerpo($bas, $ot)
{
?>
    <br><br>

<?php

    echo "Mi nombre es: {$bas["nombre"]} de {$bas["edad"]} años".PHP_EOL;
    echo "Con otros datos: {$ot}";

}


function rellenarOtras()
{
 return "de 2 DAW";
}