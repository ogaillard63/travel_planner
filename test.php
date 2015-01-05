<?php
require_once( "inc/prepend.php" );

// Breadcrumbs
//$bc = new Breadcrumb($bdd, "places", $country_id, $action);
//$smarty->assign("breadcrumbs", $bc->getCrumbs());

		$smarty->assign("titre", "test");
		$smarty->assign("content", "test/map.tpl.html");
		$smarty->display("main_map.tpl.html");
		
require_once( "inc/append.php" );
?>