<?php

class Configuracion {
    const dbHost        = '192.168.230.10';
    const dbName        = 'docentes';
    const dbUser        = 'fichadoc';
    const dbPassword    = 'fichadoc';    
    const dbms          = 'mysql';
    
    const version       = '0.1 alpha';
    
    const modDefault    = 'Curriculum'; 
    
    function Configuracion() {
        
    }
    
    public static function setIncludePath()
    {
        if(strpos(strtoupper(PHP_OS),'WIN')!==FALSE)
            $pathSep = ';';
        else
            $pathSep = ':';
        
        $sysRoot = dirname(dirname(dirname(__FILE__)));
        
        $inc_path = ini_get("include_path");
        $inc_path .= $pathSep.$sysRoot.'/clases';

        $inc_path = ini_set("include_path",$inc_path);
    }
    
    public static function incluirModulos()
    {
        require_once('modulos/CurriculumMod.class.php');
    }
}