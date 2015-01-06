<?php
/**
* @project		
* @author		Olivier Gaillard
* @version		1.0 du 29/11/2014
* @desc			Gestion des continents
*/

class ContinentManager {
	protected $bdd;

	public function __construct(PDO $bdd) {
		$this->bdd = $bdd;
	}

	/**
	* Retourne l'objet continent correspondant à l'Id
	* @param $id
	*/
	public function getContinent($id) {
	if ($id) {
		$q = $this->bdd->prepare("SELECT * FROM continents WHERE id = :id");
		$q->bindValue(':id', $id, PDO::PARAM_INT);
		$q->execute();
		return new Continent($q->fetch(PDO::FETCH_ASSOC));
		}
	}

	/**
	* Retourne la liste des continents
	*/
	public function getContinents() {
		$continents = array();
		$q = $this->bdd->prepare('SELECT * FROM continents ORDER BY id');
		$q->execute();
		while ($data = $q->fetch(PDO::FETCH_ASSOC)) {
			$continents[] = new Continent($data);
		}
		return $continents;
	}
	
	/*public function getContinents($isEagerFetch = false) {
		$continents = array();
		$q = $this->bdd->prepare('SELECT * FROM continents ORDER BY #objet2#_id');
		$q->execute();
		while ($data = $q->fetch(PDO::FETCH_ASSOC)) {
			$continent = new Continent($data);
			if ($isEagerFetch) {
				$#objet2#_manager = new #Objet2#Manager($this->bdd);
				$continent->set#Objet2#($#objet2#_manager->get#Objet2#($continent->get#Objet2#Id()));
				}
			$continents[] = $continent;
		}
		return $continents;
	}*/
	
	
	/**
	 * Retourne une liste des continents formatée pour peupler un menu déroulant
	 */
	public function getContinentsForSelect() {
		$continents = array();
		$q = $this->bdd->prepare('SELECT id, name FROM continents ORDER BY id');
		$q->execute();
		while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
			$continents[$row["id"]] =  $row["name"];
		}
		return $continents;
	}
	
	
	/**
	* Efface l'objet continent de la bdd
	* @param Continent $continent
	*/
	public function deleteContinent(Continent $continent) {
		$q = $this->bdd->prepare("DELETE FROM continents WHERE id = :id");
		$q->bindValue(':id', $continent->getId(), PDO::PARAM_INT);
		return $q->execute();
	}

	/**
	* Enregistre l'objet continent en bdd
	* @param Continent $continent
	*/
	public function saveContinent(Continent $continent) {
		if ($continent->getId() == -1) {
			$q = $this->bdd->prepare('INSERT INTO continents SET name = :name');
		} else {
			$q = $this->bdd->prepare('UPDATE continents SET name = :name WHERE id = :id');
			$q->bindValue(':id', $continent->getId(), PDO::PARAM_INT);
		}
		$q->bindValue(':name', $continent->getName(), PDO::PARAM_STR);	
		$q->execute();
		if ($continent->getId() == -1) $continent->setId($this->bdd->lastInsertId());
	}
}
?>
