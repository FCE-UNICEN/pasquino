<?php

require_once('datos/criterio/Criterio.class.php');
require_once('datos/adodb/adodb.inc.php');

abstract class DaoBase {
    /**
     * var object $_db
     */
    protected $_db;

    /**
     * var string $defaultOrder columna de orden por defecto en el find
     */
    protected $defaultOrder;
    /**
     * var string $baseFindBySQL
     */
    protected $baseFindBySQL;
    /**
     * var String $tableName nomrbe de la tabla
     */
    protected $tableName;

    /**
     * @var object Objeto tipo SimpleXML para leer lo mappings de clases
     */
    protected $_xmlMapping;
    /**
     * @var string Ruta del archivo de mappings de la clase
     */
    protected $_xmlMappingFile;

    /**
     * @var string ruta donde se encuentra el archivo que define la clase de entidad
     */
    private $_pathEntidad;
    /**
     * @var string Ultimo mensaje de error que sucedi� en un save
     */
    private $_lastError;

    protected $_dieOnFindByError=true;

    /**
     * @var array Instancia singleton del dao
     */
    private static $instances = array();
    
    /**
     * @var array Instancia singleton de conexiones db
     */
    private static $dbConections = array();

    /**
     * Constructor de DaoBase
     */
    function DaoBase() {
        $this->loadMapping();
        
        $dataSource = null;
        if(isset($this->_xmlMapping['data-source']))
            $dataSource = (string)$this->_xmlMapping['data-source'];
        $this->_db = $this->getConexion($dataSource);

        $this->tableName = $this->_xmlMapping['tabla'];
        $this->defaultOrder = $this->_xmlMapping['orden'];

        require_once($this->_pathEntidad);
    }

	final public static function getInstance()
    {
        $calledClass = get_called_class();
        
        if (!isset(self::$instances[$calledClass]))
        {
            self::$instances[$calledClass] = new $calledClass();
        }

        return self::$instances[$calledClass];
    }

    function __destruct() {
    	$this->_db->close();
    }

    function getConexion($dataSource=null)
    {
        if(!isset($dataSource))
            $dataSource = Configuracion::getDefaultDataSource();
// 		if(!isset(self::$dbConections[$dataSource]))
// 		{
	        self::$dbConections[$dataSource] = &ADONewConnection(Configuracion::getDBMS($dataSource)); # eg 'mysql' or 'postgres'
	        self::$dbConections[$dataSource]->SetFetchMode(ADODB_FETCH_ASSOC);
	        if(Configuracion::getDbDSN($dataSource)!='')
	        	self::$dbConections[$dataSource]->NConnect(Configuracion::getDbDSN($dataSource), Configuracion::getDbUser($dataSource), Configuracion::getDbPassword($dataSource));
	        else
	        	self::$dbConections[$dataSource]->NConnect(Configuracion::getDbHost($dataSource), Configuracion::getDbUser($dataSource), Configuracion::getDbPassword($dataSource), Configuracion::getDbName($dataSource));
// 		}
        return self::$dbConections[$dataSource];
    }

    private function _getMapperConfig()
    {
        $config = Configuracion::getConfigXML();
        return $config->mappings;
    }

    private function _getMappingConfig($clase)
    {
        $mappings = $this->_getMapperConfig();
        foreach($mappings->mapping as $map)
        {
            if(strtoupper($map['clase'])==strtoupper($clase))
            {
                return $map;
            }
        }
        return null;
    }

    /**
     * Carga el mapping desde el archivo XML de mappings
     * @return object Objeto SimpleXML con el mapping
     */
    protected function loadMapping()
    {
        $map = Configuracion::getMappingClase(str_replace("Dao","",get_class($this)),$this->_xmlMappingFile);

        $this->_xmlMapping = $map->clase;

        $path = (string)$map['path'];

        if(!empty($path))  $path.="/";

        if(!empty($map->clase['path']))
        {
            $path .= "{$map->clase['path']}/";
        }

        $this->_pathEntidad = "{$path}{$this->_xmlMapping['nombre']}.class.php";

        return $this->_xmlMapping;
    }

