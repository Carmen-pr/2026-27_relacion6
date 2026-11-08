ndice · PHP
<?php
include_once(dirname(__FILE__) . "/../../cabecera.php");

//controlador
// Aquí solo se prepara la estructura de la página y se crean los objetos.
// Todo lo que hay que mostrar se guarda en el array $datos y se le pasa a la vista.

$ubicacion = [
    ["TEXTO" => "Inicio", "ENLACE" => "/index.php", "ADICIONAL" => " &gt;&gt; "],
    ["TEXTO" => "Relación 4"]
];

$datos = [];

/**
 * EJERCICIOS 1, 2, 3 y 4: InstrumentoBase, IFabricable, InstrumentoViento y Flauta
 * InstrumentoBase e InstrumentoViento son abstractas, así que se prueban con Flauta.
 */
// Todo el bloque va dentro de un try: si algo falla de forma inesperada, el catch del final
// guarda el mensaje en $datos["errorInstrumentos"] y la vista lo muestra sin romper la página.
// Los try anidados prueban errores que SÍ esperamos (excepciones lanzadas a propósito).
try {
    // Flauta con valores indicados y flauta con los valores por defecto
    $flauta = Flauta::CrearDesdeArray(["material" => "bambú", "edad" => 7]);
    $flautaPorDefecto = Flauta::CrearDesdeArray([]);

    // setDescripcion es el setter (la descripción es privada); envejecer() suma un año a la edad
    $flauta->setDescripcion("Flauta de concierto");
    $flauta->envejecer();

    // Clonación: la copia tiene edad 0 y un nuevo número de instancia
    $flautaClonada = clone $flauta;

    // Se guardan los objetos en $datos para que la vista los pueda mostrar
    $datos["flauta"] = $flauta;
    $datos["flautaPorDefecto"] = $flautaPorDefecto;
    $datos["flautaClonada"] = $flautaClonada;
    // instanceof comprueba si el objeto cumple una clase o interfaz (devuelve true o false)
    $datos["esFabricable"] = $flauta instanceof IFabricable;

    // Prueba de la sobrecarga deshabilitada: leer una propiedad que no existe
    try {
        $valor = $flauta->propiedadInexistente;
        $datos["lecturaInexistente"] = "No se lanzó la excepción esperada.";
    } catch (Exception $error) {
        $datos["lecturaInexistente"] = $error->getMessage();
    }

    // Prueba de la sobrecarga deshabilitada: asignar una propiedad que no existe
    try {
        $flauta->propiedadInexistente = "valor";
        $datos["asignacionInexistente"] = "No se lanzó la excepción esperada.";
    } catch (Exception $error) {
        $datos["asignacionInexistente"] = $error->getMessage();
    }

    // El constructor es privado: se comprueba con reflexión (inspeccionar la clase desde código)
    // en lugar de hacer new Flauta(), que el editor marcaría como error.
    $constructor = (new ReflectionClass(Flauta::class))->getConstructor();
    $datos["constructorPrivado"] = ($constructor !== null && $constructor->isPrivate());

    // Datos no válidos al crear la flauta: material vacío, edad negativa y edad que no es un número.
    // Cada caso debe lanzar una InvalidArgumentException, y el mensaje se guarda en el array.
    $casosInvalidos = [
        ["material" => "", "edad" => 3],
        ["material" => "metal", "edad" => -1],
        ["material" => "madera", "edad" => "quince"]
    ];
    $datos["flautasInvalidas"] = [];
    foreach ($casosInvalidos as $caso) {
        try {
            Flauta::CrearDesdeArray($caso);
            $datos["flautasInvalidas"][] = "No se lanzó la excepción esperada.";
        } catch (InvalidArgumentException $error) {
            $datos["flautasInvalidas"][] = $error->getMessage();
        }
    }
} catch (Throwable $error) {
    $datos["errorInstrumentos"] = $error->getMessage();
}

/**
 * EJERCICIO 5: EstadoCivil y Persona
 */
