<?php
/**
 * 
 * @author lucas.vidaguren
 * @since 06/10/2008
 */

require_once('SistemaFCE/util/Session.class.php'); 
require_once('visual/smarty/libs/Smarty.class.php');
require_once('visual/xajax/xajax_core/xajax.inc.php');
require_once('visual/jscalendar/calendar.php'); 
require_once('SistemaFCE/dao/DaoUsuario.class.php');

require_once("HTML/QuickForm.php");
require_once("HTML/QuickForm/Renderer/ArraySmarty.php");


class BaseMod {
	
    var $smarty;
    var $_skinConfig;  
    var $_orderListado;
    var $_sentidoOrderListado;
    var $_menuModTplPath;
   
    var $session;
    
    var $xajax;
    
    var $errors;
    
    var $_tilePath;
    
    var $usuario;
    
    var $_form;
    
    var $_formFiltro;
    
    var $_dateFormat;
    var $_dateTimeFormat;
    var $_timeFormat;
    
    function BaseMod($skinDirName=null) {
        
        $this->session = new Session(Configuracion::getAppName());
        
        $this->_skinConfig = Configuracion::getTemplateConfigByDir($skinDirName);

		$this->_calendar = new DHTML_Calendar('js/jscalendar/', "es", "../../skins/".$this->_skinConfig['dir']."/css/cal", false);
        
        /*
         * Esto deber�a hacerse cuando exista el DaoUsuario
         */
        $daoU = new DaoUsuario();
        $this->_usuario = $daoU->findById($this->session->getIdUsuario());
        //$this->smarty->assign('nombre_usuario',"{$u->apellido}, {$u->nombre}");
        /**/
        
        $this->initSmarty();
        
        $this->xajax = new xajax();
        
        $this->_orderListado = $_SESSION[get_class($this)]['sort'];
        $this->_sentidoOrderListado = $_SESSION[get_class($this)]['sortSentido'];
        
        $this->_dateFormat = Configuracion::getDateFormat();
        $this->_dateTimeFormat = Configuracion::getDateTimeFormat();
        $this->_timeFormat = Configuracion::getTimeFormat();
        
        $this->_tilePath = Configuracion::getDefaultTplPath($skinDirName);//'decorators/default.tpl';
		$this->_form = new HTML_QuickForm('form','post',$_SERVER['PHP_SELF']);
        
        $this->registerXajax();
        
        $this->xajax->processRequest();
        
        $this->smarty->assign('xajax',$this->xajax->getJavascript('js'));
	}
    
    protected function registerXajax()
    {
    	//metodos de xajax (se debe llamar a processRequest para que esto funcione)
        $this->xajax->register(XAJAX_FUNCTION,array('hideMensaje',&$this,'hideMensaje'));
    }
    
    protected function initSmarty()
    {
    	$systemRoot = Configuracion::getSystemRootDir();
        
        $this->smarty = new Smarty(); // Handler de smarty
        $this->smarty->template_dir = $systemRoot.'/skins/'.$this->_skinConfig['dir']; // configuro directorio de templates
        $this->smarty->compile_dir = $systemRoot.'/tmp/skins/templates_c'; // configuro directorio de compilacion
        $this->smarty->cache_dir = $systemRoot.'/tmp/skins/cache'; // configuro directorio de cache
        $this->smarty->config_dir = $systemRoot.'/skins/configs'; // configuro directorio de configuraciones
        
        $this->smarty->assign('skin',$this->_skinConfig['dir']);
        $this->smarty->assign('relative_images',"skins/{$this->_skinConfig['dir']}/images");
        $this->smarty->assign('version',Configuracion::getVersion());
        $this->smarty->assign('skinPath',$systemRoot.'/skins/'.$this->_skinConfig['dir']);
        $this->smarty->assign('appName',Configuracion::getAppName());
		$this->smarty->assign('cal_files',$this->_calendar->get_load_files_code());
        
        $this->smarty->assign('dir_images',"skins/{$this->_skinConfig['dir']}/images");
        
        $mp = $this->getMenuPrincipal();
        //menu
        $this->smarty->assign('menuItems',$mp);
        $this->smarty->assign('menu',$mp);
        
        $this->smarty->assign('usuario',$this->_usuario);
        $this->smarty->assign('id_usuario_actual',$this->session->getIdUsuario());
    }
    
