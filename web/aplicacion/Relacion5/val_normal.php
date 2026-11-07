<?php
/* 
   Librería para validar datos sin usar la librería filter, solo con comparaciones
   y expresiones regulares.
   Le pongo el espacio de nombres VALNORMAL para poder usarla a la vez que la otra. 
*/

namespace VALNORMAL;

// Compruebo que el entero esté entre $min y $max
// Como el parámetro es int ya sé que es un entero, solo miro el rango
function validaEntero(int &$var, int $min, int $max, int $defecto): bool
{
    if ($var >= $min && $var <= $max) {
        return true;
    }
    // Si no cumple le pongo el valor por defecto
    $var = $defecto;
    return false;
}

// Compruebo que el real esté entre $min y $max
function validaReal(float &$var, float $min, float $max, float $defecto): bool
{
    if ($var >= $min && $var <= $max) {
        return true;
    }
    $var = $defecto;
    return false;
}

// Compruebo que sea una fecha dd/mm/aaaa
// Acepto 1/2/2023 y lo dejo como 01/02/2023
function validaFecha(string &$var, string $defecto): bool
{
    // Quito los espacios del principio y del final
    $texto = trim($var);

    // Miro que tenga el formato (día y mes con 1 o 2 cifras, año con 4) y que la fecha exista
    // checkdate pide primero el mes, después el día y después el año
    if (preg_match('#^(\d{1,2})/(\d{1,2})/(\d{4})$#', $texto, $partes) === 1
        && checkdate((int) $partes[2], (int) $partes[1], (int) $partes[3])) {
        // Lo dejo con 2 cifras en día y mes y 4 en el año
        $var = sprintf("%02d/%02d/%04d", (int) $partes[1], (int) $partes[2], (int) $partes[3]);
        return true;
    }
    $var = $defecto;
    return false;
}

// Compruebo que sea una hora hh:mm:ss
// Acepto 0:5:1 y lo dejo como 00:05:01
function validaHora(string &$var, string $defecto): bool
{
    $texto = trim($var);

    // Miro el formato y que las horas sean hasta 23, y los minutos y segundos hasta 59
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

// Compruebo que el email tenga la forma aaaa@bbbb.ccc
function validaEmail(string &$var, string $defecto): bool
{
    $texto = trim($var);

    if (preg_match('/^[A-Za-z0-9._%+-]+@[A-Za-z0-9-]+(\.[A-Za-z0-9-]+)*\.[A-Za-z]{2,}$/', $texto) === 1) {
        // Lo único que le hago es quitarle los espacios
        $var = $texto;
        return true;
    }
    $var = $defecto;
    return false;
}

// Compruebo que la cadena no pase de $longitud caracteres
// Uso mb_strlen porque cuenta bien las tildes y las ñ
function validaCadena(string &$var, int $longitud, string $defecto): bool
{
    if (mb_strlen($var) <= $longitud) {
        return true;
    }
    $var = $defecto;
    return false;
}

// Compruebo que la cadena cumpla una expresión regular
function validaExpresion(string &$var, string $expresion, string $defecto): bool
{
    // preg_match devuelve 1 si coincide
    if (preg_match($expresion, $var) === 1) {
        return true;
    }
    $var = $defecto;
    return false;
}

// Compruebo que $var sea uno de los valores de $posibles ($tipo = 1)
// o una de sus claves ($tipo = 2)
function validaRango(mixed $var, array $posibles, int $tipo = 1): bool
{
    if ($tipo === 2) {
        // Miro las claves del array
        return (is_int($var) || is_string($var)) && array_key_exists($var, $posibles);
    }
    // El true del final compara también el tipo (el "3" no es igual que el 3)
    return in_array($var, $posibles, true);
}
