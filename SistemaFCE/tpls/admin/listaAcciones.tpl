{*smarty*}
{* requiere que se le asignen:
	 $modName string el nombre de modulo
	 $entidad Entidad objeto de la entidad a la cual se le efectuarían las acciones 
*}
<div>
	<a href="?mod={$modName}&accion=modif&id={$entidad->getId()}"><span class="ui-icon ui-icon-pencil"></span></a>
</div> 				
<div>
	<span class="ui-icon ui-icon-trash" onclick="eliminar({$entidad->getId()},'{$modName}');"></span>
</div>