    /**
     * Crea el arreglo buffer para guardar
     * @return array arreglo con datos con nombre de la columna de la base (nombreCol => valor)
     */
    protected function getBuffer($elem)
    {
        $buf = array();

        foreach($this->_xmlMapping->id as $id)
        {
            $get = "get".ucfirst($id['nombre']);
            $buf[(string)$id['columna']] = $elem->$get();
        }

        foreach($this->_xmlMapping->propiedad as $prop)
        {
            if(!isset($prop->{'data-source'})) // si es de otra base no hay que ponerlo en el bufer
            {
                $get = "get".ucfirst($prop['nombre']);
                if($get!="get")
                {
                	$p = $elem->$get();
                	$col = (string)$prop['columna'];
                	if(isset($prop['tipo']) && $p != null) //si es con tipo actualizo el id
                   		$buf[$col] = $p->getId();
                	else
                    	$buf[$col] = $p;
                }
            }
        }
        return $buf;
    }

    private function _newDaoClase($clase)
    {
    	$nombreDao = $nombreDaoFile = "Dao".$clase;

        $map = Configuracion::getMappingConfigClase($clase);

        if(isset($map) && isset($map['dir']))
           $nombreDaoFile = "{$map['dir']}/{$nombreDaoFile}";

        //Siempre se espera la misma estructura para los mappings que para los daos

        if(!@include_once("{$nombreDaoFile}.class.php"))
        	require_once("daos/{$nombreDaoFile}.class.php");

        return $nombreDao::getInstance();
    }

    /**
     * Crea el objeto de la entidad a partir de un arreglo que tiene los mismos nombres de elementos
     * que los nombres de las propiedades.
     *
     * Este no tiene en cuenta las relaciones tipadas a no ser que la entidad tenga la variable id y
     * obtenga el objeto relacionado direcatemente desde el get, sin tenerlo como propiedad
     *
     * @return object el objeto con los datos a partir de $row
     * @param array $arreeglo arreglo con los datos que cada clave es identica que un nombre de propiedad nombrePropiedad => valor
     */
    function crearDesdeArreglo($arreglo)
    {
        $elem_name = $this->getClaseEntidad();
        $elem = new $elem_name();

        if(!is_array($arreglo))
            return $elem;

        foreach($arreglo as $nombreProp => $valor)
        {
        	$set = "set".ucfirst($nombreProp);
        	if(method_exists($elem,$set))
        	{
                /*
                 if(mb_detect_encoding($valor . 'a' , 'UTF-8, ISO-8859-1')=='UTF-8')
               		$valor = $valor;
               	*/
        	   	$elem->$set($valor);
        	}
        	//TODO: dar la opción de que la clave del arreglo sea la columna
        	/*
        	 * else {
        	 *  $nombreProp = $this->buscarNombrePropiedad($nombrePropiedad);
        	 *  $set = "set".ucfirst($nombreProp);
        	 *  if(method_exists($elem,$set))
        	 *  		$elem->$set($valor);
        	 * }
        	 */
        }

        return $elem;
    }

