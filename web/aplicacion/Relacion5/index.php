<?php
/* index.php de la relacion5
   Ejercicio 1: pruebas de las librerías de validación (index.php?ejercicio=1).
   Ejercicio 2: formulario con validación en el servidor (index.php?ejercicio=2),
   es el que sale por defecto.
   El controlador valida los datos y no escribe nada en la página.
   La vista recibe los datos por parámetro y los muestra. */

// Pongo la zona horaria para que las fechas y las horas salgan bien
date_default_timezone_set("Europe/Madrid");

// Cargo la plantilla y las librerías de validación
require_once $_SERVER["DOCUMENT_ROOT"] . "/cabecera.php";
require_once __DIR__ . "/val_normal.php";
require_once __DIR__ . "/val_filter.php";

use function VALNORMAL\validaCadena;
use function VALNORMAL\validaEntero;
use function VALNORMAL\validaExpresion;
use function VALNORMAL\validaFecha;
use function VALNORMAL\validaHora;
use function VALNORMAL\validaRango;
use function VALNORMAL\validaReal;

// ---------- Constantes del formulario ----------
// Opciones que son válidas
const ESTADOS = [1 => "estudiante", 2 => "en paro", 3 => "trabajando", 4 => "jubilado"];
const ESTUDIOS = [0 => "sin estudios", 1 => "primaria", 2 => "secundaria",
                  3 => "bachillerato", 4 => "ciclo formativo", 5 => "universitarios"];

// Opciones que enseño en el formulario: llevan una incorrecta (la 5 y la 6)
// para comprobar que el servidor la rechaza
const ESTADOS_FORMULARIO = [1 => "estudiante", 2 => "en paro", 3 => "trabajando",
                            4 => "jubilado", 5 => "incorrecta"];
const ESTUDIOS_FORMULARIO = [0 => "sin estudios", 1 => "primaria", 2 => "secundaria",
                            3 => "bachillerato", 4 => "ciclo formativo", 5 => "universitarios",
                            6 => "incorrecta"];

// Contraseña: de 8 a 15 caracteres con una mayúscula, una minúscula, una cifra
// y un carácter especial de $ _ - ; < > @
const REGEX_PASSWORD = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[$_\-;<>@]).{8,15}$/u';

// ======================= CONTROLADOR =======================

// Cojo un dato de $_POST y lo devuelvo siempre como texto
// Si no existe o llega un array devuelvo ""
function textoPost(string $campo): string
{
    $valor = $_POST[$campo] ?? "";
    return is_string($valor) ? $valor : "";
}

// Paso un texto dd/mm/aaaa a un objeto DateTime
// El ! pone la hora a 00:00:00 para que solo se comparen los días
function crearFecha(string $fecha): DateTime
{
    return DateTime::createFromFormat("!d/m/Y", $fecha);
}

// Miro en la URL qué ejercicio hay que enseñar (solo vale "1" o "2")
// Si pone otra cosa dejo el 2, que es el de por defecto
function obtenerEjercicio(): string
{
    $ejercicio = $_GET["ejercicio"] ?? "2";
    if (!is_string($ejercicio) || !validaRango($ejercicio, ["1", "2"])) {
        return "2";
    }
    return $ejercicio;
}

// ---------- Ejercicio 1: pruebas de las librerías ----------

// Pruebo una función en las dos librerías y miro si da lo que espero
// $esperado lleva [lo que debe devolver la función (true o false),
//                  el valor que debe quedar en la variable]
function probar(string $descripcion, string $funcion, array $esperado, mixed $valor, mixed ...$parametros): array
{
    [$okEsperado, $valorEsperado] = $esperado;

    // Cada librería usa su propia copia porque las funciones cambian la variable
    $enNormal = $valor;
    $enFilter = $valor;

    // Nombre completo de la función: espacio de nombres + nombre
    $funcionNormal = "VALNORMAL\\" . $funcion;
    $funcionFilter = "VALFILTER\\" . $funcion;

    // Llamo a la función de cada librería
    $okNormal = $funcionNormal($enNormal, ...$parametros);
    $okFilter = $funcionFilter($enFilter, ...$parametros);

    return [
        "descripcion" => $descripcion,
        "esperado" => ["ok" => $okEsperado, "valor" => $valorEsperado],
        // "pasa" es true si devuelve lo esperado y la variable queda con el valor esperado
        "normal" => ["ok" => $okNormal, "valor" => $enNormal,
                     "pasa" => ($okNormal === $okEsperado && $enNormal === $valorEsperado)],
        "filter" => ["ok" => $okFilter, "valor" => $enFilter,
                     "pasa" => ($okFilter === $okEsperado && $enFilter === $valorEsperado)],
    ];
}

