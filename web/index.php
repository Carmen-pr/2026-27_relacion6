<?php
include_once(dirname(__FILE__) . "/cabecera.php");
//controlador 
// en el conmtrolador solo va los conroladores lo demas va abajo, en el controlador va la esructura de la pagina, el resto va en la vista


/**
 * Punto de entrada de la tienda.
 * Redirijo automáticamente a la página principal (/aplicacion/principal/index.php)
 * con una cabecera header, como pide el enunciado.
 * Aquí no incluyo cabecera.php ni imprimo nada antes del header(): si saliera
 * cualquier texto o HTML antes, PHP daría el error "headers already sent".
 * El exit evita que se siga ejecutando nada después de redirigir.
 */
header('Location: /aplicacion/principal/index.php');
exit;


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
    <!-- esto va en el head -->
<?php
}

//vista
function cuerpo()
{
?>
    <!-- Contenedor que aplica el diseño de botón moderno -->
    <div class="lista-ejercicios">
        <a href="./aplicacion/clases/curso2026/index.php">Relacion 6</a>
    </div>
<?php
}