    /**
     * Crea el objeto de la entidad a la cual logra el acceso el DAO
     * @return object el objeto con los datos a partir de $row
     * @param array $row arreglo con los datos obtenidos de la base en forma nombreCol => valor
     * @param object $elem elemento pre-creado en el cual se deben cargar los parametros
     */
    protected function crearObjetoEntidad($row,$elem = null)
    {
        if($elem == null)
        {
        	$elem_name = $this->getClaseEntidad();
        	$elem = new $elem_name();
        }

        foreach($this->_xmlMapping->id as $id)
        {
	        $set = "set".ucfirst((string)$id['nombre']);
	        $elem->$set($row[(string)$id['columna']]);
        }

        //cargo las propiedades
        $propiedades = $this->_xmlMapping->propiedad;

        $antes = time();

        foreach($propiedades as $prop)
        {
            $nombreProp = (string)$prop['nombre'];
            if(!empty($nombreProp))
            {
                $set = "set".ucfirst($nombreProp);
                $valor = $row[(string)$prop['columna']];

                if(isset($prop->{"data-source"})) //busco el valor para la propiedad del datasource especificado
                {
                    $ds = $prop->{"data-source"};
                    $db = $this->getConexion((string)$ds['nombre']);
                    if($rs = $db->Execute("SELECT {$prop['columna']} FROM {$ds['tabla']} WHERE {$ds['clave']} = {$elem->getId()}"))
                    {
                        if($r = $rs->FetchRow())
                            $valor = $r[(string)$prop['columna']];
                    }
                }
                if(method_exists($elem,$set))
                {
                    if(isset($prop['tipo'])) //si es con tipo actualizo el id
                    {
                        $dao = $this->_newDaoClase($prop['tipo']);
                        $elemRelac = $dao->findById($valor);
                        $elem->$set($elemRelac);
                    }
                    else
                    //si est� definida otra data source no espero que est� en el row
                    {
                         $elem->$set($valor);
                    }
                }
            }
        }

        //cargo las listas
        $propiedades = $this->_xmlMapping->{"uno-a-muchos"};
        if($propiedades != null)
        foreach($propiedades as $prop)
        {
            $set = "set".ucfirst((string)$prop['nombre']);
            if(isset($prop['tipo'])) //todos deben tener tipo
            {
                $dao = $this->_newDaoClase($prop['tipo']);

                $colName = "{$prop['columna']}";
                if(is_a($this->_db,'ADODB_mysql') || is_a($this->_db,'ADODB_mysqli')) //ac� me aseguro por tablas con espacios en mysql
                    $colName = "`{$colName}`";

                $elemsRelac = $dao->findBy(new Criterio("{$colName} = ".$elem->getId().""));
                $elem->$set($elemsRelac);
            }
        }
        $extiende = (string)$this->_xmlMapping['extiende'];
        if($extiende != null)
        {
        	$daoExtiende = $this->_newDaoClase($extiende);
        	$criterioId = $daoExtiende->getCriterioId($elem->getId());
	       	$sql = $daoExtiende->getFindBySql($criterioId);
			$rs = $this->_db->Execute($sql) or print $this->_db->ErrorMsg().$sql;
			$row = $rs->FetchRow();
	      	$elem = $daoExtiende->crearObjetoEntidad($row,$elem);
        }

        //print "Tiempo $elem_name: ".(time()-$antes). "<br>";
    	return $elem;
    }

    /**
     * Obtiene la referencia a la base de datos
     * @return object Referencia al objeto ADODb
     */
    function getDb()
    {
    	return $this->_db;
    }

    protected function getSqlFieldsArray() {

    	foreach($this->_xmlMapping->id as $id)
    		$fields[] = $id['columna'];

    	foreach($this->_xmlMapping->propiedad as $prop)
    	{
    		if(!isset($prop->{'data-source'}))
    			$fields[] = $prop['columna'];
    	}



    	//FIXME: solo mysql permite nombres locos en el field
    	if(is_a($this->_db,'ADODB_mysql') || is_a($this->_db,'ADODB_mysqli'))
    	{
    		if(is_array($fields))
    			foreach($fields as $k => $field)
    			{
    				$fields[$k] = "`$field`";
    			}
    	}
    	return $fields;
    }

    protected function getSqlFields() {
    	$strFields = "";

		$fields = $this->getSqlFieldsArray();

		$glue = ", ";
		$strFields = implode($glue, $fields);

    	if(empty($fields))
    		$strFields = "*";

    	return $strFields;
    }

