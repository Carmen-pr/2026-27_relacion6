<?php

namespace VALNORMAL;

/**
 * Comprueba que el entero esté entre los límites, ambos incluidos.
 *
 * Si no cumple el rango, sustituye la variable por el valor por defecto.
 */
function validaEntero(int &$var, int $min, int $max, int $defecto): bool
{
    if ($var < $min || $var > $max) {
        $var = $defecto;
        return false;
    }

    return true;
}

/**
 * Comprueba que el real sea finito y esté entre los límites, ambos incluidos.
 *
 * Los valores no finitos o fuera del rango se sustituyen por el valor por defecto.
 */
function validaReal(float &$var, float $min, float $max, float $defecto): bool
{
    if (!is_finite($var) || $var < $min || $var > $max) {
        $var = $defecto;
        return false;
    }

    return true;
}

/**
 * Valida una fecha dd/mm/aaaa y normaliza el día y el mes a dos cifras.
 *
 * checkdate comprueba que la fecha exista, incluidos los años bisiestos.
 * Si el formato o la fecha no son válidos, asigna el valor por defecto.
 */
function validaFecha(string &$var, string $defecto): bool
{
    if (
        preg_match(
            '/\A([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})\z/',
            $var,
            $partes
        ) !== 1
        || !checkdate((int) $partes[2], (int) $partes[1], (int) $partes[3])
    ) {
        $var = $defecto;
        return false;
    }

    $var = sprintf(
        '%02d/%02d/%04d',
        (int) $partes[1],
        (int) $partes[2],
        (int) $partes[3]
    );
    return true;
}

/**
 * Valida una hora hh:mm:ss y normaliza cada componente a dos cifras.
 *
 * Comprueba que la hora esté entre 0 y 23 y los minutos y segundos entre 0 y 59.
 * Si el formato o alguno de los componentes no son válidos, usa el valor por defecto.
 */
function validaHora(string &$var, string $defecto): bool
{
    if (
        preg_match(
            '/\A([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})\z/',
            $var,
            $partes
        ) !== 1
    ) {
        $var = $defecto;
        return false;
    }

    $hora = (int) $partes[1];
    $minuto = (int) $partes[2];
    $segundo = (int) $partes[3];

    if ($hora > 23 || $minuto > 59 || $segundo > 59) {
        $var = $defecto;
        return false;
    }

    $var = sprintf('%02d:%02d:%02d', $hora, $minuto, $segundo);
    return true;
}
