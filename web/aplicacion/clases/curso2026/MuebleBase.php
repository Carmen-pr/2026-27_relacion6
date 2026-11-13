<?php
include_once(dirname(__FILE__) . "/cabecera.php");
//controlador 
// en el conmtrolador solo va los conroladores lo demas va abajo, en el controlador va la esructura de la pagina, el resto va en la vista


/**
 * Clase abstracta MuebleBase.
 * Es la base de todos los muebles de la tienda. La hago abstracta porque no
 * quiero que se pueda crear un "mueble a secas", solo de los tipos concretos
 * (MuebleReciclado y MuebleTradicional).
 * Todas las propiedades son privadas: desde fuera solo se tocan con set/get.
 */
abstract class MuebleBase
{
    // Número máximo de muebles que se pueden crear. Lo pongo en 20 (mayor que 5)
    // porque además del array de 5 muebles, pruebas.php y modificar.php crean más.
    const MAXIMO_MUEBLES = 20;

    // Variable de clase: la comparten todas las instancias, por eso es static.
    // Cuenta cuántos muebles se han creado hasta ahora. Se usa con self::, no con $this.
    private static int $mueblesCreados = 0;

    // Propiedades básicas de cualquier mueble
    private string $nombre;
    private string $fabricante;
    private string $pais;
    private int $anio;
    private string $fechaIniVenta;
    private string $fechaFinVenta;
    private float $precio;

    // Características extra del mueble (se crean dinámicamente)
    private Caracteristicas $caracteristicas;

    /**
     * Constructor. Solo el nombre es obligatorio; el resto tiene valor por defecto.
     * Asigno los valores llamando a los set para que se validen igual que siempre.
     *
     * @throws Exception si se superó el máximo de muebles o si el nombre no es válido
     */
    public function __construct(
        string $nombre,
        string $fabricante = 'FaMu:',
        string $pais = 'ESPAÑA',
        int $anio = 2020,
        string $fechaIni = '01/01/2020',
        string $fechaFin = '31/12/2040',
        float $precio = 30
    ) {
        // 1) Lo primero es mirar si todavía se pueden crear muebles.
        //    puedeCrear devuelve true/false y además rellena $quedan (no lo uso aquí).
        if (!self::puedeCrear($quedan)) {
            throw new Exception("Se ha superado el máximo de muebles (" . self::MAXIMO_MUEBLES . ")");
        }

        // 2) El nombre es obligatorio: si el set falla, no puede existir el mueble.
        if (!$this->setNombre($nombre)) {
            throw new Exception("El nombre del mueble no es válido");
        }

        // 3) Con el resto de campos, si el set falla pongo el valor por defecto.
        //    El orden importa: el año va antes que las fechas (la fecha de inicio
        //    depende del año) y la fecha de inicio antes que la de fin.
        if (!$this->setFabricante($fabricante)) $this->setFabricante('FaMu:');
        if (!$this->setPais($pais))             $this->setPais('ESPAÑA');
        if (!$this->setAnio($anio))             $this->setAnio(2020);

        // Caso especial del enunciado: si falla la fecha de inicio, se asigna 01/01/año
        if (!$this->setFechaIniVenta($fechaIni)) $this->setFechaIniVenta('01/01/' . $this->anio);

        if (!$this->setFechaFinVenta($fechaFin)) $this->setFechaFinVenta('31/12/2040');
        if (!$this->setPrecio($precio))          $this->setPrecio(30);

        // Creo el objeto donde se guardarán las características
        $this->caracteristicas = new Caracteristicas();

        // 4) El contador lo subo al final, cuando ya sé que el mueble se ha creado bien.
        //    Si lo pusiera antes, contaría muebles que fallaron.
        self::$mueblesCreados++;
    }

    // ===================== SET =====================
    // Todos siguen la misma regla: si el dato es válido lo guardo y devuelvo true;
    // si no, devuelvo false y no toco la propiedad.
    // Uso mixed en el parámetro para que, si llega algo raro, el set devuelva
    // false en vez de lanzar un TypeError.

    /**
     * Nombre: cadena de máximo 40 caracteres, no vacía, y se guarda en mayúsculas.
     */
    public function setNombre(mixed $nombre): bool
    {
        if (!is_string($nombre)) return false;

        // trim quita los espacios de los extremos; así "   " cuenta como vacío
        $nombre = trim($nombre);
        if ($nombre === '') return false;

        // Compruebo la longitud con mi librería. Si salta un TypeError, no vale.
        try {
            if (!validaCadena($nombre, 40, '')) return false;
        } catch (TypeError $e) {
            return false;
        }

        // mb_strtoupper (y no strtoupper) para que la ñ y las tildes también pasen a mayúscula
        $this->nombre = mb_strtoupper($nombre);
        return true;
    }

