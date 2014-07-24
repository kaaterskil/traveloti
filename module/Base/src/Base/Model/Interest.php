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

use Config\Model\Profile;

use Application\StdLib\Object;
use Doctrine\ORM\Mapping as ORM;
use Config\Model\Profile as MasterProfile;

/**
 * A traveloti member profile item. These profiles can be either user-specific
 * or trip-specific
 * @author Blair
 *
 * @ORM\Entity
 * @ORM\Table(name="interests")
 */
class Interest implements Object {

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="interest_id")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * Bidirectional one-to-many: owning side
	 * @ORM\ManyToOne(targetEntity="Traveloti", inversedBy="interests")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="traveloti_id")
	 */
	private $user;

	/**
	 * Bidirectional one-to-many: owning side
	 * @ORM\ManyToOne(targetEntity="Config\Model\Profile", inversedBy="userProfiles")
	 * @ORM\JoinColumn(name="cfg_profile_id", referencedColumnName="cfg_profile_id")
	 */
	private $profile;

	/** @ORM\Column(type="datetime", name="creation_date", nullable=true) */
	private $creationDate;

	/*----- Constructor -----*/

	public function __construct() {
	}

	/*----- Getter/Setters -----*/

	/** @return int */
	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = (int) $id;
	}

	/** @return Traveloti */
	public function getUser() {
		return $this->user;
	}

	public function setUser(Traveloti $user) {
		$this->user = $user;
	}

	/** @return Profile */
	public function getProfile() {
		return $this->profile;
	}

	public function setProfile(Profile $profile) {
		$this->profile = $profile;
	}

	/** @return DateTime */
	public function getCreationDate() {
		$this->creationDate;
	}

	public function setCreationDate($creationDate) {
		$this->creationDate = $creationDate;
	}

	/*----- Methods -----*/

	/** @see \Application\StdLib\Object::equals() */
	public function equals(Object $o) {
		if(!$o instanceof $this) {
			throw new \ClassCastException();
		}
		if($o->getId() == $this->getId()) {
			return true;
		}
		if(($o->getUser()->getId() == $this->getUser()->getId())
			&& ($o->getProfile()->getId() == $this->getProfile()->getId())) {
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
		return 'Interest[id=' . $this->getId()
		. ',userId=' . $this->getUser()->getId()
		. ',profileId=' . $this->getProfile()->getId()
		. ',creationDate=' . $this->getCreationDate()
		. ']';
	}
}
?>