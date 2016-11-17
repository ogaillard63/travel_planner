<?php
/**
* @project		
* @author		Olivier Gaillard
* @version		1.0 du 29/11/2014
* @desc			Gestion des types
*/

class TypesManager {
	protected $bdd;

	public function __construct(PDO $bdd) {
		$this->bdd = $bdd;
	}

	/**
	* Retourne l'objet type correspondant à l'Id
	* @param $id
	*/
	public function getType($id) {
	if ($id) {
		$q = $this->bdd->prepare("SELECT * FROM types WHERE id = :id");
		$q->bindValue(':id', $id, PDO::PARAM_INT);
		$q->execute();
		return new Type($q->fetch(PDO::FETCH_ASSOC));
		}
	}

	/**
	* Retourne la liste des types
	*/
	public function getTypes() {
		$types = array();
		$q = $this->bdd->prepare('SELECT * FROM types ORDER BY id');
		$q->execute();
		while ($data = $q->fetch(PDO::FETCH_ASSOC)) {
			$types[] = new Type($data);
		}
		return $types;
	}
	
	/*public function getTypes($isEagerFetch = true) {
		$types = array();
		$q = $this->bdd->prepare('SELECT * FROM types ORDER BY #objet2#_id');
		$q->execute();
		while ($data = $q->fetch(PDO::FETCH_ASSOC)) {
			$type = new Type($data);
			if ($isEagerFetch) {
				$#objet2#_manager = new #Objet2#Manager($this->bdd);
				$type->set#Objet2#($#objet2#_manager->get#Objet2#($type->get#Objet2#Id()));
				}
			$types[] = $type;
		}
		return $types;
	}*/
	
	
	/**
	 * Retourne une liste des types formatée pour peupler un menu déroulant
	 */
	public function getTypesForSelect($key = 1) {
		$types = array();
		$q = $this->bdd->prepare('SELECT id, name FROM types WHERE `key` = :key ORDER BY id');
		$q->bindValue(':key', $key, PDO::PARAM_INT);
		$q->execute();
		while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
			$types[$row["id"]] =  $row["name"];
		}
		return $types;
	} 
	
	
	/**
	* Efface l'objet type de la bdd
	* @param Type $type
	*/
	public function deleteType(Type $type) {
		$q = $this->bdd->prepare("DELETE FROM types WHERE id = :id");
		$q->bindValue(':id', $type->getId(), PDO::PARAM_INT);
		return $q->execute();
	}

	/**
	* Enregistre l'objet type en bdd
	* @param Type $type
	*/
	public function saveType(Type $type) {
		if ($type->getId() == -1) {
			$q = $this->bdd->prepare('INSERT INTO types SET `name` = :name, `key` = :key');
		} else {
			$q = $this->bdd->prepare('UPDATE types SET `name` = :name, `key` = :key WHERE id = :id');
			$q->bindValue(':id', $type->getId(), PDO::PARAM_INT);
		}
		$q->bindValue(':name', $type->getName(), PDO::PARAM_STR);
		$q->bindValue(':key', $type->getKey(), PDO::PARAM_INT);	
		$q->execute();
		if ($type->getId() == -1) $type->setId($this->bdd->lastInsertId());
	}
}
?>
