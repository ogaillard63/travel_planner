<?php
require_once( "inc/prepend.php" );

// Breadcrumbs
//$bc = new Breadcrumb($bdd, "places", $country_id, $action);
//$smarty->assign("breadcrumbs", $bc->getCrumbs());

		$smarty->assign("titre", "test");
		$smarty->assign("content", "test/upload.tpl.html");
		$smarty->display("main.tpl.html");
		
require_once( "inc/append.php" );
?>