<?php
require_once 'SistemaFCE/util/Configuracion.class.php';
require_once 'datos/criterio/Criterio.class.php';
/**
 * 
 * Modulo que permite que todo sistema FCE senga RESTfull
 * usando /entidad/
 * @author lucas.vidaguren
 *
 */
class RESTMod {
	
	private $nombreRecurso;
	
	private $range;
	
	private $methodMap;
	
	private $accept_encoding;
	
	function __construct()
	{
		$headers = apache_request_headers();
		foreach ($headers as $header => $value) {
			if($header=='Range')
    			$this->range = $value;
    		if($header=='Accept')
    			$this->accept_encoding = $value;
		}		
		
		$this->methodMap = array();
		
		$this->methodMap['GET']['lista'] = 'lista';
		$this->methodMap['PUT'] = 'modificar';
		$this->methodMap['POST'] = 'alta';
		$this->methodMap['DELETE'] = 'baja';
		$this->methodMap['GET']['recurso'] = 'info';
	}

	/**
	 * Devuelve el nombre del recurso desde la URI
	 * return string nombre del recurso, null si nen el sistema no hay un recurso con el nombre requerido
	 */
	public function getRecursoSolicitado()
	{
		$uri = $_SERVER['REQUEST_URI'];
		
		if(isset($_SERVER['PATH_INFO'])) $uri = $_SERVER['PATH_INFO'];
		
		if(!isset($this->nombreRecurso))
		{
			//por si no se est� usando mod_rewrite cambio /index.php/recurso[/id] por /recurso[/id]
			$uri = preg_replace('/\/index.php/','',$uri);
			
			if (preg_match('/([^\/]+)/i', $uri, $rec)) {				
				$this->nombreRecurso = $rec[1];
			}
		}
		return $this->nombreRecurso; 
	}
	
	/**
	 * Devuelve el nombre del recurso desde la URI
	 * return string nombre del recurso, null si nen el sistema no hay un recurso con el nombre requerido
	 */
	public function getIdRecursoSolicitado()
	{
		$uri = $_SERVER['REQUEST_URI'];
		
		if(isset($_SERVER['PATH_INFO'])) $uri = $_SERVER['PATH_INFO'];
		
		//por si no se est� usando mod_rewrite cambio /index.php/recurso[/id] por /recurso[/id]
		$uri = preg_replace('/\/index.php/','',$uri);
		
		$tipoRecurso = $this->getRecursoSolicitado();
		$uri = preg_replace("/\/{$tipoRecurso}/",'',$uri);
		
		if (preg_match('/([^\/]+)/i', $uri, $rec)) {				
			return $rec[1];
		}
		return null;				 
	}
	
	/**
	 * Consigue el nombre del recurso REST conocido por el sistema
	 * todas las entidades mapeadas pueden accederse por este restfullmod
	 * @return string el nombre de la entidad encontrada como recurso, false si no se encuentra 
	 */
	function getNombreRecurso()
	{
		$rec = $this->getRecursoSolicitado();				
		//por si no se est� usando mod_rewrite cambio /index.php/recurso[/id] por /recurso[/id]
		if($rec!=null) {
			$plural = false;
	        $config = Configuracion::getConfigXML();
	        $mappings = $config->mappings;	        
	        foreach($mappings->mapping as $map)
	        {
	        	if(strcasecmp($rec,$map['clase'])==0)
	        		return (string)$map['clase'];
	        	if(strcasecmp($rec,$map['clase']."s")==0 || strcasecmp($rec,$map['clase']."es")==0)
	        		$plural = $map['clase'];
	        }
	        if($plural!==false)
	        	return (string)$plural;
	    }
		return false;
	}
	
	/**
	 * Determina si un recurso solicitado existe en el sistema
	 * @return boolean
	 */
	function esUriRecurso()
	{
		return $this->getNombreRecurso()!==false;	
	}
	