    /**
     * Genera un arreglo con [url,tag] si el operador tiene permisos
     */
    private function _getMenuItemArray($nombreModulo,$item)
    {	
        $tienePermiso = false;
        if(!empty($item->permisos))
        {   
            foreach($item->permisos->permiso as $perm)
            {
                $tienePermiso |= $$this->_usuario->tienePermiso((string)$perm);
            }  
        }  
        $permAccion = $this->_checkPermisoAccion((string)$item['accion'],$nombreModulo);
        $tienePermiso |= $permAccion;
        
        if(!$tienePermiso)
            return null;

        $mtag = (string)$item['tag'];
        $murl = "{$_SERVER['PHP_SELF']}?mod={$nombreModulo}&accion={$item['accion']}";
        if(!empty($item['url']))
            $murl = (string)$item['url'];
        
        return array('url'=>$murl,'tag'=>$mtag);
    }
    
    /**
     * Genera a partir de una config de menu un arreglo para crear un menu en smarty
     * @return array Arreglo para que smarty pueda generar el menu definido en $menuConf
     * @param String $nombreModulo Nombre del modulo al cual pertenece el men�
     * @param object $menuConf Configuraci�n en SimpleXML de menu
     */
    private function _getMenuModuloArray($nombreModulo,$menuConf)
    {	
        $menuItems = array();
        $menu = $menuConf;
        if(!empty($menu))
        {   
            if(($mItem = $this->_getMenuItemArray($nombreModulo,$menu))==null)
                return $menuItems;
               
            $menuItems['_'] = $mItem;
            foreach($menu->menuItem as $item)
            { 
                if(($mItem = $this->_getMenuItemArray($nombreModulo,$item))==null)
                    continue;

                $name = (string) $item['name'];
                if(isset($item->menuItem))
                    $menuItems[$name] = $this->_getMenuModuloArray($nombreModulo,$item);                 
                else
                    $menuItems[$name] = $mItem;
                    
           }
        }
        return $menuItems;
    }
    
    /**
     * @return array Arreglo para que smarty pueda generar el Menu Principal
     */
    public function getMenuPrincipal()
    {
    	$modulosConfig = Configuracion::getModulosConfig();
        foreach($modulosConfig->modulo as $mod)
        {   
            $n = (string)$mod['nombre'];
            $m = $this->_getMenuModuloArray($n,$mod->menuPrincipal);
            if(!empty($m))
              $menuPpal[$n] = $m;
        }
        return $menuPpal;
    }
    
    /**
     * Obtiene la configuaci�n del m�dulo
     */
    protected function getConfigModulo($nombreMod = null)
    {
    	if(!isset($nombreMod))
            $nombreMod = get_class($this);
        else
        {
        	if(strpos($nombreMod,'Mod')===FALSE)
                $nombreMod .= 'Mod';
        }
           
        $modulos = Configuracion::getModulosConfig();
        foreach($modulos->modulo as $mod)
        {	
            if("{$mod['nombre']}Mod" == $nombreMod)
                return $mod;
        }
        return null;
    }
        
    function addError($strError)
    {
    	$this->errors[] = $strError;
    }
    
    function ajaxNoPermisos(&$objResponse)
    {
        if(isset($objResponse))
            $this->displayError($objResponse,"No tiene permisos suficientes para esa acci�n");
    }
    
