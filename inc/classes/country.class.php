<?php
/**
* @project		
* @author		Olivier Gaillard
* @version		1.0 du 29/11/2014
* @desc			Objet country
*/

class Country {
	public $id;
	public $continent_id;
	public $name;
	public $description;
	public $flag;

	public function __construct(array $data) {
		$this->hydrate($data);
	}

	public function hydrate(array $data){
		foreach ($data as $key => $value) {
			if (strpos($key, "_") !== false) {
				$method = 'set';
				foreach (explode("_", $key) as $part) {
					$method .= ucfirst($part);
				}
			}
			else $method = 'set'.ucfirst($key);
			if (method_exists($this, $method)) {
				$this->$method($value);
			}
		}
	}

	/* --- Getters et Setters --- */

	// id
	public function setId($id) {
		$this->id = (integer)$id;
	}
	public function getId() {
		return $this->id;
		}
	// continent_id
	public function setContinentId($continent_id) {
		$this->continent_id = (integer)$continent_id;
	}
	public function getContinentId() {
		return $this->continent_id;
		}
	// continent
	public function setContinent(Continent $continent) {
		$this->continent = $continent;
		}

	public function getContinent() {		
		return $this->continent;	
		}
	// name
	public function setName($name) {
		$this->name = $name;
	}
	public function getName() {
		return $this->name;
	}
	// description
	public function setDescription($description) {
		$this->description = $description;
	}
	public function getDescription() {
		return $this->description;
	}
	// flag
	public function setFlag($flag) {
		$this->flag = $flag;
	}
	public function getFlag() {
		return $this->flag;
	}
}
?>
