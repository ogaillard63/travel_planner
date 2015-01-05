<?php
/**
* @project		Travel Planner
* @author		Olivier Gaillard
* @version		1.0 du 04/01/2015
* @desc			Objet photo
*/

class Photo {
	public $id;
	public $place_id;
	public $path;
	public $caption;

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
	// path
	public function setPath($path) {
		$this->path = $path;
	}
	public function getPath() {
		return $this->path;
	}
	// caption
	public function setCaption($caption) {
		$this->caption = $caption;
	}
	public function getCaption() {
		return $this->caption;
	}
}
?>
