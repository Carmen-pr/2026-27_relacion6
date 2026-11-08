<?php
declare(strict_types=1);
 
/**
 * Serie de Fibonacci recorrible con foreach.
 *
 * Implementa la interfaz Iterator: PHP llama automáticamente a
 * rewind(), valid(), current(), key() y next() cuando hacemos foreach
 * sobre un objeto de esta clase.
 *
 * También incluye un generador estático (fFibonacci) que hace lo mismo
 * con yield, para comparar las dos formas de hacerlo.
 */
class SerieFibonacci implements Iterator
{
    /** @var int Último índice que se genera (inclusivo: con 10 se llega a F(10)). */
    private int $_limite;
 
    /** @var int Índice por el que va el recorrido ahora mismo. */
    private int $_claveActual = 0;
 
    /**
     * Crea la serie y valida el límite.
     *
     * @param int $limite Último índice a generar. No puede ser negativo.
     * @throws InvalidArgumentException Si el límite es negativo.
     */
    public function __construct(int $limite)
    {
        // Validar al construir evita tener objetos en estado incorrecto.
        if ($limite < 0) {
            throw new InvalidArgumentException('El límite no puede ser negativo.');
        }
 
        $this->_limite = $limite;
    }
 
    /**
     * Devuelve el valor de Fibonacci del índice actual.
     * Se calcula en el momento: no guardamos la serie en un array.
     *
     * @return int
     */
    public function current(): int
    {
        return self::calcularFibonacci($this->_claveActual);
    }
 
    /**
     * Devuelve la clave actual. Usamos el índice como clave: 0, 1, 2...
     *
     * @return mixed
     */
    public function key(): mixed
    {
        return $this->_claveActual;
    }
 
    /**
     * Pasa al siguiente índice. foreach lo llama al final de cada vuelta.
     *
     * @return void
     */
    public function next(): void
    {
        $this->_claveActual++;
    }
 
    /**
     * Vuelve al principio. foreach lo llama UNA vez antes de empezar,
     * y por eso se puede recorrer el mismo objeto varias veces.
     *
     * @return void
     */
    public function rewind(): void
    {
        $this->_claveActual = 0;
    }
 
    /**
     * Dice si hay que seguir iterando. foreach lo llama antes de cada vuelta;
     * si devuelve false, el bucle termina.
     * Es <= (y no <) porque el límite es inclusivo.
     *
     * @return bool
     */
    public function valid(): bool
    {
        return $this->_claveActual <= $this->_limite;
    }
 
    /**
     * Generador: una función con yield no devuelve todo de golpe, sino que
     * se "pausa" en cada yield y entrega un valor cada vez que foreach lo pide.
     * Ventaja: no hace falta guardar la serie entera en memoria.
     *
     * NOTA: el enunciado pedía que yield devuelva la CLAVE. Con
     * "yield $indice => valor" la clave es el índice de la serie.
     * Antes era "yield valor", y PHP ponía claves automáticas 0, 1, 2...
     *
     * @param int $ultimo Último índice a generar (inclusivo).
     * @return Generator Pares índice => valor de Fibonacci.
     * @throws InvalidArgumentException Si el índice es negativo.
     */
    public static function fFibonacci(int $ultimo): Generator
    {
        // OJO: en un generador este código NO se ejecuta al llamar a la función,
        // sino en la primera vuelta del foreach. Por eso la excepción de
        // índice negativo no salta hasta que se recorre el generador.
        if ($ultimo < 0) {
            throw new InvalidArgumentException('El último índice no puede ser negativo.');
        }
 
        for ($indice = 0; $indice <= $ultimo; $indice++) {
            yield $indice => self::calcularFibonacci($indice);
        }
    }
 
    // Los métodos mágicos de abajo se llaman solos cuando alguien intenta
    // usar una propiedad que NO existe en la clase. Aquí los usamos para
    // prohibirlo lanzando una excepción en lugar de crear la propiedad.
 
    /**
     * Se llama al LEER una propiedad inexistente ($obj->algo).
     *
     * @param string $nombre Nombre de la propiedad que se intentó leer.
     * @return never Siempre lanza excepción, nunca devuelve nada.
     * @throws Exception
     */
    public function __get(string $nombre): never
    {
        throw new Exception("No existe la propiedad $nombre");
    }
 
    /**
     * Se llama al ASIGNAR una propiedad inexistente ($obj->algo = 1).
     *
     * @param string $nombre Nombre de la propiedad.
     * @param mixed $valor Valor que se intentó asignar.
     * @return void
     * @throws Exception
     */
    public function __set(string $nombre, mixed $valor): void
    {
        throw new Exception("No existe la propiedad $nombre");
    }
 
    /**
     * Se llama con isset($obj->algo) sobre una propiedad inexistente.
     * Devolvemos false: "no existe", sin lanzar excepción.
     *
     * @param string $nombre
     * @return bool
     */
    public function __isset(string $nombre): bool
    {
        return false;
    }
 
    /**
     * Se llama con unset($obj->algo) sobre una propiedad inexistente.
     *
     * @param string $nombre
     * @return void
     * @throws Exception
     */
    public function __unset(string $nombre): void
    {
        throw new Exception("No existe la propiedad $nombre");
    }
 
    /**
     * Calcula F(n) con la fórmula de Binet (sin bucles ni recursión):
     *   F(n) = (phi^n - psi^n) / raíz(5)
     * donde phi = (1+√5)/2 (el número áureo) y psi = (1-√5)/2.
     *
     * Usa decimales, así que el resultado trae pequeños errores de redondeo.
     * round() los corrige y (int) lo convierte a entero. Con índices muy
     * altos (hacia n≈70) deja de ser exacta.
     *
     * Es private y static: solo se usa dentro de la clase y no necesita $this.
     *
     * @param int $indice Posición en la serie (F(0)=0, F(1)=1, F(2)=1, F(3)=2...).
     * @return int
     */
    private static function calcularFibonacci(int $indice): int
    {
        $raizCinco = sqrt(5);
        $phi = (1 + $raizCinco) / 2;
        $psi = (1 - $raizCinco) / 2;
 
        return (int) round((($phi ** $indice) - ($psi ** $indice)) / $raizCinco);
    }
}
 