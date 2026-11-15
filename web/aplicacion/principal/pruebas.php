<?php

/**
 * Página de pruebas de la tienda.
 * Aquí compruebo que funcionan las clases (MuebleBase, MuebleReciclado,
 * MuebleTradicional y Caracteristicas) y todos sus métodos, uno por uno,
 * mostrando en pantalla qué hago y qué resultado sale.
 * Sigo la estructura de siempre: arriba el controlador y abajo la vista.
 */

// Esta página está dos carpetas por debajo de cabecera.php (principal -> aplicacion -> web),
// por eso la ruta sube con /../../ . Si la dejara como en las clases no la encontraría.
include_once(dirname(__FILE__) . "/../../cabecera.php");

//controlador
// La barra de ubicación tiene dos elementos: "inicio" (con enlace) y "pruebas",
// que es donde estoy. A la página actual no le pongo ENLACE para que no sea un enlace a sí misma.
$barra = [
    [
        "TEXTO" => "inicio",
        "ENLACE" => "/index.php",
        "ADICIONAL" => " &gt; "
    ],
    [
        "TEXTO" => "pruebas"
    ],
];

//dibuja la plantilla de la vista
inicioCabecera("tienda");
cabecera();
finCabecera();
inicioCuerpo("PRUEBAS", $barra);
cuerpo();  //llamo a la vista
finCuerpo();
// **********************************************************

//vista
function cabecera()
{
?>
    <!-- esto va en el head -->
<?php
}

//vista
/**
 * Cuerpo de la página: aquí hago todas las pruebas, en este orden:
 * crear muebles, valores por defecto, datos malos, nombre vacío, set, get,
 * damePropiedadesAdicionales, puedeCrear, acceso directo, clase abstracta,
 * características y, por último, el máximo de muebles.
 * Las variables ($r, $t...) tienen que declararse aquí dentro porque una función
 * no ve las variables de fuera (ámbito de variables).
 */
