<?php
/**
* @project		
* @author		Olivier Gaillard
* @version		1.0 du 29/11/2014
* @desc			Gestion des activities
*/

class ActivitiesManager {
	protected $bdd;

	public function __construct(PDO $bdd) {
		$this->bdd = $bdd;
	}

	/**
	* Retourne l'objet activity correspondant à l'Id
	* @param $id
	*/
	public function getActivity($id) {
	if ($id) {
		$q = $this->bdd->prepare("SELECT * FROM activities WHERE id = :id");
		$q->bindValue(':id', $id, PDO::PARAM_INT);
		$q->execute();
		return new Activity($q->fetch(PDO::FETCH_ASSOC));
		}
	}

	/**
	* Retourne la liste des activities
	*/
	/*public function getActivities() {
		$activities = array();
		$q = $this->bdd->prepare('SELECT * FROM activities ORDER BY id');
		$q->execute();
		while ($data = $q->fetch(PDO::FETCH_ASSOC)) {
			$activities[] = new Activity($data);
		}
		return $activities;
	}*/
	
	public function getActivities($isEagerFetch = true) {
		$activities = array();
		$q = $this->bdd->prepare('SELECT * FROM activities ORDER BY type_id');
		$q->execute();
		while ($data = $q->fetch(PDO::FETCH_ASSOC)) {
			$activity = new Activity($data);
			if ($isEagerFetch) {
				// Type
				$type_manager = new TypeManager($this->bdd);
				$activity->setType($type_manager->getType($activity->getTypeId()));
				// Place
				$place_manager = new PlaceManager($this->bdd);
				$activity->setPlace($place_manager->getPlace($activity->getPlaceId()));
				}
			$activities[] = $activity;
		}
		return $activities;
	}
	
	
	/**
	 * Retourne une liste des activities formatée pour peupler un menu déroulant
	 */
	/* public function getActivitiesForSelect() {
		$activities = array();
		$q = $this->bdd->prepare('SELECT id, name FROM activities ORDER BY id');
		$q->execute();
		while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
			$activities[$row["id"]] =  $row["name"];
		}
		return $activities;
	} */
	
	
	/**
	* Efface l'objet activity de la bdd
	* @param Activity $activity
	*/
	public function deleteActivity(Activity $activity) {
		$q = $this->bdd->prepare("DELETE FROM activities WHERE id = :id");
		$q->bindValue(':id', $activity->getId(), PDO::PARAM_INT);
		return $q->execute();
	}

	/**
	* Enregistre l'objet activity en bdd
	* @param Activity $activity
	*/
	public function saveActivity(Activity $activity) {
		if ($activity->getId() == -1) {
			$q = $this->bdd->prepare('INSERT INTO activities SET place_id = :place_id, type_id = :type_id, name = :name, description = :description');
		} else {
			$q = $this->bdd->prepare('UPDATE activities SET place_id = :place_id, type_id = :type_id, name = :name, description = :description WHERE id = :id');
			$q->bindValue(':id', $activity->getId(), PDO::PARAM_INT);
		}
		$q->bindValue(':place_id', $activity->getPlaceId(), PDO::PARAM_INT);
		$q->bindValue(':type_id', $activity->getTypeId(), PDO::PARAM_INT);
		$q->bindValue(':name', $activity->getName(), PDO::PARAM_STR);
		$q->bindValue(':description', $activity->getDescription(), PDO::PARAM_STR);	
		$q->execute();
		if ($activity->getId() == -1) $activity->setId($this->bdd->lastInsertId());
	}
}
?>