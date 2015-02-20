<?php
require_once( "inc/prepend.php" );

ini_set('display_errors', 1);
error_reporting(E_ALL);

define('EPUB_OUT_PATH', PATH_RES.'/ebook');
$fileName = "trip333.zip";
$parts = array(); // table of content
$zip = new Zip();
$zip->setExtraField(FALSE);
$zip->addFile("application/epub+zip", "mimetype");
$zip->setExtraField(TRUE);
// META-INF
$zip->addDirectory("META-INF");
$content = file_get_contents(EPUB_OUT_PATH."/META-INF/container.xml");
$zip->addFile($content, "META-INF/container.xml", 0, NULL, FALSE);

// OEBPS
$zip->addDirectory("OEBPS");

// cover.html
//$content = file_get_contents(EPUB_OUT_PATH."/OEBPS/cover.html");
//$zip->addFile($content, "OEBPS/cover.html", 0, NULL, FALSE);
//$parts[] = array("title" => "Couverture", "link" => "cover.html");

// cover.jpg
$content = file_get_contents(EPUB_OUT_PATH."/OEBPS/cover.jpg");
$zip->addFile($content, "OEBPS/cover.jpg", 0, NULL, FALSE);

// Intro.html
$content = file_get_contents(EPUB_OUT_PATH."/OEBPS/intro.html");
$zip->addFile($content, "OEBPS/intro.html", 0, NULL, FALSE);
$parts[] = array("title" => "Introduction", "link" => "intro.html");

// styles.css
$content = file_get_contents(EPUB_OUT_PATH."/OEBPS/styles.css");
$zip->addFile($content, "OEBPS/styles.css", 0, NULL, FALSE);

// chapters

$last_country = "";
$index_country = 0;
$index_stage = 0;
$stages_manager 	= new StagesManager($bdd);
$stages  = $stages_manager->getStages(true);

$parts[] = array("title" => "Sommaire", "link" => "toc.html");

foreach ($stages as $stage) {
	$country_name = $stage->getPlace()->getCountry()->getName();
	if ($last_country <> $country_name) {
		// chapter of country
		$index_country++;
		$smarty->assign("title", $country_name);
		$smarty->assign("subtitle", "");
		$smarty->assign("content", $stage->getPlace()->getCountry()->getDescription());
		$content = $smarty->fetch("epub/chapter.tpl.html");
		$zip->addFile($content, "OEBPS/chapter".$index_country.".html", 0, NULL, FALSE);
		$parts[] = array("title" => $country_name, "link" => "chapter".$index_country.".html");

		$index_stage = 0;
		$last_country = $country_name;
	}
	// chapter of stages
	$index_stage++;
	$place_name = $stage->getPlace()->getName();
	$smarty->assign("title", $country_name);
	$smarty->assign("subtitle", $place_name);
	$smarty->assign("content", $stage->getPlace()->getDescription());
	// activities


	$smarty->assign("activities", $stage->getActivities());
	$content = $smarty->fetch("epub/chapter.tpl.html");
	$zip->addFile($content, "OEBPS/chapter".$index_country."-".$index_stage.".html", 0, NULL, FALSE);
	$parts[] = array("title" => $place_name, "link" => "chapter".$index_country."-".$index_stage.".html", "level" => 1);

}
// toc.tpl.html
$smarty->assign("parts", $parts);
$content = $smarty->fetch("epub/toc.tpl.html");
$zip->addFile($content, "OEBPS/toc.html", 0, NULL, FALSE);

// book.opf
$content = $smarty->fetch("epub/opf.tpl.html");
$zip->addFile($content, "OEBPS/book.opf", 0, NULL, FALSE);

// book.ncx
$content = $smarty->fetch("epub/ncx.tpl.html");
$zip->addFile($content, "OEBPS/book.ncx", 0, NULL, FALSE);

$zip->finalize();
if (is_file($fileName)) unlink($fileName);

$zipData = $zip->sendZip($fileName, "application/epub+zip");


?>