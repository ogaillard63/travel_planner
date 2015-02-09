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
$place_id			= Utils::get_input('place_id','post');
$country_id			= Utils::get_input('country_id','get');
$activities			= Utils::get_input('activities','post');
$activities_ids    = (is_array($activities))?implode(",", $activities):"";
$arrival_date		= Utils::dateToSql(Utils::get_input('arrival_date','post'));
$position			= Utils::get_input('position','post');
$duration			= Utils::get_input('duration','post');
$description		= Utils::get_input('description','post');

$stages_manager 	= new StageManager($bdd);

// Breadcrumbs
$bc = new Breadcrumb($bdd, "stages",0 , $action);
$smarty->assign("breadcrumbs", $bc->getCrumbs());
if ($user->isLoggedIn() ) { // BO
	switch($action) {

		case "add" :
			$smarty->assign("stage", new Stage(array("id" => -1)));
			$places_manager = new PlacesManager($bdd);
			$smarty->assign("places", $places_manager->getPlaces());
			$smarty->assign("content", "stages/edit.tpl.html");
			$smarty->display("main.tpl.html");
			break;

		case "edit" :
			$stage =  $stages_manager->getStage($id);
			$smarty->assign("stage", $stage);
			$places_manager = new PlacesManager($bdd);
			$smarty->assign("places", $places_manager->getPlaces());
			$activities_manager = new ActivitiesManager($bdd);
			$smarty->assign("activities", $activities_manager->getActivities($stage->place_id, true));
			$smarty->assign("content","stages/edit.tpl.html");
			$smarty->display("main.tpl.html");
			break;

		case "save" :
			$data = array("id" => $id, "place_id" => $place_id, "activities_ids" => $activities_ids,
				"arrival_date" => $arrival_date, "duration" => $duration, "description" => $description);
			$stages_manager->saveStage(new Stage($data));
			$log->notification($translate->__('the_stage_has_been_saved'));
			Utils::redirection("stages.php");
			break;

		case "delete":
			$stage = $stages_manager->getStage($id);
			if ($stages_manager->deleteStage($stage)) {
				$log->notification($translate->__('the_stage_has_been_deleted'));
				}
			Utils::redirection("stages.php");
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
			$smarty->assign("stages", $stages_manager->getStages(true));
			$smarty->assign("content", "stages/homepage.tpl.html");
			$smarty->display("main.tpl.html");
	}
}
else { // FO
	$smarty->assign("titre", $translate->__('list_of_stages'));
	$smarty->assign("stages", $stages_manager->getStages(true, $country_id));
	$smarty->assign("content", "stages/timeline.tpl.html");
	$smarty->display("main.tpl.html");
}
require_once( "inc/append.php" );
?>