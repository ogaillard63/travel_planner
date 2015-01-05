<?php
/**
* @project		
* @author		Olivier Gaillard
* @version		1.0 du 29/11/2014
* @desc			Gestion des places
*/

class PlaceManager {
	protected $bdd;

	public function __construct(PDO $bdd) {
		$this->bdd = $bdd;
	}

	/**
	* Retourne l'objet place correspondant à l'Id
	* @param $id
	*/
	public function getPlace($id,$isEagerFetch = true) {
	if ($id) {
		$q = $this->bdd->prepare("SELECT * FROM places WHERE id = :id");
		$q->bindValue(':id', $id, PDO::PARAM_INT);
		$q->execute();
		$place = new Place($q->fetch(PDO::FETCH_ASSOC));
		if ($isEagerFetch) {
			$country_manager = new CountryManager($this->bdd);
			$place->setCountry($country_manager->getCountry($place->getCountryId()));
			}
		return $place;
		}
	}

	/**
	* Retourne la liste des places
	*/
	/*public function getPlaces() {
		$places = array();
		$q = $this->bdd->prepare('SELECT * FROM places ORDER BY id');
		$q->execute();
		while ($data = $q->fetch(PDO::FETCH_ASSOC)) {
			$places[] = new Place($data);
		}
		return $places;
	}*/
	
	public function getPlaces($country_id, $isEagerFetch = true) {
		$places = array();
		if ($country_id > 0) {
			$q = $this->bdd->prepare('SELECT * FROM places WHERE country_id = :country_id');
			$q->bindValue(':country_id', $country_id, PDO::PARAM_INT);
			}
		else {
			$q = $this->bdd->prepare('SELECT * FROM places ORDER BY country_id');
		}
		$q->execute();
		while ($data = $q->fetch(PDO::FETCH_ASSOC)) {
			$place = new Place($data);
			if ($isEagerFetch) {
				$country_manager = new CountryManager($this->bdd);
				$place->setCountry($country_manager->getCountry($place->getCountryId()));
				}
			$places[] = $place;
		}
		return $places;
	}
	
	public function getPlacesByPage($country_id, $page_num, $lpp, $isEagerFetch = true) {
		$logs = array();
		$start = ($page_num-1)*$lpp;
		if ($country_id > 0) {
			$q = $this->bdd->prepare('SELECT * FROM places WHERE country_id = :country_id ORDER BY id DESC LIMIT :start, :lpp');
			$q->bindValue(':country_id', $country_id, PDO::PARAM_INT);
			}
		else {
			$q = $this->bdd->prepare('SELECT * FROM places ORDER BY id DESC LIMIT :start, :lpp');
		}
		$q->bindValue(':start', $start, PDO::PARAM_INT);
		$q->bindValue(':lpp', $lpp, PDO::PARAM_INT);
		$q->execute();
		while ($data = $q->fetch(PDO::FETCH_ASSOC)) {
			$place = new Place($data);
			if ($isEagerFetch) {
				$country_manager = new CountryManager($this->bdd);
				$place->setCountry($country_manager->getCountry($place->getCountryId()));
				}
			$places[] = $place;
		}
		return $places;
	}
	
	/**
	 * Retourne le nombre max de places
	 */
	public function getMaxPlaces($country_id) {
		if ($country_id > 0) {
			$q = $this->bdd->prepare('SELECT count(1) FROM places WHERE country_id = :country_id');
			$q->bindValue(':country_id', $country_id, PDO::PARAM_INT);
			}
		else {
			$q = $this->bdd->prepare('SELECT count(1) FROM places');
		}
		$q->execute();
		return intval($q->fetch(PDO::FETCH_COLUMN));
	}
	
	/**
	 * Retourne une liste des places formatée pour peupler un menu déroulant
	 */
	public function getPlacesForSelect() {
		$places = array();
		$q = $this->bdd->prepare('SELECT p.id, p.name, c.name AS country_name FROM places p 
								  JOIN countries c ON c.id = p.country_id ORDER BY c.id');
		$q->execute();
		while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
			$places[$row["id"]] =  $row["country_name"] . " / ". $row["name"];
		}
		return $places;
	}
	
	
	/**
	* Efface l'objet place de la bdd
	* @param Place $place
	*/
	public function deletePlace(Place $place) {
		$q = $this->bdd->prepare("DELETE FROM places WHERE id = :id");
		$q->bindValue(':id', $place->getId(), PDO::PARAM_INT);
		return $q->execute();
	}

	/**
	* Enregistre l'objet place en bdd
	* @param Place $place
	*/
	public function savePlace(Place $place) {
		if ($place->getId() == -1) {
			$q = $this->bdd->prepare('INSERT INTO places SET country_id = :country_id, gps_coord = :gps_coord, name = :name, description = :description');
		} else {
			$q = $this->bdd->prepare('UPDATE places SET country_id = :country_id, gps_coord = :gps_coord, name = :name, description = :description WHERE id = :id');
			$q->bindValue(':id', $place->getId(), PDO::PARAM_INT);
		}
		$q->bindValue(':country_id', $place->getCountryId(), PDO::PARAM_STR);
		$q->bindValue(':gps_coord', $place->getGpsCoord(), PDO::PARAM_STR);
		$q->bindValue(':name', $place->getName(), PDO::PARAM_STR);
		$q->bindValue(':description', $place->getDescription(), PDO::PARAM_STR);	
		$q->execute();
		if ($place->getId() == -1) $place->setId($this->bdd->lastInsertId());
	}
}
?>