function obtenerPruebasLibrerias(): array
{
    // Cada prueba lleva lo que espero que pase: [true o false, valor que queda]
    $filas = [
        probar('validaEntero(7, 1, 10, 5)', "validaEntero", [true, 7], 7, 1, 10, 5),
        probar('validaEntero(15, 1, 10, 5)', "validaEntero", [false, 5], 15, 1, 10, 5),
        probar('validaReal(3.5, 1, 10, 1)', "validaReal", [true, 3.5], 3.5, 1.0, 10.0, 1.0),
        probar('validaReal(12.5, 1, 10, 1)', "validaReal", [false, 1.0], 12.5, 1.0, 10.0, 1.0),
        probar('validaFecha("1/2/2023", "01/01/2000")', "validaFecha", [true, "01/02/2023"], "1/2/2023", "01/01/2000"),
        probar('validaFecha("31/02/2023", "01/01/2000")', "validaFecha", [false, "01/01/2000"], "31/02/2023", "01/01/2000"),
        probar('validaHora("0:5:1", "00:00:00")', "validaHora", [true, "00:05:01"], "0:5:1", "00:00:00"),
        probar('validaHora("25:00:00", "00:00:00")', "validaHora", [false, "00:00:00"], "25:00:00", "00:00:00"),
        probar('validaEmail("ana@correo.es", "")', "validaEmail", [true, "ana@correo.es"], "ana@correo.es", ""),
        probar('validaEmail("ana@correo", "")', "validaEmail", [false, ""], "ana@correo", ""),
        probar('validaCadena("hola", 10, "")', "validaCadena", [true, "hola"], "hola", 10, ""),
        probar('validaCadena("una cadena demasiado larga", 10, "")', "validaCadena", [false, ""], "una cadena demasiado larga", 10, ""),
        probar('validaExpresion("AB123", "/^[A-Z]{2}[0-9]{3}$/", "")', "validaExpresion", [true, "AB123"], "AB123", '/^[A-Z]{2}[0-9]{3}$/', ""),
        probar('validaExpresion("ab123", "/^[A-Z]{2}[0-9]{3}$/", "")', "validaExpresion", [false, ""], "ab123", '/^[A-Z]{2}[0-9]{3}$/', ""),
        probar('validaRango(3, [1, 2, 3], 1)', "validaRango", [true, 3], 3, [1, 2, 3], 1),
        probar('validaRango("b", ["a" => 1, "b" => 2], 2)', "validaRango", [true, "b"], "b", ["a" => 1, "b" => 2], 2),
        probar('validaRango(9, [1, 2, 3], 1)', "validaRango", [false, 9], 9, [1, 2, 3], 1),
    ];

    // Cuento las pruebas que han salido bien (cada fila prueba las dos librerías)
    $superadas = 0;
    foreach ($filas as $fila) {
        $superadas += (int) $fila["normal"]["pasa"] + (int) $fila["filter"]["pasa"];
    }

    return ["filas" => $filas, "superadas" => $superadas, "total" => count($filas) * 2];
}

// ---------- Ejercicio 2: formulario ----------

// Valores con los que sale el formulario la primera vez
function valoresPorDefecto(): array
{
    return [
        "nombre" => "",
        "password" => "",
        "fecha_nacimiento" => "",
        "carnet_dia" => "",
        "carnet_mes" => "",
        "carnet_anio" => "",
        "hora" => "",
        "estado" => null,        // ningún estado marcado
        "estudios" => [],        // ningún estudio marcado
        "hermanos" => 0,
        "sueldo" => 1100.0,
    ];
}

