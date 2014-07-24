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
use Base\Model\Request;
use Doctrine\ORM\Mapping as ORM;

/**
 * Notification Class
 * @author Blair
 *
 * @ORM\Entity
 * @ORM\Table(name="notifications")
 */
class Notification implements Request {

	/*----- Properties -----*/

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="notification_id")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * Unidirectional one-to-many: owning side
	 * @ORM\ManyToOne(targetEntity="Traveloti")
	 * @ORM\JoinColumn(name="sender_id", referencedColumnName="traveloti_id")
	 */
	private $sender;

	/**
	 * Unidirectional one-to-many: owning side
	 * @ORM\ManyToOne(targetEntity="Traveloti", inversedBy="notifications")
	 * @ORM\JoinColumn(name="recipient_id", referencedColumnName="traveloti_id")
	 */
	private $recipient;
	
	/** @ORM\Column(type="string", name="object_type") */
	private $type = 'notification';

	/** @ORM\Column(type="string", name="title_html") */
	private $htmlTitle;
	
	/** @ORM\Column(type="string", name="title_text") */
	private $title;
	
	/** @ORM\Column(type="string", name="body_text") */
	private $body;

	/** @ORM\Column(type="string", name="body_html") */
	private $htmlBody;
	
	/** @ORM\Column(type="integer", name="is_unread") */
	private $isUnread;
	
	/** @ORM\Column(type="integer", name="is_hidden") */
	private $isHidden;
	
	/** @ORM\Column(type="datetime", name="creation_date") */
	private $creationDate;
	
	/** @ORM\Column(type="datetime", name="last_update_date") */
	private $lastUpdateDate;

	/*----- Constructor -----*/

	public function __construct() {
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
	
	/** @see \Base\Model\Request::getType() */
	public function getType() {
		return $this->type;
	}
	
	public function setType($type) {
		$this->type = $type;
	}

	public function getTitle() {
		return $this->title;
	}

	public function setTitle($title) {
		$this->title = $title;
	}
	
	public function getHtmlTitle() {
		return $this->htmlTitle;
	}
	
	public function setHtmlTitle($htmlTitle) {
		$this->htmlTitle = $htmlTitle;
	}

	public function getBody() {
		return $this->body;
	}

	public function setBody($body) {
		$this->body = $body;
	}
	
	public function getHtmlBody() {
		return $this->htmlBody;
	}
	
	public function setHtmlBody($htmlBody) {
		$this->htmlBody = $htmlBody;
	}

	/** @see \Base\Model\Request::getIsUnread() */
	public function getIsUnread() {
		return ($this->isUnread ? true : false);
	}

	public function setIsUnread($isUnread) {
		$this->isUnread = ($isUnread ? 1 : 0);
	}

	public function getIsHidden() {
		return ($this->isHidden ? true : false);
	}

	public function setIsHidden($isHidden) {
		$this->isHidden = ($isHidden ? 1 : 0);
	}
	
	/** @see \Base\Model\Request::getMessage() */
	public function getMessage() {
		return $this->getTitle();
	}
	
	/** @see \Base\Model\Request::getCreationDate() */
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

	/*----- Methods ----- */
	
	/** @see \Application\StdLib\Object::equals() */
	public function equals(Object $o) {
		if(!$o instanceof  $this) {
			throw new \ClassCastException();
		}
		$that = $o;
		if($that->getId() == $this->getId()) {
			return true;
		}
		if(($that->getSender()->equals($this->getSender()))
			&& ($that->getRecipient()->equals($this->getRecipient()))
			&& ($that->getTitle() == $this->getTitle())
			&& $that->getCreationDate() == $this->getCreationDate()) {
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
		return 'Notification[id=' . $this->getId()
		. ',senderId=' . $this->getSender()->getId()
		. ',recipientId=' . $this->getRecipient()->getId()
		. ',type=' . $this->getType()
		. ',title=' . $this->getTitle()
		. ',body=' . $this->getBody()
		. ',isUnread=' . ($this->getIsUnread() ? 'true' : 'false')
		. ',isHidden=' . ($this->getIsHidden() ? 'true' : 'false')
		. ',creationDate=' . $this->getCreationDate()
		. ',lastUpdateDate=' . $this->getLastUpdateDate()
		. ']';
	}
}
?>