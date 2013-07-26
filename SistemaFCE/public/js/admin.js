(function(window,undefined) {
	
	//Bootstrap no conflicts
	$.fn.button.noConflict();
	
	
	
	var
	// Define a local copy of jQuery
	pQn = function() {
		// The jQuery object is actually just the init constructor 'enhanced'
		return new pQn.fn.init(  );
	};
	
	pQn.fn = pQn.prototype = {
		constructor: pQn,
		init: function(){ return this; },
		setupButtons: function(selectorBotonera,selectorBotones) {
			
			if(selectorBotones==null)
				selectorBotones='.gridAccionesItem';
			if(selectorBotonera==null)
				selectorBotonera='li.gridAccionesItem';
			
			$(selectorBotonera).find('div .button').addClass('ui-state-default ui-corner-all').css('float','left').css('margin-left','2px');
			
			$(selectorBotones).delegate('a>.ui-icon-pencil:not(.ui-state-disabled)','click',function(e){
				e.preventDefault();
				if(typeof $fnBindModifBtn == 'function') {
					$fnBindModifBtn($(this).parent("a").attr("href"));			
				}		
			});
			if(typeof $fnBindAltaBtn == 'function')	$fnBindAltaBtn();
			if(typeof $fnBindFiltro == 'function')	$fnBindFiltro();

			$(selectorBotones+' a').tooltip();
			
		},
		setupPaginacion : function(idLista, $formFiltro) {
			
			var selectorSource = " #"+idLista;
			var srcObj = $(selectorSource);
			if(!srcObj.exists()) {
				srcObj = $(idLista).parent();
			}
			laLista = srcObj.parents('.lista');
			
			laLista.on('click','footer > .pagination > ul > li > a',function(e){
				e.preventDefault();
				if($(this).parent().hasClass('disabled') || $(this).parent().hasClass('active')) 
					return;
				
				var page = $(this).attr('pag');
				var count = $(this).attr('count');
				
				var $hiddenPag = $('<input type="hidden" name="pag" value="'+page+'"/>');
				var $hiddenCount = $('<input type="hidden" name="count" value="'+count+'"/>');
				$hiddenPag.appendTo($formFiltro);
				$hiddenCount.appendTo($formFiltro);
				$formFiltro.submit();
				$hiddenCount.remove();
				$hiddenPag.remove();
			});
		},
		ultimoKeyup: null,
		filtroAnterior: '',
		/**
		 * Metodo para realiar el filtro automatico, enviando el submit del form
		 * @param tiempo
		 * @param aInputName
		 * @param aFormID Id del form
		 */
		doFilter: function(tiempo,aInputName){	
			if(tiempo==pQn.ultimoKeyup){
				console.log("entro");
				var aInput=$("input[name="+aInputName+"]");
				var aInputVal=aInput.val();
				console.log(this.filtroAnterior," - ",aInputVal);
				if(this.filtroAnterior!=aInputVal){				
					this.filtroAnterior=aInputVal;
					aInput.parent("form").submit();
				}
			}
		},
		keyUpFilter : function(elCampo,aSourceID,aMod,aAction,aOptions){
			var form = elCampo.parent("form");
			form.unbind('submit');
			form.submit(function(e) {
				var selectorSource = " #"+aSourceID;
				var srcObj = $(selectorSource);
				if(!srcObj.exists())
				{
					selectorSource = aSourceID;
					srcObj = $(aSourceID).parent();
				}
				elCampo.addClass("loading");
				
				$.ajax({
					url: getAccionUrl(aMod,aAction,"plain")+ "&" +$(this).serialize(),
					success: function(response, status, xhr) {	
						
						elCampo.removeClass("loading");
				  		if (status == "error") {
				  			alert("Ocurrio un Error: " + xhr.status + " " + xhr.statusText);
				  		}else{				  			
				  			$("body").crearDiv("tmp");
							$("#tmp").hide().html("").html(response);
							var grid = $("#tmp").find(selectorSource);
							var footer = $("#tmp").find("footer");
							$("#tmp").html("");
							srcObj.html(grid.html())
							srcObj.parents(".lista").find("footer").html(footer.html());
							
				  			if(aOptions!=null &&
				  			   aOptions.success!=null && 
				  			   (typeof aOptions.success == 'function')) 
				  				aOptions.success();
			
				  		}
					}
						
				});

				e.preventDefault();
			});
			elCampo.unbind('keyup');
			elCampo.keyup(function(event){
				setTimeout("pQn.fn.doFilter("+event.timeStamp+",'"+ elCampo.attr('name') +"')",800);
				pQn.ultimoKeyup = event.timeStamp;
			});
		},
		getStatusResponse: function(data){
			$("body").crearDiv("tmp");
			
			$("#tmp").hide().html("").html(data);
			var status = $("#tmp status").attr('status');
			return status;
		},
		alertError: function(errorMsg){
			alert(errorMsg);
		},
		dialogoGuardar: function(idDialogo,url,titulo,idForm,opciones){
			var nombreBotonGuardar,valueBotonGuardar,anchoDialogo,callback,nombreBotonCancelar;
			if(opciones!=null) {
				nombreBotonGuardar = opciones.nombreBotonGuardar;
				valueBotonGuardar = opciones.valueBotonGuardar;
				anchoDialogo = opciones.anchoDialogo;
				nombreBotonCancelar = opciones.nombreBotonCancelar;
			}
			else
				opciones={};

			if(nombreBotonGuardar==null)	nombreBotonGuardar = 'guardar';
			if(valueBotonGuardar==null)		valueBotonGuardar = 'Guardar';
			if(anchoDialogo==null)			anchoDialogo = 'auto';
			if(nombreBotonCancelar==null)	nombreBotonCancelar = 'cancelar';
				
			if(opciones.modal==null) opciones.modal=true;
			if(opciones.resizable==null) opciones.resizable=false;
			if(opciones.top==null) opciones.top=69;
			if(opciones.width==null) opciones.width=anchoDialogo;
			if(opciones.minHeight==null) opciones.minHeight=30;
			if(opciones.autoOpen==null) opciones.autoOpen=false;
			
			var dlgOpts = opciones;
			dlgOpts.title = titulo;
			
			$("body").crearDiv(idDialogo);
			var dlg = $("#"+idDialogo);
			dlg.html(htmlCargando).load(url,function(){
				dlg.dialog('option','position','center');
				if( opciones.onLoad )
					opciones.onLoad();
				
				var btn = $("form#"+idForm+" input[name='"+nombreBotonGuardar+"']");
				var $procesarForm = function(e){
					$.post('./',$("form#"+idForm).serialize(),
							function(data) {
							var status = pQn.fn.getStatusResponse(data);
							dlg.attr('status',status);
							if(status=='OK')
							{	
								if( opciones.success )
									opciones.success();
								dlg.dialog('close').remove();
							}
							else if(status=='ERR')
								pQn.fn.alertError($("#tmp status").attr('msg'));
						}
					);
					e.preventDefault();
				};
				
				//si en las opciones se define guardarCallback se reemplaza la función de guardar
				if(opciones.guardarCallback)
					$procesarForm = opciones.guardarCallback;					

				if(btn.exists())
					btn.button($procesarForm).click($procesarForm).attr('value',valueBotonGuardar);
				else
					$("form#"+idForm).submit($procesarForm);
				
				var btnCancelar = $("form#"+idForm+" input[name='"+nombreBotonCancelar+"']");
				if(btnCancelar.exists())
					btnCancelar.button().click(function(e){dlg.html("").dialog('close');});
				
				
			});
			dlg.dialog(dlgOpts).dialog('open');
		}	
	},
	window.pQn = pQn;
})(window);

