<?php
/* val_filter.php
   Librería de validación con la librería FILTER de PHP.
   Espacio de nombres: VALFILTER.
   Donde FILTER no tiene un filtro concreto, se copia lo hecho en val_normal.php. */

namespace VALFILTER;

// Entero entre $min y $max
function validaEntero(int &$var, int $min, int $max, int $defecto): bool
{
    $opciones = ["options" => ["min_range" => $min, "max_range" => $max]];
    $resultado = filter_var($var, FILTER_VALIDATE_INT, $opciones);
    // Se compara con === false porque 0 es un valor válido
    if ($resultado === false) {
        $var = $defecto;
        return false;
    }
    $var = $resultado;   // valor saneado
    return true;
}

// Real entre $min y $max
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

// Fecha dd/mm/aaaa: FILTER no tiene un filtro para fechas, se copia la versión normal
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

// Hora hh:mm:ss: tampoco hay filtro para horas, se copia la versión normal
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

// Email: se sanea y se valida con filtros
function validaEmail(string &$var, string $defecto): bool
{
    // FILTER_SANITIZE_EMAIL quita los caracteres que no pueden aparecer en un email
    $limpio = filter_var(trim($var), FILTER_SANITIZE_EMAIL);
    // FILTER_VALIDATE_EMAIL comprueba el formato aaaa@bbbb.ccc
    if ($limpio !== false && filter_var($limpio, FILTER_VALIDATE_EMAIL) !== false) {
        $var = $limpio;
        return true;
    }
    $var = $defecto;
    return false;
}

// Cadena de longitud máxima: no hay filtro para la longitud, se copia la versión normal
function validaCadena(string &$var, int $longitud, string $defecto): bool
{
    if (mb_strlen($var) <= $longitud) {
        return true;
    }
    $var = $defecto;
    return false;
}

// Expresión regular: FILTER_VALIDATE_REGEXP
function validaExpresion(string &$var, string $expresion, string $defecto): bool
{
    $opciones = ["options" => ["regexp" => $expresion]];
    if (filter_var($var, FILTER_VALIDATE_REGEXP, $opciones) === false) {
        $var = $defecto;
        return false;
    }
    return true;
}

// Rango: no hay filtro para esto, se copia la versión normal
function validaRango(mixed $var, array $posibles, int $tipo = 1): bool
{
    if ($tipo === 2) {
        return (is_int($var) || is_string($var)) && array_key_exists($var, $posibles);
    }
    return in_array($var, $posibles, true);
}
