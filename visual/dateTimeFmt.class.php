<?php
/**
* Se define la clase dateTimeFmt 
*
* @author       Lucas Vidaguren <vidaguren@econ.unicen.edu.ar>
* @copyright    Lucas Vidaguren <vidaguren@econ.unicen.edu.ar>
*
* @package      visual
* @since 1.0 - 02/11/2006
*/

/**
* Tiene funciones de traducci�n entre formato e internaci�n de fechas y tiemo
*
* @author       Lucas Vidaguren <vidaguren@econ.unicen.edu.ar>
* @copyright    Lucas Vidaguren <vidaguren@econ.unicen.edu.ar>
*
* @package      visual
* @since 1.0 - 02/11/2006
*/
class dateTimeFmt {

    function dateTimeFmt() {
    }
    
    function segundosAStrTiempo($segundos)
    {
        $horas = $segundos>0?floor($segundos/3600):ceil($segundos/3600);
        $minutos = abs(ceil($segundos/60) - $horas*60);
        
        return sprintf("%02d:%02d",$horas,$minutos);
    }
    
    /**
     * Convierte una fecha en el formato
     */
    function fechaArgtotime($fecha)
    {
        if(ereg ("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})", $fecha, $dmY))
           return strtotime("{$dmY[3]}-{$dmY[2]}-{$dmY[1]}");
        
        if(!empty($fecha))
          return strtotime($fecha);
        
        return time();
    }
}