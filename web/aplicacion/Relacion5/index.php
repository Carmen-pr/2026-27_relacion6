<?php
declare(strict_types=1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/cabecera.php';
require_once __DIR__ . '/val_normal.php';
require_once __DIR__ . '/val_filter.php';

// Las dos librerías se incluyen juntas para comparar sus resultados.

/**
 * Ejecuta un caso de prueba y comprueba el resultado y el valor saneado.
 *
 * El validador modifica la variable por referencia. Se compara tanto su
 * booleano de retorno como el valor final con los resultados esperados.
 *
 * @return array{valido: bool, valor: int|float|string, correcto: bool}
 */
function comprobarCaso(
    int|float|string $entrada,
    callable $validador,
    bool $validoEsperado,
    int|float|string $valorEsperado
): array {
    $valor = $entrada;
    $valido = $validador($valor);

    return [
        'valido' => $valido,
        'valor' => $valor,
        'correcto' => $valido === $validoEsperado && $valor === $valorEsperado,
    ];
}

/**
 * Controlador: prepara casos válidos e inválidos para ambas librerías.
 */
function obtenerResultados(): array
{
    $casos = [];

    // Cada prueba contiene: descripción, entrada, validador, validez esperada y valor esperado.
    foreach ([
        'VALNORMAL' => [
            // Validación directa: casos válidos, fuera de rango y con formato que se normaliza.
            [
                'Entero dentro del rango',
                12,
                static function (int &$valor): bool {
                    return \VALNORMAL\validaEntero($valor, 0, 20, 0);
                },
                true,
                12,
            ],
            [
                'Entero fuera del rango',
                25,
                static function (int &$valor): bool {
                    return \VALNORMAL\validaEntero($valor, 0, 20, 0);
                },
                false,
                0,
            ],
            [
                'Real dentro del rango',
                1250.5,
                static function (float &$valor): bool {
                    return \VALNORMAL\validaReal($valor, 1000.0, 150000.0, 1100.0);
                },
                true,
                1250.5,
            ],
            [
                'Real fuera del rango',
                999.0,
                static function (float &$valor): bool {
                    return \VALNORMAL\validaReal($valor, 1000.0, 150000.0, 1100.0);
                },
                false,
                1100.0,
            ],
            [
                'Fecha con día y mes de una cifra',
                '1/2/2023',
                static function (string &$valor): bool {
                    return \VALNORMAL\validaFecha($valor, '01/01/2000');
                },
                true,
                '01/02/2023',
            ],
            [
                'Fecha inexistente',
                '31/02/2023',
                static function (string &$valor): bool {
                    return \VALNORMAL\validaFecha($valor, '01/01/2000');
                },
                false,
                '01/01/2000',
            ],
            [
                'Hora con componentes de una cifra',
                '0:5:1',
                static function (string &$valor): bool {
                    return \VALNORMAL\validaHora($valor, '00:00:00');
                },
                true,
                '00:05:01',
            ],
            [
                'Hora fuera de rango',
                '24:00:00',
                static function (string &$valor): bool {
                    return \VALNORMAL\validaHora($valor, '00:00:00');
                },
                false,
                '00:00:00',
            ],
        ],
        'VALFILTER' => [
            // Los mismos casos permiten comparar los resultados de la librería FILTER.
            [
                'Entero dentro del rango',
                12,
                static function (int &$valor): bool {
                    return \VALFILTER\validaEntero($valor, 0, 20, 0);
                },
                true,
                12,
            ],
            [
                'Entero fuera del rango',
                25,
                static function (int &$valor): bool {
                    return \VALFILTER\validaEntero($valor, 0, 20, 0);
                },
                false,
                0,
            ],
            [
                'Real dentro del rango',
                1250.5,
                static function (float &$valor): bool {
                    return \VALFILTER\validaReal($valor, 1000.0, 150000.0, 1100.0);
                },
                true,
                1250.5,
            ],
            [
                'Real fuera del rango',
                999.0,
                static function (float &$valor): bool {
                    return \VALFILTER\validaReal($valor, 1000.0, 150000.0, 1100.0);
                },
                false,
                1100.0,
            ],
            [
                'Fecha con día y mes de una cifra',
                '1/2/2023',
                static function (string &$valor): bool {
                    return \VALFILTER\validaFecha($valor, '01/01/2000');
                },
                true,
                '01/02/2023',
            ],
            [
                'Fecha inexistente',
                '31/02/2023',
                static function (string &$valor): bool {
                    return \VALFILTER\validaFecha($valor, '01/01/2000');
                },
                false,
                '01/01/2000',
            ],
            [
                'Hora con componentes de una cifra',
                '0:5:1',
                static function (string &$valor): bool {
                    return \VALFILTER\validaHora($valor, '00:00:00');
                },
                true,
                '00:05:01',
            ],
            [
                'Hora fuera de rango',
                '24:00:00',
                static function (string &$valor): bool {
                    return \VALFILTER\validaHora($valor, '00:00:00');
                },
                false,
                '00:00:00',
            ],
        ],
    ] as $biblioteca => $pruebas) {
        foreach ($pruebas as [$nombre, $entrada, $validador, $validoEsperado, $valorEsperado]) {
            $resultado = comprobarCaso($entrada, $validador, $validoEsperado, $valorEsperado);

            // Guarda la entrada original junto al resultado para mostrar y revisar cada prueba.
            $casos[] = [
                'biblioteca' => $biblioteca,
                'nombre' => $nombre,
                'entrada' => $entrada,
                'valido' => $resultado['valido'],
                'valor' => $resultado['valor'],
                'correcto' => $resultado['correcto'],
            ];
        }
    }

    return $casos;
}

// Vista: presenta cada prueba y señala si el saneamiento coincide con lo esperado.
function mostrarResultados(array $casos): void
{
    // Escapa el contenido dinámico antes de insertarlo en el HTML.
    $escapar = static fn (int|float|string $valor): string =>
        htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

    echo '<h2>Comprobación de los cuatro primeros validadores</h2>';
    // El contenedor permite desplazar la tabla horizontalmente en pantallas estrechas.
    echo '<div class="tabla-validaciones-contenedor">';
    echo '<table class="tabla-validaciones">';
    echo '<thead><tr>';
    echo '<th>Librería</th>';
    echo '<th>Prueba</th>';
    echo '<th>Entrada</th>';
    echo '<th>Válido</th>';
    echo '<th>Valor saneado</th>';
    echo '<th>Comprobación</th>';
    echo '</tr></thead>';
    echo '<tbody>';

    foreach ($casos as $caso) {
        echo '<tr>';
        echo '<td>' . $escapar($caso['biblioteca']) . '</td>';
        echo '<td>' . $escapar($caso['nombre']) . '</td>';
        echo '<td>' . $escapar($caso['entrada']) . '</td>';
        echo '<td>' . ($caso['valido'] ? 'Sí' : 'No') . '</td>';
        echo '<td>' . $escapar($caso['valor']) . '</td>';
        echo '<td>' . ($caso['correcto'] ? 'Correcto' : 'Error') . '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
    echo '</div>';
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