// Valido todos los datos del formulario con las funciones de validacion.php
// Devuelve los datos ya corregidos y los errores
function validarFormulario(): array
{
    $datos = valoresPorDefecto();
    $errores = [];
    $hoy = new DateTime("today");

    // ---- Nombre: máximo 25, no puede estar vacío ni empezar por H, y lo paso a mayúsculas ----
    $nombre = mb_strtoupper(trim(textoPost("nombre")));
    if (!validaCadena($nombre, 25, "")) {
        $errores["nombre"] = "El nombre no puede tener más de 25 caracteres";
    } elseif (mb_strlen($nombre) === 0) {
        $errores["nombre"] = "El nombre no puede estar vacío";
    } elseif (mb_substr($nombre, 0, 1) === "H") {
        $errores["nombre"] = "El nombre no puede empezar por H";
        $nombre = "";
    }
    $datos["nombre"] = $nombre;

    // ---- Contraseña: máximo 15 caracteres y que cumpla la expresión regular ----
    // No le hago trim porque los espacios también cuentan en una contraseña
    $password = textoPost("password");
    if (!validaCadena($password, 15, "")) {
        $errores["password"] = "La contraseña no puede tener más de 15 caracteres";
    } elseif (!validaExpresion($password, REGEX_PASSWORD, "")) {
        // Uso comillas simples porque con dobles PHP intenta leer $_ como una variable
        $errores["password"] = 'La contraseña debe tener de 8 a 15 caracteres, con una mayúscula, una minúscula, una cifra y un carácter especial ($_-;<>@)';
    }
    $datos["password"] = $password;

    // ---- Fecha de nacimiento: formato dd/mm/aaaa y que no sea de futuro ----
    $fechaNacimiento = trim(textoPost("fecha_nacimiento"));
    if (!validaFecha($fechaNacimiento, "")) {
        $errores["fecha_nacimiento"] = "La fecha de nacimiento no es válida (formato dd/mm/aaaa)";
    } elseif (crearFecha($fechaNacimiento) > $hoy) {
        $errores["fecha_nacimiento"] = "La fecha de nacimiento no puede ser futura";
        $fechaNacimiento = "";
    }
    $datos["fecha_nacimiento"] = $fechaNacimiento;

    // ---- Fecha de carnet: llegan 3 campos y los junto en una sola fecha ----
    $dia = trim(textoPost("carnet_dia"));
    $mes = trim(textoPost("carnet_mes"));
    $anio = trim(textoPost("carnet_anio"));
    $fechaCarnet = $dia . "/" . $mes . "/" . $anio;
    if (!validaFecha($fechaCarnet, "")) {
        $errores["carnet"] = "La fecha del carnet no es válida";
    } elseif (crearFecha($fechaCarnet) > $hoy) {
        $errores["carnet"] = "La fecha del carnet no puede ser futura";
        $fechaCarnet = "";
    } elseif ($fechaNacimiento !== "") {
        // Le resto 18 años a la fecha del carnet
        // Si sale anterior al nacimiento, cuando se sacó el carnet era menor de edad
        $limite = crearFecha($fechaCarnet);
        $limite->sub(new DateInterval("P18Y"));
        $intervalo = crearFecha($fechaNacimiento)->diff($limite);
        // invert vale 1 si la fecha límite es anterior al nacimiento
        if ($intervalo->invert === 1) {
            $errores["carnet"] = "El carnet debe sacarse siendo mayor de edad (18 años cumplidos)";
            $fechaCarnet = "";
        }
    }
    // Vuelvo a separar día, mes y año para enseñar los tres campos en el formulario
    [$datos["carnet_dia"], $datos["carnet_mes"], $datos["carnet_anio"]] =
        ($fechaCarnet === "") ? ["", "", ""] : explode("/", $fechaCarnet);

    // ---- Hora de levantarse: formato hh:mm:ss ----
    $hora = trim(textoPost("hora"));
    if (!validaHora($hora, "")) {
        $errores["hora"] = "La hora no es válida (formato hh:mm:ss)";
    }
    $datos["hora"] = $hora;

    // ---- Estado: es obligatorio y tiene que ser uno de los válidos ----
    $estadoTexto = trim(textoPost("estado"));
    if ($estadoTexto === "") {
        $errores["estado"] = "Debe indicar un estado";
    } elseif (!ctype_digit($estadoTexto) || !validaRango((int) $estadoTexto, ESTADOS, 2)) {
        // ctype_digit mira que sean solo cifras, porque (int) "abc" daría 0 sin avisar
        $errores["estado"] = "El estado elegido no es válido";
    } else {
        $datos["estado"] = (int) $estadoTexto;
    }

    // ---- Estudios: al menos uno y todos válidos. Si está el 0, ningún otro ----
    $enviados = $_POST["estudios"] ?? [];
    if (!is_array($enviados)) {
        $enviados = [];
    }
    // Recorro los códigos que han llegado y me quedo solo con los válidos
    $estudios = [];
    $hayNoValidos = false;
    foreach ($enviados as $codigo) {
        if (is_string($codigo) && ctype_digit($codigo) && validaRango((int) $codigo, ESTUDIOS, 2)) {
            $estudios[] = (int) $codigo;
        } else {
            $hayNoValidos = true;
        }
    }
    // Quito los repetidos y los ordeno
    $estudios = array_values(array_unique($estudios));
    sort($estudios);
    // Si está el 0 (sin estudios) dejo solo ese y desmarco el resto
    if (in_array(0, $estudios, true)) {
        $estudios = [0];
    }
    if ($hayNoValidos) {
        $errores["estudios"] = "Has marcado una opción de estudios que no es válida";
    } elseif (count($estudios) === 0) {
        $errores["estudios"] = "Debe seleccionar al menos un estudio";
    }
    $datos["estudios"] = $estudios;

    // ---- Hermanos: entero entre 0 y 20 ----
    // validaEntero necesita un int, así que antes miro que el texto sea un número entero
    $hermanosTexto = trim(textoPost("hermanos"));
    $hermanos = 0;
    $correcto = preg_match('/^-?\d+$/', $hermanosTexto) === 1;
    if ($correcto) {
        $hermanos = (int) $hermanosTexto;
        $correcto = validaEntero($hermanos, 0, 20, 0);
    }
    if (!$correcto) {
        $errores["hermanos"] = "Los hermanos deben ser un número entero entre 0 y 20";
    }
    $datos["hermanos"] = $hermanos;

    // ---- Sueldo: real entre 1000 y 150000 ----
    // validaReal necesita un float, así que miro que sea un número
    // y cambio la coma por un punto por si lo escriben con coma
    $sueldoTexto = str_replace(",", ".", trim(textoPost("sueldo")));
    $sueldo = 1100.0;
    $correcto = is_numeric($sueldoTexto);
    if ($correcto) {
        $sueldo = (float) $sueldoTexto;
        $correcto = validaReal($sueldo, 1000.0, 150000.0, 1100.0);
    }
    if (!$correcto) {
        $errores["sueldo"] = "El sueldo debe ser un número entre 1000 y 150000";
    }
    $datos["sueldo"] = $sueldo;

    return [$datos, $errores];
}

