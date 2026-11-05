<?php
/**Ejercicio 1: pruebas de las librerías de validación (index.php?ejercicio=1).
   Ejercicio 2: formulario con validación en el servidor (index.php?ejercicio=2, por defecto).
   - CONTROLADOR: lee y valida los datos, sin escribir nada en la página.
   - VISTA: muestra los datos que recibe por parámetro. */

date_default_timezone_set("Europe/Madrid");

// Plantilla, librerías de validación (normal y con filter) y la librería reutilizable
require_once $_SERVER["DOCUMENT_ROOT"] . "/cabecera.php";
require_once __DIR__ . "/val_normal.php";
require_once __DIR__ . "/val_filter.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/scripts/librerias/validacion.php";

// ---------- Constantes del formulario ----------
// Opciones válidas
const ESTADOS = [1 => "estudiante", 2 => "en paro", 3 => "trabajando", 4 => "jubilado"];
const ESTUDIOS = [0 => "sin estudios", 1 => "primaria", 2 => "secundaria",
                  3 => "bachillerato", 4 => "ciclo formativo", 5 => "universitarios"];

// Opciones que se muestran: incluyen una opción incorrecta para poder probar la validación
const ESTADOS_FORMULARIO = [1 => "estudiante", 2 => "en paro", 3 => "trabajando",
                            4 => "jubilado", 5 => "incorrecta"];
const ESTUDIOS_FORMULARIO = [0 => "sin estudios", 1 => "primaria", 2 => "secundaria",
                            3 => "bachillerato", 4 => "ciclo formativo", 5 => "universitarios",
                            6 => "incorrecta"];

// Contraseña: 8 a 15 caracteres, con mayúscula, minúscula, cifra y uno de $ _ - ; < > @
const REGEX_PASSWORD = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[$_\-;<>@]).{8,15}$/u';

// ======================= CONTROLADOR =======================

// Devuelve el valor de $_POST como texto ("" si no existe o si no es texto, por ejemplo un array)
function textoPost(string $campo): string
{
    $valor = $_POST[$campo] ?? "";
    return is_string($valor) ? $valor : "";
}

// Crea un DateTime a partir de "dd/mm/aaaa". El "!" pone la hora a 00:00:00 para comparar solo días
function crearFecha(string $fecha): DateTime
{
    return DateTime::createFromFormat("!d/m/Y", $fecha);
}

// Decide qué ejercicio se muestra. Solo se admite "1" o "2" (se valida con validaRango)
function obtenerEjercicio(): string
{
    $ejercicio = $_GET["ejercicio"] ?? "2";
    if (!is_string($ejercicio) || !validaRango($ejercicio, ["1", "2"])) {
        return "2";
    }
    return $ejercicio;
}

// ---------- Ejercicio 1: pruebas de las librerías ----------

// Ejecuta la misma validación en las dos librerías y devuelve una fila con los resultados
function probar(string $descripcion, string $funcion, mixed $valor, mixed ...$parametros): array
{
    // Cada librería recibe su propia copia, porque las funciones modifican la variable
    $enNormal = $valor;
    $enFilter = $valor;

    // Nombre completo de la función: espacio de nombres + nombre
    $funcionNormal = "VALNORMAL\\" . $funcion;
    $funcionFilter = "VALFILTER\\" . $funcion;

    $okNormal = $funcionNormal($enNormal, ...$parametros);
    $okFilter = $funcionFilter($enFilter, ...$parametros);

    return [
        "descripcion" => $descripcion,
        "normal" => ["ok" => $okNormal, "valor" => $enNormal],
        "filter" => ["ok" => $okFilter, "valor" => $enFilter],
    ];
}