    function ajaxCheckPermisos(&$objResponse=null)
    {
        if(!$this->session->LogIn())
        {   
            if(isset($objResponse))
            {
                $this->displayError($objResponse,"Debe reingresar al sistema");
                $objResponse->script("setTimeout('location.href=\'index.php\'',3100)");
            }
            return false;
        }

        return true;
    }
    
    
    private function _checkPermisoAccion($accion,$nombreModulo=null)
    {
        if(!isset($this->_usuario))
            return false;
        // chequeo a partir de la config del m�dulo  
        $conf = $this->getConfigModulo($nombreModulo);
        
        //   Busco los permisos para la acci�n
        $acciones = $conf->acciones;
        if(empty($acciones->accion)) return false;
        foreach($acciones->accion as $acc)
        {
            
            $nombreAccion = (string)$acc['nombre'];
            if($nombreAccion == $accion)
            {   
                $tienePermiso = true;
                $perms = $acc->permisos;
                
                //si no tiene restricciones cualquiera tiene permisos
                if(!empty($perms->permiso))
                    foreach($perms->permiso as  $p)
                    {
                        $perm = (string)$p;
                        $tienePermiso &= $this->_usuario->tienePermiso($perm);
                    }
                
                return $tienePermiso;
            }
        }
        return false;
    }
    
    function checkPermisos($req)
    {
    	if(!$this->session->LogIn())
        {   
            $this->_tilePath = Configuracion::getBaseTplPath($this->_skinConfig['dir']);
            $this->mostrar('formLogin.tpl');
            exit();
        }
        if( !$this->ajaxCheckPermisos() || !$this->_checkPermisoAccion($req['accion']) )
        {
        	$this->_menuModTplPath = '';
            $this->mostrar('sinPermisos.tpl');
            die();
        }
        
        return true;
    }
    
    function mostrar($tpl)
    {
        if(!empty($this->errors))
            $this->smarty->assign('errores',$this->errors);
        
        $this->smarty->assign('menuMod',$this->_menuModTplPath);
        $this->smarty->assign('pantalla',$tpl);
        $this->smarty->assign('ajax',$this->xajax->getJavascript('js/'));
        $this->_form = new HTML_QuickForm('form','post',$_SERVER.PHP_SELF);
        $this->smarty->Display($this->_tilePath);
    }
    
    /**
     * Retorna un string el criterio y senditdo de ordenamiento en los listados a partir del request
     * @param array $req Arreglo del request
     * @return string Cadena de "criterio sentido" de ordenamiento (tipo SQL)
     */
    function getOrder($req){
        if(!empty($req['sort']))
        {
            if($req['sort']!=$this->_orderListado)
            {
            	$this->_orderListado = $req['sort'];
                $this->_sentidoOrderListado = "ASC";
            }
            else{
                if($this->_sentidoOrderListado == "ASC")
                    $this->_sentidoOrderListado = "DESC";
                else
                    $this->_sentidoOrderListado = "ASC";
            }	
        }
        else
        {
            $this->_orderListado = null;
            $this->_sentidoOrderListado = null;
            return null;
        }
        
        $_SESSION[get_class($this)]['sort'] = $this->_orderListado;
        $_SESSION[get_class($this)]['sortSentido'] = $this->_sentidoOrderListado; 
        
        return "{$this->_orderListado} {$this->_sentidoOrderListado}";  
    }
    
    /**
     * Genera un objeto Criterio a partir de los filtros pasados por request
     * @param array $req
     */
    function getFiltro($req){}
    
    /**
     * redirecciona a la home del modulo 
     */
    function redirectHomeModulo($req=null)
    {
        if(!isset($req)) $req = $_GET;
        
        header("Location: {$_SERVER['PHP_SELF']}?mod={$req['mod']}");
        exit();
    }
    
    /**
     * Redirecciona a la home del sistema 
     */
    function redirectHomeSistema()
    {
        setcookie('mod',null);
        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    }
    
    /**
     * Asigna valores a las variables miembro que guardan informaci�n recibida de request
     * @param array $req
     */
    function setMiembros($req) { }
    