/**
 * Genera una query string para llamar a una accion de un modulo
 * @param modulo nombre del modulo
 * @param accion nombre de la acción
 * @param mostrar
 * @returns {String}
 */
function getAccionUrl(modulo,accion,mostrar) {
	if(mostrar==null) mostrar='full';
	var qs = "?";
	if(modulo!=null)
		qs+="mod="+modulo;
	if(qs!="?")
		qs+="&";
	
	qs += "accion="+accion;
	
	qs+="&";
	qs += "display="+mostrar;
	
	return qs;
}

function getUrlBaja(mod,id){
	return getAccionUrl(mod,'baja','plain')+'&id='+id;
}

function getUrlModif(mod,id){
	return getAccionUrl(mod,'modif','plain')+'&id='+id;
}

function getUrlAlta(mod){
	return getAccionUrl(mod,'alta','plain');
}

/**
 * efectúa los binds de los botones de alta, modificación y filtro
 */
jQuery['setupButtons']=function(selectorBotonera,selectorBotones) {
	pQn.fn.setupButtons(selectorBotonera,selectorBotones);	
};

function setupButtons(selectorBotonera,selectorBotones){
	$.setupButtons(selectorBotonera,selectorBotones);
}


var htmlCargando = "<div class='dialog-load'>cargando...</div>";
jQuery.fn.exists = function(){return this.length>0;};
jQuery.fn.crearDiv = function(idDiv){
	if(!$("#"+idDiv).exists())
		return this.append("<div id='"+idDiv+"'></div>");
	return $("#"+idDiv);
};


