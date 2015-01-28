<?php
/**
* @project		
* @author		Olivier Gaillard
* @version		1.0 du 28/11/2014
* @desc			Controleur des objets : routes
*/

require_once( "inc/prepend.php" );
$user->isLoggedIn(); // Espace privé

// Récupération des variables
$action				= Utils::get_input('action','both');
$id					= Utils::get_input('id','both');
$country_id			= Utils::get_input('country_id','both');
$from_place_id		= Utils::get_input('from_place_id','post');
$to_place_id		= Utils::get_input('to_place_id','post');
$name				= Utils::get_input('name','post');
$description		= Utils::get_input('description','post');

$routes_manager = new RoutesManager($bdd);

// Breadcrumbs
$bc = new Breadcrumb($bdd, "places", $country_id, $action);
$smarty->assign("breadcrumbs", $bc->getCrumbs());

switch($action) {
	
	case "add" :
		$smarty->assign("route", new Route(array("id" => -1, "country_id" => $country_id)));
		$places_manager = new PlacesManager($bdd);
		$smarty->assign("places", $places_manager->getPlacesForSelect($country_id));
		$smarty->assign("content", "routes/edit.tpl.html");
		$smarty->display("main.tpl.html");
		break;
	
	case "edit" :
		$smarty->assign("route", $routes_manager->getRoute($id));
		$places_manager = new PlacesManager($bdd);
		$smarty->assign("places", $places_manager->getPlacesForSelect($country_id));
		$smarty->assign("content","routes/edit.tpl.html");
		$smarty->display("main.tpl.html");
		break;

	case "save" :
		$data = array("id" => $id, "country_id" => $country_id, "from_place_id" => $from_place_id, "to_place_id" => $to_place_id, "name" => $name, "description" => $description);
		$routes_manager->saveRoute(new Route($data));
		$log->alert($translate->__('the_route_has_been_saved'));
		Utils::redirection("routes.php?country_id=".$country_id);
		break;

	case "delete" :
		if ($user->getProfil() >= ADMIN) {
			$route = $routes_manager->getRoute($id);
			if ($routes_manager->deleteRoute($route)) {
				$log->alert($translate->__('the_route_has_been_deleted'));
			}
		}
		else $log->alert($translate->__('you_are_not_allowed_to_do_this_action'));
		Utils::redirection("routes.php?country_id=".$country_id);
		break;

	default:
		$smarty->assign("titre", $translate->__('list_of_routes'));
		$smarty->assign("routes", $routes_manager->getRoutes($country_id, true));
		$smarty->assign("country_id", $country_id);
		$smarty->assign("content", "routes/list.tpl.html");
		$smarty->display("main.tpl.html");
}
require_once( "inc/append.php" );
?>