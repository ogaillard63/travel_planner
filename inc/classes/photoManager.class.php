<?php
/**
* @project		Travel Planner
* @author		Olivier Gaillard
* @version		1.0 du 04/01/2015
* @desc			Gestion des photos
*/

class PhotoManager {
	protected $bdd;

	public function __construct(PDO $bdd) {
		$this->bdd = $bdd;
	}

	/**
	* Retourne l'objet photo correspondant à l'Id
	* @param $id
	*/
	public function getPhoto($id) {
	if ($id) {
		$q = $this->bdd->prepare("SELECT * FROM photos WHERE id = :id");
		$q->bindValue(':id', $id, PDO::PARAM_INT);
		$q->execute();
		return new Photo($q->fetch(PDO::FETCH_ASSOC));
		}
	}

	/**
	* Retourne la liste des photos
	*/
	public function getPhotos() {
		$photos = array();
		$q = $this->bdd->prepare('SELECT * FROM photos ORDER BY id');
		$q->execute();
		while ($data = $q->fetch(PDO::FETCH_ASSOC)) {
			$photos[] = new Photo($data);
		}
		return $photos;
	}
	
	/*public function getPhotos($isEagerFetch = true) {
		$photos = array();
		$q = $this->bdd->prepare('SELECT * FROM photos ORDER BY #objet2#_id');
		$q->execute();
		while ($data = $q->fetch(PDO::FETCH_ASSOC)) {
			$photo = new Photo($data);
			if ($isEagerFetch) {
				$#objet2#_manager = new #Objet2#Manager($this->bdd);
				$photo->set#Objet2#($#objet2#_manager->get#Objet2#($photo->get#Objet2#Id()));
				}
			$photos[] = $photo;
		}
		return $photos;
	}*/
	
	
	/**
	 * Retourne une liste des photos formatée pour peupler un menu déroulant
	 */
	/* public function getPhotosForSelect() {
		$photos = array();
		$q = $this->bdd->prepare('SELECT id, name FROM photos ORDER BY id');
		$q->execute();
		while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
			$photos[$row["id"]] =  $row["name"];
		}
		return $photos;
	} */
	
	
	/**
	* Efface l'objet photo de la bdd
	* @param Photo $photo
	*/
	public function deletePhoto(Photo $photo) {
		$q = $this->bdd->prepare("DELETE FROM photos WHERE id = :id");
		$q->bindValue(':id', $photo->getId(), PDO::PARAM_INT);
		return $q->execute();
	}

	/**
	* Enregistre l'objet photo en bdd
	* @param Photo $photo
	*/
	public function savePhoto(Photo $photo) {
		if ($photo->getId() == -1) {
			$q = $this->bdd->prepare('INSERT INTO photos SET place_id = :place_id, path = :path, caption = :caption');
		} else {
			$q = $this->bdd->prepare('UPDATE photos SET place_id = :place_id, path = :path, caption = :caption WHERE id = :id');
			$q->bindValue(':id', $photo->getId(), PDO::PARAM_INT);
		}
		$q->bindValue(':place_id', $photo->getPlaceId(), PDO::PARAM_INT);
		$q->bindValue(':path', $photo->getPath(), PDO::PARAM_STR);
		$q->bindValue(':caption', $photo->getCaption(), PDO::PARAM_STR);	
		$q->execute();
		if ($photo->getId() == -1) $photo->setId($this->bdd->lastInsertId());
	}
}
?>
