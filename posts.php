<?php
/**
* @project		Travel Planner
* @author		Olivier Gaillard
* @version		1.0 du 04/01/2015
* @desc			Controleur des objets : posts
*/

require_once( "inc/prepend.php" );
//$user->isLoggedIn(); // Espace privé

// Récupération des variables
$action			= Utils::get_input('action','both');
$id				= Utils::get_input('id','both');
$place_id		= Utils::get_input('place_id','post');
$title			= Utils::get_input('title','post');
$text			= Utils::get_input('text','post');

$posts_manager = new PostManager($bdd);
// Breadcrumbs
$bc = new Breadcrumb($bdd, "articles");
$smarty->assign("breadcrumbs", $bc->getCrumbs());

switch($action) {
	
	case "add" :
		$smarty->assign("post", new Post(array("id" => -1)));
		$smarty->assign("content", "posts/edit.tpl.html");
		$smarty->display("main.tpl.html");
		break;
	
	case "edit" :
		$smarty->assign("post", $posts_manager->getPost($id));
		$smarty->assign("content","posts/edit.tpl.html");
		$smarty->display("main.tpl.html");
		break;

	case "save" :
		$data = array("id" => $id, "place_id" => $place_id, "title" => $title, "text" => $text);
		$posts_manager->savePost(new Post($data));
		$log->notification($translate->__('the_post_has_been_saved'));
		Utils::redirection("posts.php");
		break;

	case "delete" :
		$post = $posts_manager->getPost($id);
		if ($posts_manager->deletePost($post)) {
			$log->notification($translate->__('the_post_has_been_deleted'));
		}
		Utils::redirection("posts.php");
		break;

	default:
		$smarty->assign("titre", $translate->__('list_of_posts'));
		$smarty->assign("posts", $posts_manager->getPosts());
		$smarty->assign("content", "posts/list.tpl.html");
		$smarty->display("main.tpl.html");
}
require_once( "inc/append.php" );
?>