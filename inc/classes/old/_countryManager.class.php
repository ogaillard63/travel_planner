<?php
/**
* @project		
* @author		Olivier Gaillard
* @version		1.0 du 29/11/2014
* @desc			Gestion des countries
*/

class CountryManager {
	protected $bdd;

	public function __construct(PDO $bdd) {
		$this->bdd = $bdd;
	}

	/**
	* Retourne l'objet country correspondant à l'Id
	* @param $id
	*/
	public function getCountry($id) {
	if ($id) {
		$q = $this->bdd->prepare("SELECT * FROM countries WHERE id = :id");
		$q->bindValue(':id', $id, PDO::PARAM_INT);
		$q->execute();
		return new Country($q->fetch(PDO::FETCH_ASSOC));
		}
	}

	/**
	* Retourne la liste des countries
	*/
	/*public function getCountries() {
		$countries = array();
		$q = $this->bdd->prepare('SELECT * FROM countries ORDER BY id');
		$q->execute();
		while ($data = $q->fetch(PDO::FETCH_ASSOC)) {
			$countries[] = new Country($data);
		}
		return $countries;
	}*/
	
	public function getCountries($continent_id, $isEagerFetch = true) {
		$countries = array();
		if ($continent_id > 0) {
			$q = $this->bdd->prepare('SELECT * FROM countries WHERE continent_id = :continent_id');
			$q->bindValue(':continent_id', $continent_id, PDO::PARAM_INT);
			}
		else {
			$q = $this->bdd->prepare('SELECT * FROM countries ORDER BY continent_id');
		}
		$q->execute();
		while ($data = $q->fetch(PDO::FETCH_ASSOC)) {
			$country = new Country($data);
			if ($isEagerFetch) {
				$continent_manager = new ContinentManager($this->bdd);
				$country->setContinent($continent_manager->getContinent($country->getContinentId()));
				}
			$countries[] = $country;
		}
		return $countries;
	}
	
	/**
	 * Retourne une liste des countries formatée pour peupler un menu déroulant
	 */
	public function getCountriesForSelect() {
		$countries = array();
		$q = $this->bdd->prepare('SELECT id, name FROM countries ORDER BY id');
		$q->execute();
		while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
			$countries[$row["id"]] =  $row["name"];
		}
		return $countries;
	}
	
	
	/**
	* Efface l'objet country de la bdd
	* @param Country $country
	*/
	public function deleteCountry(Country $country) {
		$q = $this->bdd->prepare("DELETE FROM countries WHERE id = :id");
		$q->bindValue(':id', $country->getId(), PDO::PARAM_INT);
		return $q->execute();
	}

	/**
	* Enregistre l'objet country en bdd
	* @param Country $country
	*/
	public function saveCountry(Country $country) {
		if ($country->getId() == -1) {
			$q = $this->bdd->prepare('INSERT INTO countries SET continent_id = :continent_id, name = :name, description = :description, flag = :flag');
		} else {
			$q = $this->bdd->prepare('UPDATE countries SET continent_id = :continent_id, name = :name, description = :description, flag = :flag WHERE id = :id');
			$q->bindValue(':id', $country->getId(), PDO::PARAM_INT);
		}
		$q->bindValue(':continent_id', $country->getContinentId(), PDO::PARAM_STR);
		$q->bindValue(':name', $country->getName(), PDO::PARAM_STR);
		$q->bindValue(':description', $country->getDescription(), PDO::PARAM_STR);
		$q->bindValue(':flag', $country->getFlag(), PDO::PARAM_STR);	
		$q->execute();
		if ($country->getId() == -1) $country->setId($this->bdd->lastInsertId());
	}
}
?>
