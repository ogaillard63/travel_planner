<?php
/**
* @project		Travel Planner
* @author		Olivier Gaillard
* @version		1.0 du 28/01/2015
* @desc			Gestion des stages
*/

class StageManager {
	protected $bdd;

	public function __construct(PDO $bdd) {
		$this->bdd = $bdd;
	}

	/**
	* Retourne l'objet stage correspondant à l'Id
	* @param $id
	*/
	public function getStage($id) {
	if ($id) {
		$q = $this->bdd->prepare("SELECT * FROM stages WHERE id = :id");
		$q->bindValue(':id', $id, PDO::PARAM_INT);
		$q->execute();
		return new Stage($q->fetch(PDO::FETCH_ASSOC));
		}
	}

	/**
	* Retourne la liste des stages
	*/
	/*public function getStages() {
		$stages = array();
		$q = $this->bdd->prepare('SELECT * FROM stages ORDER BY id');
		$q->execute();
		while ($data = $q->fetch(PDO::FETCH_ASSOC)) {
			$stages[] = new Stage($data);
		}
		return $stages;
	}*/
	
	 public function getStages($isEagerFetch = false) {
		$stages = array();
		$q = $this->bdd->prepare('SELECT * FROM stages ORDER BY position');
		$q->execute();
		while ($data = $q->fetch(PDO::FETCH_ASSOC)) {
			$stage = new Stage($data);
			if ($isEagerFetch) {
				// place
				$places_manager = new PlacesManager($this->bdd);
				$stage->setPlace($places_manager->getPlace($stage->getPlaceId()));

				// activities
				$activities = array();
				//var_dump($stage);
				if (strlen($stage->getActivitiesIds()) > 0) {
					//echo $stage->getActivitiesIds();
					$activities_manager = new ActivitiesManager($this->bdd);
					foreach ($activities_manager->getActivities($stage->getPlaceId()) as $activity) {
						if (strrpos(strval($stage->getActivitiesIds()), strval($activity->getId())) !== false) {
							$activities[] = $activity;
						}
					}
				}
				$stage->setActivities($activities);
			}
			$stages[] = $stage;
		}
		return $stages;
	}

	/**
	* Retourne la liste des stages par page
	*/
	/* public function getStagesByPage($_id, $page_num, $lpp, $isEagerFetch = true) {
		$stages = array();
		$start = ($page_num-1)*$lpp;
		if ($_id > 0) {
			$q = $this->bdd->prepare('SELECT * FROM stages WHERE _id = :_id ORDER BY id DESC LIMIT :start, :lpp');
			$q->bindValue(':_id', $_id, PDO::PARAM_INT);
			}
		else {
			$q = $this->bdd->prepare('SELECT * FROM stages ORDER BY id DESC LIMIT :start, :lpp');
		}
		$q->bindValue(':start', $start, PDO::PARAM_INT);
		$q->bindValue(':lpp', $lpp, PDO::PARAM_INT);
		$q->execute();
		while ($data = $q->fetch(PDO::FETCH_ASSOC)) {
			$stage = new Stage($data);
			if ($isEagerFetch) {
				$place_manager = new PlaceManager($this->bdd);
				$stage->setPlace($place_manager->getPlace($stage->getPlaceId()));
				}
			$stages[] = $stage;
		}
		return $stages;
	} */
	
	/**
	 * Retourne une liste des stages formatée pour peupler un menu déroulant
	 */
	 public function getStagesForSelect() {
		$stages = array();
		$q = $this->bdd->prepare('SELECT id, name FROM stages ORDER BY id');
		$q->execute();
		while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
			$stages[$row["id"]] =  $row["name"];
		}
		return $stages;
	}
	
	/**
	 * Retourne le nombre max de places
	 */
	public function getMaxStages($_id) {
		if ($_id > 0) {
			$q = $this->bdd->prepare('SELECT count(1) FROM stages WHERE _id = :_id');
			$q->bindValue(':_id', $_id, PDO::PARAM_INT);
			}
		else {
			$q = $this->bdd->prepare('SELECT count(1) FROM stages');
		}
		$q->execute();
		return intval($q->fetch(PDO::FETCH_COLUMN));
	}
	
	/**
	* Efface l'objet stage de la bdd
	* @param Stage $stage
	*/
	public function deleteStage(Stage $stage) {
		$q = $this->bdd->prepare("DELETE FROM stages WHERE id = :id");
		$q->bindValue(':id', $stage->getId(), PDO::PARAM_INT);
		return $q->execute();
	}

	/**
	* Enregistre l'objet stage en bdd
	* @param Stage $stage
	*/
	public function saveStage(Stage $stage) {
		if ($stage->getId() == -1) {
			$q = $this->bdd->prepare('INSERT INTO stages SET place_id = :place_id, activities_ids = :activities_ids, arrival_date = :arrival_date, position = :position, duration = :duration, description = :description');
			$q->bindValue(':position', $this->getMaxPosition()+1, PDO::PARAM_STR);
		} else {
			$q = $this->bdd->prepare('UPDATE stages SET place_id = :place_id, activities_ids = :activities_ids, arrival_date = :arrival_date, duration = :duration, description = :description WHERE id = :id');
			$q->bindValue(':id', $stage->getId(), PDO::PARAM_INT);
		}
		$q->bindValue(':place_id', $stage->getPlaceId(), PDO::PARAM_STR);
		$q->bindValue(':activities_ids', $stage->getActivitiesIds(), PDO::PARAM_STR);
		$q->bindValue(':arrival_date', $stage->getArrivalDate(), PDO::PARAM_STR);
		$q->bindValue(':duration', $stage->getDuration(), PDO::PARAM_STR);
		$q->bindValue(':description', $stage->getDescription(), PDO::PARAM_STR);	
		$q->execute();
		if ($stage->getId() == -1) $stage->setId($this->bdd->lastInsertId());
	}


	/**
	 * Met à jour l'ordre de classement
	 * @param $ids
	 */
	public function updatePositions($ids) {
		$position = 1;
		foreach ($ids as $id) {
			$q = $this->bdd->prepare("UPDATE stages SET position = :position WHERE id = :id");
			$q->bindValue(':position', $position++, PDO::PARAM_INT);
			$q->bindValue(':id', $id, PDO::PARAM_INT);
			$q->execute();
		}
	}

	/**
	 * Retourne la plus grande valeur de position
	 * @param $country_id
	 */
	public function getMaxPosition() {
		$q = $this->bdd->prepare("SELECT MAX(position) FROM stages");
		$q->execute();
		return intval($q->fetch(PDO::FETCH_COLUMN));
	}
}
?>
