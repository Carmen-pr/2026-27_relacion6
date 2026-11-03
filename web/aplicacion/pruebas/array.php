<?php
include_once(dirname(__FILE__) . "/../../cabecera.php");
define("NUME" ,25);
const NUME1=56;

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
        "TEXTO" => "eje.basicos"
    ]
];

//dibuja la plantilla de la vista
inicioCabecera("APLICACION PRIMER TRIMESTRE");
cabecera();
finCabecera();
inicioCuerpo("pruebas basicas", $barra); // Cambiado el array vacío por $barra
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
    $miArray[3]="valor";
    $miArray[7]=1234;
   //miArray['nueva']=54;
    $miArray[]=54;


    $total=$miArray[6];


    $final=count($miArray);
    for($i=0;$i<($final);$i++){
        if (isset($miArray[$i]))
    $total=$miArray[$i]+$total;
        else
        $total++;
    }

    $miArray["nueva"]=54;
    $total1=0;
    foreach($miArray as $i=>$valor){
        $total=$miArray[$i];
        $total1+=$valor;
    }




?>
  
<?php
}