// Preparo los datos del ejercicio 2
function obtenerDatosFormulario(): array
{
    // Si no se ha pulsado enviar (primera vez) saco el formulario con los valores por defecto
    if (!isset($_POST["enviar"])) {
        return ["datos" => valoresPorDefecto(), "errores" => [], "correcto" => false];
    }

    // Si se ha enviado, valido los datos
    [$datos, $errores] = validarFormulario();
    return ["datos" => $datos, "errores" => $errores, "correcto" => count($errores) === 0];
}

// ======================= CONTROLADOR (variables para la vista) =======================
// Miro qué ejercicio hay que enseñar
$ejercicio = obtenerEjercicio();

// Preparo los datos del ejercicio
$datos = ($ejercicio === "1") ? obtenerPruebasLibrerias() : obtenerDatosFormulario();

// Barra de ubicación
$ubicacion = [
    ["TEXTO" => "Inicio", "ENLACE" => "/index.php", "ADICIONAL" => " &gt; "],
    ["TEXTO" => "Relación 5", "ENLACE" => "index.php", "ADICIONAL" => " &gt; "],
    ["TEXTO" => "Ejercicio " . $ejercicio],
];

// ****************************************************************
// Dibujo la plantilla de la vista
inicioCabecera("Relación 5");
cabecera();
finCabecera();
inicioCuerpo("Relación 5", $ubicacion);
cuerpo($ejercicio, $datos);   // llamo a la vista y le paso los datos por parámetro
finCuerpo();
// ****************************************************************

// ======================= VISTA =======================

// Cosas extra de la cabecera (de momento no pongo nada)
function cabecera()
{
}

// Vista principal: según el ejercicio enseño las pruebas o el formulario
function cuerpo(string $ejercicio, array $datos): void
{
    mostrarEnlaces();
    if ($ejercicio === "1") {
        mostrarPruebas($datos);
    } else {
        mostrarEjercicio2($datos);
    }
}

