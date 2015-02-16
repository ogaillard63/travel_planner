<?php
/**
* @project		Travel Planner
* @author		Olivier Gaillard
* @version		1.0 du 16/02/2015
* @desc			Controleur des objets : infos
*/

require_once( "inc/prepend.php" );
//$user->isLoggedIn(); // Espace privé

// Récupération des variables
$action				= Utils::get_input('action','both');
$id					= Utils::get_input('id','both');
$page				= Utils::get_input('page','both');
$place_id			= Utils::get_input('place_id','both');
$title				= Utils::get_input('title','post');
$description		= Utils::get_input('description','post');

$infos_manager = new InfosManager($bdd);
$bc = new Breadcrumb($bdd, "activities", $place_id, $action); // Breadcrumbs

if ($user->isLoggedIn() ) { // BO
	switch ($action) {

		case "add" :
			$smarty->assign("info", new Info(array("id" => -1, "place_id" => $place_id)));
			$smarty->assign("content", "infos/edit.tpl.html");
			break;

		case "edit" :
			$smarty->assign("info", $infos_manager->getInfo($id));
			$smarty->assign("content", "infos/edit.tpl.html");
			break;

		case "save" :
			$data = array("id" => $id, "place_id" => $place_id, "title" => $title, "description" => $description);
			$infos_manager->saveInfo(new Info($data));
			$log->notification($translate->__('the_info_has_been_saved'));
			Utils::redirection("infos.php?place_id=" . $place_id);
			break;

		case "delete" :
			$info = $infos_manager->getInfo($id);
			if ($infos_manager->deleteInfo($info)) {
				$log->notification($translate->__('the_info_has_been_deleted'));
			}
			Utils::redirection("infos.php?place_id=" . $place_id);
			break;

		default:
			$smarty->assign("titre", $translate->__('list_of_infos'));
			$places_manager = new PlacesManager($bdd);
			$smarty->assign("place", $places_manager->getPlace($place_id));
			$smarty->assign("infos", $infos_manager->getInfos($place_id));
			$smarty->assign("content", "infos/list.tpl.html");
	}
}
else {
	switch ($action) {

		case "view" :
			$bc = new Breadcrumb($bdd, "info", $id, $action); // Breadcrumbs
			$smarty->assign("info", $infos_manager->getInfo($id));
			$smarty->assign("content", "infos/view_front.tpl.html");
			break;

		default:
			/*
			$smarty->assign("titre", $translate->__('list_of_infos'));
			$places_manager = new PlacesManager($bdd);
			$smarty->assign("place", $places_manager->getPlace($place_id));
			$smarty->assign("place_id", $place_id); // btn add
			$smarty->assign("activities", $activities_manager->getActivities($place_id));
			$infos_manager = new InfosManager($bdd);
			$smarty->assign("infos", $infos_manager->getInfos($place_id));
			$smarty->assign("content", "activities/list_front.tpl.html");
			*/
	}
}

$smarty->assign("breadcrumbs", $bc->getCrumbs());
$smarty->display("main.tpl.html");
require_once( "inc/append.php" );
?>