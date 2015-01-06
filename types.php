<?php
/**
* @project		
* @author		Olivier Gaillard
* @version		1.0 du 29/11/2014
* @desc			Controleur des objets : types
*/

require_once( "inc/prepend.php" );
$user->isLoggedIn(); // Espace privé

// Récupération des variables
$action			= Utils::get_input('action','both');
$id				= Utils::get_input('id','both');
$name			= Utils::get_input('name','post');
$key			= Utils::get_input('key','post');

$types_manager = new TypesManager($bdd);

switch($action) {
	
	case "add" :
		$smarty->assign("type", new Type(array("id" => -1)));
		$smarty->assign("content", "types/edit.tpl.html");
		$smarty->display("main.tpl.html");
		break;
	
	case "edit" :
		$smarty->assign("type", $types_manager->getType($id));
		$smarty->assign("content","types/edit.tpl.html");
		$smarty->display("main.tpl.html");
		break;

	case "save" :
		$data = array("id" => $id, "name" => $name, "key" => $key);
		$types_manager->saveType(new Type($data));
		$log->alert($translate->__('the_type_has_been_saved'));
		Utils::redirection("types.php");
		break;

	case "delete" :
		$type = $types_manager->getType($id);
		if ($types_manager->deleteType($type)) {
			$log->alert($translate->__('the_type_has_been_deleted'));
		}
		Utils::redirection("types.php");
		break;

	default:
		$smarty->assign("titre", $translate->__('list_of_types'));
		$smarty->assign("types", $types_manager->getTypes());
		$smarty->assign("content", "types/list.tpl.html");
		$smarty->display("main.tpl.html");
}
require_once( "inc/append.php" );
?>