    /**
     *
     * Genera el sql utilizado para buscar elementos
     * @param mixed $filtro puede ser string o Criterio
     * @param string $order
     * @param int $limitCount cantidad de elementos a buscar
     * @param int $limitOffset limite inicial desde donde buscar (offset)
     */
    function getFindBySql($filtro = null,$order=null,$limitCount=null,$limitOffset=null,$group=null)
    {
    	if(!empty($this->baseFindBySQL))
            $sql = $this->baseFindBySQL;
        else
        {
            $tabla = $this->tableName;

            if($tabla == $this->_xmlMapping['tabla'] && (is_a($this->_db,'ADODB_mysql') || is_a($this->_db,'ADODB_mysqli') ))
            	//ac� me aseguro por tablas con espacios en mysql
                $tabla = "`{$tabla}`";

            $fields = $this->getSqlFields();

            $sql = "SELECT {$fields} FROM {$tabla}";
        }

        $strFiltro = ($filtro != null)?$filtro->getCondicion($this->_xmlMapping['nombre']):'';

        if($strFiltro!='')
        {
        	if(stripos($sql," WHERE ")===false || stripos($sql," WHERE ")==-1)
                $sql .= " WHERE ";
            else
                $sql .= " AND ";

            $sql .= $strFiltro;
        }

        if(isset($group))
        	$sql .= " GROUP BY {$group}";

        if(!isset($order))
        	$order = $this->defaultOrder;

        if(isset($order))
            $sql .= " ORDER BY {$order}";

         if(isset($limitCount))
         {
            $sql .= " LIMIT {$limitCount}";
            if(isset($limitOffset))
            	$sql .= " OFFSET {$limitOffset}";
         }
         //print $sql;

         return $sql;

    }

    /**
     *
     * Genera el sql utilizado para eliminar elementos
     * @param mixed $filtro puede ser string o Criterio
      */
    function getDeleteBySql($filtro = null)
    {
        $tabla = $this->tableName;

        if(is_a($this->_db,'ADODB_mysql') || is_a($this->_db,'ADODB_mysqli')) //ac� me aseguro por tablas con espacios en mysql
                $tabla = "`{$tabla}`";

        $sql = "DELETE FROM {$tabla}";

        if($filtro != null && $filtro->getCondicion()!='')
            $sql .= " WHERE ".$filtro->getCondicion();

         return $sql;
    }



    /**
     *
     * Cuenta la cantidad de elementos
     * @param unknown_type $filtro
     * @param unknown_type $order
     */
    function count($filtro = null,$order=null)
    {

    	$sql = $this->getFindBySql($filtro,$order);

    	$sql = substr($sql, stripos($sql,"select "),7). " COUNT(*) as cant " . substr($sql, stripos($sql, "from"));

    	if(!($rs = $this->_db->Execute($sql)))
            die($this->_db->ErrorMsg()." $sql");
        return $rs->fields['cant'];
    }

    /**
     * Agrega la entidad dada a la lista (Array) dada con el criterio que se arma la lista
     * @param Entidad $entidad
     * @param array $listaFindBy
     */
    protected function addEntidadAListaFindBy($entidad,&$listaFindBy) {
    	$listaFindBy[] = $entidad;
    }

    /**
     * Crea y devuelvo el objeto/arrglo que se va a usar en la lista
     * @return multitype:
     */
    protected function createListaFindBy() {
    	return array();
    }

    /**
     * Obtiene una lista de objetos de la entidad
     * @param object $filtro Objeto de clase Criterio
     * @param string $order Columna o columnas separadas por coma (,) para ordenar la busqueda
     * @return array
     */
    function findBy($filtro = null,$order=null,$limitCount=null,$limitOffset=null,$group=null){

        $sql = $this->getFindBySql($filtro,$order,$limitCount,$limitOffset,$group);

        if(!($rs = $this->_db->Execute($sql)))
        {
            if($this->_dieOnFindByError)
            	die( $this->_db->ErrorMsg()." $sql");
        }
        $lista = $this->createListaFindBy();
        if($rs)
        {
	        while($row = $rs->FetchRow())
	        {
	        	$this->addEntidadAListaFindBy($this->crearObjetoEntidad($row), $lista);
	        }
        }
        return $lista;
    }

