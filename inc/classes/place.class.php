<?php
/**
* @project		
* @author		Olivier Gaillard
* @version		1.0 du 29/11/2014
* @desc			Objet place
*/

class Place {
	public $id;
	public $country_id;
	public $country;
	public $gps_coord;
	public $name;
	public $description;
	public $photo;
	public $sort;

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
	// country_id
	public function setCountryId($country_id) {
		$this->country_id = (integer)$country_id;
	}
	public function getCountryId() {
		return $this->country_id;
	}
	// country
	public function setCountry(Country $country) {
		$this->country = $country;
	}
	public function getCountry() {
		return $this->country;
	}
	// gps_coord
	public function setGpsCoord($gps_coord) {
		$this->gps_coord = $gps_coord;
	}
	public function getGpsCoord() {
		return $this->gps_coord;
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
	// photo
	public function setPhoto($photo) {
		$this->photo = $photo;
	}
	public function getPhoto() {
		return $this->photo;
	}
	// position
	public function setPosition($position) {
		$this->position = $position;
	}
	public function getPosition() {
		return $this->position;
	}
}
?>
