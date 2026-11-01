<?php
declare(strict_types=1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/cabecera.php';

function obtenerDatos(): array
{
    $resultados = [];
    $errores = [];

    $probar = static function (string $titulo, callable $prueba) use (&$resultados, &$errores): void {
        try {
            $resultados[$titulo] = (string) $prueba();
        } catch (Throwable $error) {
            $errores[] = $titulo . ': ' . $error->getMessage();
        }
    };

    $probar('Instrumento y fábrica', static function (): string {
        $flauta = Flauta::CrearDesdeArray(['material' => 'bambú', 'edad' => 7]);
        $flauta->setDescripcion('Flauta de concierto');
        $flauta->envejecer();
        $copia = clone $flauta;

        return implode(' | ', [
            (string) $flauta,
            $flauta->sonido(),
            $flauta->afinar(),
            $flauta->metodoFabricacion(),
            $flauta->metodoReciclado(),
            'Copia: ' . (string) $copia,
        ]);
    });

    $probar('Acceso a una propiedad inexistente', static function (): string {
        $flauta = Flauta::CrearDesdeArray([]);

        try {
            return (string) $flauta->propiedadInexistente;
        } catch (Exception $error) {
            return 'Excepción controlada: ' . $error->getMessage();
        }
    });

    $probar('Asignación no permitida', static function (): string {
        $flauta = Flauta::CrearDesdeArray([]);

        try {
            $flauta->propiedadInexistente = 'valor';
        } catch (Exception $error) {
            return 'Excepción controlada: ' . $error->getMessage();
        }

        return 'No se lanzó la excepción esperada.';
    });

    $probar('Propiedad dinámica y recorrido', static function (): string {
        $objeto = new ClaseMisPropiedades();
        $objeto->propiedad1 = 'valor uno';
        $objeto->propiedad2 = 42;
        $objeto->propPublica = 'visible';

        $elementos = [];
        foreach ($objeto as $clave => $valor) {
            $elementos[] = $clave . '=' . (string) $valor;
        }

        return 'Lectura: ' . $objeto->propiedad1
            . ' | isset(inexistente): ' . (isset($objeto->inexistente) ? 'true' : 'false')
            . ' | ' . implode(', ', $elementos);
    });

    $probar('Protección de la propiedad privada', static function (): string {
        $objeto = new ClaseMisPropiedades();

        try {
            $objeto->_propPrivada = 99;
        } catch (Exception $error) {
            return 'Excepción controlada: ' . $error->getMessage()
                . ' | valor conservado: ' . $objeto->_propPrivada;
        }

        return 'No se lanzó la excepción esperada.';
    });

    $probar('Excepción al leer propiedad dinámica inexistente', static function (): string {
        $objeto = new ClaseMisPropiedades();

        try {
            return (string) $objeto->noAsignada;
        } catch (Exception $error) {
            return 'Excepción controlada: ' . $error->getMessage();
        }
    });

    $probar('Enumeración EstadoCivil', static function (): string {
        $valores = array_map(
            static fn (EstadoCivil $estado): string => $estado->descripcion() . '=' . $estado->valor(),
            EstadoCivil::casos()
        );

        return implode(', ', $valores);
    });

    $probar('Registro de persona', static function (): string {
        $valoresEstado = array_map(
            static fn (EstadoCivil $estado): int => $estado->valor(),
            EstadoCivil::casos()
        );
        $valorAleatorio = $valoresEstado[random_int(0, count($valoresEstado) - 1)];
        $estadoAleatorio = EstadoCivil::from($valorAleatorio);

        $persona = Persona::registrarPersona(
            'María Pérez',
            '15/08/2001',
            'Calle del Sol, 12',
            'Antequera',
            $estadoAleatorio
        );

        return (string) $persona . ' | Estado asignado desde el valor aleatorio ' . $valorAleatorio;
    });

    $probar('Validación de fecha', static function (): string {
        try {
            Persona::registrarPersona(
                'Persona de prueba',
                '31/02/2000',
                'Calle Mayor',
                'Málaga',
                EstadoCivil::Soltero
            );
        } catch (InvalidArgumentException $error) {
            return 'Excepción controlada: ' . $error->getMessage();
        }

        return 'No se lanzó la excepción esperada.';
    });

    $probar('Constructor privado de Persona', static function (): string {
        $constructor = (new ReflectionClass(Persona::class))->getConstructor();

        if ($constructor === null || !$constructor->isPrivate()) {
            throw new LogicException('El constructor de Persona debería ser privado.');
        }

        return 'Constructor privado verificado mediante reflexión.';
    });

    $probar('Iterador SerieFibonacci(10)', static function (): string {
        $serie = new SerieFibonacci(10);
        $valores = [];

        foreach ($serie as $valor) {
            $valores[] = (string) $valor;
        }

        return implode(' ', $valores);
    });

    $probar('Generador Fibonacci', static function (): string {
        $valores = [];
        foreach (SerieFibonacci::fFibonacci(10) as $valor) {
            $valores[] = (string) $valor;
        }

        return implode(' ', $valores);
    });

    $probar('Límite Fibonacci no válido', static function (): string {
        try {
            new SerieFibonacci(-1);
        } catch (InvalidArgumentException $error) {
            return 'Excepción controlada: ' . $error->getMessage();
        }

        return 'No se lanzó la excepción esperada.';
    });

    return ['resultados' => $resultados, 'errores' => $errores];
}

function mostrarDatos(array $datos): void
{
    echo '<main>';

    foreach ($datos['resultados'] as $titulo => $resultado) {
        echo '<section>';
        echo '<h2>' . htmlspecialchars((string) $titulo, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</h2>';
        echo '<p>' . htmlspecialchars((string) $resultado, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</p>';
        echo '</section>';
    }

    if ($datos['errores'] !== []) {
        echo '<section><h2>Errores no previstos</h2><ul>';
        foreach ($datos['errores'] as $error) {
            echo '<li>' . htmlspecialchars((string) $error, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</li>';
        }
        echo '</ul></section>';
    }

    echo '</main>';
}

function ejecutarPagina(): void
{
    $ubicacion = [
        ['TEXTO' => 'Inicio', 'ENLACE' => '/index.php'],
        ['TEXTO' => 'Relación 4'],
    ];

    inicioCabecera('Práctica: relación 4');
    finCabecera();
    inicioCuerpo('Práctica: relación 4', $ubicacion);
    mostrarDatos(obtenerDatos());
    finCuerpo();
}

ejecutarPagina();