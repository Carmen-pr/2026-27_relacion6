<?php

namespace VALFILTER;

/**
 * Valida el entero con FILTER_VALIDATE_INT y comprueba el rango inclusivo.
 *
 * Si el filtro falla o el valor queda fuera de rango, asigna el valor por defecto.
 */
function validaEntero(int &$var, int $min, int $max, int $defecto): bool
{
    $saneado = filter_var($var, FILTER_VALIDATE_INT);

    if ($saneado === false || $saneado < $min || $saneado > $max) {
        $var = $defecto;
        return false;
    }

    $var = $saneado;
    return true;
}

/**
 * Valida el real con FILTER_VALIDATE_FLOAT y comprueba el rango inclusivo.
 *
 * También rechaza valores no finitos; ante cualquier error, usa el valor por defecto.
 */
function validaReal(float &$var, float $min, float $max, float $defecto): bool
{
    $saneado = filter_var($var, FILTER_VALIDATE_FLOAT);

    if (
        $saneado === false
        || !is_finite((float) $saneado)
        || $saneado < $min
        || $saneado > $max
    ) {
        $var = $defecto;
        return false;
    }

    $var = (float) $saneado;
    return true;
}

/**
 * Comprueba el formato con FILTER_VALIDATE_REGEXP y la fecha con checkdate.
 *
 * Si ambas validaciones pasan, normaliza el día y el mes a dos cifras.
 * En caso contrario, sustituye la entrada por el valor por defecto.
 */
function validaFecha(string &$var, string $defecto): bool
{
    $saneado = filter_var(
        $var,
        FILTER_VALIDATE_REGEXP,
        [
            'options' => [
                'regexp' => '~\A([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})\z~',
            ],
        ]
    );

    if ($saneado === false) {
        $var = $defecto;
        return false;
    }

    [$dia, $mes, $anio] = array_map('intval', explode('/', $saneado));

    if (!checkdate($mes, $dia, $anio)) {
        $var = $defecto;
        return false;
    }

    $var = sprintf('%02d/%02d/%04d', $dia, $mes, $anio);
    return true;
}

/**
 * Comprueba el formato con FILTER_VALIDATE_REGEXP y los límites de la hora.
 *
 * Normaliza los componentes a dos cifras o asigna el valor por defecto si no son válidos.
 */
function validaHora(string &$var, string $defecto): bool
{
    $saneado = filter_var(
        $var,
        FILTER_VALIDATE_REGEXP,
        [
            'options' => [
                'regexp' => '~\A([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})\z~',
            ],
        ]
    );

    if ($saneado === false) {
        $var = $defecto;
        return false;
    }

    [$hora, $minuto, $segundo] = array_map('intval', explode(':', $saneado));

    if ($hora > 23 || $minuto > 59 || $segundo > 59) {
        $var = $defecto;
        return false;
    }

    $var = sprintf('%02d:%02d:%02d', $hora, $minuto, $segundo);
    return true;
}
