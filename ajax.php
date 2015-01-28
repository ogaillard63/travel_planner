<?php
/**
 * @project		Travel Planner
 * @author		Olivier Gaillard
 * @version		1.0 du 05/01/2015
 * @desc	   	Gestion des traitements en Ajax
 */
require_once("inc/prepend.php");

$action			= Utils::get_input('action','both');
$id			    = Utils::get_input('id','both');

switch($action) {

    case "crop_photo_place" :
        $crop = new CropPhoto($_POST['photo_src'], $_POST['photo_data'], $_FILES['photo_file']);
        $response = array(
            'state'  => 200,
            'message' => $crop -> getMsg(),
            'result' => $crop -> getResult()
        );

        echo json_encode($response);
        break;

    case "sort_places" :
        $places_manager = new PlacesManager($bdd);
        $places_manager->updatePositions($_POST['position']);
        echo "ok";
        break;

    case "sort_activities" :
        $activities_manager = new ActivitiesManager($bdd);
        $activities_manager->updatePositions($_POST['position']);
        echo "ok";
        break;

    case "get_activities" :
        $html = "";
        $activities_manager = new ActivitiesManager($bdd);
        $activities = $activities_manager->getActivities($id, true);

        foreach ($activities as $activity) {
            $html .= "<input type='checkbox' name= 'activities[]' value='" .$activity->id. "'><span>" .$activity->type->name . " | " . $activity->name . " (" . $activity->duration . " J)</span><br/>\n";
        }
        echo $html;
        break;

    default:
}
?>