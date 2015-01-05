<?php
/**
 * @project		Travel Planner
 *
 * @author		Olivier Gaillard <olivier.gaillard@centrefrance.com>
 * @version		1.0 du 18/11/2014
 * @desc	   	Gestion des users
 */

define('TAG', "UTILISATEURS");
require_once( "inc/prepend.php" );
$user->isLoggedIn(); // Espace privé
$user->isAllowed(SUPER_ADMIN);


// Récupération des variables
$action 			= Utils::get_input('action','both');
$id	 				= Utils::get_input('id','both');
$nom 				= Utils::get_input('nom','post');
$prenom 			= Utils::get_input('prenom','post');
$identifiant 		= Utils::get_input('identifiant','post');
$email 				= Utils::get_input('email','post');
$profil_id			= Utils::get_input('profil_id','post');
$mdp1 				= Utils::get_input('mdp1','post');
$mdp2 				= Utils::get_input('mdp2','post');
$expiration			= Utils::date2sql(Utils::get_input('expiration','post'));
$users_manager 		= new UserManager($bdd);

// Breadcrumbs
$bc = new Breadcrumb($bdd, "users");
$smarty->assign("breadcrumbs", $bc->getCrumbs());

switch($action) {
	
	case "add" :
		$smarty->assign("titre", "Ajouter un user");
		$dans_un_an = date("Y-m-d", time()+365*24*60*60); // dans 1 an
		$smarty->assign("user", new User(array("id" => -1, "expiration" => $dans_un_an)));
		$smarty->assign("profils", $users_manager->getProfilsList());
		$smarty->assign("form",	array("action" => "save", "bouton" => "Enregistrer"));
		$smarty->assign("content", "users/edit.tpl.html");
		$smarty->display("main.tpl.html");
		break;
	
	case "edit" :
		$smarty->assign("titre", "Modification d'un profil user");
		$smarty->assign("form",	array("action" => "save", "bouton" => "Enregistrer les modifications"));
		$smarty->assign("user", $users_manager->getUser($id));
		$smarty->assign("profils", $users_manager->getProfilsList());
		$smarty->assign("content","users/edit.tpl.html");
		$smarty->display("main.tpl.html");
		break;

	case "save" :
		if ((strlen($mdp1)) && ($mdp1 == $mdp2)) {
			$data = array("id" => $id, "nom" => $nom, "prenom" => $prenom, "identifiant" => $identifiant, 
			"email" => $email, "mdp" =>  MD5($mdp1), "profil_id" => $profil_id, "expiration" => $expiration);
			$users_manager->saveUser(new User($data));
			$log->alert("Le profil user a été enregistré avec succès.");
		}
		else {
			$log->alert("Vérifier la saisie du mot de passe !", "danger");
		}
		Utils::redirection("users.php");
		break;

	case "delete" :
		$user = $users_manager->getUser($id);
		if ($users_manager->deleteUser($user)) {
			$log->info(TAG, "Le profil user de " . $user->getPrenom()." ". $user->getNom()." a été effacé.");
			$log->alert("Le profil user de " . $user->getPrenom()." ". $user->getNom()." a été effacé.");
		}
		Utils::redirection("users.php");
		break;

	default:
		$smarty->assign("titre", "Liste des users"); 
		$smarty->assign("users", $users_manager->getUsers());
		$smarty->assign("content", "users/list.tpl.html");
		$smarty->display("main.tpl.html");
}
require_once( "inc/append.php" );
?>