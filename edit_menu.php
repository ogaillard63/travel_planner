<?php
/**
 * @project		WebApp Generator
 * @author		Olivier Gaillard
 * @version		1.0 du 27/11/2014
 * @desc	   	Gestion du menu
 */

define('MENU_TPL_FILEPATH',  'tpl/menu.tpl.html');
 
require_once( "inc/prepend.php" );
//$user->isLoggedIn(); // Espace privé

// Récupération des variables
$action			= Utils::get_input('action','both');
$content		= Utils::get_input('content','post');


switch($action) {
	
	case "save" :
		file_put_contents(MENU_TPL_FILEPATH, $content);
		//$log->notification($translate->__('the_country_has_been_saved'));
		Utils::redirection("edit_menu.php");
	break;
		
	default :
	$smarty->assign("file_content", file_get_contents(MENU_TPL_FILEPATH));
	$smarty->assign("titre", "Modification du menu"); 
	$smarty->assign("content", "misc/edit_menu.tpl.html");
	$smarty->display("main.tpl.html");
}
require_once( "inc/append.php" );
?>