    /**
     * Ejecuta la acci�n del modulo, a partir de la variable accion recibida por request
     * @param array $req 
     */
    function ejecutar($req)
    {
    	if(empty($req["accion"])) $req["accion"] = 'listar';
    	
    	$accion = $req["accion"];
        
        if(isset($req['logout']) || $accion == 'logout')
        {
            $this->session->LogOut();
            $this->redirectHomeSistema();
        }
        
        
        
        $this->checkPermisos($req);
        $this->setMiembros($req);
        
        $this->smarty->assign('accion',$accion);
        
        $metodoAccion = "accion".ucfirst($accion);
        
        if(!method_exists($this,$metodoAccion) && $accion != 'listar')
        {
            $req['accion'] = 'listar';
            $this->ejecutar($req);
            return;
        } 
        
        $this->$metodoAccion($req);
           
    }
    
    /**
     * Ejecuta una acci�n de alta de un elemento 
     * lo guarda llamando al metodo alta si viene por post, 
     * sino muestra el formulario
     */
    protected function accionAlta($req)
    {
    	if(!empty($_POST))
        {
            $this->alta($_POST);
            $this->redirectHomeModulo();
        }
        $this->form();
    }
    
    /**
     * Ejecuta una acci�n de modificaci�n de un elemento,
     * lo guarda llamando al metodo modificacion si viene por post,
     * sino muestra el formulario
     */
    protected function accionModif($req)
    {
    	if(!empty($_POST))
        {
            $this->modificacion($req);                    
            $this->redirectHomeModulo($req);
        }                
        $this->form($req);
    }
    
    /**
     * Ejecuta una acci�n de baja de un elemento llamando al metodo baja
     * luego redirecciona a la home del m�dulo
     */
    protected function accionBaja($req)
    {
    	$this->baja($req);
        $this->redirectHomeModulo();
    }
    
    /**
     * Ejecuta una acci�n de informaci�n de un elemento llamando al metodo info
     */
    protected function accionInfo($req)
    {
    	$this->info($req);
    }

    /**
     * Ejecuta una acci�n de listar los elementos llamando al metodo lista
     */    
    protected function accionListar($req)
    {
    	 $this->lista();
    }
    
    /* funciones abstractas */
    function alta($req){}
    function baja($req){}
    function lista(){}
    function form($req=null){}
    function modificacion($req){}
    
    function caracteres_html($str)
    {
    	$tr = array('�'=>'&aacute;','�'=>'&eacute;','�'=>'&iacute;','�'=>'&oacute;','�'=>'&uacute;',
                    '�'=>'&Aacute;','�'=>'&eacute;','�'=>'&iacute;','�'=>'&oacute;','�'=>'&uacute;',
                    '�'=>'&ntilde;','�'=>'&Ntilde;','�'=>'&uuml;','�'=>'&Uuml;');
        return strtr($str,$tr);
    }
    
    /**
     * Obtiene el c�digo html de un template utilizando smarty
     * @param string $tpl nombre de archivo del template
     * @return string c�digo html procesado por smarty  
     */
    function fetch($tpl)
    {
    	return $this->caracteres_html($this->smarty->fetch($tpl));
    }
    
    /**
     * Muestra un mensaje usando xajax
     */
    function displayMensaje(&$xajaxObjResponse,$mensaje,$className='message',$xPos=null,$yPos=null,$idDiv='message')
    {
    	
        $xajaxObjResponse->script("clearTimeout(tMsg)");
        $xajaxObjResponse->assign($idDiv,"innerHTML", "<div style='float:right; font-size:5px;'><button onclick='xajax_hideMensaje()'>X</button></div>".$this->caracteres_html($mensaje));
        $xajaxObjResponse->assign($idDiv,"className", $className);
        if(isset($xPos))
            $xajaxObjResponse->assign($idDiv,"style.left", $xPos+"px");
        if(isset($yPos))
            $xajaxObjResponse->assign($idDiv,"style.top", $yPos+"px");
        $xajaxObjResponse->script("tMsg = setTimeout('xajax_hideMensaje()',3000)");
    }
    
