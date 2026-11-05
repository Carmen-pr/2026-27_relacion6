<?php
/* val_normal.php
   Librería de validación "normal": tipos, comparaciones y expresiones regulares.
   Espacio de nombres: VALNORMAL. */

namespace VALNORMAL;

// Entero entre $min y $max. El tipo int ya garantiza que es un entero.
function validaEntero(int &$var, int $min, int $max, int $defecto): bool
{
    if ($var >= $min && $var <= $max) {
        return true;
    }
    $var = $defecto;
    return false;
}

// Real entre $min y $max
function validaReal(float &$var, float $min, float $max, float $defecto): bool
{
    if ($var >= $min && $var <= $max) {
        return true;
    }
    $var = $defecto;
    return false;
}

// Fecha dd/mm/aaaa. Se acepta 1/2/2023 y se sanea a 01/02/2023.
function validaFecha(string &$var, string $defecto): bool
{
    $texto = trim($var);
    // 1 o 2 cifras para día y mes, 4 para el año, y además tiene que ser una fecha real
    if (preg_match('#^(\d{1,2})/(\d{1,2})/(\d{4})$#', $texto, $partes) === 1
        && checkdate((int) $partes[2], (int) $partes[1], (int) $partes[3])) {
        // Saneado: dos cifras para día y mes, cuatro para el año
        $var = sprintf("%02d/%02d/%04d", (int) $partes[1], (int) $partes[2], (int) $partes[3]);
        return true;
    }
    $var = $defecto;
    return false;
}

// Hora hh:mm:ss. Se acepta 0:5:1 y se sanea a 00:05:01.
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

// Email con el formato aaaa@bbbb.ccc
function validaEmail(string &$var, string $defecto): bool
{
    $texto = trim($var);
    if (preg_match('/^[A-Za-z0-9._%+-]+@[A-Za-z0-9-]+(\.[A-Za-z0-9-]+)*\.[A-Za-z]{2,}$/', $texto) === 1) {
        $var = $texto;   // saneado: sin espacios al principio ni al final
        return true;
    }
    $var = $defecto;
    return false;
}

// Cadena de longitud máxima $longitud (se cuentan caracteres, no bytes)
function validaCadena(string &$var, int $longitud, string $defecto): bool
{
    if (mb_strlen($var) <= $longitud) {
        return true;
    }
    $var = $defecto;
    return false;
}

// Cadena que cumple una expresión regular
function validaExpresion(string &$var, string $expresion, string $defecto): bool
{
    // preg_match devuelve 1 si coincide, 0 si no coincide y false si la expresión está mal escrita
    if (preg_match($expresion, $var) === 1) {
        return true;
    }
    $var = $defecto;
    return false;
}

// $var es uno de los elementos de $posibles ($tipo = 1) o una de sus claves ($tipo = 2)
function validaRango(mixed $var, array $posibles, int $tipo = 1): bool
{
    if ($tipo === 2) {
        return (is_int($var) || is_string($var)) && array_key_exists($var, $posibles);
    }
    return in_array($var, $posibles, true);   // true: compara también el tipo
}
