<?php
/**
 * @project		WebApp Base
 *
 * @author		Olivier Gaillard <ogaillard63@gmail.com>
 * @version		1.0 du 13/03/2014
 * @desc	   	Initialisation des ressources
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);

setlocale(LC_ALL, 'fr_FR');
date_default_timezone_set('Europe/Paris');

define('PATH_APP', 			realpath(dirname (__FILE__).'/..'));
define('PATH_INC', 			PATH_APP.'/inc');
define('PATH_LANG',			PATH_APP.'/lang');
define('PATH_PROPERTIES', 	PATH_INC.'/properties');
define('PATH_CLASSES', 		PATH_INC.'/classes');
define('PATH_SMARTY', 		PATH_INC.'/smarty');

// autoloader de classes
function autoloader($class) {
	if (substr($class, 0, 7) == 'Smarty_') {
		require_once PATH_SMARTY.'/sysplugins/'.strtolower($class).'.php';
	}
	else {
		if ($class == 'Smarty') require_once PATH_SMARTY.'/'.$class.'.class.php';
		else require_once PATH_CLASSES.'/'.lcfirst($class).'.class.php';
	}
}
spl_autoload_register('autoloader');

// Paramètres de l'application
$properties_filepath = PATH_PROPERTIES.'/properties.ini';
$prop = parse_ini_file($properties_filepath);

// Paramètres des chemins d'accès
define('PATH_TPL', 				PATH_APP.'/tpl/'.$prop["theme"]); // Dossier des templates de vues
define('PATH_TPL_RELATIVE',		'tpl/'.$prop["theme"]); 

// Session
$session = Session::getInstance();

// Initialisation du gestionnaire de templates
$smarty = new Smarty;
$smarty->setTemplateDir(PATH_TPL.'/');
$smarty->config_dir  =  'lang';
$smarty->compile_dir  = 'tpl_cache';
$smarty->compile_check = true;
$smarty->force_compile = true;
$smarty->assign('tpl', PATH_TPL_RELATIVE);

// Connexion à la base de données
$bdd = new PDO("mysql:host=".$prop['db_hostname'].";port=".$prop['db_port'].";dbname=".$prop['db_name'].";charset=utf8", $prop['db_username'], $prop['db_password']);
$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Log
$log = new Logger($bdd, $smarty, $session);

// Authentification
$user = new UserAuth($bdd);

// Profils
define('SUPER_ADMIN', 		300);
define('ADMIN', 			200);
define('USER', 				100);

// Gestion des traductions
$_SESSION['filePathLang'] = PATH_LANG.'/'."fr.txt";
if(isset($_GET['cnt'])) {
	if (in_array($_GET['cnt'], array('fr','en')))
	$_SESSION['filePathLang'] = PATH_LANG.'/'.$_GET['cnt'].".txt";
	}
$translate = new Translator($_SESSION['filePathLang']); 

// Gestion des notifications
if ($alert = $session->getValue("alert")) {
	$smarty->assign("alert", array("msg" => $alert["msg"], "type" => "alert-".$alert["type"]));
	$session->unsetKey("alert");

}

/*
foreach (array("DEBUG", "INFO", "ERROR") as $type) {
	if ($msg = $session->getValue($type)) {
		$smarty->assign("alert", array("msg" => $msg, "type" => "alert-success"));
		$session->unsetKey($type);
	}
}
*/
?>