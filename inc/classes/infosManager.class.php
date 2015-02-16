<?php
/**
* @project		Travel Planner
* @author		Olivier Gaillard
* @version		1.0 du 16/02/2015
* @desc			Gestion des infos
*/

class InfosManager {
	protected $bdd;

	public function __construct(PDO $bdd) {
		$this->bdd = $bdd;
	}

	/**
	* Retourne l'objet info correspondant à l'Id
	* @param $id
	*/
	public function getInfo($id) {
	if ($id) {
		$q = $this->bdd->prepare("SELECT * FROM infos WHERE id = :id");
		$q->bindValue(':id', $id, PDO::PARAM_INT);
		$q->execute();
		return new Info($q->fetch(PDO::FETCH_ASSOC));
		}
	}

	/**
	* Retourne la liste des infos
	*/



	public function getInfos($place_id = 0) {
		$infos = array();
		if ($place_id > 0) {
			$q = $this->bdd->prepare('SELECT * FROM infos WHERE place_id = :place_id');
			$q->bindValue(':place_id', $place_id, PDO::PARAM_INT);
		}
		else {
			$q = $this->bdd->prepare('SELECT * FROM infos');
		}
		$q->execute();
		while ($data = $q->fetch(PDO::FETCH_ASSOC)) {
			$infos[] = new Info($data);
		}
		return $infos;
	}
	
	/**
	 * Retourne la liste des infos par page
	 */
	 public function getInfosByPage($page_num, $count) {
		return $this->getInfos(($page_num-1)*$count, $count);
	 }

	/**
	 * Retourne le nombre max de places
	 */
	public function getMaxInfos() {
		$q = $this->bdd->prepare('SELECT count(1) FROM infos');
		$q->execute();
		return intval($q->fetch(PDO::FETCH_COLUMN));
	}


	/**
	* Efface l'objet info de la bdd
	* @param Info $info
	*/
	public function deleteInfo(Info $info) {
		$q = $this->bdd->prepare("DELETE FROM infos WHERE id = :id");
		$q->bindValue(':id', $info->getId(), PDO::PARAM_INT);
		return $q->execute();
	}

	/**
	* Enregistre l'objet info en bdd
	* @param Info $info
	*/
	public function saveInfo(Info $info) {
		if ($info->getId() == -1) {
			$q = $this->bdd->prepare('INSERT INTO infos SET place_id = :place_id, title = :title, description = :description');
		} else {
			$q = $this->bdd->prepare('UPDATE infos SET place_id = :place_id, title = :title, description = :description WHERE id = :id');
			$q->bindValue(':id', $info->getId(), PDO::PARAM_INT);
		}
		$q->bindValue(':place_id', $info->getPlaceId(), PDO::PARAM_INT);
		$q->bindValue(':title', $info->getTitle(), PDO::PARAM_STR);
		$q->bindValue(':description', $info->getDescription(), PDO::PARAM_STR);
		$q->execute();
		if ($info->getId() == -1) $info->setId($this->bdd->lastInsertId());
	}


	/* ----------- fonctions optionnelles ----------- */

	/**
	 * Retourne la liste des infos avec les objets de type Place avec EagerFetch
	 */
	/* public function getInfos($isEagerFetch = false, $offset = null, $count = null) {
		$infos = array();
		if (isset($offset) && isset($count)) {
			$q = $this->bdd->prepare('SELECT * FROM infos ORDER BY id DESC LIMIT :offset, :count');
			$q->bindValue(':offset', $offset, PDO::PARAM_INT);
			$q->bindValue(':count', $count, PDO::PARAM_INT);
			}
		else {
			$q = $this->bdd->prepare('SELECT * FROM infos ORDER BY id');
			}
		$q->execute();
		while ($data = $q->fetch(PDO::FETCH_ASSOC)) {
			$info = new Info($data);
			if ($isEagerFetch) {
				$place_manager = new PlaceManager($this->bdd);
				$info->setPlace($place_manager->getPlace($info->getPlaceId()));
				}
			$infos[] = $info;
		}
		return $infos;
	} */

	/**
	 * Retourne la liste des infos par page avec EagerFetch
	 */
	/* public function getInfosByPage($page_num, $count, $isEagerFetch = false) {
		return $this->getInfos($isEagerFetch, ($page_num-1)*$count, $count);
	} */

	/**
	 * Retourne une liste des infos formatée pour peupler un menu déroulant
	 */
	/*public function getInfosForSelect() {
		$infos = array();
		$q = $this->bdd->prepare('SELECT id, name FROM infos ORDER BY id');
		$q->execute();
		while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
			$infos[$row["id"]] =  $row["name"];
		}
		return $infos;
	}*/


	/**
	 * Retourne la liste des infos par parent
	 */
	/*public function getInfosByParent() {
		$infos = array();
		$q1 = $this->bdd->prepare('SELECT * FROM infos WHERE parent_id = 0');
		$q1->execute();
		while ($data = $q1->fetch(PDO::FETCH_ASSOC)) {
			$info = new Info($data);
			$infos[] = $info;
			$q2 = $this->bdd->prepare('SELECT * FROM infos WHERE parent_id = :parent_id');
			$q2->bindValue(':parent_id', $info->getId(), PDO::PARAM_INT);
			$q2->execute();
			while ($data = $q2->fetch(PDO::FETCH_ASSOC)) {
				$infos[] = new Info($data);
			}
		}
		return  $infos;
	}
	*/

}
?>