<?php
error_reporting(0);
ini_set('error_reporting', 0);
//ini_set('display_errors', 1);

require_once( "inc/prepend.php" );
$content_start =
"<?xml version=\"1.0\" encoding=\"utf-8\"?>\n"
. "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\"\n"
. "    \"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">\n"
. "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n"
. "<head>"
. "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n"
. "<link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\" />\n"
. "<title>Test Book</title>\n"
. "</head>\n"
. "<body>\n";

$bookEnd = "</body>\n</html>\n";

// setting timezone for time functions used for logging to work properly
date_default_timezone_set('Europe/Berlin');

$fileDir = './PHPePub';

include_once("inc/epub/EPub.php");
$book = new EPub(); // Default is EPub::BOOK_VERSION_EPUB2

// Title and Identifier are mandatory!
$book->setTitle("Trip 333");
$book->setIdentifier("http://www.trip333.com/", EPub::IDENTIFIER_URI); // Could also be the ISBN number, prefered for published books, or a UUID.
$book->setLanguage("fr"); // Not needed, but included for the example, Language is mandatory, but EPub defaults to "en". Use RFC3066 Language codes, such as "en", "da", "fr" etc.
$book->setDescription("Itinéraire de voyage");
$book->setAuthor("Travel Planner", "Travel Planner");
$book->setPublisher("Travel Planner Publications", "http://www.trip333.com/"); // I hope this is a non existant address :)

$book->setSubject("Trip 333");
$book->setSubject("Itinéraire de voyage");

$cssData = file_get_contents('inc/epub/css/styles.css', true);;
$book->addCSSFile("styles.css", "css1", $cssData);

$book->setCoverImage("Cover.jpg", file_get_contents("inc/epub/demo/cover-image.jpg"), "image/jpeg");
$cover = $content_start . "<h1>Trip 333</h1>\n<h2>Itinéraire de voyage</h2>\n" . $bookEnd;
$book->addChapter("Notices", "Cover.html", $cover);
$book->buildTOC(NULL, "toc", "Table of Contents", TRUE, TRUE);
//    function buildTOC($cssFileName = NULL, $tocCSSClass = "toc", $title = "Table of Contents", $addReferences = TRUE, $addToIndex = FALSE, $tocFileName = "TOC.xhtml") {

$stages_manager 	= new StagesManager($bdd);
$stages  = $stages_manager->getStages(true);
$chapters = array();
$index = 0;
$sub_index = 0;
$last_country = "";
$country_name = "";
$search  = array("<div>", "</div>");
$replace = array("<p>", "</p>");

foreach ($stages as $stage) {
    $activities = array();
    $index ++;
    $sub_index = 0;
    $html = "";
    $country_name = $stage->getPlace()->getCountry()->getName();

    if ($last_country <>  $country_name) {

        // COUNTRY

        $book->backLevel();
        $html = $content_start . "<h1 class='country'>" .  $country_name . "</h1>\n";
        //$html .= "<h2>".$place_name."</h2>\n";
        $html .= str_replace($search, $replace, $stage->getPlace()->getCountry()->getDescription())."\n";
        $html .= $bookEnd;
        $book->addChapter($country_name, "Chapter".$index.".html", $html);
        //$book->backLevel();

        $last_country = $country_name;
    }

    // PLACE
    $book->subLevel();
    $html = $content_start . "<h1>" .  $country_name . "</h1>\n";
    $html .= "<h2 class='place'>".$stage->getPlace()->getName()."</h2>\n";



    $html .= str_replace($search, $replace, $stage->getPlace()->getDescription())."\n";

    //$html .= "<h2>".$stage->getDescription()."</h2>\n";


    $activities = $stage->getActivities();
    if (sizeof($activities) > 0) {
        foreach ($activities as $activity) {

            $sub_index ++;
            //var_dump($activity);
            $activity_name = $activity->getName();
            $html .= "<h3 class='activity'>".$activity_name."</h3>\n";
            $html .= str_replace($search, $replace, $activity->getDescription())."\n";
        }
        $html .= $bookEnd;
        $book->addChapter($stage->getPlace()->getName(), "Chapter".$index."-".$sub_index.".html", $html);

    }

    $book->backLevel();




}

$book->rootLevel();


//$book->addChapter("Log", "Log.html", $content_start . $log->getLog() . "\n</pre>" . $bookEnd);
/*
if ($book->isLogging) { // Only used in case we need to debug EPub.php.
    $epuplog = $book->getLog();
    $book->addChapter("ePubLog", "ePubLog.html", $content_start . $epuplog . "\n</pre>" . $bookEnd);
}
*/
$book->finalize(); // Finalize the book, and build the archive.

//$book->saveBook('Trip333', '.');

// Send the book to the client. ".epub" will be appended if missing.
$zipData = $book->sendBook("Trip333");
?>