/**
 * Metodo para realiar el filtro automatico, enviando el submit del form
 * @param tiempo
 * @param aInputName
 * @param aFormID Id del form
 */
jQuery['doFilter']=function(tiempo,aInputName){	
	pQn.fn.doFilter(tiempo,aInputName);
};

/**
 * Metodo agregado a los objetos jQuery para realizar bind de input de filtro, 
 * con una espera para realizar filtro automatico 
 * @param aSourceID id o selector Objeto desde el cual se carga TargetID
 * @param aMod Modulo a ejecutar
 * @param aAction Metodo a Ejecutar
 * @param aOptions objeto con opciones (success).
 */
jQuery.fn.keyUpFilter = function(aSourceID,aMod,aAction,aOptions){
	pQn.fn.keyUpFilter($(this),aSourceID,aMod,aAction,aOptions);
};


/**
 * Metodo para eliminar una entidad usando ajax procesando la respuesta <status>
 * @param id el id del elemento a eliminar
 * @param mod el id del elemento a eliminar
 * @returns {Boolean} 
 */
function eliminar(id,mod,nombreEntidad){
	var ret=false;
	var strConfirmQ = "¿Está seguro de eliminar el elemento seleccionado?"; 
	if(nombreEntidad!=null && nombreEntidad!='')
		strConfirmQ = "¿Está seguro de eliminar "+nombreEntidad+"?";
		
	if(confirm(strConfirmQ))
	{
		$.ajax({
			url: getAccionUrl(mod,'baja','plain')+"&id="+id,
			type: 'POST',
			data: '' , 
			async:false,
			success: function(data,  textStatus, jqXHR) {
				$("body").crearDiv('tmp');
				$("#tmp").hide().html(data);
				if($("#tmp status").attr('status')!='OK'){
					pQn.fn.alertError($("#tmp status").attr('msg'));
					ret=false;
				}else{
					if($('#formFiltro').exists())
						$('#formFiltro').submit();
					else
						alert($("#tmp status").attr('msg'));
					ret=true;
				}
			}
		});
	}
	return ret;
}

jQuery['dialogoGuardar'] = function(idDialogo,url,titulo,idForm,opciones){
	pQn.fn.dialogoGuardar(idDialogo,url,titulo,idForm,opciones);
};

pQn.fn.bindAltaBtn = function(idBotonAlta,idDialogo,nombreEntidadPrincipal,idForm,options) {
	$("#"+idBotonAlta).botonAlta(idDialogo,"Nuevo "+nombreEntidadPrincipal ,idForm,options);
};

pQn.fn.bindModifBtn = function(href,idDialogo,nombreEntidadPrincipal,idForm,options) {
	$.dialogoGuardar(idDialogo,href+"&display=plain","Modificar "+nombreEntidadPrincipal,idForm,options);
};

pQn.fn.bindFiltro =  function(nombreCampoFiltro,idLista,nombreModulo,options) {
	$("input[name='"+nombreCampoFiltro+"']").keyUpFilter(idLista,nombreModulo,"listar",options);
};
	

/**
 * 
 */
