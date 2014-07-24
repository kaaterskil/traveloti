<?php
/**
 * Traveloti Library
 *
 * @category	Traveloti
 * @package		Traveloti_
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Base\Model;

use Application\StdLib\Constants;
use Application\StdLib\Object;
use Base\Model\Request;
use Doctrine\ORM\Mapping as ORM;

/**
 * FriendRequest Class
 * @author Blair
 *
 * @ORM\Entity
 * @ORM\Table(name="friend_request")
 */
class FriendRequest implements Request {

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="friend_request_id")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * Bidirectional one-to-many: owning side
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="sentFriendRequests")
	 * @ORM\JoinColumn(name="user_from_id", referencedColumnName="traveloti_id")
	 */
	private $sender;

	/**
	 * Bidirectional one-to-many: owning side
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="receivedFriendRequests")
	 * @ORM\JoinColumn(name="user_to_id", referencedColumnName="traveloti_id")
	 */
	private $recipient;

	/** @ORM\Column(type="integer", name="unread") */
	private $isUnread = 1;

	/** @ORM\Column(type="string") */
	private $message;

	/** @ORM\Column(type="datetime", name="creation_date") */
	private $creationDate;
	
	private $type = 'friendRequest';

	public function __construct(){
	}

	/*----- Getter/Setters -----*/

	/** @see \Base\Model\Request::getId() */
	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = (int) $id;
	}

	/** @see \Base\Model\Request::getSender() */
	public function getSender() {
		return $this->sender;
	}

	public function setSender(User $sender) {
		$this->sender = $sender;
	}

	/** @see \Base\Model\Request::getRecipient() */
	public function getRecipient() {
		return $this->recipient;
	}

	public function setRecipient(User $recipient) {
		$this->recipient = $recipient;
	}

	/** @see \Base\Model\Request::getIsUnread() */
	public function getIsUnread() {
		return ($this->isUnread ? true : false);
	}

	public function setIsUnread($isUnread) {
		$this->isUnread = ($isUnread ? 1 : 0);
	}

	/** @see \Base\Model\Request::getMessage() */
	public function getMessage() {
		return $this->message;
	}

	public function setMessage($message) {
		$this->message = $message;
	}

	/** @see \Base\Model\Request::getCreationDate() */
	public function getCreationDate() {
		return $this->creationDate;
	}

	public function setCreationDate($creationDate) {
		$this->creationDate = $creationDate;
	}
	
	/** @see \Base\Model\Request::getType() */
	public function getType() {
		return $this->type;
	}

	/*----- Methods -----*/

	public function getState() {
		return $this->getIsUnread() ? 'unread' : 'read';
	}

	/** @see \Application\StdLib\Object::equals() */
	public function equals(Object $o) {
		if(!$o instanceof $this) {
			throw new \ClassCastException();
		}
		if($o->getId() == $this->getId()) {
			return true;
		}
		if(($o->getSender()->equals($this->getSender()))
			&& ($o->getRecipient()->equals($this->getRecipient())) &&
			($o->getCreationDate() == $this->getCreationDate())) {
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
		return 'FriendRequest[id=' . $this->getId()
		. ',senderId=' . $this->getSender()->getId()
		. ',recipientId=' . $this->getRecipient()->getId()
		. ',isUnread' . ($this->getIsUnread() ? 'true' : 'false')
		. ',message=' . $this->getMessage()
		. ',creationDate=' . $this->getCreationDate()
		. ']';
	}
}
?>