	/**
	 * Genera un Criterio a partir de variables de filtro en el request
	 * @param array $req
	 */
	function getFiltro($req)
	{
		$crit = new Criterio();
		$mapping =  Configuracion::getMappingClase($this->getNombreRecurso());
		//var_dump($req);
		foreach($mapping->clase->propiedad as $prop)
		{
			$col = (string)$prop['columna'];
			$val = 	$req[$col];		
			if(isset($val))
			{
				$val = str_replace("*", "%", $val);				
				if(strpos($val, "%")!==false)
					$crit->add(Restricciones::like($col, $val));
				else
					$crit->add(Restricciones::eq($col, $val));
	
			}
		}
		return $crit;
	}
	
	/**
	 * genera la string de orden para el findby a partir de lo pasado en el request
	 * @param array $req
	 */
	function getOrden($req)
	{
		
	}
	
	/**
	 * 
	 * Genera una lista 
	 * @param string $datos los datos enviados en el request (json)
	 * @param array $req el arreglo de request (obtenido de la query string)
	 * @return string la cadena json que representa la lista buscada
	 */
	function lista($datos,$req)
	{	
		$nombreRec = $this->getNombreRecurso();
		$nombreDao = "Dao".ucfirst($nombreRec);
		$dao = new $nombreDao();
		
		$crit = $this->getFiltro($req);
		$orden = $this->getOrden($req);
		
		if($this->range)
		{
			$cant = $dao->count($crit,$orden);
			$posItems = strpos($this->range,"items=")+6;
			$posMenos = strpos($this->range,'-',$posItems);
			
			$limitOffset = substr($this->range, $posItems ,$posMenos-$posItems);
			$limitCant = substr($this->range, $posMenos+1 ) - $limitOffset;

			header("Content-Range: items {$limitOffset}-{$limitCant}/{$cant}");
		}
		
		$lista = $dao->findBy($crit,$orden,$limitCant,$limitOffset);
		$arr = array();
		foreach($lista as $recurso)
		{
			if(method_exists($recurso, "getVars"))
				$arr[] = $recurso->getVars();
		}	$json = json_encode($arr);

		return $json;
	}
	
	/**
	 * 
	 * Obtiene un objeto entidad 
	 * @param string $datos los datos enviados en el request (json)
	 * @param array $req el arreglo de request (obtenido de la query string)
	 * @return string la cadena json que representa la lista buscada
	 */
	function info($datos,$req)
	{	
		$nombreRec = $this->getNombreRecurso();
		$nombreDao = "Dao".ucfirst($nombreRec);
		$dao = new $nombreDao();
		
		$idRecurso = $this->getIdRecursoSolicitado();
		
		$recurso = $dao->findById($idRecurso);
		
		if(method_exists($recurso, "getVars"))
			$json = json_encode($recurso->getVars());

		return $json;
	}
	
	
	/**
	 * Ejecuta la llamada de un
	 * Enter description here ...
	 */
	function ejecutar($req)
	{
		$method = $_SERVER['REQUEST_METHOD'];
		
		$idRecurso = $this->getIdRecursoSolicitado();
		//var_dump($idRecurso);		
		
		if($method=='GET')
		{
			$methodKey = 'lista';
			if($idRecurso!=null)
				$methodKey = 'recurso';
			
			$callback = $this->methodMap[$method][$methodKey]; 
		}
		else 
			$callback = $this->methodMap[$method];
		
		if(method_exists($this, $callback))
		{
			// get the request data
	        $datos = NULL;
	        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	            $datos = $_GET;
	        } else if ($tmp = file_get_contents('php://input')) {
	            $datos = json_decode($tmp);
	        }
			
	        // 	execute the function/method and return the results
	        header("{$_SERVER['SERVER_PROTOCOL']} 200 OK");
	        if(strpos($this->accept_encoding, "application/json")!==false)
	        	header('Content-Type: application/json');
	        else
	        	header('Content-Type: text/plain');
	        print $this->$callback($datos, $req);
		}
		else 
		{
	        header("{$_SERVER['SERVER_PROTOCOL']} 404 Not Found");
	        // print 404 page here
	        exit;
	    }
	}
}