// Se obtienen los casos del enum, se registra una persona con un estado elegido al azar
// y se prueban dos validaciones (fecha que no existe y nombre vacío).
try {
    $casos = EstadoCivil::casos();

    // Lista de casos con su valor y lista solo de valores
    $datos["casosEstado"] = [];
    $valoresEstado = [];
    foreach ($casos as $caso) {
        $datos["casosEstado"][$caso->descripcion()] = $caso->valor();
        $valoresEstado[] = $caso->valor();
    }

    // Se elige un valor al azar de los posibles y se convierte en caso con from()
    $valorAleatorio = $valoresEstado[array_rand($valoresEstado)];
    $estadoAleatorio = EstadoCivil::from($valorAleatorio);

    $persona = Persona::registrarPersona("María Pérez", "15/08/2001", "Calle del Sol, 12", "Antequera", $estadoAleatorio);
    $datos["valorAleatorio"] = $valorAleatorio;
    $datos["persona"] = $persona->toString();

    // Fecha que no existe: el 31 de febrero. registrarPersona debe lanzar la excepción.
    try {
        Persona::registrarPersona("Prueba", "31/02/2000", "Calle Mayor", "Málaga", EstadoCivil::Soltero);
        $datos["fechaInvalida"] = "No se lanzó la excepción esperada.";
    } catch (InvalidArgumentException $error) {
        $datos["fechaInvalida"] = $error->getMessage();
    }

    // Nombre formado solo por espacios
    try {
        $persona->setNombre("   ");
        $datos["nombreVacio"] = "No se lanzó la excepción esperada.";
    } catch (InvalidArgumentException $error) {
        $datos["nombreVacio"] = $error->getMessage();
    }
} catch (Throwable $error) {
    $datos["errorPersona"] = $error->getMessage();
}

/**
 * EJERCICIO 6: SerieFibonacci (iterador y generador)
 */
// Se recorre la serie de dos formas distintas (objeto Iterator y generador con yield)
// y se prueba que los límites negativos lancen excepción.
try {
    // Con el iterador: foreach llama solo a rewind, valid, current, key y next
    $datos["serieIterador"] = [];
    foreach (new SerieFibonacci(10) as $valor) {
        $datos["serieIterador"][] = $valor;
    }

    // Con el generador: devuelve clave => valor
    $datos["serieGenerador"] = [];
    foreach (SerieFibonacci::fFibonacci(10) as $clave => $valor) {
        $datos["serieGenerador"][$clave] = $valor;
    }

    // Límite negativo en el iterador
    try {
        new SerieFibonacci(-1);
        $datos["limiteNegativo"] = "No se lanzó la excepción esperada.";
    } catch (InvalidArgumentException $error) {
        $datos["limiteNegativo"] = $error->getMessage();
    }

    // Índice negativo en el generador: la excepción salta al pedir el primer valor
    try {
        SerieFibonacci::fFibonacci(-1)->current();
        $datos["generadorNegativo"] = "No se lanzó la excepción esperada.";
    } catch (InvalidArgumentException $error) {
        $datos["generadorNegativo"] = $error->getMessage();
    }
} catch (Throwable $error) {
    $datos["errorFibonacci"] = $error->getMessage();
}

/**
 * EJERCICIO 7: ClaseMisPropiedades
 */
