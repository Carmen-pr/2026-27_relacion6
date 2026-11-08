<?php
declare(strict_types=1);
 
/**
 * Interfaz: un "contrato". Solo dice QUÉ métodos debe tener una clase,
 * nunca cómo funcionan. Cualquier clase que haga "implements IFabricable"
 * está obligada a escribir estos dos métodos.
 *
 * Diferencia con una clase abstracta: una clase solo hereda de UNA clase,
 * pero puede implementar VARIAS interfaces.
 */
interface IFabricable
{
    /**
     * Describe cómo se fabrica el objeto.
     *
     * @return string
     */
    public function metodoFabricacion(): string;
 
    /**
     * Describe cómo se recicla el objeto.
     *
     * @return string
     */
    public function metodoReciclado(): string;
}
