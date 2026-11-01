<?php
declare(strict_types=1);

interface IFabricable
{
    public function metodoFabricacion(): string;

    public function metodoReciclado(): string;
}