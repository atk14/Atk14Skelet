<?php
/**
 *	{!"edit"|icon}
 *	{!"remove"|icon}
 */
function smarty_modifier_icon($style){
	return sprintf('<span class="glyphicon glyphicon-%s"></span>',$style);
}