jQuery['initModulo'] = function(idDialogo,nombreEntidadPrincipal,idForm,nombreCampoFiltro,idBotonAlta,idLista,aMod,options) {
	pQn.fn.initModulo(idDialogo,nombreEntidadPrincipal,idForm,nombreCampoFiltro,idBotonAlta,idLista,aMod,options);
};
pQn.fn.initModulo = function(idDialogo,nombreEntidadPrincipal,idForm,nombreCampoFiltro,idBotonAlta,idLista,aMod,options) {
	var $formFiltro = $("input[name='"+nombreCampoFiltro+"']").parent("form");

	var optionsAltaModif= { success:function(){$formFiltro.submit();} };
	var optionsFiltro= { success:function(){$.setupButtons();} };
	
	if(opcionesInit) //si hay una objeto global opcionesInit mergeo a las options lo que está definido en ella
	{
		if(options==null)
			options = {};
		options = $.extend(options,opcionesInit);
	}
	
	if(options)
	{
		optionsAltaModif = $.extend(optionsAltaModif,options);
		optionsFiltro = $.extend(optionsFiltro,options);
	}
	

	
	$fnBindAltaBtn = function(){ 
		pQn.fn.bindAltaBtn(idBotonAlta,idDialogo, nombreEntidadPrincipal, idForm, optionsAltaModif);		
	};
	$fnBindModifBtn = function(href){ 
		pQn.fn.bindModifBtn(href, idDialogo, nombreEntidadPrincipal, idForm, optionsAltaModif);
	};
	$fnBindFiltro = function() {
		pQn.fn.bindFiltro(nombreCampoFiltro, idLista, aMod, optionsFiltro);
	};
	$.setupButtons();
	pQn.fn.setupPaginacion(idLista,$formFiltro);
	
	if((options) && ('onInit' in options) && (typeof options.onInit == 'function'))
		options.onInit();
	
};

function initModulo(idDialogo,nombreEntidadPrincipal,idForm,nombreCampoFiltro,idBotonAlta,idLista,aMod,options) {
	$.initModulo(idDialogo,nombreEntidadPrincipal,idForm,nombreCampoFiltro,idBotonAlta,idLista,aMod,options);
}


/**
 * Crea una funcion de jQuery que le asigna la funcionalidad de boton de alta al elemento que la llama
 * suponiendo que es un contenedor html (div,td,span...) que tiene un a href con la URL para el alta
 */
jQuery.fn.botonAlta = function(idDialogo,tituloDialogo,idFormAlta,opciones) {
	var linkAlta = $(this).addClass("ui-state-default ui-corner-all").css('float','right').css('display','block').find('a');
	if(!linkAlta.find("span.ui-icon-plus").exists())
		linkAlta.append('<span class="ui-icon ui-icon-plus"></span>');
	$(this).delegate("a","click",function(e){
		e.preventDefault();
		$.dialogoGuardar(idDialogo,$(this).attr('href')+"&display=plain",tituloDialogo,idFormAlta,opciones);
	});
};


/**
 * Redimensiona verticalmente la pantalla
 */
function resize() {
	var newHeight = $(window).height()-$("body > header").outerHeight(true)-$("footer").outerHeight(true)-1;

	if($("#main-section").outerHeight()<=newHeight)
		$("#main-section").css('min-height',newHeight+'px');
	else
		$("#main-section").css('min-height','auto');
}
window.onresize = resize;
//window.onscroll = resize;

$(document).ready(function(){
	resize();
	
	//Menú de usuario
	$('.dropdown-toggle').dropdown();
	
	/**
	 * Sidebar accordion
	 */
	$('#sidebar ul li.has-sub > a').click(function(e) {
		e.preventDefault();
	    $(e.target).next('ul').slideToggle().parent().toggleClass('open');;
	});
	
	$("#sidebar ul li.active").parents("li").addClass("active open");
});

/*
function confirm(msg,title) {
	
	//Variables default
	if(msg == null) title = "¿Está seguro que desea continuar?";
	if(title == null) title = "Confirmación";
	
	$("body").crearDiv("tmp-conf");
	var $dlg = $( "#tmp-conf" );
	$dlg.html(msg);
	$dlg.dialog({
	      resizable: false,
	      height:140,
	      title: title,
	      modal: true,
	      buttons: {
	        "Aceptar": function() {
	          $( this ).dialog( "close" );
	          return true;
	        },
	        "Cancelar": function() {
	          $( this ).dialog( "close" );
	          return false;
	        }
	      }
	    });
}
*/