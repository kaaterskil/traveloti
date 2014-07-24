<?php
/**
 * Traveloti Library
 *
 * @category	Traveloti
 * @package		Traveloti_Config
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Config\Model;

use Application\StdLib\Exception\ClassCastException;

use Application\StdLib\Object;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * A profile category
 * @author Blair
 *
 * @ORM\Entity
 * @ORM\Table(name="cfg_profile_categories")
 */
class ProfileCategory implements Object {

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="cfg_profile_category_id")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;
	
	/** @ORM\Column(type="string") */
	private $name;
	
	/** @ORM\Column(type="string", name="display_name") */
	private $displayName;
	
	/**
	 * One-to-many bidirectional: inverse side
	 * @ORM\OneToMany(targetEntity="Config\Model\Profile", mappedBy="category")
	 * @ORM\OrderBy({"displayName"="asc"})
	 */
	private $profiles;
	
	public function __construct(){
		$this->profiles = new ArrayCollection();
	}
	
	/*----- Getter/Setters -----*/
	
	/** @return int */
	public function getId() {
		return $this->id;
	}
	
	public function setId($id) {
		$this->id = (int) $id;
	}
	
	/** @return string */
	public function getName() {
		return $This->name;
	}
	
	public function setName($name) {
		$this->name = $name;
	}
	
	/** @return string */
	public function getDisplayName() {
		return $this->displayName;
	}
	
	public function setDisplayName($displayName) {
		$this->displayName = $displayName;
	}
	
	/** @return ArrayCollection */
	public function getProfiles() {
		return $this->profiles;
	}
	
	public function setProfiles(ArrayCollection $profiles) {
		$this->profiles = $profiles;
	}
	
	/*----- Methods -----*/
	
	public function addProfile(Profile $profile) {
		$profile->setCategory($this);
		$this->profiles->add($profile);
	}
	
	public function equals(Object $o) {
		if(!$o instanceof $this) {
			throw new ClassCastException();
		}
		$that = $o;
		if($that->getId() == $this->getId()) {
			return true;
		}
		if($that->getName() == $this->getName()) {
			return true;
		}
		return false;
	}
	
	public function getClass() {
		return get_class($this);
	}
	
	public function __toString() {
		return 'ProfileCategory[id=' . $this->getId()
			. ',name=' . $this->getName()
			. ',displayName=' . $this->getDisplayName()
			. ']';
	}
}
?>