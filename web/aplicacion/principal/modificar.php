<?php

/**
 * Página para modificar un mueble (se abre en una ventana nueva desde index.php).
 * Funciona en dos momentos, en el mismo fichero:
 *   A) Llego desde index.php con el índice: dibujo el formulario con los datos actuales.
 *   B) Se envía el formulario: valido los datos; si todo es correcto actualizo el mueble y
 *      enseño un resumen, y si hay errores los enseño y repinto el formulario.
 * La validación se hace de dos formas, como pide el enunciado: una parte con una instancia
 * auxiliar (llamando a sus set) y otra directamente con condiciones.
 * No uso "global": cuerpo() recibe el array de muebles como parámetro.
 * Como no uso sesiones, el cambio solo dura esta petición: al volver a index.php no se ve.
 */

// Esta página está dos carpetas por debajo de cabecera.php (principal -> aplicacion -> web)
include_once(dirname(__FILE__) . "/../../cabecera.php");

//controlador
// "inicio" lleva enlace a la página principal; "modificar mueble" es donde estoy, sin enlace.
$barra = [
    [
        "TEXTO" => "inicio",
        "ENLACE" => "/aplicacion/principal/index.php",
        "ADICIONAL" => " &gt; "
    ],
    [
        "TEXTO" => "modificar mueble"
    ],
];

//dibuja la plantilla de la vista
inicioCabecera("tienda");
cabecera();
finCabecera();
inicioCuerpo("MODIFICAR MUEBLE", $barra);
cuerpo($muebles);  // se lo paso como parámetro en vez de usar global
finCuerpo();
// **********************************************************

//vista
function cabecera() {}

//vista
/**
 * Cuerpo de la página. Averigua qué mueble hay que modificar, en cuál de los dos momentos
 * estoy y qué hay que mostrar (error, formulario o resumen).
 *
 * @param array $muebles Array con los muebles (índices a partir de 1)
 */
