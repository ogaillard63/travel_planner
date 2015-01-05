<?php
/**
 * @project		WebApp Base
 *
 * @author		Olivier Gaillard <ogaillard63@gmail.com>
 * @version		1.0 du 13/03/2014
 * @desc	   	Gestion de l'identification des users
 */

define('TAG', "IDENTIFICATION");
require_once( "inc/prepend.php" );

// Récupération des variables
$action			= Utils::get_input('action','both');


if ($action == "logout" || $action == "timeout") {
	$user->logout();
}
else {
	$smarty->assign("warning", $user->login()); // Authentification
	$smarty->assign("titre", "Identification");
	$smarty->display("login.tpl.html");
}

require_once("inc/append.php");
?>