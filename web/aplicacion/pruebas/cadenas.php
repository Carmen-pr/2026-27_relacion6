php
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
        "TEXTO" => "cadenas"
    ]
];

//dibuja la plantilla de la vista
inicioCabecera("APLICACION PRIMER TRIMESTRE");
cabecera();
finCabecera();
inicioCuerpo("Cadenas", $barra); // Cambiado el array vacío por $barra
cuerpo(); //llamo a la vista
finCuerpo();
// **********************************************************

//vista
function cabecera() {}
//vista
function cuerpo()
{

    ?>

    El ni&ntilde;o me ha escrito una carta.
    Qué&eacute &lt;&gt; bonita

<?php
}
