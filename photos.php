<?php
/**
* @project		Travel Planner
* @author		Olivier Gaillard
* @version		1.0 du 04/01/2015
* @desc			Controleur des objets : photos
*/

require_once( "inc/prepend.php" );
//$user->isLoggedIn(); // Espace privé

// Récupération des variables
$action			= Utils::get_input('action','both');
$id				= Utils::get_input('id','both');
$place_id		= Utils::get_input('place_id','post');
$path			= Utils::get_input('path','post');
$caption		= Utils::get_input('caption','post');

$photos_manager = new PhotoManager($bdd);
// Breadcrumbs
$bc = new Breadcrumb($bdd, "photos");
$smarty->assign("breadcrumbs", $bc->getCrumbs());

switch($action) {
	
	case "add" :
		$smarty->assign("photo", new Photo(array("id" => -1)));
		$smarty->assign("content", "photos/edit.tpl.html");
		$smarty->display("main.tpl.html");
		break;
	
	case "edit" :
		$smarty->assign("photo", $photos_manager->getPhoto($id));
		$smarty->assign("content","photos/edit.tpl.html");
		$smarty->display("main.tpl.html");
		break;

	case "save" :
		$data = array("id" => $id, "place_id" => $place_id, "path" => $path, "caption" => $caption);
		$photos_manager->savePhoto(new Photo($data));
		$log->notification($translate->__('the_photo_has_been_saved'));
		Utils::redirection("photos.php");
		break;

	case "delete" :
		$photo = $photos_manager->getPhoto($id);
		if ($photos_manager->deletePhoto($photo)) {
			$log->notification($translate->__('the_photo_has_been_deleted'));
		}
		Utils::redirection("photos.php");
		break;

	default:
		$smarty->assign("titre", $translate->__('list_of_photos'));
		$smarty->assign("photos", $photos_manager->getPhotos());
		$smarty->assign("content", "photos/list.tpl.html");
		$smarty->display("main.tpl.html");
}
require_once( "inc/append.php" );
?>