function obtenerPruebasLibrerias(): array
{
    return [
        probar('validaEntero(7, 1, 10, 5)', "validaEntero", 7, 1, 10, 5),
        probar('validaEntero(15, 1, 10, 5)', "validaEntero", 15, 1, 10, 5),
        probar('validaReal(3.5, 1, 10, 1)', "validaReal", 3.5, 1.0, 10.0, 1.0),
        probar('validaReal(12.5, 1, 10, 1)', "validaReal", 12.5, 1.0, 10.0, 1.0),
        probar('validaFecha("1/2/2023", "01/01/2000")', "validaFecha", "1/2/2023", "01/01/2000"),
        probar('validaFecha("31/02/2023", "01/01/2000")', "validaFecha", "31/02/2023", "01/01/2000"),
        probar('validaHora("0:5:1", "00:00:00")', "validaHora", "0:5:1", "00:00:00"),
        probar('validaHora("25:00:00", "00:00:00")', "validaHora", "25:00:00", "00:00:00"),
        probar('validaEmail("ana@correo.es", "")', "validaEmail", "ana@correo.es", ""),
        probar('validaEmail("ana@correo", "")', "validaEmail", "ana@correo", ""),
        probar('validaCadena("hola", 10, "")', "validaCadena", "hola", 10, ""),
        probar('validaCadena("una cadena demasiado larga", 10, "")', "validaCadena", "una cadena demasiado larga", 10, ""),
        probar('validaExpresion("AB123", "/^[A-Z]{2}[0-9]{3}$/", "")', "validaExpresion", "AB123", '/^[A-Z]{2}[0-9]{3}$/', ""),
        probar('validaExpresion("ab123", "/^[A-Z]{2}[0-9]{3}$/", "")', "validaExpresion", "ab123", '/^[A-Z]{2}[0-9]{3}$/', ""),
        probar('validaRango(3, [1, 2, 3], 1)', "validaRango", 3, [1, 2, 3], 1),
        probar('validaRango("b", ["a" => 1, "b" => 2], 2)', "validaRango", "b", ["a" => 1, "b" => 2], 2),
        probar('validaRango(9, [1, 2, 3], 1)', "validaRango", 9, [1, 2, 3], 1),
    ];
}

// ---------- Ejercicio 2: formulario ----------

// Valores con los que se muestra el formulario la primera vez
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
        "estado" => null,        // ninguno marcado
        "estudios" => [],        // ninguno marcado
        "hermanos" => 0,
        "sueldo" => 1100.0,
    ];
}

