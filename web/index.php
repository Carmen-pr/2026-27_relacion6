<?php
include_once(dirname(__FILE__) . "/cabecera.php");
//controlador 
// en el conmtrolador solo va los conroladores lo demas va abajo, en el controlador va la esructura de la pagina, el resto va en la vista

$barra=[
    [
        "TEXTO"=> "inicio", 
        "ENLACE" => "/index.php",
        "ADICIONAL" =>""
    ],
];

//dibuja la plantilla de la vista
inicioCabecera("APLICACION PRIMER TRIMESTRE");
cabecera();
finCabecera();
inicioCuerpo("2DAW APLICACION INDEX", $barra);
cuerpo();  //llamo a la vista
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
function cuerpo()
{
?>
    <!-- Contenedor que aplica el diseño de botón moderno -->
    <div class="lista-ejercicios">
        <a href="./aplicacion/pruebas/index.php">Acceso a pruebas</a>
    </div>
<?php
}