function cuerpo()
{
    echo "<h2>Pruebas de las clases</h2>\n";

    // ---------------------------------------------------------------
    // PRUEBA 1: creo un mueble de cada tipo con todos los datos correctos y los
    // convierto en cadena. Al hacer echo se ejecuta el __toString de MuebleBase,
    // que además de los datos básicos tiene que enseñar las propiedades de la hija.
    seccion("1. Crear un mueble de cada tipo y convertirlo en cadena (__toString)");
    $r = new MuebleReciclado('silla', 'Sillas SA', 'ESPAÑA', 2022, '01/03/2022', '31/12/2035', 80, 60);
    $t = new MuebleTradicional('mesa roble', 'Maderas', 'PORTUGAL', 2021, '01/01/2021', '31/12/2040', 250, 40.5, 'S02');
    linea("Reciclado", $r);
    linea("Tradicional", $t);

    // ---------------------------------------------------------------
    // PRUEBA 2: solo indico el nombre, que es lo único obligatorio.
    // Todo lo demás tiene que quedarse con su valor por defecto.
    seccion("2. Solo con el nombre: tienen que salir los valores por defecto");
    $d = new MuebleReciclado('sofa');
    linea("Reciclado por defecto", $d);
    $d2 = new MuebleTradicional('armario');
    linea("Tradicional por defecto", $d2);

    // ---------------------------------------------------------------
    // PRUEBA 3: pongo datos malos en todos los campos que NO son el nombre.
    // El enunciado dice que si un set falla en el constructor, se asigna el
    // valor por defecto (y en la fecha de inicio, 01/01/año). Al lado de cada
    // dato dejo anotado qué tiene que salir.
    seccion("3. Datos malos en campos NO obligatorios: tienen que salir los valores por defecto");
    $m = new MuebleTradicional(
        'estante',
        str_repeat('x', 50),                              // fabricante largo: no falla, se recorta a 30
        'UN PAIS CON UN NOMBRE DEMASIADO LARGO',          // país de más de 20: queda ESPAÑA
        1999,                                             // año menor de 2020: queda 2020
        '01/01/1990',                                     // inicio anterior al año: queda 01/01/2020
        '01/01/1980',                                     // fin anterior al inicio: queda 31/12/2040
        5,                                                // precio menor de 30: queda 30
        1000,                                             // peso mayor de 300: queda 15
        ''                                                // serie vacía: queda S01
    );
    linea("Mueble con datos malos", $m);

    // ---------------------------------------------------------------
    // PRUEBA 4: el nombre es obligatorio, así que si falla el constructor tiene
    // que lanzar una excepción. Pruebo con una cadena vacía y con solo espacios
    // (este segundo caso comprueba que el trim funciona).
    // Lo meto en try/catch para que la excepción no rompa la página.
    seccion("4. Nombre vacío: tiene que saltar una excepción");
    try {
        $x = new MuebleReciclado('');
        // Si llego a esta línea es que no ha saltado la excepción y algo está mal
        linea("ERROR", "se ha creado un mueble sin nombre");
    } catch (Exception $e) {
        linea("Excepción con ''", $e->getMessage());
    }
    try {
        $x = new MuebleReciclado('     ');
        linea("ERROR", "se ha creado un mueble con el nombre en blanco");
    } catch (Exception $e) {
        linea("Excepción con solo espacios", $e->getMessage());
    }

    // ---------------------------------------------------------------
    // PRUEBA 5: llamo a cada set con un valor malo (tiene que devolver false) y con
    // uno bueno (true). Después de los malos compruebo con el get que el valor
    // no ha cambiado. El orden de las pruebas de año y fechas importa: primero cambio
    // el año a 2023 y luego pruebo las fechas, porque dependen de él.
    seccion("5. Los set: cada uno con un valor malo (false) y uno bueno (true)");
    linea("setNombre('')", $r->setNombre(''));
    linea("setNombre('Sillón')", $r->setNombre('Sillón'));
    linea("getNombre()  (en mayúsculas)", $r->getNombre());

    linea("setFabricante(12)  (no es texto)", $r->setFabricante(12));
    linea("setFabricante('Sillas')", $r->setFabricante('Sillas'));
    linea("getFabricante()  (con FaMu: delante)", $r->getFabricante());

    linea("setPais(25 letras)", $r->setPais(str_repeat('a', 25)));
    linea("setPais('FRANCIA')", $r->setPais('FRANCIA'));

    linea("setAnio(2019)", $r->setAnio(2019));
    linea("setAnio(2023)", $r->setAnio(2023));

    linea("setFechaIniVenta('01/01/2022')  (anterior al año 2023)", $r->setFechaIniVenta('01/01/2022'));
    linea("setFechaIniVenta('31/02/2023')  (no existe)", $r->setFechaIniVenta('31/02/2023'));
    linea("setFechaIniVenta('15/06/2023')", $r->setFechaIniVenta('15/06/2023'));

    linea("setFechaFinVenta('01/01/2023')  (anterior al inicio)", $r->setFechaFinVenta('01/01/2023'));
    linea("setFechaFinVenta('31/12/2030')", $r->setFechaFinVenta('31/12/2030'));

    linea("setPrecio(10)", $r->setPrecio(10));
    linea("setPrecio('hola')", $r->setPrecio('hola'));
    linea("setPrecio(99.5)", $r->setPrecio(99.5));

    linea("setPorcentajeReciclado(150)", $r->setPorcentajeReciclado(150));
    linea("setPorcentajeReciclado(75)", $r->setPorcentajeReciclado(75));

    // Los de MuebleTradicional los pruebo con $t
    linea("setPeso(10)", $t->setPeso(10));
    linea("setPeso(120.5)", $t->setPeso(120.5));
    linea("setSerie('')", $t->setSerie(''));
    linea("setSerie('S05')", $t->setSerie('S05'));

    // ---------------------------------------------------------------
    // PRUEBA 6: llamo a todos los get. Fíjate en que el precio tiene que ser 99.5:
    // los intentos fallidos de arriba (10 y 'hola') no han podido cambiarlo.
    seccion("6. Los get (tras los cambios anteriores)");
    linea("getNombre", $r->getNombre());
    linea("getFabricante", $r->getFabricante());
    linea("getPais", $r->getPais());
    linea("getAnio", $r->getAnio());
    linea("getFechaIniVenta", $r->getFechaIniVenta());
    linea("getFechaFinVenta", $r->getFechaFinVenta());
    linea("getPrecio  (tiene que seguir en 99.5)", $r->getPrecio());
    linea("getPorcentajeReciclado", $r->getPorcentajeReciclado());
    linea("getPeso", $t->getPeso());
    linea("getSerie", $t->getSerie());

    // ---------------------------------------------------------------
    // PRUEBA 7: cada hija tiene que devolver los nombres de sus propiedades extra.
    // Es el método que luego uso en index.php y en modificar.php para no tener
    // que hacer un if por cada tipo de mueble.
    seccion("7. damePropiedadesAdicionales()");
    linea("MuebleReciclado", $r->damePropiedadesAdicionales());
    linea("MuebleTradicional", $t->damePropiedadesAdicionales());

    // ---------------------------------------------------------------
    // PRUEBA 8: puedeCrear es un método de clase, así que lo llamo con el nombre
    // de la clase, sin ningún objeto. Devuelve true/false y, por el parámetro
    // por referencia, deja en $quedan cuántos muebles se pueden crear todavía.
    seccion("8. puedeCrear() (método de clase, devuelve cuántos quedan por la referencia)");
    $hayHueco = MuebleBase::puedeCrear($quedan);
    linea("¿Se pueden crear más?", $hayHueco);
    linea("Muebles que aún se pueden crear", $quedan);
    linea("Máximo permitido (MAXIMO_MUEBLES)", MuebleBase::MAXIMO_MUEBLES);

    // ---------------------------------------------------------------
    // PRUEBA 9: la clase no permite la sobrecarga dinámica ni el acceso directo.
    // Primero intento crear una propiedad que no existe y después leer una privada;
    // en los dos casos se ejecuta el __set / __get de MuebleBase, que lanzan excepción.
    seccion("9. Acceso directo a propiedades: no se permite");
    try {
        $r->loquesea = 5;
        linea("ERROR", "se ha creado una propiedad dinámica");
    } catch (Exception $e) {
        linea("Excepción al crear una propiedad nueva", $e->getMessage());
    }
    try {
        $valor = $r->precio;
        linea("ERROR", "se ha leído una propiedad privada");
    } catch (Exception $e) {
        linea("Excepción al leer una propiedad privada", $e->getMessage());
    }

    // ---------------------------------------------------------------
    // PRUEBA 10: MuebleBase es abstracta, así que no se puede hacer new de ella.
    // Guardo el nombre de la clase en una variable y hago new $clase: PHP lo permite
    // igual, y así el editor no subraya la línea en rojo (si escribiera
    // new MuebleBase directamente, VS Code lo marcaría como error aunque fuera a propósito).
    // Lo que se lanza es un Error, no una Exception, y por eso el catch es de Error:
    // un catch (Exception) no lo capturaría y la página se rompería.
    seccion("10. MuebleBase es abstracta: no se puede instanciar");
    $clase = 'MuebleBase';
    try {
        $b = new $clase('prueba');
        linea("ERROR", "se ha instanciado una clase abstracta");
    } catch (Error $e) {
        linea("Error al instanciar MuebleBase", $e->getMessage());
    }

    // ---------------------------------------------------------------
    // PRUEBA 11: las características. Voy por partes:
    // a) añado varias de golpe (pares nombre, valor);
    // b) paso un número impar de argumentos: el último sobra y se tiene que descartar;
    // c) recorro con foreach, que tiene que salir en orden inverso al de entrada;
    // d) pruebo isset y el acceso directo (también a una que no existe);
    // e) modifico una existente;
    // f) indico ULTIMA: una nueva tiene que dar error, pero modificar una que ya
    //    existe tiene que seguir funcionando.
    seccion("11. Características");
    $r->anadirCaracteristicas('color', 'verde', 'saliente', true, 'precio', 300);
    linea("Tras añadir color, saliente y precio", $r->getCaracteristicas());

    $r->anadirCaracteristicas('plegable', false, 'sobrante');
    linea("Tras añadir 'plegable' y un argumento suelto (se descarta 'sobrante')", $r->getCaracteristicas());

    // Recorro las características. El valor puede ser un booleano, por eso
    // lo paso por aTexto: si no, un true saldría como 1 y un false como vacío.
    echo "<p><b>foreach sobre las características (orden inverso):</b></p>\n<ul>\n";
    foreach ($r->getCaracteristicas() as $nombre => $valor) {
        echo "<li>" . htmlspecialchars($nombre) . " = " . htmlspecialchars(aTexto($valor)) . "</li>\n";
    }
    echo "</ul>\n";

    // getCaracteristicas devuelve el objeto, así que puedo usarlo con isset y con ->
    $c = $r->getCaracteristicas();
    linea("isset(color)", isset($c->color));
    linea("isset(noExiste)", isset($c->noExiste));
    linea("valor de color", $c->color);
    try {
        $valor = $c->noExiste;
        linea("ERROR", "se ha leído una característica que no existe");
    } catch (Exception $e) {
        linea("Excepción al leer una característica que no existe", $e->getMessage());
    }

    // Modificar una que ya existe: vuelvo a añadirla con otro valor
    $r->anadirCaracteristicas('color', 'azul');
    linea("Modificar una existente (color pasa a azul)", $r->getCaracteristicas());

    // Con ULTIMA se cierra la lista de características
    $r->anadirCaracteristicas('ULTIMA', true);
    linea("Tras indicar ULTIMA", $r->getCaracteristicas());
    try {
        $r->anadirCaracteristicas('nueva', 1);
        linea("ERROR", "se ha podido añadir una característica nueva después de ULTIMA");
    } catch (Exception $e) {
        linea("Excepción al añadir una nueva tras ULTIMA", $e->getMessage());
    }
    // Pero las que ya existían se pueden seguir modificando
    $r->anadirCaracteristicas('color', 'rojo');
    linea("Modificar una existente después de ULTIMA (color pasa a rojo)", $r->getCaracteristicas());

    // Y el mueble entero tiene que enseñar sus características al convertirlo en cadena
    linea("El mueble completo, con sus características", $r);

    // ---------------------------------------------------------------
    // PRUEBA 12: la dejo la última porque agota el cupo de muebles y ya no podría
    // crear más después. Creo muebles en un bucle hasta que el constructor se
    // niegue; en ese momento tiene que saltar la excepción, y $i me dice en
    // qué mueble ha ocurrido. Inicio $i antes del try para poder usarlo en el catch.
    seccion("12. Superar el máximo de muebles: tiene que saltar una excepción");
    $i = 0;
    try {
        for ($i = 1; $i <= 100; $i++) {
            new MuebleReciclado("mueble $i");
        }
        linea("ERROR", "se han creado 100 muebles sin llegar al máximo");
    } catch (Exception $e) {
        linea("La creación nº $i ha fallado", $e->getMessage());
    }
    // Al terminar, puedeCrear tiene que decir que ya no se puede crear ninguno
    $hayHueco = MuebleBase::puedeCrear($quedan);
    linea("¿Se pueden crear más?", $hayHueco);
    linea("Muebles que aún se pueden crear", $quedan);
}