    /**
     * Fabricante: máximo 30 caracteres y siempre empieza por "FaMu:".
     */
    public function setFabricante(mixed $fabricante): bool
    {
        if (!is_string($fabricante)) return false;

        // Si no empieza por FaMu: se lo pongo delante
        if (!str_starts_with($fabricante, 'FaMu:')) {
            $fabricante = 'FaMu:' . $fabricante;
        }

        // Primero añado el prefijo y DESPUÉS recorto a 30, para que el prefijo no se corte
        $fabricante = mb_substr($fabricante, 0, 30);

        try {
            if (!validaCadena($fabricante, 30, 'FaMu:')) return false;
        } catch (TypeError $e) {
            return false;
        }

        $this->fabricante = $fabricante;
        return true;
    }

    /**
     * País: cadena de máximo 20 caracteres.
     * El enunciado solo pide mayúsculas para el nombre, así que aquí lo guardo tal cual.
     */
    public function setPais(mixed $pais): bool
    {
        if (!is_string($pais)) return false;

        try {
            if (!validaCadena($pais, 20, 'ESPAÑA')) return false;
        } catch (TypeError $e) {
            return false;
        }

        $this->pais = $pais;
        return true;
    }

    /**
     * Año de fabricación: entero entre 2020 y el año actual.
     * El año actual lo calculo con date('Y') para que no se quede viejo.
     */
    public function setAnio(mixed $anio): bool
    {
        try {
            if (!validaEntero($anio, 2020, (int) date('Y'), 2020)) return false;
        } catch (TypeError $e) {
            return false;
        }

        // La asignación va FUERA del catch: solo llego aquí si el año es válido
        $this->anio = $anio;
        return true;
    }

    /**
     * Fecha de inicio de venta (dd/mm/aaaa).
     * No puede ser anterior al 1 de enero del año de fabricación.
     */
    public function setFechaIniVenta(mixed $fecha): bool
    {
        if (!is_string($fecha)) return false;

        try {
            // validaFecha comprueba que exista y la deja normalizada como dd/mm/aaaa
            if (!validaFecha($fecha, '')) return false;
        } catch (TypeError $e) {
            return false;
        }

        // No puede ser anterior al 1 de enero del año de fabricación.
        // Las fechas son texto, así que las convierto a DateTime para poder compararlas.
        if ($this->aFecha($fecha) < $this->aFecha('01/01/' . $this->anio)) return false;

        $this->fechaIniVenta = $fecha;
        return true;
    }

    /**
     * Fecha de fin de venta (dd/mm/aaaa).
     * No puede ser anterior a la fecha de inicio de venta (igual sí vale).
     */
    public function setFechaFinVenta(mixed $fecha): bool
    {
        if (!is_string($fecha)) return false;

        try {
            if (!validaFecha($fecha, '')) return false;
        } catch (TypeError $e) {
            return false;
        }

        // Comparo con la fecha de inicio (que ya está asignada porque el
        // constructor llama antes a setFechaIniVenta)
        if ($this->aFecha($fecha) < $this->aFecha($this->fechaIniVenta)) return false;

        $this->fechaFinVenta = $fecha;
        return true;
    }

    /**
     * Precio: real, no puede ser menor de 30.
     * Como no hay máximo uso PHP_FLOAT_MAX, que es el mayor real posible.
     */
    public function setPrecio(mixed $precio): bool
    {
        try {
            if (!validaReal($precio, 30, PHP_FLOAT_MAX, 30)) return false;
        } catch (TypeError $e) {
            return false;
        }

        $this->precio = $precio;
        return true;
    }

    // ===================== GET =====================
    // Solo devuelven el valor de la propiedad, sin validar nada.

    public function getNombre(): string
    {
        return $this->nombre;
    }
    public function getFabricante(): string
    {
        return $this->fabricante;
    }
    public function getPais(): string
    {
        return $this->pais;
    }
    public function getAnio(): int
    {
        return $this->anio;
    }
    public function getFechaIniVenta(): string
    {
        return $this->fechaIniVenta;
    }
    public function getFechaFinVenta(): string
    {
        return $this->fechaFinVenta;
    }
    public function getPrecio(): float
    {
        return $this->precio;
    }

    // ============ SIN SOBRECARGA DINÁMICA ============
    // Estos métodos solo se ejecutan cuando alguien toca una propiedad que no
    // existe (o que es privada y se usa desde fuera). Lanzo excepción para que no
    // se puedan crear propiedades nuevas ni acceder directamente a las que hay.

