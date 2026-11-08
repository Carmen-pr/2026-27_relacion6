<?php
/* 
   Librería para validar datos sin usar la librería filter, solo con comparaciones
   y expresiones regulares.
   Le pongo el espacio de nombres VALNORMAL para poder usarla a la vez que la otra. 
*/

namespace VALNORMAL;

/**
 * Compruebo que $var sea un entero entre $min y $max (ambos incluidos).
 * Como el parámetro es int, solo hace falta mirar el rango.
 *
 * @param int &$var Entero a validar. Por referencia: al terminar tiene el valor arreglado o $defecto.
 * @param int $min Valor mínimo permitido.
 * @param int $max Valor máximo permitido.
 * @param int $defecto Valor que se asigna si no es válido.
 * @return bool true si es correcto, false si no.
 */
function validaEntero(int &$var, int $min, int $max, int $defecto): bool
{
    if ($var >= $min && $var <= $max) {
        return true;
    }
    // Si no cumple le pongo el valor por defecto
    $var = $defecto;
    return false;
}

/**
 * Compruebo que $var sea un real entre $min y $max (ambos incluidos).
 *
 * @param float &$var Real a validar. Por referencia: al terminar tiene el valor arreglado o $defecto.
 * @param float $min Valor mínimo permitido.
 * @param float $max Valor máximo permitido.
 * @param float $defecto Valor que se asigna si no es válido.
 * @return bool true si es correcto, false si no.
 */
function validaReal(float &$var, float $min, float $max, float $defecto): bool
{
    if ($var >= $min && $var <= $max) {
        return true;
    }
    $var = $defecto;
    return false;
}

/**
 * Compruebo que $var sea una fecha dd/mm/aaaa que exista.
 * Acepta 1/2/2023 y la deja arreglada como 01/02/2023.
 *
 * @param string &$var Fecha a validar. Por referencia: al terminar tiene la fecha arreglada o $defecto.
 * @param string $defecto Valor que se asigna si no es válida.
 * @return bool true si es correcta, false si no.
 */
function validaFecha(string &$var, string $defecto): bool
{
    // Quito los espacios del principio y del final
    $texto = trim($var);

    // Miro que tenga el formato (día y mes con 1 o 2 cifras, año con 4) y que la fecha exista
    // checkdate pide primero el mes, después el día y después el año
    if (
        preg_match('#^(\d{1,2})/(\d{1,2})/(\d{4})$#', $texto, $partes) === 1
        && checkdate((int) $partes[2], (int) $partes[1], (int) $partes[3])
    ) {
        // Lo dejo con 2 cifras en día y mes y 4 en el año
        $var = sprintf("%02d/%02d/%04d", (int) $partes[1], (int) $partes[2], (int) $partes[3]);
        return true;
    }
    $var = $defecto;
    return false;
}

/**
 * Compruebo que $var sea una hora hh:mm:ss correcta.
 * Acepta 0:5:1 y la deja arreglada como 00:05:01.
 *
 * @param string &$var Hora a validar. Por referencia: al terminar tiene la hora arreglada o $defecto.
 * @param string $defecto Valor que se asigna si no es válida.
 * @return bool true si es correcta, false si no.
 */
function validaHora(string &$var, string $defecto): bool
{
    $texto = trim($var);

    // Miro el formato y que las horas sean hasta 23, y los minutos y segundos hasta 59
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
 * Solo se le quitan los espacios sobrantes; la comprobación es con una expresión regular.
 *
 * @param string &$var Email a validar. Por referencia: al terminar tiene el email arreglada o $defecto.
 * @param string $defecto Valor que se asigna si no es válido.
 * @return bool true si es correcto, false si no.
 */
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

/**
 * Compruebo que $var no tenga más de $longitud caracteres.
 * Usa mb_strlen para contar bien las tildes y las ñ.
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
 * Usa preg_match, que devuelve 1 si hay coincidencia.
 *
 * @param string &$var Cadena a validar. Por referencia: se deja igual si es válida o pasa a valer $defecto.
 * @param string $expresion Expresión regular que debe cumplir.
 * @param string $defecto Valor que se asigna si no la cumple.
 * @return bool true si cumple la expresión, false si no.
 */
function validaExpresion(string &$var, string $expresion, string $defecto): bool
{
    // preg_match devuelve 1 si coincide
    if (preg_match($expresion, $var) === 1) {
        return true;
    }
    $var = $defecto;
    return false;
}

/**
 * Compruebo que $var sea uno de los valores de $posibles ($tipo = 1)
 * o una de sus claves ($tipo = 2).
 *
 * @param mixed $var Valor a buscar.
 * @param array $posibles Valores (o claves) permitidos.
 * @param int $tipo 1 para comparar con los valores, 2 para comparar con las claves.
 * @return bool true si está en el array, false si no.
 */
function validaRango(mixed $var, array $posibles, int $tipo = 1): bool
{
    if ($tipo === 2) {
        // Miro las claves del array
        return (is_int($var) || is_string($var)) && array_key_exists($var, $posibles);
    }
    // El true del final compara también el tipo (el "3" no es igual que el 3)
    return in_array($var, $posibles, true);
}
