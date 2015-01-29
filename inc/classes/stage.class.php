<?php
/**
* @project		Travel Planner
* @author		Olivier Gaillard
* @version		1.0 du 28/01/2015
* @desc			Objet stage
*/

class Stage {
	public $id;
	public $place_id;
	public $place;
	public $activities_ids;
	public $activities;
	public $arrival_date;
	public $position;
	public $duration;
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
		$this->id = $id;
	}
	public function getId() {
		return $this->id;
	}
	// place_id
	public function setPlaceId($place_id) {
		$this->place_id = $place_id;
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
	// activities
	public function setActivitiesIds($activities_ids) {
		$this->activities_ids = $activities_ids;
	}
	public function getActivitiesIds() {
		return $this->activities_ids;
	}
	// activities
	public function setActivities($activities) {
		$this->activities = $activities;
	}
	public function getActivities() {
		return $this->activities;
	}
	// arrival_date
	public function setArrivalDate($arrival_date) {
		$this->arrival_date = $arrival_date;
	}
	public function getArrivalDate() {
		return $this->arrival_date;
	}
	// position
	public function setPosition($position) {
		$this->position = $position;
	}
	public function getPosition() {
		return $this->position;
	}
	// duration
	public function setDuration($duration) {
		$this->duration = $duration;
	}
	public function getDuration() {
		return $this->duration;
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
