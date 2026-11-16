<?php

/**
 * Página principal de la tienda.
 * Muestra una tabla con todos los muebles y un único formulario con un select
 * (para elegir el mueble) y dos botones: "Mostrar Mueble", que enseña debajo todos
 * los datos del mueble elegido, y "Modificar Mueble", que abre en una ventana nueva
 * la página modificar.php.
 * Sigo la estructura de siempre: arriba el controlador y abajo la vista.
 */

// Esta página está dos carpetas por debajo de cabecera.php (principal -> aplicacion -> web),
// por eso la ruta sube con /../../ . De esta cabecera sale también el array $muebles.
include_once(dirname(__FILE__) . "/../../cabecera.php");

//controlador
// En el controlador solo dejo la estructura de la página; lo demás va en la vista.
// Estoy en la página principal, así que "inicio" no lleva enlace (no tiene sentido
// un enlace a la propia página).
$barra = [
    [
        "TEXTO" => "inicio"
    ],
];

//dibuja la plantilla de la vista
inicioCabecera("tienda");
cabecera();
finCabecera();
inicioCuerpo("TIENDA", $barra);
cuerpo($muebles);  //llamo a la vista y le paso el array, porque una función no ve las variables de fuera
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
/**
 * Cuerpo de la página principal.
 * Decide si hay que mostrar un mueble (si se ha pulsado "Mostrar Mueble" y el
 * índice recibido es válido) y dibuja la tabla, el formulario y, si toca, los datos.
 *
 * @param array $muebles Array con los muebles, con los índices a partir de 1
 */
function cuerpo(array $muebles)
{
    // Índice del mueble que tengo que mostrar. Empieza en null (= ninguno).
    $indice = null;
    $accionMostrar = isset($_POST['accion']) && $_POST['accion'] === 'mostrar';

    if ($accionMostrar) {
        // No me fío de lo que llega del formulario: lo convierto a entero con filter_var
        // (si no es un entero, o es un array, devuelve false) y compruebo que ese
        // índice existe de verdad en el array antes de usarlo.
        $candidato = filter_var($_POST['indice'] ?? null, FILTER_VALIDATE_INT);
        if ($candidato !== false && isset($muebles[$candidato])) {
            $indice = $candidato;
        }
    }

    tablaMuebles($muebles);
    formularioElegir($muebles, $indice);

    if ($indice !== null) {
        mostrarMueble($muebles[$indice]);
    } elseif ($accionMostrar) {
        // Se pulsó el botón pero el índice no era válido
        echo "<p>El mueble elegido no existe.</p>\n";
    }
}

/**
 * Dibuja la tabla con todos los muebles. Una fila por mueble y 7 columnas
 * (el enunciado pide 5 o más, incluyendo índice, tipo y nombre).
 * Uso htmlspecialchars en todo lo que imprimo para que ningún carácter raro
 * se interprete como HTML.
 *
 * @param array $muebles Array con los muebles
 */
function tablaMuebles(array $muebles)
{
    echo "<h2>Muebles de la tienda</h2>\n";
    echo "<table border=\"1\" cellpadding=\"4\">\n";
    echo "<tr><th>Índice</th><th>Tipo</th><th>Nombre</th><th>Fabricante</th><th>País</th><th>Año</th><th>Precio</th><th>Material</th></tr>\n";

    // $i es el índice del array (empieza en 1) y $m el mueble de esa posición
    foreach ($muebles as $i => $m) {
        echo "<tr>";
        echo "<td>" . $i . "</td>";
        echo "<td>" . htmlspecialchars(get_class($m)) . "</td>";   // get_class da el tipo real: MuebleReciclado o MuebleTradicional
        echo "<td>" . htmlspecialchars($m->getNombre()) . "</td>";
        echo "<td>" . htmlspecialchars($m->getFabricante()) . "</td>";
        echo "<td>" . htmlspecialchars($m->getPais()) . "</td>";
        echo "<td>" . $m->getAnio() . "</td>";
        echo "<td>" . $m->getPrecio() . " €</td>";
        echo "<td>" . $m->getMaterial() . "</td>";
        echo "</tr>\n";
    }

    echo "</table>\n";
}

