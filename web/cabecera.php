<?php 

//error_reporting(0);

date_default_timezone_set('Europe/Berlin');

define("RUTABASE", dirname(__FILE__));
//define("MODO_TRABAJO","produccion"); //en "produccion o en desarrollo
define("MODO_TRABAJO","desarrollo"); //en "produccion o en desarrollo

if (MODO_TRABAJO=="produccion")
    error_reporting(0);
    else 
        error_reporting(E_ALL);  

    //Aqui esta se garantiza la autocarga
spl_autoload_register(function ($clase){
   $ruta = __DIR__ . '/aplicacion/clases/curso2026/' . $clase . '.php';
     if (file_exists($ruta)) {
        require_once $ruta;
    }
});

include(RUTABASE."/aplicacion/plantilla/plantilla.php");

//creo todos los objetos que necesita mi aplicación
