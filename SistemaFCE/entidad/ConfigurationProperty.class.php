<?php
require_once 'SistemaFCE/util/Configuracion.class.php';
require_once 'SistemaFCE/etidad/Entidad.class.php';

class ConfigurationProperty extends Entidad {

    
    /**
     * Obtiene el mapping (simple_xml_object) asociado al objeto para ORM
     */
    protected function getMapping() {
    	$configurationFound=Configuracion::getMappingClase(get_class($this));
    	if ($configurationFound)
    		return Configuracion::getMappingClase(get_class($this));
    	$xmlstr = <<<XML
    	<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE mapping PUBLIC "-//FCEunicen//DTD Mapping//ES" "http://apps.econ.unicen.edu.ar/public/dtd/mapping.dtd" >
<mapping path="SistemaFCE/entidades">
	<clase nombre="ConfigurationProperty" tabla="configurationproperty">		
    			<id columna="key" nombre="key" />
				<propiedad columna="value" nombre="value" />
	</clase>
</mapping>
XML;
    	return new SimpleXMLElement($xmlstr);
    }
    
}