// Valida todos los datos enviados. Devuelve [datos corregidos, errores]
function validarFormulario(): array
{
    $datos = valoresPorDefecto();
    $errores = [];
    $hoy = new DateTime("today");

    // ---- Nombre: máximo 25, no vacío, no empieza por H, en mayúsculas ----
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

    // ---- Contraseña: máximo 15 y cumplir la expresión regular ----
    $password = textoPost("password");   // no se hace trim: los espacios también cuentan
    if (!validaCadena($password, 15, "")) {
        $errores["password"] = "La contraseña no puede tener más de 15 caracteres";
    } elseif (!validaExpresion($password, REGEX_PASSWORD, "")) {
        // Comillas simples: con dobles, PHP intentaría leer $_ como una variable
        $errores["password"] = 'La contraseña debe tener de 8 a 15 caracteres, con una mayúscula, una minúscula, una cifra y un carácter especial ($_-;<>@)';
    }
    $datos["password"] = $password;

    // ---- Fecha de nacimiento: dd/mm/aaaa y no futura ----
    $fechaNacimiento = trim(textoPost("fecha_nacimiento"));
    if (!validaFecha($fechaNacimiento, "")) {
        $errores["fecha_nacimiento"] = "La fecha de nacimiento no es válida (formato dd/mm/aaaa)";
    } elseif (crearFecha($fechaNacimiento) > $hoy) {
        $errores["fecha_nacimiento"] = "La fecha de nacimiento no puede ser futura";
        $fechaNacimiento = "";
    }
    $datos["fecha_nacimiento"] = $fechaNacimiento;

    // ---- Fecha de carnet: tres campos que se unen en una sola fecha ----
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
        // Fecha de carnet menos 18 años: no puede ser anterior al nacimiento
        $limite = crearFecha($fechaCarnet);
        $limite->sub(new DateInterval("P18Y"));
        $intervalo = crearFecha($fechaNacimiento)->diff($limite);
        if ($intervalo->invert === 1) {   // 1: el límite es anterior al nacimiento
            $errores["carnet"] = "El carnet debe sacarse siendo mayor de edad (18 años cumplidos)";
            $fechaCarnet = "";
        }
    }
    // Se separa de nuevo en día, mes y año para volver a mostrar los tres campos
    [$datos["carnet_dia"], $datos["carnet_mes"], $datos["carnet_anio"]] =
        ($fechaCarnet === "") ? ["", "", ""] : explode("/", $fechaCarnet);

    // ---- Hora de levantarse: hh:mm:ss ----
    $hora = trim(textoPost("hora"));
    if (!validaHora($hora, "")) {
        $errores["hora"] = "La hora no es válida (formato hh:mm:ss)";
    }
    $datos["hora"] = $hora;

    // ---- Estado: obligatorio y uno de los válidos ----
    $estadoTexto = trim(textoPost("estado"));
    if ($estadoTexto === "") {
        $errores["estado"] = "Debe indicar un estado";
    } elseif (!ctype_digit($estadoTexto) || !validaRango((int) $estadoTexto, ESTADOS, 2)) {
        $errores["estado"] = "El estado elegido no es válido";
    } else {
        $datos["estado"] = (int) $estadoTexto;
    }

    // ---- Estudios: al menos uno, todos válidos; si está el 0, ningún otro ----
    $enviados = $_POST["estudios"] ?? [];
    if (!is_array($enviados)) {
        $enviados = [];
    }
    $estudios = [];
    $hayNoValidos = false;
    foreach ($enviados as $codigo) {
        if (is_string($codigo) && ctype_digit($codigo) && validaRango((int) $codigo, ESTUDIOS, 2)) {
            $estudios[] = (int) $codigo;
        } else {
            $hayNoValidos = true;
        }
    }
    $estudios = array_values(array_unique($estudios));   // sin repetidos
    sort($estudios);
    if (in_array(0, $estudios, true)) {
        $estudios = [0];   // "sin estudios" no se combina con nada: se desmarcan las demás
    }
    if ($hayNoValidos) {
        $errores["estudios"] = "Has marcado una opción de estudios que no es válida";
    } elseif (count($estudios) === 0) {
        $errores["estudios"] = "Debe seleccionar al menos un estudio";
    }
    $datos["estudios"] = $estudios;

    // ---- Hermanos: entero entre 0 y 20 ----
    // validaEntero exige un int: primero se comprueba que el texto es un entero
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
    // validaReal exige un float: se admite la coma como separador decimal
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

// Prepara los datos del ejercicio 2: formulario inicial o formulario validado
function obtenerDatosFormulario(): array
{
    // Primera visita: formulario con los valores por defecto
    if (!isset($_POST["enviar"])) {
        return ["datos" => valoresPorDefecto(), "errores" => [], "correcto" => false];
    }

    [$datos, $errores] = validarFormulario();
    return ["datos" => $datos, "errores" => $errores, "correcto" => count($errores) === 0];
}

function obtenerResultados(): array
{
    $ejercicio = obtenerEjercicio();

    return [
        "ejercicio" => $ejercicio,
        "pruebas" => $ejercicio === "1" ? obtenerPruebasLibrerias() : [],
        "formulario" => $ejercicio === "2" ? obtenerDatosFormulario() : null,
    ];
}

