<?php
/**
* @project		
* @author		Olivier Gaillard
* @version		1.0 du 29/11/2014
* @desc			Controleur des objets : continents
*/

require_once( "inc/prepend.php" );
$user->isLoggedIn(); // Espace privé

// Récupération des variables
$action			= Utils::get_input('action','both');
$id				= Utils::get_input('id','both');
$name			= Utils::get_input('name','post');

$continents_manager = new ContinentManager($bdd);

switch($action) {
	
	case "add" :
		$smarty->assign("continent", new Continent(array("id" => -1)));
		$smarty->assign("content", "continents/edit.tpl.html");
		$smarty->display("main.tpl.html");
		break;
	
	case "edit" :
		$smarty->assign("continent", $continents_manager->getContinent($id));
		$smarty->assign("content","continents/edit.tpl.html");
		$smarty->display("main.tpl.html");
		break;

	case "save" :
		$data = array("id" => $id, "name" => $name);
		$continents_manager->saveContinent(new Continent($data));
		$log->alert($translate->__('the_continent_has_been_saved'));
		Utils::redirection("continents.php");
		break;

	case "delete" :
		$continent = $continents_manager->getContinent($id);
		if ($continents_manager->deleteContinent($continent)) {
			$log->alert($translate->__('the_continent_has_been_deleted'));
		}
		Utils::redirection("continents.php");
		break;

	default:
		$smarty->assign("titre", $translate->__('list_of_continents'));
		$smarty->assign("continents", $continents_manager->getContinents());
		$smarty->assign("content", "continents/list.tpl.html");
		$smarty->display("main.tpl.html");
}
require_once( "inc/append.php" );
?>