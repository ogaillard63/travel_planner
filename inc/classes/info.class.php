<?php
/**
* @project		Travel Planner
* @author		Olivier Gaillard
* @version		1.0 du 16/02/2015
* @desc			Objet info
*/

class Info {
	public $id;
	public $place_id;
	public $place;
	public $type_id;
	public $type;
	public $title;
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
	// place_id
	public function setPlaceId($place_id) {
		$this->place_id = (integer)$place_id;
	}
	public function getPlaceId() {
		return $this->place_id;
	}
	// place
	public function setPlace($place) {
		$this->place = $place;
	}
	public function getPlace() {
		return $this->place;
	}
	// type_id
	public function setTypeId($type_id) {
		$this->type_id = (integer)$type_id;
	}
	public function getTypeId() {
		return $this->type_id;
	}
	// type
	public function setType($type) {
		$this->type = $type;
	}
	public function getType() {
		return $this->type;
	}
	// title
	public function setTitle($title) {
		$this->title = $title;
	}
	public function getTitle() {
		return $this->title;
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
