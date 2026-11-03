<?php
include_once(dirname(__FILE__) . "/../../cabecera.php");

include_once ("./mis_funciones.php");

include_once ("./mis_funciones.php");

        
        
//controlador

$barra=[
     [
       "TEXTO"=> "inicio",
       "ENLACE" =>"/index.php", // Cambiado LINK por ENLACE
     ],
       [ 
       "TEXTO"=> "pruebas",   
       "ENLACE" =>"./index.php", // Cambiado LINK por ENLACE y puesto en ruta relativa
     ], 
       [ 
       "TEXTO"=> "eje. funciones",   
     ] 
];

$numero=doble(25);
//comprobacion
if ($numero===false)
    {
        //hay un error lo resuelve
        $numero=5;
    }

//if (($numero=doble($numero))===false)
if (!$numero=doble($numero))
    {
        exit;
    }


$numero=variosParametros(25,"2aaa8769",56);

$numero=variosParametros(25,"2");

$numero=variosParametros(25,par3:100);

$numero=variosParametros(25,"2aaa8769",56,23,56,89,24);


$numero=suma(1,2,3,4,5,6,7,8,9,10);
$numero=suma1(1,2,3,4,5,6,7,8,9,10);

$r=[5,6,2,4,9,1,3];
$numero=suma(1,2,...$r); //rellenamos $a y $b y pasamos el resto

$numero=suma(...$r);  //pasamos todos los parametros de forma que
//el 5 es $a y el 6 $b .....


$n1=15;
$otra="n1";

//variable-variable
$numero=$$otra;

//variable funcion
$otra="suma";
$numero=$otra(1,2,3,4);

//funciones anonimas
$func= function (float $a, float $b, float ...$resto): float
{
    $res = $a + $b;
    foreach ($resto as $num)
        $res += $num;
    return $res;
};

$numero=$func(1,2,3,4);

$s=function (int $a, int $b){
  return $a+$b;
};

$numero=$s(3,20);

$s=fn(int $a,int $b)=>$a+$b+$numero;
$numero=$s(29,20);

$numero=$s(29,20);


//dibuja la plantilla de la vista
inicioCabecera("APLICACION PRIMER TRIMESTRE");
cabecera();
finCabecera();
inicioCuerpo("pruebas basicas",$barra);
cuerpo($numero); //llamo a la vista
finCuerpo();
// **********************************************************

//vista
function cabecera() {}
//vista


/**
 * funcion que me permite mostrar la vista
 *
 * @return void
 */
function cuerpo(int $n)
{
        echo "el doble del numero es $n".PHP_EOL;
        echo "el numero $n es el doble de otro ".PHP_EOL;

}