function escapar(string $texto): string
{
    return htmlspecialchars($texto, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
}

function mostrarResultados(array $resultados): void
{
    echo '<nav aria-label="Ejercicios">';
    echo '<a href="?ejercicio=1">Pruebas de las librerías</a> | ';
    echo '<a href="?ejercicio=2">Formulario</a>';
    echo '</nav>';

    if ($resultados["ejercicio"] === "1") {
        echo "<h2>Pruebas de validación</h2>";
        echo '<table><thead><tr><th>Prueba</th><th>Normal</th><th>Con FILTER</th></tr></thead><tbody>';
        foreach ($resultados["pruebas"] as $prueba) {
            echo "<tr>";
            echo "<th>" . escapar($prueba["descripcion"]) . "</th>";
            foreach (["normal", "filter"] as $tipo) {
                $detalle = $prueba[$tipo];
                $valor = print_r($detalle["valor"], true);
                echo "<td>" . ($detalle["ok"] ? "Correcta" : "Incorrecta")
                    . "<pre>" . escapar($valor) . "</pre></td>";
            }
            echo "</tr>";
        }
        echo "</tbody></table>";
        return;
    }

    $formulario = $resultados["formulario"];
    $datos = $formulario["datos"];
    $errores = $formulario["errores"];

    if ($formulario["correcto"]) {
        echo "<p>Formulario validado correctamente.</p>";
    }
    if ($errores !== []) {
        echo '<div role="alert"><p>Revisa estos campos:</p><ul>';
        foreach ($errores as $error) {
            echo "<li>" . escapar($error) . "</li>";
        }
        echo "</ul></div>";
    }

    echo '<h2>Introducción de información</h2>';
    echo '<form method="post" action="?ejercicio=2">';
    echo '<p><label>Nombre (máximo 25 caracteres, no puede empezar por H): ';
    echo '<input name="nombre" maxlength="25" value="' . escapar($datos["nombre"]) . '"></label></p>';
    echo '<p><label>Contraseña: <input type="password" name="password" maxlength="15"></label></p>';
    echo '<p><label>Fecha de nacimiento (dd/mm/aaaa): ';
    echo '<input name="fecha_nacimiento" value="' . escapar($datos["fecha_nacimiento"]) . '"></label></p>';
    echo '<fieldset><legend>Fecha del carnet</legend>';
    foreach (["carnet_dia" => "Día", "carnet_mes" => "Mes", "carnet_anio" => "Año"] as $campo => $etiqueta) {
        echo '<label>' . escapar($etiqueta) . ': <input name="' . escapar($campo)
            . '" value="' . escapar($datos[$campo]) . '" size="4"></label> ';
    }
    echo "</fieldset>";
    echo '<p><label>Hora de levantarse (hh:mm:ss): ';
    echo '<input name="hora" value="' . escapar($datos["hora"]) . '"></label></p>';

    echo "<fieldset><legend>Estado</legend>";
    foreach (ESTADOS_FORMULARIO as $codigo => $estado) {
        $seleccionado = $datos["estado"] === $codigo ? " checked" : "";
        echo '<label><input type="radio" name="estado" value="' . $codigo . '"' . $seleccionado
            . '> ' . escapar($estado) . "</label> ";
    }
    echo "</fieldset>";

    echo "<fieldset><legend>Estudios</legend>";
    foreach (ESTUDIOS_FORMULARIO as $codigo => $estudio) {
        $seleccionado = in_array($codigo, $datos["estudios"], true) ? " checked" : "";
        echo '<label><input type="checkbox" name="estudios[]" value="' . $codigo . '"' . $seleccionado
            . '> ' . escapar($estudio) . "</label> ";
    }
    echo "</fieldset>";
    echo '<p><label>Número de hermanos (0-20): ';
    echo '<input type="number" name="hermanos" min="0" max="20" value="' . escapar((string) $datos["hermanos"]) . '"></label></p>';
    echo '<p><label>Sueldo (1000-150000): ';
    echo '<input name="sueldo" value="' . escapar((string) $datos["sueldo"]) . '"></label></p>';
    echo '<p><button type="submit" name="enviar" value="1">Enviar</button></p>';
    echo "</form>";

    if ($formulario["correcto"]) {
        echo "<h2>Datos recibidos</h2><dl>";
        foreach ($datos as $campo => $valor) {
            if ($campo === "password") {
                continue;
            }
            $texto = is_array($valor) ? implode(", ", $valor) : (string) $valor;
            echo "<dt>" . escapar($campo) . "</dt><dd>" . escapar($texto) . "</dd>";
        }
        echo "</dl>";
    }
}

$ubicacion = [
    ['TEXTO' => 'Inicio', 'ENLACE' => '/index.php', 'ADICIONAL' => ' &gt;&gt; '],
    ['TEXTO' => 'Relación 5'],
];

// Estructura común de la página y llamada al controlador y a la vista.
inicioCabecera('Práctica: relación 5');
finCabecera();
inicioCuerpo('Relación 5: Introducción de información', $ubicacion);
mostrarResultados(obtenerResultados());
finCuerpo();