<?php
/**
 * @project		Travel Planner
 * @author		Olivier Gaillard
 * @version		1.0 du 05/01/2015
 * @desc	   	Gestion des traitements en Ajax
 */
require_once("inc/prepend.php");

$action			= Utils::get_input('action','both');

switch($action) {
    case "sort_places" :
        $places_manager = new PlacesManager($bdd);
        $places_manager->updatePositions($_POST['position']);
        echo "ok";
        break;
	default:
}
?>