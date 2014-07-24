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

use Application\StdLib\Observable;
use Application\StdLib\Observer;
use Application\StdLib\Object;
use Base\Model\Notifiable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Like Class
 * @author Blair
 *
 * @ORM\Entity
 * @ORM\Table(name="likes")
 */
class Like implements Observable, Notifiable {

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="status_id")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * Bidirectional one-to-many: owning side
	 * @ORM\ManyToOne(targetEntity="Traveloti")
	 * @ORM\JoinColumn(name="from_id", referencedColumnName="traveloti_id")
	 */
	private $from;

	/**
	 * Bidirectional one-to-many: owning side
	 * @ORM\ManyToOne(targetEntity="Status")
	 * @ORM\JoinColumn(name="status_id", referencedColumnName="status_id")
	 */
	private $status;

	/**
	 * Bidirectional one-to-many: owning side
	 * @ORM\ManyToOne(targetEntity="Link")
	 * @ORM\JoinColumn(name="link_id", referencedColumnName="link_id")
	 */
	private $link;

	/**
	 * Bidirectional one-to-many: owning side
	 * @ORM\ManyToOne(targetEntity="Photo")
	 * @ORM\JoinColumn(name="photo_id", referencedColumnName="photo_id")
	 */
	private $photo;

	/**
	 * Bidirectional one-to-many: owning side
	 * @ORM\ManyToOne(targetEntity="Comment")
	 * @ORM\JoinColumn(name="comment_id", referencedColumnName="comment_id")
	 */
	private $comment;
	
	/** @ORM\Column(type="string") */
	private $type = 'like';

	/** @ORM\Column(type="datetime", name="creation_date", nullable=true) */
	private $creationDate;
	
	/** @var array */
	private $observers = array();
	
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

	/** @see \Base\Model\Notifiable::getFrom() */
	public function getFrom() {
		return $this->from;
	}

	public function setFrom(User $from) {
		$this->from = $from;
	}

	/** @return Status */
	public function getStatus() {
		return $this->status;
	}

	public function setStatus(Status $status) {
		$this->status = $status;
	}

	/** @return Comment */
	public function getComment() {
		return $this->comment;
	}

	public function setComment(Comment $comment) {
		$this->comment = $comment;
	}

	/** @return Link */
	public function getLink() {
		return $this->link;
	}

	public function setLink(Link $link) {
		$this->link = $link;
	}

	/** @return Photo */
	public function getPhoto() {
		return $this->photo;
	}

	public function setPhoto(Photo $photo) {
		$this->photo = $photo;
	}
	
	/** @see \Base\Model\Notifiable::getType() */
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
		if(!in_array($observer, $this->observers)) {
			array_push($this->observers, $observer);
		}
	}

	/** @see \Application\StdLib\Observable::detach() */
	public function detach(Observer $observer) {
		/* @var $obs Observer */
		foreach ($this->observers as $key => $obs) {
			if($obs->equals($observer)) {
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
		if(($that->getFrom()->equals($this->getFrom()))
				&& ($that->getCreationDate() == $this->getCreationDate())) {
			return true;
		}
		return false;
	}

	/** @see \Application\StdLib\Object::getClass() */
	public function getClass() {
		return get_class($this);
	}
	
	/** @see \Base\Model\Notifiable::getNotifierText() */
	public function getNotifierText(Traveloti $sender) {
		$postText = '';
		if($this->getStatus() != null) {
			$postText = 'wall post "' . $this->getStatus()->getMessage() . '"';
			
		} else if($this->getLink() != null) {
			$postText = 'link: "' . $this->getLink()->getLink() . '"';
			
		} elseif($this->getPhoto() != null) {
			$caption = $this->getPhoto()->getName()
				? $this->getPhoto()->getName()
				: $this->getPhoto()->getSource();
			$postText = 'photo "' . $caption . '"';
			
		} elseif($this->getComment() != null) {
			$postText = 'comment "' . $this->getComment()->getMessage() . '"';
			
		} else {
			$postText = '<Unknown post>';
		}
		
		$title = $sender->getDisplayName() . 'likes your ' . $postText . '.';
		$htmlTitle = '<span class="blue-name">'
			. $sender->getDisplayName()
			. '</span> likes your ' . $postText . '.';

		return new NotifierText($title, $htmlTitle, null, null);
	}

	/** @see \Application\StdLib\Observable::notify() */
	public function notify() {
		/* @var $obs Observer */
		foreach ($this->observers as $key => $obs) {
			$obs->update($this);
		}
	}

	/** @see \Application\StdLib\Object::__toString() */
	public function __toString() {
		return 'Like[id=' . $this->getId()
		. ',fromId=' . $this->getFrom()->getId()
		. ',statusId=' . (null != $this->getStatus() ? $this->getStatus()->getId() : null)
		. ',linkId=' . (null != $this->getLink() ? $this->getLink()->getId() : null)
		. ',photoId=' . (null != $this->getPhoto() ? $this->getPhoto()->getId() : null)
		. ',commentId=' . (null != $this->getComment() ? $this->getComment()->getId() : null)
		. ',creationDate=' . $this->getCreationDate()
		. ']';
	}
}
?>