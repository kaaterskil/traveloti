<?php
/**
 * Traveloti Library
 *
 * @package		Traveloti_Base
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Base\Model;

use Application\StdLib\Object;
use Base\Model\Notifiable;
use Base\Model\User;
use Base\Model\TravelLog;
use Doctrine\ORM\Mapping as ORM;

/**
 * An administrator of a Travel Log
 * @author Blair
 *
 * @ORM\Entity
 * @ORM\Table(name="log_administrator")
 */
class TravelLogAdmin implements Object, Notifiable {
	const ADMINISTER = "administer";
	const EDIT_PROFILE = "edit_profile";
	const CREATE_CONTENT = "create_content";
	const MODERATE_CONTENT = "moderate_content";
	const BASIC_ADMIN = "basic_admin";

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="log_administrator_id")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;
	
	/**
	 * Bidirectional one-to-many: owning side
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="travel_logs")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="traveloti_id")
	 */
	private $user;
	
	/**
	 * Bidirectional one-to-many: owning side
	 * @ORM\ManyToOne(targetEntity="TravelLog", inversedBy="administrators")
	 * @ORM\JoinColumn(name="travel_log_id", referencedColumnName="traveloti_id")
	 */
	private $travelLog;
	
	/** @ORM\Column(type="string") */
	private $role = self::ADMINISTER;

	/**
	 * One directional many-to-one
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(name="created_by", referencedColumnName="traveloti_id")
	 */
	private $createdBy;
	
	/** @ORM\Column(type="datetime", name="creation_date") */
	private $creationDate;
	
	/** @ORM\Column(type="datetime", name="last_update_date") */
	private $lastUpdateDate;
	
	private $type = 'travelLogAdmin';
	
	/*----- Getter/Setters -----*/
	
	/** @return int */
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
	
	public function setUser(User $user) {
		$this->user = $user;
	}
	
	/** @return TravelLog */
	public function getTravelLog() {
		return $this->travelLog;
	}
	
	public function setTravelLog(TravelLog $travelLog) {
		$this->travelLog = $travelLog;
	}
	
	/** @return string */
	public function getRole() {
		return $this->role;
	}
	
	public function setRole($role) {
		$this->role = $role;
	}
	
	/** @return User */
	public function getCreatedBy() {
		return $this->createdBy;
	}
	
	public function setCreatedBy(User $createdBy) {
		$this->createdBy = $createdBy;
	}
	
	/** @return DateTime */
	public function getCreationDate() {
		return $this->creationDate;
	}
	
	public function setCreationDate($creationDate) {
		$this->creationDate = $creationDate;
	}
	
	/** @return DateTime */
	public function getLastUpdateDate() {
		return $this->lastUpdateDate;
	}
	
	public function setLastUpdateDate($lastUpdateDate) {
		$this->lastUpdateDate = $lastUpdateDate;
	}
	
	/**
	 * @return string
	 * @see \Base\Model\Notifiable::getType()
	 */
	public function getType() {
		return $this->type;
	}
	
	/*----- Methods -----*/
	
	/**
	 * @param Object $o
	 * @return boolean
	 * @see \Application\StdLib\Object::equals()
	 */
	public function equals(Object $o) {
		if(!$o instanceof $this) {
			return false;
		}
		$that = $o;
		if($that->getId() == $this->getId()) {
			return true;
		}
		if(($that->getUser()->equals($this->getUser()))
				&& ($that->getLog()->equals($this->getLog()))) {
			return true;
		}
		return false;
	}
	
	/**
	 * @return string
	 * @see \Application\StdLib\Object::getClass()
	 */
	public function getClass() {
		return get_class($this);
	}

	/**
	 * @return User
	 * @see \Base\Model\Notifiable::getFrom()
	 */
	public function getFrom() {
		return $this->getCreatedBy();
	}
	
	/**
	 * @param Traveloti $sender
	 * @return \Base\Model\NotifierText
	 * @see \Base\Model\Notifiable::getNotifierText()
	 */
	public function getNotifierText(Traveloti $sender) {
		$title = $sender->getDisplayName()
			. ' added you as a Travel Log Administrator.';
		$body = $sender->getDisplayName() . 'added you as an administrator'
			. ' for one of their Travel Logs.';
		$htmlTitle = '<span class="blue-name">' . $sender->getDisplayName()
			. '</span> added you as a Travel Log Administrator.';
		$htmlBody = '<span class=""blue-name>' . $sender->getDisplayName()
			. '</span> added as an administrator for one of their'
			. ' Travel Logs.';
		return new NotifierText($title, $htmlTitle, $body, $htmlBody);
	}
	
	/**
	 * @return string
	 * @see \Application\StdLib\Object::__toString()
	 */
	public function __toString() {
		return 'TravelLogAdmin[id=' . $this->getId()
			. ',userId=' . ($this->getUser() ? $this->getUser()->getId() : null)
			. '.travelLogId=' . ($this->getTravelLog() ? $this->getTravelLog()->getId() : null)
			. ',role=' . $this->getRole()
			. ',creationDate=' . $this->getCreationDate()
			. ',lastUpdateDate=' . $this->getLastUpdateDate()
			. ']';
	}
}
?>