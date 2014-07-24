<?php
/**
 * Traveloti Library
 *
 * @category	Traveloti
 * @package		Traveloti_
 * @author		Blair
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Base\Model;

use Application\StdLib\Observable;
use Application\StdLib\Observer;
use Application\StdLib\Object;
use Base\Model\Photo;
use Base\Model\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Tag Class
 * @author Blair
 *
 * @ORM\Entity
 * @ORM\Table(name="tags")
 */
class Tag implements Observable {

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="tag_id")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * Bidirectional one-to-many: owning side
	 * @ORM\ManyToOne(targetEntity="Photo")
	 * @ORM\JoinColumn(name="photo_id", referencedColumnName="photo_id")
	 */
	private $photo;

	/**
	 * Bidirectional one-to-many: owning side
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(name="subject_id", referencedColumnName="traveloti_id")
	 */
	private $subject;

	/** @ORM\Column(type="string") */
	private $message;

	/** @ORM\Column(type="integer", name="x_coord") */
	private $xCoord;

	/** @ORM\Column(type="integer", name="y_coord") */
	private $yCoord;

	/** @var string */
	private $type = 'tag';

	/** @ORm\Column(type="datetime", name="creation_date") */
	private $creationDate;

	/** @var array */
	private $observers = array();

	/*----- Constructor -----*/

	public function __construct(){
	}

	/*----- Getter/Setters -----*/

	/** @return int */
	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = (int) $id;
	}

	/** @return Photo */
	public function getPhoto() {
		return $this->photo;
	}

	public function setPhoto(Photo $photo) {
		$this->photo = $photo;
	}

	/** @return User */
	public function getSubject() {
		return $this->subject;
	}

	public function setSubject(User $subject) {
		$this->subject = $subject;
	}

	/** @return string */
	public function getMessage() {
		return $this->message;
	}

	public function setMessage($message) {
		$this->message = $message;
	}

	/** @return int */
	public function getXCoord() {
		return $this->xCoord;
	}

	public function setXCoord($xCoord) {
		$this->xCoord = $xCoord;
	}

	/** @return int */
	public function getYCoord() {
		return $this->yCoord;
	}

	public function setYCoord($yCoord) {
		$this->yCoord = $yCoord;
	}

	/** @return string */
	public function getType() {
		return $this->type;
	}

	/** @return \DateTime */
	public function getCreationDate() {
		return $this->creationDate;
	}

	public function setCreationDate($creationDate) {
		$this->creationDate = $creationDate;
	}

	/** @return array */
	public function getObservers() {
		return $this->observers;
	}

	public function setObservers(array $observers = array()) {
		$this->observers = $observers;
	}

	/*----- Methods -----*/

	/** @see \Application\StdLib\Observable::attach() */
	public function attach(Observer $observer) {
		if(!in_array($this->observers, $observer)) {
			array_push($this->observers, $observer);
		}
	}

	/** @see \Application\StdLib\Observable::detach() */
	public function detach(Observer $observer) {
		/* @var $o Observer */
		foreach ($this->observers as $key => $o) {
			if($o->equals($observer)) {
				unset($this->observers[$key]);
			}
		}
	}

	/** @see \Application\StdLib\Object::equals() */
	public function equals(Object $o) {
		if(!$o instanceof $this) {
			throw new \ClassCastException();
		}
		$that = $o;
		if($that->getId() == $this->getId()) {
			return true;
		}
		if(($that->getPhoto()->equals($this->getPhoto()))
			&& ($that->getSubject()->equals($this->getSubject))
			&& ($that->getCreationDate() == $this->getCreationDate())) {
			return true;
		}
		return false;
	}

	/** @see \Application\StdLib\Object::getClass() */
	public function getClass() {
		return get_class($this);
	}

	/** @see \Application\StdLib\Observable::notify() */
	public function notify() {
		/* @var $o Observer */
		foreach ($this->observers as $key => $o) {
			$o->update($this);
		}
	}

	/** @see \Application\StdLib\Object::__toString() */
	public function __toString() {
		return 'Tag[id=' . $this->getId()
		. ',photoId=' . $this->getPhoto()->getId()
		. ',subjectId=' . $this->getSubject()->getId()
		. ',message=' . $this->getMessage()
		. ',xCoord=' . $this->getXCoord()
		. ',yCoord=' . $this->getYCoord()
		. ',type=' . $this->getType()
		. ',creationDate=' . $this->getCreationDate()
		. ']';
	}
}
?>