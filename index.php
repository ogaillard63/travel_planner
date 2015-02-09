<?php
/**
 * @project		WebApp Generator
 * @author		Olivier Gaillard
 * @version		1.0 du 04/06/2012
 * @desc	   	Accueil
 */
require_once( "inc/prepend.php" );
$user->isLoggedIn(); // Espace privé

// Récupération des variables
$action			= Utils::get_input('action','both');
$id				= Utils::get_input('id','both');
$code			= Utils::get_input('code','both');
$items			= Utils::get_input('items','both');

$continents_manager = new ContinentsManager($bdd);
$places_manager = new PlacesManager($bdd);

// convert world map click
//$valid_codes = array("cl" => 22, "bo" => 21, "pe" => 20, "us" => 31, "nz" => 30, "au" => 11);
//if (array_key_exists($code, $valid_codes)) $id = $valid_codes[$code];

// Breadcrumbs
$bc = new Breadcrumb($bdd, $items, $id, $action);
$smarty->assign("breadcrumbs", $bc->getCrumbs());
/*
switch($action) {

	default:
		if (!empty($items) && !empty($id))	{
			//echo "items = ".$items; 
			
			$pageTitle = $translate->__('list_of_'.$items);
			$className  = ucfirst($items)."Manager";
			$funcName = "get".ucfirst($items);
			
			$smarty->assign("titre", $translate->__($pageTitle));
			$manager = new $className($bdd);
			$smarty->assign($items, $manager->$funcName($id));
			$smarty->assign("content", $items."/homepage.tpl.html");
			}
		else {
			$smarty->assign("titre", "Dashboard");
			$smarty->assign("content", "misc/dashboard.tpl.html");
		}
		$smarty->display("main.tpl.html");
}
*/
$smarty->assign("titre", $translate->__('list_of_continents'));
$smarty->assign("continents", $continents_manager->getContinents(true));
$smarty->assign("allPlaces", $places_manager->getPlaces());

$smarty->assign("content", "misc/homepage.tpl.html");
$smarty->display("main.tpl.html");
?>