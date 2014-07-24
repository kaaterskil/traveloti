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
use Base\Model\Interest;
use Config\Model\ProfileCategory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * A master profile
 * @author Blair
 *
 *@ORM\Entity
 *@ORM\Table(name="cfg_profiles")
 */
class Profile implements Object {
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="cfg_profile_id")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;
	
	/**
	 * One-to-many bidirectional: owning side
	 * @ORM\ManyToOne(targetEntity="ProfileCategory", inversedBy="profiles")
	 * @ORM\JoinColumn(name="cfg_profile_category_id", referencedColumnName="cfg_profile_category_id")
	 */
	private $category;
	
	/** @ORM\Column(type="string") */
	private $name;
	
	/** @ORM\Column(type="string", name="display_name", nullable=true) */
	private $displayName;
	
	/** @ORM\Column(type="string", nullable=true) */
	private $description;
	
	/** @ORM\Column(type="integer") */
	private $enabled = 1;
	
	/** @ORM\Column(type="datetime", name="creation_date") */
	private $creationDate;
	
	/**
	 * One-to-many bidirectional: inverse side
	 * @ORM\OneToMany(targetEntity="Base\Model\Interest", mappedBy="profile")
	 */
	private $userProfiles;
	
	public function __construct(){
		$this->userProfiles = new ArrayCollection();
	}
	
	/*----- Getter/Setters -----*/
	
	/** @return int */
	public function getId() {
		return $this->id;
	}
	
	public function setId($id) {
		$this->id = (int) $id;
	}
	
	/** @return ProfileCategory */
	public function getCategory() {
		return $this->category;
	}
	
	public function setCategory(ProfileCategory $category) {
		$this->category = $category;
	}
	
	/** @return string */
	public function getName() {
		return $this->name;
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

	/** @return string */
	public function getDescription() {
		return $this->description;
	}
	
	public function setDescription($description) {
		$this->description = $description;
	}
	
	/** @return boolean */
	public function getEnabled() {
		return $this->enabled ? true : false;
	}
	
	public function setEnabled($enabled) {
		$this->enabled = $enabled ? 1 : 0;
	}

	/** @return DateTime */
	public function getCreationDate() {
		return $this->creationDate;
	}

	public function setCreationDate($creationDate) {
		$this->creationDate = $creationDate;
	}
	
	/** @return ArrayCollection */
	public function getInterests() {
		return $this->userProfiles;
	}
	
	public function setInterests(ArrayCollection $userProfiles) {
		$this->userProfiles = $userProfiles;
	}
	
	/*----- Methods -----*/
	
	public function addInterest(Interest $interest) {
		$interest->setProfile($this);
		$this->userProfiles->add($interest);
	}
	
	/** @see \Application\StdLib\Object::equals() */
	public function equals(Object $o) {
		if(!$o instanceof $this) {
			throw new ClassCastException();
		}
		$that = $o;
		if($that->getId() == $this->getId()) {
			return true;
		}
		if(($that->getName() == $this->getName())
				&& ($that->getCategory()->equals($this->getCategory()))) {
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
		return 'Profile[id=' . $this->getId()
			. ',categoryId=' . ($this->getCategory() ? $this->getCategory()->getName() : null)
			. ',name=' . $this->getName()
			. ',displayName=' . $this->getDisplayName()
			. ',description=' . $this->getDescription()
			. ',enabled=' . ($this->enabled ? 'true' : 'false')
			. ',creationDate=' . date('Y-m-d H:i:s', $this->getCreationDate()->getTimestamp())
			. ']';
	}
}
?>