// Se crean propiedades dinámicas (gracias a __set), se leen (gracias a __get)
// y se comprueba que la propiedad privada está protegida.
try {
    $objeto = new ClaseMisPropiedades();
    $objeto->propPublica = "publica";
    // propiedad1 y propiedad2 no están declaradas en la clase: __set las guarda en el array interno
    $objeto->propiedad1 = 25;
    $objeto->propiedad2 = "cadena de texto";

    $datos["propiedad1"] = $objeto->propiedad1;
    // isset llama a __isset y no da error aunque la propiedad no exista
    $datos["existePropiedad3"] = isset($objeto->propiedad3);

    // Leer una propiedad que nunca se asignó
    try {
        $valor = $objeto->propiedad3;
        $datos["lecturaPropiedad3"] = "No se lanzó la excepción esperada.";
    } catch (Exception $error) {
        $datos["lecturaPropiedad3"] = $error->getMessage();
    }

    // Asignar la propiedad privada desde fuera
    try {
        $objeto->_propPrivada = "privada";
        $datos["asignarPrivada"] = "No se lanzó la excepción esperada.";
    } catch (Exception $error) {
        $datos["asignarPrivada"] = $error->getMessage();
    }

    // Recorrido del objeto: las claves llevan el prefijo oi_
    $datos["recorrido"] = [];
    foreach ($objeto as $clave => $valor) {
        $datos["recorrido"][$clave] = $valor;
    }
} catch (Throwable $error) {
    $datos["errorPropiedades"] = $error->getMessage();
}

//dibuja la plantilla de la vista
inicioCabecera("APLICACION PRIMER TRIMESTRE");
cabecera();
finCabecera();
inicioCuerpo("Relación 4: Clases, objetos y enumeraciones", $ubicacion);
cuerpo($datos); //llamo a la vista pasándole los datos
finCuerpo();
// **********************************************************

//vista
function cabecera() {}

/**
 * Prepara un valor para mostrarlo en HTML sin que se interprete como código.
 *
 * @param mixed $valor Valor a mostrar (texto, número u objeto con __toString).
 * @return string El texto con los caracteres especiales convertidos.
 */
function escapar(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
}
 
//vista
/**
 * Muestra los resultados de las pruebas de las clases.
 *
 * @param array $datos Datos preparados por el controlador.
 * @return void
 */
