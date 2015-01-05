<?php
/**
* @project		
* @author		Olivier Gaillard
* @version		1.0 du 29/11/2014
* @desc			Controleur des objets : countries
*/

require_once( "inc/prepend.php" );
$user->isLoggedIn(); // Espace privé

// Récupération des variables
$action				= Utils::get_input('action','both');
$id					= Utils::get_input('id','both');
$continent_id		= Utils::get_input('continent_id','both');
$name				= Utils::get_input('name','post');
$description		= Utils::get_input('description','post');
$flag				= Utils::get_input('flag','post');

$countries_manager = new CountriesManager($bdd);

// Breadcrumbs
$bc = new Breadcrumb($bdd, "countries", $continent_id, $action);
$smarty->assign("breadcrumbs", $bc->getCrumbs());

switch($action) {
	
	case "add" :
		$smarty->assign("country", new Country(array("id" => -1, "continent_id", $continent_id)));
		$continents_manager = new ContinentsManager($bdd);
		$smarty->assign("continents", $continents_manager->getContinentsForSelect());
		$smarty->assign("content", "countries/edit.tpl.html");
		$smarty->display("main.tpl.html");
		break;
	
	case "edit" :
		$smarty->assign("country", $countries_manager->getCountry($id));
		$continents_manager = new ContinentsManager($bdd);
		$smarty->assign("continents", $continents_manager->getContinentsForSelect());
		$smarty->assign("content","countries/edit.tpl.html");
		$smarty->display("main.tpl.html");
		break;

	case "save" :
		$data = array("id" => $id, "continent_id" => $continent_id, "name" => $name, "description" => $description, "flag" => $flag);
		$countries_manager->saveCountry(new Country($data));
		$log->alert($translate->__('the_country_has_been_saved'));
		Utils::redirection("countries.php?continent_id=".$continent_id);
		break;

	case "delete" :
		if ($user->getProfil() >= ADMIN) {
			$country = $countries_manager->getCountry($id);
			if ($countries_manager->deleteCountry($country)) {
				$log->alert($translate->__('the_country_has_been_deleted'));
			}
		}
		else $log->alert($translate->__('you_are_not_allowed_to_do_this_action'));
		Utils::redirection("countries.php?continent_id=".$continent_id);
		break;

	case "pdf" :
		// get the HTML
		ob_start();

		$country = $countries_manager->getCountry($id);
		$smarty->assign("country", $country);
		$places_manager = new PlacesManager($bdd);
		$smarty->assign("places", $places_manager->getPlaces($country->getId()));

		$activities_manager = new ActivitiesManager($bdd);
		$smarty->assign("activities", $activities_manager->getActivitiesForCountry($country->getId()));

		$smarty->display("pdf/styles.tpl.html");
		$smarty->display("pdf/country.tpl.html");
		$content = ob_get_clean();
		// convert to PDF
		require_once('inc/html2pdf/html2pdf.class.php');
		try
		{
			$html2pdf = new HTML2PDF('P', 'A4', 'fr', true, 'UTF-8', 3);
			$html2pdf->pdf->SetDisplayMode('fullpage');
			$html2pdf->writeHTML($content, isset($_GET['vuehtml']));
			$html2pdf->Output( str_replace(" ", "_", $country->getName()).'.pdf');
		}
		catch(HTML2PDF_exception $e) {
			echo $e;
			exit;
		}
		break;

	default:
		$smarty->assign("titre", $translate->__('list_of_countries'));
		$smarty->assign("countries", $countries_manager->getCountries($continent_id));
		$smarty->assign("continent_id", $continent_id); // btn add 
		$smarty->assign("breadcrumb", array("continent" => "Amerique du Sud")); 
		$smarty->assign("content", "countries/list.tpl.html");
		$smarty->display("main.tpl.html");
}
require_once( "inc/append.php" );
?>