/**
 * Dibuja el formulario: un solo formulario con un select y dos botones de envío.
 * El valor de cada opción es el índice del mueble en el array, y el texto muestra
 * datos del mueble para poder elegirlo.
 * El botón "Mostrar Mueble" envía el formulario a esta misma página. El botón
 * "Modificar Mueble" tiene su propio formaction (envía a modificar.php en lugar de a
 * esta página) y formtarget="_blank" (lo abre en una ventana nueva).
 *
 * @param array    $muebles Array con los muebles
 * @param int|null $elegido Índice que estaba seleccionado (para que siga marcado al recargar)
 */
function formularioElegir(array $muebles, ?int $elegido)
{
    echo "<h2>Elegir un mueble</h2>\n";
    echo "<form method=\"post\" action=\"/aplicacion/principal/index.php\">\n";
    echo "<select name=\"indice\">\n";

    foreach ($muebles as $i => $m) {
        // Si es el mueble que se acaba de mostrar, lo dejo seleccionado
        $seleccionado = ($i === $elegido) ? " selected" : "";
        $texto = $i . " - " . get_class($m) . " - " . $m->getNombre();
        echo "<option value=\"" . $i . "\"" . $seleccionado . ">" . htmlspecialchars($texto) . "</option>\n";
    }

    echo "</select>\n";
    echo "<button type=\"submit\" name=\"accion\" value=\"mostrar\">Mostrar Mueble</button>\n";
    echo "<button type=\"submit\" formaction=\"/aplicacion/principal/modificar.php\" formtarget=\"_blank\">Modificar Mueble</button>\n";
    echo "</form>\n";
}

/**
 * Muestra todos los datos de un mueble, cada uno en una línea: primero las
 * propiedades de MuebleBase, luego las adicionales de su clase y por último las
 * características. No uso el convertir la instancia en cadena (__toString):
 * el enunciado dice que aquí hay que sacar cada propiedad con sus get.
 *
 * @param MuebleBase $mueble Mueble que quiero mostrar
 */
function mostrarMueble(MuebleBase $mueble)
{
    echo "<h2>Datos del mueble</h2>\n";

    // Propiedades de MuebleBase
    linea("Tipo", get_class($mueble));
    linea("Nombre", $mueble->getNombre());
    linea("Fabricante", $mueble->getFabricante());
    linea("País", $mueble->getPais());
    linea("Año de fabricación", $mueble->getAnio());
    linea("Inicio de venta", $mueble->getFechaIniVenta());
    linea("Fin de venta", $mueble->getFechaFinVenta());
    linea("Precio", $mueble->getPrecio() . " €");
    linea("Material", $mueble->getMaterial());

    // Propiedades adicionales. No sé de antemano de qué tipo es el mueble, así que
    // se lo pregunto con damePropiedadesAdicionales(): me devuelve sus nombres
    // ('Peso', 'Serie'...). Con el nombre construyo la llamada al get ('get' + 'Peso')
    // y así sirve para cualquier tipo sin hacer un if por cada uno.
    foreach ($mueble->damePropiedadesAdicionales() as $propiedad) {
        $get = 'get' . $propiedad;
        linea($propiedad, $mueble->$get());
    }

    // Características: el foreach las recorre en orden inverso al que se añadieron.
    // Uso $hay para saber si había alguna y, si no, decirlo.
    echo "<p><b>Características:</b></p>\n";
    $hay = false;
    echo "<ul>\n";
    foreach ($mueble->getCaracteristicas() as $nombre => $valor) {
        $hay = true;
        echo "<li>" . htmlspecialchars($nombre) . ": " . htmlspecialchars(aTexto($valor)) . "</li>\n";
    }
    echo "</ul>\n";
    if (!$hay) {
        echo "<p>(este mueble no tiene características)</p>\n";
    }
}

// ===================== FUNCIONES AUXILIARES DE LA VISTA =====================

/**
 * Convierte un valor en texto para poder mostrarlo.
 * Lo necesito porque un true se imprimiría como 1 y un false como vacío.
 *
 * @param mixed $valor Valor que quiero mostrar
 * @return string El valor como texto
 */
function aTexto(mixed $valor): string
{
    if (is_bool($valor)) {
        return $valor ? 'true' : 'false';
    }
    return (string) $valor;
}

/**
 * Imprime una línea con el formato "etiqueta: valor".
 *
 * @param string $etiqueta Nombre del dato
 * @param mixed  $valor    Valor del dato
 */
function linea(string $etiqueta, mixed $valor)
{
    echo "<p><b>" . htmlspecialchars($etiqueta) . ":</b> " . htmlspecialchars(aTexto($valor)) . "</p>\n";
}