function cuerpo(array $datos): void
{
    /**
     *  EJERCICIO 1 ***********************************************************************************
     */
    // Se muestran los datos de la flauta que creó el controlador. Como InstrumentoBase es abstracta,
    // se prueba a través de Flauta. escapar() evita que el texto se interprete como HTML.
    echo "<h1>Ejercicio 1: InstrumentoBase</h1>";

    // Si el controlador guardó un error, se muestra y no se intenta pintar el resto

    if (isset($datos["errorInstrumentos"])) {
        echo "ERROR: " . escapar($datos["errorInstrumentos"]) . "<br>";
    } else {
        echo "Flauta indicada: " . escapar($datos["flauta"]) . "<br>";
        echo "Descripción: " . escapar($datos["flauta"]->getDescripcion()) . "<br>";
        echo "Edad: " . escapar($datos["flauta"]->getEdad()) . "<br>";
        echo "Sonido: " . escapar($datos["flauta"]->sonido()) . "<br>";
        echo "Afinar: " . escapar($datos["flauta"]->afinar()) . "<br>";
        echo "Leer una propiedad inexistente: " . escapar($datos["lecturaInexistente"]) . "<br>";
        echo "Asignar una propiedad inexistente: " . escapar($datos["asignacionInexistente"]) . "<br>";

        /**
         *  EJERCICIO 2 ***********************************************************************************
         */
        // La interfaz IFabricable obliga a tener metodoFabricacion() y metodoReciclado()
        echo "<h1>Ejercicio 2: IFabricable</h1>";

        echo "¿La flauta es IFabricable? " . ($datos["esFabricable"] ? "Sí" : "No") . "<br>";
        echo "Fabricación: " . escapar($datos["flauta"]->metodoFabricacion()) . "<br>";
        echo "Reciclado: " . escapar($datos["flauta"]->metodoReciclado()) . "<br>";

        /**
         *  EJERCICIO 3 ***********************************************************************************
         */
        // La flauta por defecto muestra el material (madera) y la edad (15) que pone InstrumentoViento
        echo "<h1>Ejercicio 3: InstrumentoViento</h1>";

        echo "Flauta con valores por defecto (material y edad): " . escapar($datos["flautaPorDefecto"]) . "<br>";

        /**
         *  EJERCICIO 4 ***********************************************************************************
         */
        // Se compara la flauta original con su clon y se muestran los errores de datos no válidos
        echo "<h1>Ejercicio 4: Flauta</h1>";

        echo "Original: " . escapar($datos["flauta"]) . "<br>";
        echo "Clon (edad 0 y nuevo número de instancia): " . escapar($datos["flautaClonada"]) . "<br>";
        echo "¿El constructor de Flauta es privado? " . ($datos["constructorPrivado"] ? "Sí" : "No") . "<br>";
        echo "Datos no válidos al crear una flauta:<br>";
        // Un <li> por cada mensaje de error guardado por el controlador
        echo "<ul>";
        foreach ($datos["flautasInvalidas"] as $mensaje) {
            echo "<li>" . escapar($mensaje) . "</li>";
        }
        echo "</ul>";
    }

    /**
     *  EJERCICIO 5 ***********************************************************************************
     */
    // Lista de casos del enum, persona con estado aleatorio y los dos errores de validación
    echo "<h1>Ejercicio 5: EstadoCivil y Persona</h1>";

    if (isset($datos["errorPersona"])) {
        echo "ERROR: " . escapar($datos["errorPersona"]) . "<br>";
    } else {
        echo "Casos de EstadoCivil:<br>";
        echo "<ul>";
        foreach ($datos["casosEstado"] as $nombre => $valor) {
            echo "<li>" . escapar($nombre) . " = " . escapar($valor) . "</li>";
        }
        echo "</ul>";
        echo "Valor aleatorio elegido: " . escapar($datos["valorAleatorio"]) . "<br>";
        echo "Persona: " . escapar($datos["persona"]) . "<br>";
        echo "Fecha no válida: " . escapar($datos["fechaInvalida"]) . "<br>";
        echo "Nombre vacío: " . escapar($datos["nombreVacio"]) . "<br>";
    }

    /**
     *  EJERCICIO 6 ***********************************************************************************
     */
    // implode une los valores del array en un texto separados por un espacio
    echo "<h1>Ejercicio 6: SerieFibonacci</h1>";

    if (isset($datos["errorFibonacci"])) {
        echo "ERROR: " . escapar($datos["errorFibonacci"]) . "<br>";
    } else {
        echo "Con el iterador: " . escapar(implode(" ", $datos["serieIterador"])) . "<br>";
        // El generador devuelve clave => valor, por eso el foreach recoge las dos cosas
        echo "Con el generador (clave => valor):<br>";
        echo "<ul>";
        foreach ($datos["serieGenerador"] as $clave => $valor) {
            echo "<li>" . escapar($clave) . " => " . escapar($valor) . "</li>";
        }
        echo "</ul>";
        echo "Límite negativo en el iterador: " . escapar($datos["limiteNegativo"]) . "<br>";
        echo "Índice negativo en el generador: " . escapar($datos["generadorNegativo"]) . "<br>";
    }

    /**
     *  EJERCICIO 7 ***********************************************************************************
     */
    // Se muestran las propiedades dinámicas, los errores esperados y el recorrido del objeto
    echo "<h1>Ejercicio 7: ClaseMisPropiedades</h1>";

    if (isset($datos["errorPropiedades"])) {
        echo "ERROR: " . escapar($datos["errorPropiedades"]) . "<br>";
    } else {
        echo "La propiedad 1 vale " . escapar($datos["propiedad1"]) . "<br>";
        echo "¿Existe la propiedad 3? " . ($datos["existePropiedad3"] ? "Sí" : "No") . "<br>";
        echo "Leer la propiedad 3 sin asignarla: " . escapar($datos["lecturaPropiedad3"]) . "<br>";
        echo "Asignar _propPrivada: " . escapar($datos["asignarPrivada"]) . "<br>";
        // Las claves del recorrido llevan el prefijo oi_ y las dos últimas son propPublica y _propPrivada
        echo "Recorrido del objeto:<br>";
        echo "<ul>";
        foreach ($datos["recorrido"] as $clave => $valor) {
            echo "<li>" . escapar($clave) . " => " . escapar($valor) . "</li>";
        }
        echo "</ul>";
    }
}
