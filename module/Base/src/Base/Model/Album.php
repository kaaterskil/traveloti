<?php
/**
 * Traveloti Library
 *
 * @category	Traveloti
 * @package		Traveloti_Base
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Base\Model;

use Application\StdLib\Object;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Base\Model\User;

/**
 * Album Class
 * @author Blair
 *
 * @ORM\Entity
 * @ORM\Table(name="albums")
 */
class Album implements Object {

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="album_id")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * Bidirectional one-to-many: owning side
	 * @ORM\ManyToOne(targetEntity="Traveloti", inversedBy="albums")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="traveloti_id")
	 */
	private $user;

	/** @ORM\Column(type="string") */
	private $name;
	
	/** @ORM\Column(type="string") */
	private $description;

	/** @ORM\Column(type="string") */
	private $location;

	/** @ORM\Column(type="string") */
	private $visibility;

	/** @ORM\Column(type="datetime", name="creation_date") */
	private $creationDate;

	/** @ORM\Column(type="datetime", name="last_update_date") */
	private $lastUpdateDate;

	/**
	 * Bidirectional one-to-many: inverse side
	 * @ORM\OneToMany(targetEntity="Photo", mappedBy="album")
	 */
	private $photos;
	
	/*----- Constructor -----*/

	public function __construct() {
		$this->photos = new ArrayCollection();
	}

	/*----- Getter/Setters -----*/

	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = (int) $id;
	}

	/** @return User */
	public function getUser() {
		return $this->user;
	}

	public function setUser(Traveloti $user) {
		$this->user = $user;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function setName($name) {
		$this->name = $name;
	}
	
	public function getDescription() {
		return $this->description;
	}
	
	public function setDescription($description) {
		$this->description = $description;
	}

	public function getLocation() {
		return $this->location;
	}

	public function setLocation($location) {
		$this->location = $location;
	}

	public function getVisibility() {
		return $this->visibility;
	}

	public function setVisibility($visibility) {
		$this->visibility = $visibility;
	}

	public function getCreationDate() {
		return $this->creationDate;
	}

	public function setCreationDate($creationDate) {
		$this->creationDate = $creationDate;
	}

	public function getLastUpdateDate() {
		return $this->lastUpdateDate;
	}

	public function setLastUpdateDate($lastUpdateDate) {
		$this->lastUpdateDate = $lastUpdateDate;
	}
	
	/** @return ArrayCollection */
	public function getPhotos() {
		return $this->photos;
	}
	
	public function setPhotos(ArrayCollection $photos) {
		$this->photos = $photos;
	}
	
	
	public function addPhoto(Photo $photo) {
		$photo->setAlbum($this);
		$this->photos->add($photo);
	}
	
	public function contains(Photo $photo) {
		/* @var $test Photo */
		
		$result = false;
		$photos = $this->getPhotos()->getValues();
		foreach ($photos as $test) {
			if($test->equals($photo)) {
				$result = true;
				break;
			}
		}
		return $result;
	}
	
	/** @see \Application\StdLib\Object::equals() */
	public function equals(Object $o) {
		if(!$o instanceof $this) {
			throw new \ClassCastException();
		}
		if($o->getId() == $this->getId()) {
			return true;
		}
		if(($o->getUser()->getId() == $this->getUser()->getId())
				&& ($o->getName() == $this->getName())) {
			return true;
		}
		return false;
	}
	
	/** @see \Application\StdLib\Object::getClass() */
	public function getClass() {
		return get_class($this);
	}
	
	/** @see \Application\StdLib\Object::__toString() */
	public function __toString() {
		return 'Album[id=' . $this->getId()
			. ',userId=' . $this->getUser()->getId()
			. ',name=' . $this->getName()
			. ',location=' . $this->getLocation()
			. ',visibility' . $this->getVisibility()
			. ',creationDate=' . $this->getCreationDate()
			. ',lastUpdateDate=' . $this->getLastUpdateDate()
			. ']';
	}
}
?>