    public function __set(string $nombre, mixed $valor): void
    {
        throw new Exception("No se puede asignar la propiedad '$nombre' directamente. Usa los métodos set");
    }

    public function __get(string $nombre): mixed
    {
        throw new Exception("No se puede leer la propiedad '$nombre' directamente. Usa los métodos get");
    }

    // ===================== OTROS MÉTODOS =====================

    /**
     * Devuelve los nombres de las propiedades que añade cada clase hija.
     * Aquí devuelve un array vacío porque MuebleBase no añade ninguna.
     * Las hijas lo redefinen. Los nombres tienen que coincidir con sus getters
     * (por ejemplo 'Peso' -> getPeso) para poder usarlos en el __toString.
     */
    public function damePropiedadesAdicionales(): array
    {
        return [];
    }

    /**
     * Método de clase (static): dice si todavía se pueden crear más muebles.
     * Devuelve true/false y, además, deja en $numero cuántos quedan por crear.
     * Uso & (paso por referencia) para que $numero cambie en la variable
     * original; sin el & solo cambiaría una copia.
     * Dentro de un método static no existe $this, solo puedo usar self::.
     */
    public static function puedeCrear(&$numero): bool
    {
        $numero = self::MAXIMO_MUEBLES - self::$mueblesCreados;
        return $numero > 0;
    }

    /**
     * Añade características al mueble recibiendo pares (nombre, valor).
     * Ejemplo: anadirCaracteristicas('saliente', true, 'precio', 300)
     * Uso ...$args para aceptar un número variable de argumentos.
     * Si llega un número impar, el último sobra y lo descarto.
     * Si ya se indicó ULTIMA y la característica es nueva, Caracteristicas
     * lanza una excepción y se propaga hasta quien me llamó.
     */
    public function anadirCaracteristicas(mixed ...$args): void
    {
        // Avanzo de 2 en 2. La condición $i + 1 < count comprueba que exista
        // pareja; así, si sobra uno, el bucle no llega a procesarlo.
        for ($i = 0; $i + 1 < count($args); $i += 2) {
            $nombre = (string) $args[$i];
            // Esto llama al __set de Caracteristicas, que es el que lo guarda
            $this->caracteristicas->$nombre = $args[$i + 1];
        }
    }

    /**
     * Devuelve el objeto Caracteristicas del mueble (como objeto, no como texto).
     */
    public function getCaracteristicas(): Caracteristicas
    {
        return $this->caracteristicas;
    }

    /**
     * Convierte el mueble en texto cuando hago echo $mueble.
     * Después de los datos básicos añado las propiedades de la clase hija y
     * por último las características.
     */
    public function __toString(): string
    {
        // get_class($this) devuelve la clase real (MuebleReciclado, MuebleTradicional...)
        $texto = "MUEBLE de clase " . get_class($this)
            . " con nombre {$this->nombre}, fabricante {$this->fabricante}"
            . ", fabricado en {$this->pais} a partir del año {$this->anio}"
            . ", vendido desde {$this->fechaIniVenta} hasta {$this->fechaFinVenta}"
            . ", precio {$this->precio}";

        // Recorro las propiedades extra de la hija. Construyo el nombre del getter
        // ('get' + 'Peso' = 'getPeso') y lo llamo, así me sirve para cualquier hija.
        foreach ($this->damePropiedadesAdicionales() as $propiedad) {
            $getter = 'get' . $propiedad;
            $texto .= ", $propiedad " . $this->$getter();
        }

        return $texto . ". Caracteristicas: " . $this->caracteristicas;
    }

    // ===================== AUXILIARES =====================

    /**
     * Convierte un texto dd/mm/aaaa en un objeto DateTime.
     * Lo necesito porque comparar fechas como texto da resultados absurdos
     * (empiezan por el día). El ! del formato pone la hora a cero para que la
     * comparación sea justa. La llamo siempre con fechas que ya pasaron por
     * validaFecha, así que no puede fallar.
     */
    private function aFecha(string $fecha): DateTime
    {
        return DateTime::createFromFormat('!d/m/Y', $fecha);
    }
}




$barra = [
    [
        "TEXTO" => "inicio",
        "ENLACE" => "/index.php",
        "ADICIONAL" => ""
    ],
];

//dibuja la plantilla de la vista
inicioCabecera("tienda");
cabecera();
finCabecera();
inicioCuerpo("TIENDA", $barra);
cuerpo();  //llamo a la vista
finCuerpo();
// **********************************************************

//vista
function cabecera()
{
?>
    <?php
}

//vista
function cuerpo()
{
    ?>
<?php


}
