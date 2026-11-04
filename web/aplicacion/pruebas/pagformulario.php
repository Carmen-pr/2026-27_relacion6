<?php
include_once(dirname(__FILE__) . "/../../cabecera.php");
//controlador

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
       "TEXTO"=> "clases"  
    ] 
];


$res= filter_var(0x123, FILTER_VALIDATE_INT);

$o=[
    "options"=> [
        "min_range"=> 100,
        "max_range"=> 200,
        "default"=> 100
    ],
    "flags"=> FILTER_FLAG_ALLOW_HEX
];

$res= filter_var(0x81, FILTER_VALIDATE_INT, $o);


//inicializaciones
$datos = [
    "nombre" => "vic",
    "precio" => 38
];
$errores = [];


if (isset($_POST['cr'])) {
    //verificacion nombre

}

//dibuja la plantilla de la vista
inicioCabecera("APLICACION PRIMER TRIMESTRE");
cabecera();
finCabecera();
inicioCuerpo("2DAW APLICACION", $barra);
cuerpo($datos, $errores); //llamo a la vista
finCuerpo();
// **********************************************************

//vista
function cabecera() {}
//vista
function cuerpo(array $datos, array $errores)
{
?>
    <br>
    <br>
    <br>
<?php
    formulario($datos, $errores);
}

function formulario(array $datos, array $errores)
{
    if ($errores) { //mostrar los errores
        echo "<div class='error'>";
        foreach ($errores as $clave => $valor) {
            foreach ($valor as $error)
                echo "$clave => $error<br>" . PHP_EOL;
        }
        echo "</div>";
    }

    /**
     * DIFERENCIA ENTRE GET Y POST
     * 
     * GET:
     * - Recupera datos del servidor (Solo lectura).
     * - Envía parámetros visibles en la URL.
     * - Capacidad limitada y no apto para datos sensibles.
     * - Permite almacenamiento en caché y marcadores.
     * 
     * POST:
     * - Envía/Modifica datos en el servidor (Creación/Edición).
     * - Envía datos ocultos en el cuerpo (Body) de la petición.
     * - Sin límite de tamaño y permite archivos binarios.
     */

?>
    <form action="" method="post">    <!-- get o post  -->
        <label for="nombre">Nombre: </label>
        <input type="text" name="nombre" id="nombre"
            value="<?= $datos["nombre"]; ?>" size=21
            maxlength="20"><br>
        <label for="precio">Precio: </label>
        <input type="text" name="precio" id="precio"
            value="<?= $datos["precio"]; ?>" size=4
            maxlength="3"><br>
        <input type="submit" class="boton" value="Crear" name="cr">
    </form>
<?php
}