    /**
     * Muestra un mensaje de error usando xajax
     */
    function displayError(&$xajaxObjResponse,$mensaje)
    {
        $this->displayMensaje($xajaxObjResponse,$mensaje,'error');
    }
    
    /**
     * Oculta un mensaje mostrado utilizando xajax 
     */
    function hideMensaje($idDiv='message')
    {
    	// Instantiate the xajaxResponse object
        $objResponse = new xajaxResponse();
        
        $objResponse->script("clearTimeout(tMsg)");
        $objResponse->assign($idDiv,"className", "");
        $objResponse->assign($idDiv,"innerHTML", "");
        
        return $objResponse;
    }

   /**
     * Crea el input con el calendario selector de fecha
     * @return String con el html listo para insertar en el template
     */
    function getCalendarInput($name, $value = "", $format = null)
	{
		if(is_null($format)) $format = $this->_dateFormat;
		ob_start();
    	$this->_calendar->make_input_field(
    	// calendar options go here; see the documentation and/or calendar-setup.js
        array('firstDay'       => 1, // show Monday first
              'showsTime'      => false,
              'singleClick'    => true,
              'showOthers'     => true,
              'ifFormat'       => $format
             ),
        // field attributes go here
        array('name'        => $name,
              'value'       => $value));
		return ob_get_clean();
	}
    
    /**
     * Genera un arreglo con opciones para un select
     * @param array $listaElementos Lista de elementos que deben tener getId y getNombre definidos
     * @param integre $vacio si se debe crear una opcion vacia
     * @param integre $otro si se debe crear una opcion de "Otro", si est� definido el nro ser� el id
     * @return array arreglo asociativo id => nombre 
     */
    function getArregloSelect($listaElementos,$vacio=true,$otro=null,$otroLabel='Otra')
    {
    	$arregloOpciones = array();
        
        if($vacio)
            $arregloOpciones[0] = '';
        
        
        if(is_array($listaElementos))     
            foreach($listaElementos as $elem)
            {
                if(method_exists($elem,'getId') && method_exists($elem,'getNombre'))
                    $arregloOpciones[$elem->getId()] = $elem->getNombre();
            }
        
        if(isset($otro))
            $arregloOpciones[$otro] = $otroLabel;
        
        return $arregloOpciones;
    }
	
    /**
     * Obtiene el c�digo HTML de un input select
     * @param string $name
     * @param array $options opciones compatibles con las opciones de HTML_QuickForm_select
     * @param mixed $attributes atributos compatibles con los atributos de HTML_QuickForm_select
     */
	function getSelectInput($name,$options,$attributes,$selected=null)
	{
        $this->_form->addElement('select',$name,'label:',$options,$attributes);
        
        if(isset($selected))
        {
            $this->_form->setDefaults(array($name=>$selected));	
        }   
        
        $rendered = $this->getRenderedForm();
       
		return $rendered[$name]['html'];
	}
    
    /**
     * Genera el arreglo renderizado para smarty del form
     * @return array arreglo renderizado con el renderer de array de smarty
     */
    protected function getRenderedForm()
    {
        $renderer= new HTML_QuickForm_Renderer_ArraySmarty($this->smarty);// creacion del renderer para smarty
        
        $this->_form->accept($renderer);// inclusion en el form del renderer
        
        return $renderer->toArray();// pasaje a arreglo del renderer   	
    }
    
    /**
     * Asigna el formulairo pasado para smarty a la variable fomrulario de $this->smarty
     * @param string $nombreVarSmarty Nombre de la variable que ser� asignada en smarty con el contenido del formulario 
     */
    protected function renderForm($nombreVarSmarty = 'formulario')
    {
    	$this->smarty->assign($nombreVarSmarty,$this->getRenderedForm());
    }
}