// ===================== FUNCIONES AUXILIARES DE LA VISTA =====================
// Solo sirven para mostrar los resultados de forma ordenada en la página.

/**
 * Convierte cualquier valor en texto para poder mostrarlo.
 * Lo necesito porque un true se imprimiría como 1 y un false como vacío,
 * y un array no se puede imprimir con echo.
 *
 * @param mixed $valor Valor que quiero mostrar
 * @return string El valor como texto
 */
function aTexto(mixed $valor): string
{
    // Los booleanos los escribo como true/false para que se entiendan
    if (is_bool($valor)) {
        return $valor ? 'true' : 'false';
    }
    // Los arrays los uno con comas; si está vacío lo digo expresamente
    if (is_array($valor)) {
        return $valor ? implode(', ', $valor) : '(array vacío)';
    }
    // Si es un objeto, esta conversión ejecuta su __toString
    return (string) $valor;
}

/**
 * Imprime una línea con el formato "etiqueta: valor".
 * Uso htmlspecialchars para que cualquier carácter raro del texto (por ejemplo
 * un < o un &) se vea tal cual y no se interprete como HTML.
 *
 * @param string $etiqueta Lo que estoy probando
 * @param mixed  $valor    El resultado que ha dado
 */
function linea(string $etiqueta, mixed $valor): void
{
    echo "<p><b>" . htmlspecialchars($etiqueta) . ":</b> " . htmlspecialchars(aTexto($valor)) . "</p>\n";
}

/**
 * Imprime el título de cada bloque de pruebas.
 *
 * @param string $titulo Texto del título
 */
function seccion(string $titulo): void
{
    echo "<h3>" . htmlspecialchars($titulo) . "</h3>\n";
}
