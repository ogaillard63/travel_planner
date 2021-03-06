<?php
/**
* @project		
* @author		Olivier Gaillard
* @version		1.0 du 28/11/2014
* @desc			Gestion des routes
*/

class RoutesManager {
	protected $bdd;

	public function __construct(PDO $bdd) {
		$this->bdd = $bdd;
	}

	/**
	* Retourne l'objet route correspondant à l'Id
	* @param $id
	*/
	public function getRoute($id, $isEagerFetch = true) {
	if ($id) {
		$q = $this->bdd->prepare("SELECT * FROM routes WHERE id = :id");
		$q->bindValue(':id', $id, PDO::PARAM_INT);
		$q->execute();
		return new Route($q->fetch(PDO::FETCH_ASSOC));
		}
	}

	/**
	* Retourne la liste des routes
	*/
	/*public function getRoutes() {
		$routes = array();
		$q = $this->bdd->prepare('SELECT * FROM routes ORDER BY id');
		$q->execute();
		while ($data = $q->fetch(PDO::FETCH_ASSOC)) {
			$routes[] = new Route($data);
		}
		return $routes;
	}*/

		public function getRoutes($country_id = 0, $isEagerFetch = false) {
		$routes = array();
		if ($country_id > 0) {
			$q = $this->bdd->prepare('SELECT * FROM routes WHERE country_id = :country_id ORDER BY id');
			$q->bindValue(':country_id', $country_id, PDO::PARAM_INT);
			}
		else {
			$q = $this->bdd->prepare('SELECT * FROM routes ORDER BY id');
		}
		$q->execute();
		while ($data = $q->fetch(PDO::FETCH_ASSOC)) {
			$route = new Route($data);
			if ($isEagerFetch) {
				// FromPlace
				$places_manager = new PlacesManager($this->bdd);
				$route->setFromPlace($places_manager->getPlace($route->getFromPlaceId()));
				// ToPlace
				$places_manager = new PlacesManager($this->bdd);
				$route->setToPlace($places_manager->getPlace($route->getToPlaceId()));
				}
			$routes[] = $route;
		}
		return $routes;
	}
	
	
	/**
	* Efface l'objet route de la bdd
	* @param Route $route
	*/
	public function deleteRoute(Route $route) {
		$q = $this->bdd->prepare("DELETE FROM routes WHERE id = :id");
		$q->bindValue(':id', $route->getId(), PDO::PARAM_INT);
		return $q->execute();
	}

	/**
	* Enregistre l'objet route en bdd
	* @param Route $route
	*/
	public function saveRoute(Route $route) {
		if ($route->getId() == -1) {
			$q = $this->bdd->prepare('INSERT INTO routes SET country_id = :country_id, from_place_id = :from_place_id, to_place_id = :to_place_id, name = :name, description = :description');
		} else {
			$q = $this->bdd->prepare('UPDATE routes SET country_id = :country_id, from_place_id = :from_place_id, to_place_id = :to_place_id, name = :name, description = :description WHERE id = :id');
			$q->bindValue(':id', $route->getId(), PDO::PARAM_INT);
		}
		$q->bindValue(':country_id', $route->getCountryId(), PDO::PARAM_STR);
		$q->bindValue(':from_place_id', $route->getFromPlaceId(), PDO::PARAM_INT);
		$q->bindValue(':to_place_id', $route->getToPlaceId(), PDO::PARAM_INT);
		$q->bindValue(':name', $route->getName(), PDO::PARAM_STR);
		$q->bindValue(':description', $route->getDescription(), PDO::PARAM_STR);	
		$q->execute();
		if ($route->getId() == -1) $route->setId($this->bdd->lastInsertId());
	}
}
?>
