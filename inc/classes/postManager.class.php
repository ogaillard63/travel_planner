<?php
/**
* @project		Travel Planner
* @author		Olivier Gaillard
* @version		1.0 du 04/01/2015
* @desc			Gestion des posts
*/

class PostManager {
	protected $bdd;

	public function __construct(PDO $bdd) {
		$this->bdd = $bdd;
	}

	/**
	* Retourne l'objet post correspondant à l'Id
	* @param $id
	*/
	public function getPost($id) {
	if ($id) {
		$q = $this->bdd->prepare("SELECT * FROM posts WHERE id = :id");
		$q->bindValue(':id', $id, PDO::PARAM_INT);
		$q->execute();
		return new Post($q->fetch(PDO::FETCH_ASSOC));
		}
	}

	/**
	* Retourne la liste des posts
	*/
	public function getPosts() {
		$posts = array();
		$q = $this->bdd->prepare('SELECT * FROM posts ORDER BY id');
		$q->execute();
		while ($data = $q->fetch(PDO::FETCH_ASSOC)) {
			$posts[] = new Post($data);
		}
		return $posts;
	}
	
	/*public function getPosts($isEagerFetch = true) {
		$posts = array();
		$q = $this->bdd->prepare('SELECT * FROM posts ORDER BY #objet2#_id');
		$q->execute();
		while ($data = $q->fetch(PDO::FETCH_ASSOC)) {
			$post = new Post($data);
			if ($isEagerFetch) {
				$#objet2#_manager = new #Objet2#Manager($this->bdd);
				$post->set#Objet2#($#objet2#_manager->get#Objet2#($post->get#Objet2#Id()));
				}
			$posts[] = $post;
		}
		return $posts;
	}*/
	
	
	/**
	 * Retourne une liste des posts formatée pour peupler un menu déroulant
	 */
	/* public function getPostsForSelect() {
		$posts = array();
		$q = $this->bdd->prepare('SELECT id, name FROM posts ORDER BY id');
		$q->execute();
		while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
			$posts[$row["id"]] =  $row["name"];
		}
		return $posts;
	} */
	
	
	/**
	* Efface l'objet post de la bdd
	* @param Post $post
	*/
	public function deletePost(Post $post) {
		$q = $this->bdd->prepare("DELETE FROM posts WHERE id = :id");
		$q->bindValue(':id', $post->getId(), PDO::PARAM_INT);
		return $q->execute();
	}

	/**
	* Enregistre l'objet post en bdd
	* @param Post $post
	*/
	public function savePost(Post $post) {
		if ($post->getId() == -1) {
			$q = $this->bdd->prepare('INSERT INTO posts SET place_id = :place_id, title = :title, text = :text');
		} else {
			$q = $this->bdd->prepare('UPDATE posts SET place_id = :place_id, title = :title, text = :text WHERE id = :id');
			$q->bindValue(':id', $post->getId(), PDO::PARAM_INT);
		}
		$q->bindValue(':place_id', $post->getPlaceId(), PDO::PARAM_INT);
		$q->bindValue(':title', $post->getTitle(), PDO::PARAM_STR);
		$q->bindValue(':text', $post->getText(), PDO::PARAM_STR);	
		$q->execute();
		if ($post->getId() == -1) $post->setId($this->bdd->lastInsertId());
	}
}
?>
