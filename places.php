<?php
/**
* @project		
* @author		Olivier Gaillard
* @version		1.0 du 29/11/2014
* @desc			Controleur des objets : places
*/

require_once( "inc/prepend.php" );
$user->isLoggedIn(); // Espace privé


$action				= Utils::get_input('action','both');
$page				= Utils::get_input('page','both');
$id					= Utils::get_input('id','both');
$code				= Utils::get_input('code','both');
$country_id			= Utils::get_input('country_id','both');
$gps_coord			= Utils::get_input('gps_coord','post');
$name				= Utils::get_input('name','post');
$description		= Utils::get_input('description','post');

// convert world map click code
$valid_codes = array("cl" => 22, "bo" => 21, "pe" => 20, "us" => 31, "ca" => 34, "nz" => 30, "au" => 11);
if (!empty($code)) {
	if (array_key_exists($code, $valid_codes)) $country_id = $valid_codes[$code];
	else Utils::redirection("index.php"); // code invalid
	}
$place_manager = new PlaceManager($bdd);

// Breadcrumbs
$bc = new Breadcrumb($bdd, "places", $country_id, $action);
$smarty->assign("breadcrumbs", $bc->getCrumbs());

switch($action) {
	
	case "add" :
		$smarty->assign("place", new Place(array("id" => -1, "country_id" => $country_id)));
		$smarty->assign("content", "places/edit.tpl.html");
		$smarty->display("main.tpl.html");
		break;
	
	case "edit" :
		$smarty->assign("place", $place_manager->getPlace($id));
		$smarty->assign("content","places/edit.tpl.html");
		$smarty->display("main.tpl.html");
		break;

	case "save" :
		$data = array("id" => $id, "country_id" => $country_id, "gps_coord" => $gps_coord, "name" => $name, "description" => $description);
		$place_manager->savePlace(new Place($data));
		$log->alert($translate->__('the_place_has_been_saved'));
		Utils::redirection("places.php?country_id=".$country_id);
		break;

	case "pdf" :
		// get the HTML
		ob_start();
		$place = $place_manager->getPlace($id, true);
		$activities_manager = new ActivitiesManager($bdd);
		$smarty->assign("place", $place);
		$smarty->assign("activities", $activities_manager->getActivities($place->getId()));
		$smarty->display("pdf/styles.tpl.html");
		$smarty->display("pdf/place.tpl.html");
		$content = ob_get_clean();
		// convert to PDF
		require_once('inc/html2pdf/html2pdf.class.php');
		try
		{
			$html2pdf = new HTML2PDF('P', 'A4', 'fr', true, 'UTF-8', 3);
			$html2pdf->pdf->SetDisplayMode('fullpage');
			$html2pdf->writeHTML($content, isset($_GET['vuehtml']));
			$html2pdf->Output( str_replace(" ", "_", $place->getName()).'.pdf');
		}
		catch(HTML2PDF_exception $e) {
			echo $e;
			exit;
		}
		break;

	case "delete" :
		if ($user->getProfil() >= ADMIN) {
			$place = $place_manager->getPlace($id);
			if ($place_manager->deletePlace($place)) {
				$log->alert($translate->__('the_place_has_been_deleted'));
			}
		}
		else $log->alert($translate->__('you_are_not_allowed_to_do_this_action'));
		Utils::redirection("places.php?country_id=".$country_id);
		break;

	default:
		$rpp = 20;
		$smarty->assign("titre", $translate->__('list_of_places'));
		if (empty($page)) $page = 1; // default page
		$smarty->assign("allPlaces", $place_manager->getPlaces($country_id)); // for map
		$smarty->assign("places", $place_manager->getPlacesByPage($country_id, $page, $rpp));

		$pagination = new Pagination($page, $place_manager->getMaxPlaces($country_id), $rpp); // pagination
		$smarty->assign("country_id", $country_id);
		$smarty->assign("btn_infos", $pagination->getNavigation());
		$smarty->assign("content", "places/list.tpl.html");
		$smarty->display("main.tpl.html");
}
require_once( "inc/append.php" );
?>