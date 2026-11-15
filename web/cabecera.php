<?php

/**
 * Cabecera común de la tienda.
 * Todas las páginas la incluyen lo primero. Aquí dejo preparado todo lo que
 * necesitan: constantes, modo de trabajo, librería de validaciones, autocarga
 * de clases y el array con los muebles. Al final cargo la plantilla (HTML).
 */

//error_reporting(0);

date_default_timezone_set('Europe/Berlin');

define("RUTABASE", dirname(__FILE__));
//define("MODO_TRABAJO","produccion"); //en "produccion o en desarrollo
define("MODO_TRABAJO", "desarrollo"); //en "produccion o en desarrollo

if (MODO_TRABAJO == "produccion")
    error_reporting(0);
else
    error_reporting(E_ALL);

// Mi librería de validaciones son funciones sueltas, no clases, así que la
// autocarga no las carga: las incluyo yo a mano. Tiene que ir antes de crear
// ningún mueble, porque los set de las clases llaman a validaCadena, validaEntero...
require_once RUTABASE . "/scripts/librerias/validacion.php";

//Aqui se garantiza la autocarga
// Cuando uso una clase que todavía no está cargada, PHP llama a esta función con
// su nombre y yo busco el fichero con ese mismo nombre en la carpeta de las clases.
spl_autoload_register(function ($clase) {
    $ruta = __DIR__ . '/aplicacion/clases/curso2026/' . $clase . '.php';
    if (file_exists($ruta)) {
        require_once $ruta;
    }
});

//creo todos los objetos que necesita mi aplicación
// Como todavía no sé guardar datos de forma permanente, defino aquí, en un único
// punto, un array con 5 muebles. Al incluir esta cabecera en todas las páginas,
// el array está disponible en todas. Los índices empiezan en 1, como pide el
// enunciado, y mezclo los dos tipos de mueble.
// Orden de los datos en cada constructor:
//   nombre, fabricante, país, año, fecha inicio venta, fecha fin venta, precio, y después
//   los propios del tipo: porcentaje (MuebleReciclado) o peso y serie (MuebleTradicional).
$muebles = [
    1 => new MuebleReciclado('silla verde', 'Sillas SA', 'ESPAÑA', 2021, '01/03/2021', '31/12/2035', 80, 60),
    2 => new MuebleTradicional('mesa roble', 'Maderas del Sur', 'PORTUGAL', 2022, '01/01/2022', '31/12/2040', 250, 40.5, 'S02'),
    3 => new MuebleReciclado('estante pino', 'Pinos SL', 'ESPAÑA', 2023, '01/06/2023', '31/12/2038', 120, 30),
    4 => new MuebleTradicional('armario nogal', 'Nogales', 'FRANCIA', 2021, '01/01/2021', '31/12/2040', 450, 120, 'S03'),
    5 => new MuebleReciclado('banco palet', 'Palets SA', 'ESPAÑA', 2024, '01/01/2024', '31/12/2040', 65, 90),
];

// A cada mueble le añado al menos dos características además de las básicas.
// Las pongo por pares (nombre, valor); anadirCaracteristicas las guarda una a una.
$muebles[1]->anadirCaracteristicas('color', 'verde', 'saliente', true);
$muebles[2]->anadirCaracteristicas('material', 'roble', 'plegable', false);
$muebles[3]->anadirCaracteristicas('baldas', 4, 'color', 'natural');
$muebles[4]->anadirCaracteristicas('puertas', 3, 'espejo', true);
$muebles[5]->anadirCaracteristicas('respaldo', false, 'plazas', 3);

// La plantilla la cargo la última: solo define funciones para dibujar la página,
// y así ya tengo preparado todo lo que se va a mostrar.
include(RUTABASE . "/aplicacion/plantilla/plantilla.php");