function cuerpo(array $muebles)
{
    // Qué mueble modifico: lo convierto a entero con filter_var (false si no lo es) y compruebo
    // que existe. Si alguien abre esta página sin pasar por index.php, no habrá mueble válido.
    $indice = filter_var($_POST['indice'] ?? null, FILTER_VALIDATE_INT);
    $valido = $indice !== false && isset($muebles[$indice]);

    $errores = [];
    $guardado = false;
    $valores = [];

    if ($valido) {
        $mueble = $muebles[$indice];
        $enviado = isset($_POST['guardar']);   // false = momento A, true = momento B

        if ($enviado) {
            $errores = validarDatos($mueble);
            if (!$errores) {
                // Todo correcto: actualizo el mueble real
                actualizarMueble($mueble);
                $guardado = true;
            }
        }
        // Valores para el formulario: lo que escribió (si hay errores) o los datos actuales
        $valores = valoresFormulario($mueble, $enviado);
    }

    // Campos que tienen todos los muebles: name => texto de la etiqueta
    $camposBase = [
        'nombre'     => 'Nombre',
        'fabricante' => 'Fabricante',
        'pais'       => 'País',
        'anio'       => 'Año de fabricación',
        'fechaini'   => 'Inicio de venta (dd/mm/aaaa)',
        'fechafin'   => 'Fin de venta (dd/mm/aaaa)',
        'precio'     => 'Precio',
    ];
?>
    <?php if (!$valido): ?>
        <p>No se ha indicado ningún mueble válido para modificar.</p>

    <?php elseif ($guardado): ?>
        <?php // Resumen: leo los datos con los get del mueble, que son los que de verdad se han
        // guardado (el nombre ya está en mayúsculas y el fabricante lleva FaMu:). 
        ?>
        <h2>Mueble <?= $indice ?> modificado</h2>
        <p>Resumen de los datos recogidos:</p>
        <p><b>Tipo:</b> <?= get_class($mueble) ?></p>
        <p><b>Nombre:</b> <?= htmlspecialchars($mueble->getNombre()) ?></p>
        <p><b>Fabricante:</b> <?= htmlspecialchars($mueble->getFabricante()) ?></p>
        <p><b>País:</b> <?= htmlspecialchars($mueble->getPais()) ?></p>
        <p><b>Año de fabricación:</b> <?= $mueble->getAnio() ?></p>
        <p><b>Inicio de venta:</b> <?= $mueble->getFechaIniVenta() ?></p>
        <p><b>Fin de venta:</b> <?= $mueble->getFechaFinVenta() ?></p>
        <p><b>Precio:</b> <?= $mueble->getPrecio() ?> €</p>
        <?php foreach ($mueble->damePropiedadesAdicionales() as $propiedad): ?>
            <?php $get = 'get' . $propiedad; ?>
            <p><b><?= $propiedad ?>:</b> <?= htmlspecialchars((string) $mueble->$get()) ?></p>
        <?php endforeach; ?>
        <p>Aviso: al no usar sesiones, al volver a Index el mueble aparecerá con sus datos originales.</p>

    <?php else: ?>
        <?php if ($errores): ?>
            <p><b>No se han guardado los cambios. Hay errores:</b></p>
            <ul>
                <?php foreach ($errores as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <h2>Modificar el mueble <?= $indice ?> (<?= get_class($mueble) ?>)</h2>
        <?php // El índice viaja en un campo oculto para no perder qué mueble estoy modificando.
        // Los campos propios del tipo los saco de damePropiedadesAdicionales(), así sirve
        // para MuebleReciclado y MuebleTradicional. htmlspecialchars con ENT_QUOTES evita
        // que unas comillas escritas por el usuario rompan el atributo value. 
        ?>
        <form method="post" action="/aplicacion/principal/modificar.php">
            <input type="hidden" name="indice" value="<?= $indice ?>">
            <table>
                <?php foreach ($camposBase as $nombre => $etiqueta): ?>
                    <tr>
                        <td><label for="<?= $nombre ?>"><?= $etiqueta ?>:</label></td>
                        <td><input type="text" id="<?= $nombre ?>" name="<?= $nombre ?>" value="<?= htmlspecialchars($valores[$nombre] ?? '', ENT_QUOTES) ?>"></td>
                    </tr>
                <?php endforeach; ?>
                <?php foreach ($mueble->damePropiedadesAdicionales() as $propiedad): ?>
                    <tr>
                        <td><label for="<?= $propiedad ?>"><?= $propiedad ?>:</label></td>
                        <td><input type="text" id="<?= $propiedad ?>" name="<?= $propiedad ?>" value="<?= htmlspecialchars($valores[$propiedad] ?? '', ENT_QUOTES) ?>"></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <button type="submit" name="guardar" value="1">Guardar cambios</button>
        </form>
    <?php endif; ?>

    <p><a href="/aplicacion/principal/index.php">Ir a Index.php</a></p>
<?php
}

// ===================== FUNCIONES AUXILIARES =====================

/**
 * Devuelve un dato enviado por el formulario, siempre como texto.
 * Alguien podría enviar un array en lugar de un texto (nombre[]=x); si pasa, devuelvo
 * cadena vacía para no romper las funciones que esperan un texto.
 *
 * @param string $clave Nombre del campo del formulario
 * @return string Valor enviado, o '' si no existe o no es un texto
 */
function dato(string $clave): string
{
    $valor = $_POST[$clave] ?? '';
    return is_string($valor) ? $valor : '';
}

/**
 * Devuelve los valores que tienen que llevar los campos del formulario: lo que escribió el
 * usuario (momento B, para que no tenga que escribirlo otra vez si hay errores) o los datos
 * actuales del mueble (momento A). Las propiedades extra las saco con
 * damePropiedadesAdicionales() y 'get' + nombre.
 *
 * @param MuebleBase $mueble  Mueble que se modifica
 * @param bool       $enviado true si ya se envió el formulario
 * @return array Valores, con la misma clave que el name de cada campo
 */
function valoresFormulario(MuebleBase $mueble, bool $enviado): array
{
    $valores = [];

    if ($enviado) {
        foreach (['nombre', 'fabricante', 'pais', 'anio', 'fechaini', 'fechafin', 'precio'] as $clave) {
            $valores[$clave] = dato($clave);
        }
        foreach ($mueble->damePropiedadesAdicionales() as $propiedad) {
            $valores[$propiedad] = dato($propiedad);
        }
    } else {
        $valores = [
            'nombre'     => $mueble->getNombre(),
            'fabricante' => $mueble->getFabricante(),
            'pais'       => $mueble->getPais(),
            'anio'       => (string) $mueble->getAnio(),
            'fechaini'   => $mueble->getFechaIniVenta(),
            'fechafin'   => $mueble->getFechaFinVenta(),
            'precio'     => (string) $mueble->getPrecio(),
        ];
        foreach ($mueble->damePropiedadesAdicionales() as $propiedad) {
            $get = 'get' . $propiedad;
            $valores[$propiedad] = (string) $mueble->$get();
        }
    }

    return $valores;
}

/**
 * Valida todos los datos enviados en el formulario.
 * Combina dos formas de validación:
 *   1) Con condiciones directas para comprobar formato básico/obligatoriedad de fechas y números.
 *   2) Con una instancia auxiliar (clone) llamando a sus setters para validar reglas de negocio.
 *
 * @param MuebleBase $mueble Mueble original a validar
 * @return array Lista de mensajes de error (vacía si todo es válido)
 */
function validarDatos(MuebleBase $mueble): array
{
    $errores = [];

    // 1) Validación directa con condiciones sobre tipos/formatos básicos
    $anioRaw   = trim(dato('anio'));
    $precioRaw = str_replace(',', '.', trim(dato('precio')));
    $fechaIni  = trim(dato('fechaini'));
    $fechaFin  = trim(dato('fechafin'));

    if ($anioRaw === '' || filter_var($anioRaw, FILTER_VALIDATE_INT) === false) {
        $errores[] = "El año de fabricación debe ser un número entero.";
    }

    if ($precioRaw === '' || filter_var($precioRaw, FILTER_VALIDATE_FLOAT) === false) {
        $errores[] = "El precio debe ser un número válido.";
    }

    if (!preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $fechaIni)) {
        $errores[] = "La fecha de inicio de venta debe tener formato dd/mm/aaaa.";
    }

    if (!preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $fechaFin)) {
        $errores[] = "La fecha de fin de venta debe tener formato dd/mm/aaaa.";
    }

    // 2) Validación mediante instancia auxiliar llamando a sus métodos set
    $auxiliar = clone $mueble;

    if (!$auxiliar->setNombre(dato('nombre'))) {
        $errores[] = "El nombre no es válido.";
    }
    if (!$auxiliar->setFabricante(dato('fabricante'))) {
        $errores[] = "El fabricante no es válido.";
    }
    if (!$auxiliar->setPais(dato('pais'))) {
        $errores[] = "El país no es válido.";
    }
    if (filter_var($anioRaw, FILTER_VALIDATE_INT) !== false && !$auxiliar->setAnio((int) $anioRaw)) {
        $errores[] = "El año de fabricación está fuera del rango permitido.";
    }
    if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $fechaIni) && !$auxiliar->setFechaIniVenta($fechaIni)) {
        $errores[] = "La fecha de inicio de venta no es válida.";
    }
    if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $fechaFin) && !$auxiliar->setFechaFinVenta($fechaFin)) {
        $errores[] = "La fecha de fin de venta no es válida o es anterior a la de inicio.";
    }
    if (filter_var($precioRaw, FILTER_VALIDATE_FLOAT) !== false && !$auxiliar->setPrecio((float) $precioRaw)) {
        $errores[] = "El precio no es válido.";
    }

    // Validación de las propiedades propias de cada subclase (PorcentajeReciclado, Peso, etc.)
    foreach ($mueble->damePropiedadesAdicionales() as $propiedad) {
        $set = 'set' . $propiedad;
        $valorProp = trim(dato($propiedad));

        if ($valorProp === '') {
            $errores[] = "El campo $propiedad es obligatorio.";
            continue;
        }

        $entero = filter_var($valorProp, FILTER_VALIDATE_INT);
        $decimal = filter_var(str_replace(',', '.', $valorProp), FILTER_VALIDATE_FLOAT);
        $param = ($entero !== false) ? $entero : (($decimal !== false) ? $decimal : $valorProp);

        if (!$auxiliar->$set($param)) {
            $errores[] = "El valor de $propiedad no es válido.";
        }
    }

    return $errores;
}

/**
 * Actualiza el mueble real una vez que todos los datos han pasado la validación.
 *
 * @param MuebleBase $mueble Mueble original a modificar
 */
function actualizarMueble(MuebleBase $mueble): void
{
    $mueble->setNombre(dato('nombre'));
    $mueble->setFabricante(dato('fabricante'));
    $mueble->setPais(dato('pais'));
    $mueble->setAnio((int) trim(dato('anio')));
    $mueble->setFechaIniVenta(trim(dato('fechaini')));
    $mueble->setFechaFinVenta(trim(dato('fechafin')));
    $mueble->setPrecio((float) str_replace(',', '.', trim(dato('precio'))));

    foreach ($mueble->damePropiedadesAdicionales() as $propiedad) {
        $set = 'set' . $propiedad;
        $valorProp = trim(dato($propiedad));
        $entero = filter_var($valorProp, FILTER_VALIDATE_INT);
        $decimal = filter_var(str_replace(',', '.', $valorProp), FILTER_VALIDATE_FLOAT);
        $param = ($entero !== false) ? $entero : (($decimal !== false) ? $decimal : $valorProp);

        $mueble->$set($param);
    }
}
