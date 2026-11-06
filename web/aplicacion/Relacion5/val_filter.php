<?php
/* val_filter.php
   Librería para validar datos usando la librería filter de PHP.
   Le pongo el espacio de nombres VALFILTER.
   Donde filter no tiene nada que me sirva, copio lo que hice en val_normal.php. */

namespace VALFILTER;

// Compruebo que el entero esté entre $min y $max
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

// Compruebo que el real esté entre $min y $max
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

// Fecha dd/mm/aaaa
// En filter no hay nada para fechas, así que copio lo de val_normal
function validaFecha(string &$var, string $defecto): bool
{
    $texto = trim($var);

    if (preg_match('#^(\d{1,2})/(\d{1,2})/(\d{4})$#', $texto, $partes) === 1
        && checkdate((int) $partes[2], (int) $partes[1], (int) $partes[3])) {
        $var = sprintf("%02d/%02d/%04d", (int) $partes[1], (int) $partes[2], (int) $partes[3]);
        return true;
    }
    $var = $defecto;
    return false;
}

// Hora hh:mm:ss
// Tampoco hay nada para horas en filter, copio lo de val_normal
function validaHora(string &$var, string $defecto): bool
{
    $texto = trim($var);

    if (preg_match('#^(\d{1,2}):(\d{1,2}):(\d{1,2})$#', $texto, $partes) === 1
        && (int) $partes[1] <= 23
        && (int) $partes[2] <= 59
        && (int) $partes[3] <= 59) {
        $var = sprintf("%02d:%02d:%02d", (int) $partes[1], (int) $partes[2], (int) $partes[3]);
        return true;
    }
    $var = $defecto;
    return false;
}

// Email: primero lo saneo y después lo valido, los dos con filter
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

// Longitud de la cadena
// En filter no hay nada para la longitud, copio lo de val_normal
function validaCadena(string &$var, int $longitud, string $defecto): bool
{
    if (mb_strlen($var) <= $longitud) {
        return true;
    }
    $var = $defecto;
    return false;
}

// Expresión regular, con FILTER_VALIDATE_REGEXP
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

// Rango
// En filter no hay nada para esto, copio lo de val_normal
function validaRango(mixed $var, array $posibles, int $tipo = 1): bool
{
    if ($tipo === 2) {
        return (is_int($var) || is_string($var)) && array_key_exists($var, $posibles);
    }
    return in_array($var, $posibles, true);
}