    function deleteBy($filtro = null){
    	$sql = $this->getDeleteBySql($filtro);

        if(!($rs = $this->_db->Execute($sql)))
            die($this->_db->ErrorMsg()." $sql");
    }

    /**
     * Obtiene una instancia de la entidad a partir de un id dado
     * @param integer $idElemento
     */
    function findById($idElemento)
    {
        if(isset($idElemento))
        {
	    	$c = $this->getCriterioId($idElemento);
	        $arr = $this->findBy($c);
	        if(!empty($arr) && is_array($arr))
	           return current($arr);
        }

        return null;
    }

    /**
     * Genera una lista de todos los objetos de los cuales accede a datos esta clase
     */
    function findAll()
    {
    	return $this->findBy();
    }

    /**
     * Guarda creando si no existe o actualizando si existe a partir de una instancia de la entidad
     * @param object $elem
     */
    function save(&$elem) {

        $mode   = 'INSERT';
        $where  = false;

    	if(($extiende = (string)$this->_xmlMapping['extiende']) != null)
         {
        	$daoExtiende = $this->_newDaoClase($extiende);
        	if(!$daoExtiende->save($elem))
        		$this->_lastError =  $daoExtiende->getLastError();
         }

         $buf = $this->getBuffer($elem);
        /*
         * Busco el elemento por id, si existe debo actualizarlo
         */
        if($this->findById($elem->getId()))
        {
            $mode  = 'UPDATE';
            $where = $this->getCriterioId($elem->getId())->getCondicion();
         }

        $ret = $this->_db->AutoExecute((string)$this->tableName,$buf,$mode,$where,true,true);

		if(!$ret)
        {
            $this->_lastError =  $this->_db->ErrorMsg();
        }
        $id = $elem->getId();
        if($mode == 'INSERT' && empty($id))
        {
            $id = $this->_xmlMapping->id;
            $set = "set".ucfirst($id['nombre']);
            $elem->$set($this->_db->Insert_ID());
        }

        return $ret;
    }

    function getLastInsertId()
    {
    	return $this->_db->Insert_ID();
    }

    /**
     * Genera la el criterio de condici�n de id usada para la actualizaci�n y eliminaci�n
     * @return object instancia de Criterio para filtrar por id
     * @param mixed $id
     */
    protected function getCriterioId($idElemento)
    {
    	$c = new Criterio();

    	$cantIds= count($this->_xmlMapping->id);
    	if($cantIds == 1)
    	{
    		$nombreCol = (string)$this->_xmlMapping->id['columna'];
			$c->add(Restricciones::eq($nombreCol,$idElemento));
    	}
    	elseif($cantIds>1)
    	{
    		foreach($this->_xmlMapping->id as $prop)
			{
				$col = (string)$prop['columna'];
				$c->add(Restricciones::eq($col,$idElemento[$col]));
			}

    	}
        return $c;
    }

    /**
     * Obtiene de un arreglo que tiene el formato {"nombrePropiedad"="valorPropiedad"} el valor del id
     * @param array $arr
     */
    public function getIdElementoDeArreglo($arr){

    	$cantIds= count($this->_xmlMapping->id);

    	$elem_name = $this->getClaseEntidad();
        $elem = new $elem_name();

        if($cantIds == 1)
    	{
    		$nombreCol = (string)$this->_xmlMapping->id['columna'];
    		$nombreProp = (string)$this->_xmlMapping->id['nombre'];
    		if(isset($arr[$nombreCol]))
    			$id = $arr[$nombreCol];
    		else if(isset($arr[$nombreProp]))
    			$id = $arr[$nombreProp];
    		else
    			$id = $arr['id'];

    		return $id;
    	}
    	elseif($cantIds>1)
    	{
    		$arrId=array();
    		foreach($this->_xmlMapping->id as $prop)
			{
				$nombreCol = (string)$prop['columna'];
				$nombreProp = (string)$prop['nombre'];

				$arrId[$nombreCol]=(isset($arr[$nombreCol]))?$arr[$nombreCol]:$arr[$nombreProp];
			}
			return $arrId;
    	}
    }