// Escapo el texto para que lo que escriba el usuario no se interprete como HTML
function esc(string $texto): string
{
    return htmlspecialchars($texto, ENT_QUOTES, "UTF-8");
}

// Si el campo tiene un error, escribo el mensaje
function mostrarError(array $errores, string $campo): void
{
    if (isset($errores[$campo])) {
        echo '<div class="error">' . esc($errores[$campo]) . '</div>';
    }
}

function claseCampo(array $errores, string $campo): string
{
    return isset($errores[$campo]) ? 'for_texto input-error' : 'for_texto';
}

// Enlaces para cambiar entre el ejercicio 1 y el 2
function mostrarEnlaces(): void
{
    echo '<p><a href="index.php?ejercicio=1">Ejercicio 1: pruebas de las librerías</a> | ';
    echo '<a href="index.php?ejercicio=2">Ejercicio 2: formulario</a></p>';
}

// Escribe "correcto" o "incorrecto" junto con el valor que queda
function textoResultado(array $resultado): string
{
    $texto = $resultado["ok"] ? "correcto" : "incorrecto";
    return $texto . " &rarr; " . esc((string) $resultado["valor"]);
}

// Ejercicio 1: tabla con lo esperado y lo que da cada librería
function mostrarPruebas(array $resultado): void
{
    echo "<h2>Ejercicio 1: pruebas de las librerías</h2>";
    echo "<p>Pruebas superadas: " . $resultado["superadas"] . " de " . $resultado["total"] . "</p>";
    echo '<table class="tabla" style="border-collapse: separate; border-spacing: 10px 12px; width: 100%; table-layout: fixed;">';
    echo "<tr><th style=\"padding: 8px 10px; text-align: left;\">Prueba</th><th style=\"padding: 8px 10px; text-align: left;\">Esperado</th><th style=\"padding: 8px 10px; text-align: left;\">VALNORMAL</th><th style=\"padding: 8px 10px; text-align: left;\">VALFILTER</th></tr>";
    foreach ($resultado["filas"] as $fila) {
        echo "<tr><td style=\"padding: 8px 10px; vertical-align: top;\">" . esc($fila["descripcion"]) . "</td>";
        echo "<td style=\"padding: 8px 10px; vertical-align: top;\">" . textoResultado($fila["esperado"]) . "</td>";
        foreach (["normal", "filter"] as $libreria) {
            $datosLibreria = $fila[$libreria];
            // Si la prueba falla pongo la celda en rojo con la clase error
            $clase = $datosLibreria["pasa"] ? "" : ' class="error"';
            $marca = $datosLibreria["pasa"] ? "OK" : "FALLO";
            echo "<td style=\"padding: 8px 10px; vertical-align: top;\"" . $clase . ">" . $marca . ": " . textoResultado($datosLibreria) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}

// Ejercicio 2: formulario
// Recibe los valores que tengo que enseñar y los errores
function mostrarFormulario(array $datos, array $errores): void
{
?>
    <h2>Ejercicio 2: formulario</h2>
    <form method="post" action="index.php?ejercicio=2">
        <div class="campo-form">
            <label>Nombre:
                <input type="text" class="<?php echo claseCampo($errores, "nombre"); ?>" name="nombre" value="<?php echo esc($datos["nombre"]); ?>">
            </label>
            <?php mostrarError($errores, "nombre"); ?>
        </div>
        <div class="campo-form">
            <label>Contraseña:
                <input type="password" class="<?php echo claseCampo($errores, "password"); ?>" name="password">
            </label>
            <?php mostrarError($errores, "password"); ?>
        </div>
        <div class="campo-form">
            <label>Fecha de nacimiento (dd/mm/aaaa):
                <input type="text" class="<?php echo claseCampo($errores, "fecha_nacimiento"); ?>" name="fecha_nacimiento" value="<?php echo esc($datos["fecha_nacimiento"]); ?>">
            </label>
            <?php mostrarError($errores, "fecha_nacimiento"); ?>
        </div>
        <div class="campo-form">
            <label>Fecha de carnet:</label>
            <input type="text" class="<?php echo claseCampo($errores, "carnet"); ?>" name="carnet_dia" size="2" placeholder="dd" value="<?php echo esc($datos["carnet_dia"]); ?>">
            <input type="text" class="<?php echo claseCampo($errores, "carnet"); ?>" name="carnet_mes" size="2" placeholder="mm" value="<?php echo esc($datos["carnet_mes"]); ?>">
            <input type="text" class="<?php echo claseCampo($errores, "carnet"); ?>" name="carnet_anio" size="4" placeholder="aaaa" value="<?php echo esc($datos["carnet_anio"]); ?>">
            <?php mostrarError($errores, "carnet"); ?>
        </div>
        <div class="campo-form">
            <label>Hora de levantarse (hh:mm:ss):
                <input type="text" class="<?php echo claseCampo($errores, "hora"); ?>" name="hora" value="<?php echo esc($datos["hora"]); ?>">
            </label>
            <?php mostrarError($errores, "hora"); ?>
        </div>
        <p>
            Estado:
            <?php foreach (ESTADOS_FORMULARIO as $codigo => $texto) { ?>
                <label>
                    <input type="radio" name="estado" value="<?php echo $codigo; ?>"
                        <?php if ($datos["estado"] === $codigo) { echo "checked"; } ?>>
                    <?php echo $codigo . " - " . esc($texto); ?>
                </label>
            <?php } ?>
            <?php mostrarError($errores, "estado"); ?>
        </p>
        <p>
            Estudios:
            <?php foreach (ESTUDIOS_FORMULARIO as $codigo => $texto) { ?>
                <label>
                    <input type="checkbox" name="estudios[]" value="<?php echo $codigo; ?>"
                        <?php if (in_array($codigo, $datos["estudios"], true)) { echo "checked"; } ?>>
                    <?php echo $codigo . " - " . esc($texto); ?>
                </label>
            <?php } ?>
            <?php mostrarError($errores, "estudios"); ?>
        </p>
        <p>
            <label>Hermanos (0 a 20):
                <input type="text" class="<?php echo claseCampo($errores, "hermanos"); ?>" name="hermanos" value="<?php echo esc((string) $datos["hermanos"]); ?>">
            </label>
            <?php mostrarError($errores, "hermanos"); ?>
        </p>
        <p>
            <label>Sueldo (1000 a 150000 €):
                <input type="text" class="<?php echo claseCampo($errores, "sueldo"); ?>" name="sueldo" value="<?php echo esc((string) $datos["sueldo"]); ?>">
            </label>
            <?php mostrarError($errores, "sueldo"); ?>
        </p>
        <p><input type="submit" name="enviar" value="Enviar"></p>
    </form>
<?php
}

// Ejercicio 2: resumen cuando todos los datos están bien
function mostrarResumen(array $datos): void
{
    // Paso los códigos de estudios a su texto
    $estudios = array_map(fn(int $codigo): string => ESTUDIOS[$codigo], $datos["estudios"]);

    echo "<h2>Datos introducidos</h2>";
    echo "<ul>";
    echo "<li>Nombre: " . esc($datos["nombre"]) . "</li>";
    // La contraseña no la enseño, pongo asteriscos
    echo "<li>Contraseña: " . str_repeat("*", mb_strlen($datos["password"])) . "</li>";
    echo "<li>Fecha de nacimiento: " . esc($datos["fecha_nacimiento"]) . "</li>";
    echo "<li>Fecha de carnet: " . esc($datos["carnet_dia"] . "/" . $datos["carnet_mes"] . "/" . $datos["carnet_anio"]) . "</li>";
    echo "<li>Hora de levantarse: " . esc($datos["hora"]) . "</li>";
    echo "<li>Estado: " . esc(ESTADOS[$datos["estado"]]) . "</li>";
    echo "<li>Estudios: " . esc(implode(", ", $estudios)) . "</li>";
    echo "<li>Hermanos: " . $datos["hermanos"] . "</li>";
    echo "<li>Sueldo: " . number_format($datos["sueldo"], 2, ",", ".") . " €</li>";
    echo "</ul>";
    echo '<p><a href="index.php?ejercicio=2">Volver al formulario</a></p>';
}

// Ejercicio 2: si no hay errores enseño el resumen y si no el formulario
function mostrarEjercicio2(array $resultado): void
{
    if ($resultado["correcto"]) {
        mostrarResumen($resultado["datos"]);
    } else {
        mostrarFormulario($resultado["datos"], $resultado["errores"]);
    }
}