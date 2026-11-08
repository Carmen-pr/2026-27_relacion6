<?php
/* 
   Librería para validar datos usando la librería filter de PHP.
   Le pongo el espacio de nombres VALFILTER.
   Donde filter no tiene nada que me sirva, copio lo que hice en val_normal.php. 
*/

namespace VALFILTER;

/**
 * Compruebo que $var sea un entero entre $min y $max (ambos incluidos).
 * Uso filter_var con FILTER_VALIDATE_INT y las opciones min_range y max_range.
 *
 * @param int &$var Entero a validar. Por referencia: al terminar tiene el valor arreglado o $defecto.
 * @param int $min Valor mínimo permitido.
 * @param int $max Valor máximo permitido.
 * @param int $defecto Valor que se asigna si no es válido.
 * @return bool true si es correcto, false si no.
 */
function validaEntero(int &$var, int $min, int $max, int $defecto): bool
{
    // Le paso a filter_var el rango en las opciones
    $opciones = ["options" => ["min_range" => $min, "max_range" => $max]];
    $resultado = filter_var($var, FILTER_VALIDATE_INT, $opciones);

    // Comparo con === false porque el 0 también es un valor correcto
    if ($resultado === false) {
        $var = $defecto;
        return false;
    }
    // filter_var me devuelve el número ya convertido, lo guardo en $var
    $var = $resultado;
    return true;
}

/**
 * Compruebo que $var sea un real entre $min y $max (ambos incluidos).
 * Uso filter_var con FILTER_VALIDATE_FLOAT y las opciones min_range y max_range.
 *
 * @param float &$var Real a validar. Por referencia: al terminar tiene el valor arreglado o $defecto.
 * @param float $min Valor mínimo permitido.
 * @param float $max Valor máximo permitido.
 * @param float $defecto Valor que se asigna si no es válido.
 * @return bool true si es correcto, false si no.
 */
function validaReal(float &$var, float $min, float $max, float $defecto): bool
{
    $opciones = ["options" => ["min_range" => $min, "max_range" => $max]];
    $resultado = filter_var($var, FILTER_VALIDATE_FLOAT, $opciones);

    if ($resultado === false) {
        $var = $defecto;
        return false;
    }
    $var = $resultado;
    return true;
}

/**
 * Compruebo que $var sea una fecha dd/mm/aaaa que exista.
 * Acepta 1/2/2023 y la deja arreglada como 01/02/2023.
 * En FILTER no hay nada para fechas, se usa la misma comprobación que en val_normal.php.
 *
 * @param string &$var Fecha a validar. Por referencia: al terminar tiene la fecha arreglada o $defecto.
 * @param string $defecto Valor que se asigna si no es válida.
 * @return bool true si es correcta, false si no.
 */
function validaFecha(string &$var, string $defecto): bool
{
    $texto = trim($var);

    if (
        preg_match('#^(\d{1,2})/(\d{1,2})/(\d{4})$#', $texto, $partes) === 1
        && checkdate((int) $partes[2], (int) $partes[1], (int) $partes[3])
    ) {
        $var = sprintf("%02d/%02d/%04d", (int) $partes[1], (int) $partes[2], (int) $partes[3]);
        return true;
    }
    $var = $defecto;
    return false;
}

/**
 * Compruebo que $var sea una hora hh:mm:ss correcta.
 * Acepta 0:5:1 y la dejoa arreglada como 00:05:01.
 * En FILTER no hay nada para horas, se usa la misma comprobación que en val_normal.php.
 *
 * @param string &$var Hora a validar. Por referencia: al terminar tiene la hora arreglada o $defecto.
 * @param string $defecto Valor que se asigna si no es válida.
 * @return bool true si es correcta, false si no.
 */
function validaHora(string &$var, string $defecto): bool
{
    $texto = trim($var);

    if (
        preg_match('#^(\d{1,2}):(\d{1,2}):(\d{1,2})$#', $texto, $partes) === 1
        && (int) $partes[1] <= 23
        && (int) $partes[2] <= 59
        && (int) $partes[3] <= 59
    ) {
        $var = sprintf("%02d:%02d:%02d", (int) $partes[1], (int) $partes[2], (int) $partes[3]);
        return true;
    }
    $var = $defecto;
    return false;
}

/**
 * Compruebo que $var sea un email con la forma aaaa@bbbb.ccc.
 * Primero lo arreglo con FILTER_SANITIZE_EMAIL y luego lo valido con FILTER_VALIDATE_EMAIL.
 *
 * @param string &$var Email a validar. Por referencia: al terminar tiene el email arreglada o $defecto.
 * @param string $defecto Valor que se asigna si no es válido.
 * @return bool true si es correcto, false si no.
 */
function validaEmail(string &$var, string $defecto): bool
{
    // FILTER_SANITIZE_EMAIL quita los caracteres que no pueden estar en un email
    $limpio = filter_var(trim($var), FILTER_SANITIZE_EMAIL);

    // FILTER_VALIDATE_EMAIL mira que el formato sea aaaa@bbbb.ccc
    if ($limpio !== false && filter_var($limpio, FILTER_VALIDATE_EMAIL) !== false) {
        $var = $limpio;
        return true;
    }
    $var = $defecto;
    return false;
}

/**
 * Compruebo que $var no tenga más de $longitud caracteres.
 * Uso mb_strlen para contar bien las tildes y las ñ.
 * En FILTER no hay nada para la longitud, se usa la misma comprobación que en val_normal.php.
 *
 * @param string &$var Cadena a validar. Por referencia: se deja igual si es válida o pasa a valer $defecto.
 * @param int $longitud Longitud máxima permitida.
 * @param string $defecto Valor que se asigna si no es válida.
 * @return bool true si es correcta, false si no.
 */
function validaCadena(string &$var, int $longitud, string $defecto): bool
{
    if (mb_strlen($var) <= $longitud) {
        return true;
    }
    $var = $defecto;
    return false;
}

/**
 * Compruebo que $var cumpla la expresión regular $expresion.
 * Us0 filter_var con FILTER_VALIDATE_REGEXP.
 *
 * @param string &$var Cadena a validar. Por referencia: se deja igual si es válida o pasa a valer $defecto.
 * @param string $expresion Expresión regular que debe cumplir.
 * @param string $defecto Valor que se asigna si no la cumple.
 * @return bool true si cumple la expresión, false si no.
 */
function validaExpresion(string &$var, string $expresion, string $defecto): bool
{
    // La expresión se la paso en las opciones
    $opciones = ["options" => ["regexp" => $expresion]];

    if (filter_var($var, FILTER_VALIDATE_REGEXP, $opciones) === false) {
        $var = $defecto;
        return false;
    }
    return true;
}

/**
 * Compruebo que $var sea uno de los valores de $posibles ($tipo = 1)
 * o una de sus claves ($tipo = 2).
 * En FILTER no hay nada para rangos, se usa la misma comprobación que en val_normal.php.
 *
 * @param mixed $var Valor a buscar.
 * @param array $posibles Valores (o claves) permitidos.
 * @param int $tipo 1 para comparar con los valores, 2 para comparar con las claves.
 * @return bool true si está en el array, false si no.
 */
function validaRango(mixed $var, array $posibles, int $tipo = 1): bool
{
    if ($tipo === 2) {
        return (is_int($var) || is_string($var)) && array_key_exists($var, $posibles);
    }
    return in_array($var, $posibles, true);
}