    public function getLastError()
    {
    	return $this->_lastError;
    }

    /**
     * Elimina de la base de datos el elemento con el id dado
     * @param integer $id
     */
    function deletePorId($id)
    {
    	$tn = "{$this->tableName}";
        if(is_a($this->_db,'ADODB_mysql') || is_a($this->_db,'ADODB_mysqli')) //ac� me aseguro por tablas con espacios en mysql
           $tn = "`{$tn}`";
        $sql = "DELETE FROM {$tn} WHERE ".$this->getCriterioId($id)->getCondicion();
        $ret = $this->_db->Execute($sql);
        if(!$ret)
        	$this->_lastError = $this->_db->ErrorMsg() . " {$sql}";
        return $ret;
    }

    /**
     * Devuelve la primera entiadad encontrada dados un criterio de filtro y un orden
     * @param Criterio $filtro Criterio de filtro
     * @param string $order Comlumna(s) de orden separadas por comas
     * @return Ambigous Entidad|NULL si no encuentra entidad devuelve null
     */
    function findFirst($filtro = null,$order=null) {
    	$l = $this->findBy($filtro,$order);
    	if(is_array($l) && !empty($l))
    		return current($l);

    	return null;
    }

    /**
     *
     * Obtiene el nombre de clase de la entidad a la cual hace el acceso a datos
     *
     *  @return string el nombre de clase de la entidad
     */
    function getClaseEntidad() {
    	return (string)$this->_xmlMapping['nombre'];
    }

    /**
     * Define y devuelve el criterio base del Dao
     */
    public function getCriterioBase() {
    	return new Criterio();
    }


    protected function buscarNombrePropiedad($nombreColumna)
    {
    	$propiedades = $this->_xmlMapping->propiedad;
    	if(is_array($propiedades))
    		foreach($propiedades as $prop)
    		{
    			$nombreCol = (string)$prop['columna'];
    			$nombreProp = (string)$prop['nombre'];
    			if($nombreCol == $nombreColumna)
    				return $nombreProp;
    		}
    	return $nombreColumna;
    }

    /**
     * Determina si existe una entidad dado un criterio
     * @param Criterio $c
     * @return boolean
     */
    public function checkIfExistsBy(Criterio $c)
    {
    	$sql = $this->getFindBySql($c);

    	if($rs = $this->_db->Execute($sql))
    	{
    		if($rs->RowCount()>0)
    			return true;
    	}
    	else
    		$this->_lastError = $this->_db->ErrorMsg()." $sql";

    	return false;
    }

    /**
     * Determina si existe una entidad identifica por $id
     * @param mixed $id identificador de la entidad
     * @return boolean
     */
    public function checkIfExistsById($id) {
    	if(isset($id))
    	{
    		$c = $this->getCriterioId($id);
    		return $this->checkIfExistsBy($c);
    	}
    	return false;
    }

    /**
     * Obtiene un arreglo con los valores de un enum dado el campo
     * @param unknown $strField
     * @return multitype:
     */
    protected function getEnumArray($strField)
    {
    	if($rs = $this->getDb()->Execute("SHOW COLUMNS FROM `$this->tableName` LIKE '$strField'"))
    	{
    		$row = $rs->FetchRow();
    		$valuestring = $row['Type'];
    		$valuestring = str_replace("enum", "", $valuestring);
    		$valuestring = str_replace("(", "", $valuestring);
    		$valuestring = str_replace(")", "", $valuestring);
    		$valuestring = str_replace("'", "", $valuestring);
    		$values = split(",", $valuestring);
    	}
    	return $values;
    }

}
