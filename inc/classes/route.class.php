<?php
/**
* @project		
* @author		Olivier Gaillard
* @version		1.0 du 28/11/2014
* @desc			Objet route
*/

class Route {
	public $id;
	public $country_id;
	public $from_place_id;
	public $from_place;
	public $to_place_id;
	public $to_place;
	public $name;
	public $description;

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
	// from_place_id
	public function setFromPlaceId($from_place_id) {
		$this->from_place_id = (integer)$from_place_id;
	}
	public function getFromPlaceId() {
		return $this->from_place_id;
	}

	// from_place
	public function setFromPlace($from_place) {
		$this->from_place = $from_place;
	}
	public function getFromPlace() {
		return $this->from_place;
	}
	// to_place_id
	public function setToPlaceId($to_place_id) {
		$this->to_place_id = (integer)$to_place_id;
	}
	public function getToPlaceId() {
		return $this->to_place_id;
	}
	// to_place
	public function setToPlace($to_place) {
		$this->to_place = $to_place;
	}
	public function getToPlace() {
		return $this->to_place;
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
}
?>
