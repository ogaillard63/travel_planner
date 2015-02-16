<?php
/**
 * @project
 * @author		Olivier Gaillard
 * @version		1.0 du 29/11/2014
 * @desc			Controleur des objets : activities
 */



require_once( "inc/prepend.php" );

// Récupération des variables
$action			= Utils::get_input('action','both');
$id				= Utils::get_input('id','both');
$place_id		= Utils::get_input('place_id','both');
$type_id		= Utils::get_input('type_id','post');
$file_path		= Utils::get_input('file_path','post');
$gps_coord		= Utils::get_input('gps_coord','post');
$name			= Utils::get_input('name','post');
$getting_there	= Utils::get_input('getting_there','post');
$description	= Utils::get_input('description','post');
$duration		= Utils::get_input('duration','post');

$activities_manager = new ActivitiesManager($bdd);
$bc = new Breadcrumb($bdd, "activities", $place_id, $action); // Breadcrumbs

if ($user->isLoggedIn() ) { // BO

	switch($action) {

		case "add" :
			$smarty->assign("activity", new Activity(array("id" => -1, "place_id" => $place_id)));
			$type_manager = new TypesManager($bdd);
			$smarty->assign("types", $type_manager->getTypesForSelect());
			$smarty->assign("content", "activities/edit.tpl.html");
			break;

		case "edit" :
			$smarty->assign("activity", $activities_manager->getActivity($id));
			$type_manager = new TypesManager($bdd);
			$smarty->assign("types", $type_manager->getTypesForSelect());
			$smarty->assign("content","activities/edit.tpl.html");
			break;

		case "save" :
			//die($_FILES['gps_file']['name']);
			if (!empty($_FILES['gps_file']['name']) && (basename($file_path) != basename($_FILES['gps_file']['name']))) {
				$file_gps = new UploadFile($_FILES['gps_file'], 'res/files/gps', array("gpx"));
				$result_upload = $file_gps->upload();
				$file_path = $result_upload["file_path"];
			}
			// $log->alert($result_upload['error']);
			$data = array("id" => $id, "place_id" => $place_id, "type_id" => $type_id, "gps_coord" => $gps_coord, "name" => $name,
				"file_path" => $file_path, "getting_there" => $getting_there, "description" => $description, "duration" => $duration);
			$activities_manager->saveActivity(new Activity($data));
			$log->alert($translate->__('the_activity_has_been_saved'));
			//$log->alert(basename($file_path) ." - " . basename($_FILES['gps_file']['name']) );
			Utils::redirection("activities.php?place_id=".$place_id);
			break;

		case "delete" :
			$activity = $activities_manager->getActivity($id);
			if ($activities_manager->deleteActivity($activity)) {
				$log->alert($translate->__('the_activity_has_been_deleted'));
			}
			Utils::redirection("activities.php?place_id=".$place_id);
			break;

		default:
			$smarty->assign("titre", $translate->__('list_of_activities'));
			$places_manager = new PlacesManager($bdd);
			$smarty->assign("place", $places_manager->getPlace($place_id));
			$smarty->assign("activities", $activities_manager->getActivities($place_id));
			$smarty->assign("place_id", $place_id); // btn add
			$smarty->assign("content", "activities/list.tpl.html");
	}
}
else { // FO
	switch($action) {
		case "view" :
			$bc = new Breadcrumb($bdd, "activity", $id, $action); // Breadcrumbs
			$smarty->assign("activity", $activities_manager->getActivity($id, true));
			$smarty->assign("content", "activities/view_front.tpl.html");
			break;

		default:
			$smarty->assign("titre", $translate->__('list_of_activities'));
			$places_manager = new PlacesManager($bdd);
			$smarty->assign("place", $places_manager->getPlace($place_id));
			$smarty->assign("place_id", $place_id); // btn add
			$smarty->assign("activities", $activities_manager->getActivities($place_id));
			$infos_manager = new InfosManager($bdd);
			$smarty->assign("infos", $infos_manager->getInfos($place_id));
			$smarty->assign("content", "activities/list_front.tpl.html");
	}
}

$smarty->assign("breadcrumbs", $bc->getCrumbs());
$smarty->display("main.tpl.html");
require_once( "inc/append.php" );
?>