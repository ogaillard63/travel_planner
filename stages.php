<?php
/**
* @project		Travel Planner
* @author		Olivier Gaillard
* @version		1.0 du 28/01/2015
* @desc			Controleur des objets : stages
*/

require_once( "inc/prepend.php" );
//$user->isLoggedIn(); // Espace privé

// Récupération des variables
$action				= Utils::get_input('action','both');
$id					= Utils::get_input('id','both');
$page				= Utils::get_input('page','both');
$country_id			= Utils::get_input('country_id','both');
$place_id			= Utils::get_input('place_id','post');
$activities			= Utils::get_input('activities','post');
$list_activities    = (is_array($activities))?implode(",", $activities):"";
$arrival_date		= Utils::get_input('arrival_date','post');
$position			= Utils::get_input('position','post');
$duration			= Utils::get_input('duration','post');
$description		= Utils::get_input('description','post');

$stages_manager 	= new StageManager($bdd);

// Breadcrumbs
$bc = new Breadcrumb($bdd, "stages", $country_id, $action);
$smarty->assign("breadcrumbs", $bc->getCrumbs());

switch($action) {
	
	case "add" :
		$smarty->assign("stage", new Stage(array("id" => -1, "country_id" => $country_id)));
		$places_manager = new PlacesManager($bdd);
		$smarty->assign("places", $places_manager->getPlaces($country_id));
		$smarty->assign("content", "stages/edit.tpl.html");
		$smarty->display("main.tpl.html");
		break;
	
	case "edit" :
		$stage =  $stages_manager->getStage($id);
		$smarty->assign("stage", $stage);
		$places_manager = new PlacesManager($bdd);
		$smarty->assign("places", $places_manager->getPlaces($country_id));
		$activities_manager = new ActivitiesManager($bdd);
		$smarty->assign("activities", $activities_manager->getActivities($stage->place_id, true));
		$smarty->assign("content","stages/edit.tpl.html");
		$smarty->display("main.tpl.html");
		break;

	case "save" :
		$data = array("id" => $id, "country_id" => $country_id, "place_id" => $place_id, "activities" => $list_activities,
			"arrival_date" => $arrival_date, "duration" => $duration, "description" => $description);
		$stages_manager->saveStage(new Stage($data));
		$log->notification($translate->__('the_stage_has_been_saved'));
		Utils::redirection("stages.php?country_id=".$country_id);
		break;

	case "delete" :
		$stage = $stages_manager->getStage($id);
		if ($stages_manager->deleteStage($stage)) {
			$log->notification($translate->__('the_stage_has_been_deleted'));
		}
		Utils::redirection("stages.php?country_id=".$country_id);
		break;

	default:
		$smarty->assign("titre", $translate->__('list_of_stages'));
		/*$rpp = 5;
		if (empty($page)) $page = 1; // Display first pagination page
		$smarty->assign("stages", $stages_manager->getStagesByPage($_id, $page, $rpp));
		$pagination = new Pagination($page, $stages_manager->getMaxStages($_id), $rpp);
		$smarty->assign("btn_nav", $pagination->getNavigation());
		//$smarty->assign("_id", $_id);
		*/
		$smarty->assign("stages", $stages_manager->getStages($country_id, true));
		$smarty->assign("country_id", $country_id);
		$smarty->assign("content", "stages/list.tpl.html");
		$smarty->display("main.tpl.html");
}
require_once( "inc/append.php" );
?>