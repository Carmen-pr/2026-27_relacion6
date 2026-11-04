<?php
declare(strict_types=1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/cabecera.php';

function obtenerDatos(): array
{    // El controlador guarda los resultados y los errores de cada prueba.
    $resultados = [];
    $errores = [];

    // Ejecuta cada prueba por separado para que un error no detenga las demás.
    $probar = static function (string $titulo, callable $prueba) use (&$resultados, &$errores): void {
        try {
            $resultados[$titulo] = (string) $prueba();
        } catch (Throwable $error) {
            $errores[] = $titulo . ': ' . $error->getMessage();
        }
    };

    /**
     *  EJERCICIO 1: creación, modificación, clonación y métodos de Flauta
     */
    $probar('Instrumento y fábrica', static function (): string {
        // InstrumentoBase es abstracta: se ejercita mediante la clase concreta Flauta.
        $flauta = Flauta::CrearDesdeArray(['material' => 'bambú', 'edad' => 7]);
        $flauta->setDescripcion('Flauta de concierto');
        $flauta->envejecer();
        $copia = clone $flauta;

        return implode(' | ', [
            (string) $flauta,
            'Descripción: ' . $flauta->getDescripcion(),
            'Edad: ' . $flauta->getEdad(),
            $flauta->sonido(),
            $flauta->afinar(),
            $flauta->metodoFabricacion(),
            $flauta->metodoReciclado(),
            'Copia: ' . (string) $copia,
        ]);
    });

    /**
     *  EJERCICIO 2: acceso a una propiedad que no existe
     */
    $probar('Acceso a una propiedad inexistente', static function (): string {
        $flauta = Flauta::CrearDesdeArray([]);

        try {
            return (string) $flauta->propiedadInexistente;
        } catch (Exception $error) {
            return 'Excepción controlada: ' . $error->getMessage();
        }
    });

    /**
     *  EJERCICIO 3: intento de asignación a una propiedad no permitida
     */
    $probar('Asignación no permitida', static function (): string {
        $flauta = Flauta::CrearDesdeArray([]);

        try {
            $flauta->propiedadInexistente = 'valor';
        } catch (Exception $error) {
            return 'Excepción controlada: ' . $error->getMessage();
        }

        return 'No se lanzó la excepción esperada.';
    });

    /**
     *  EJERCICIO 4: validación de los datos opcionales de la fábrica
     */
    $probar('Validación de datos de Flauta', static function (): string {
        $casosInvalidos = [
            ['material' => '', 'edad' => 3],
            ['material' => 'metal', 'edad' => -1],
            ['material' => 'madera', 'edad' => 'quince'],
        ];
        $mensajes = [];

        foreach ($casosInvalidos as $datos) {
            try {
                Flauta::CrearDesdeArray($datos);
            } catch (InvalidArgumentException $error) {
                $mensajes[] = $error->getMessage();
            }
        }

        return implode(' | ', $mensajes);
    });

    /**
     *  EJERCICIO 5: propiedades dinámicas, isset y recorrido del objeto
     */
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

    /**
     *  EJERCICIO 6: protección del valor de una propiedad privada
     */
    $probar('Protección de la propiedad privada', static function (): string {
        $objeto = new ClaseMisPropiedades();

        try {
            $objeto->_propPrivada = 99;
        } catch (Exception $error) {
            $elementos = iterator_to_array($objeto->getIterator());
            return 'Excepción controlada: ' . $error->getMessage()
                . ' | valor conservado: ' . $elementos['oi__propPrivada'];
        }

        return 'No se lanzó la excepción esperada.';
    });

    /**
     *  EJERCICIO 7: lectura de una propiedad dinámica no asignada
     */
    $probar('Excepción al leer propiedad dinámica inexistente', static function (): string {
        $objeto = new ClaseMisPropiedades();

        try {
            return (string) $objeto->noAsignada;
        } catch (Exception $error) {
            return 'Excepción controlada: ' . $error->getMessage();
        }
    });

    /**
     *  EJERCICIO 8: obtención de los casos de la enumeración EstadoCivil
     */
    $probar('Enumeración EstadoCivil', static function (): string {
        $valores = array_map(
            static fn (EstadoCivil $estado): string => $estado->descripcion() . '=' . $estado->valor(),
            EstadoCivil::casos()
        );

        return implode(', ', $valores);
    });

    /**
     *  EJERCICIO 9: registro de una persona con un estado civil aleatorio
     */
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

        return $persona->toString() . ' | Estado asignado desde el valor aleatorio ' . $valorAleatorio;
    });

    /**
     *  EJERCICIO 10: validación de una fecha de nacimiento incorrecta
     */
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

    /**
     *  EJERCICIO 11: rechazo de un texto vacío en un setter
     */
    $probar('Validación de texto de Persona', static function (): string {
        $persona = Persona::registrarPersona(
            'Persona de prueba',
            '15/08/2001',
            'Calle Mayor',
            'Málaga',
            EstadoCivil::Soltero
        );

        try {
            $persona->setNombre('   ');
        } catch (InvalidArgumentException $error) {
            return 'Excepción controlada: ' . $error->getMessage();
        }

        return 'No se lanzó la excepción esperada.';
    });

    /**
     *  EJERCICIO 12: comprobación mediante reflexión del constructor privado
     */
    $probar('Constructor privado de Persona', static function (): string {
        $constructor = (new ReflectionClass(Persona::class))->getConstructor();

        if ($constructor === null || !$constructor->isPrivate()) {
            throw new LogicException('El constructor de Persona debería ser privado.');
        }

        return 'Constructor privado verificado mediante reflexión.';
    });

    /**
     *  EJERCICIO 13: recorrido de la serie Fibonacci con un iterador
     */
    $probar('Iterador SerieFibonacci(10)', static function (): string {
        $serie = new SerieFibonacci(10);
        $valores = [];

        foreach ($serie as $valor) {
            $valores[] = (string) $valor;
        }

        return implode(' ', $valores);
    });

    /**
     *  EJERCICIO 14: generación de la serie Fibonacci con un generador
     */
    $probar('Generador Fibonacci', static function (): string {
        $valores = [];
        foreach (SerieFibonacci::fFibonacci(10) as $valor) {
            $valores[] = (string) $valor;
        }

        return implode(' ', $valores);
    });

    /**
     *  EJERCICIO 15: rechazo de un límite Fibonacci negativo
     */
    $probar('Límite Fibonacci no válido', static function (): string {
        try {
            new SerieFibonacci(-1);
        } catch (InvalidArgumentException $error) {
            return 'Excepción controlada: ' . $error->getMessage();
        }

        return 'No se lanzó la excepción esperada.';
    });

    /**
     *  EJERCICIO 16: rechazo de un índice negativo en el generador
     */
    $probar('Índice no válido en el generador Fibonacci', static function (): string {
        try {
            SerieFibonacci::fFibonacci(-1);
        } catch (InvalidArgumentException $error) {
            return 'Excepción controlada: ' . $error->getMessage();
        }

        return 'No se lanzó la excepción esperada.';
    });

    return ['resultados' => $resultados, 'errores' => $errores];
}

function mostrarDatos(array $datos): void
{
    // Vista: muestra cada resultado escapado para evitar interpretar su contenido como HTML.
    echo '<main>';

    foreach ($datos['resultados'] as $titulo => $resultado) {
        echo '<section>';
        echo '<h2>' . htmlspecialchars((string) $titulo, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</h2>';
        $resultadoEscapado = htmlspecialchars((string) $resultado, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        if ($titulo === 'Instrumento y fábrica') {
            $resultadoEscapado = str_replace(' | ', '<br>', $resultadoEscapado);
        }
        echo '<p>' . $resultadoEscapado . '</p>';
        echo '</section>';
    }

    // Los errores inesperados se presentan aparte de los resultados correctos.
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
    // Configura la ruta de navegación y monta la plantilla común de la página.
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