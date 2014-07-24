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
use Application\StdLib\Observable;
use Application\StdLib\Observer;
use Base\Model\Notifiable;
use Base\Model\Post;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * A comment on a post (status, link, photo or video).
 * @author Blair
 * @see http://developers.facebook.com/docs/reference/api/Comment/
 *
 * @ORM\Entity
 * @ORM\Table(name = "comments")
 */
class Comment implements Observable, Notifiable {
	
	/*----- Properties -----*/
	
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="comment_id")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;
	
	/**
	 * Bidirectional one-to-many: owning side
	 * @ORM\ManyToOne(targetEntity="Status")
	 * @ORM\JoinColumn(name="status_id", referencedColumnName="status_id")
	 */
	private $status;
	
	/**
	 * Bidirectional one-to-many: owning side
	 * @ORM\ManyToOne(targetEntity="Photo")
	 * @ORM\JoinColumn(name="photo_id", referencedColumnName="photo_id")
	 */
	private $photo;
	
	/**
	 * Bidirectional one-to-many: owning side
	 * @ORM\ManyToOne(targetEntity="Link")
	 * @ORM\JoinColumn(name="link_id", referencedColumnName="link_id")
	 */
	private $link;
	
	/**
	 * Bidirectional one-to-many: owning side
	 * @ORM\ManyToOne(targetEntity="Comment")
	 * @ORM\JoinColumn(name="parent_id", referencedColumnName="comment_id")
	 */
	private $parent;
	
	/**
	 * Bidirectional one-to-many: owning side
	 * @ORM\ManyToOne(targetEntity="Traveloti")
	 * @ORM\JoinColumn(name="from_id", referencedColumnName="traveloti_id")
	 */
	private $from;
	
	/** @ORM\Column(type="string") */
	private $message;
	
	/** @ORM\Column(type="integer", name="user_likes") */
	private $userLikes;

	/** @ORM\Column(type="integer", name="can_like") */
	private $canLike;

	/** @ORM\Column(type="integer", name="can_remove") */
	private $canRemove;

	/** @ORM\Column(type="integer", name="is_private") */
	private $isPrivate;

	/** @ORM\Column(type="string", name="type") */
	private $type = 'comment';
	
	/** @ORM\Column(type="datetime", name="creation_date", nullable=true) */
	private $creationDate;
	
	/*----- Collections -----*/
	
	/** @ORM\OneToMany(targetEntity="Comment", mappedBy="parent") */
	private $comments;
	
	/** @ORM\OneToMany(targetEntity="Like", mappedBy="comment") */
	private $likes;
	
	/** @var array */
	private $observers = array ();
	
	/*----- Constructor -----*/
	
	public function __construct() {
		$this->comments = new ArrayCollection();
		$this->likes = new ArrayCollection();
	}
	
	/*----- Getter/Setters -----*/
	
	/** @return int */
	public function getId() {
		return $this->id;
	}
	
	public function setId($id) {
		$this->id = (int) $id;
	}
	
	/** @return Status */
	public function getStatus() {
		return $this->status;
	}
	
	public function setStatus(Status $status) {
		$this->status = $status;
	}
	
	/** @return Photo */
	public function getPhoto() {
		return $this->photo;
	}
	
	public function setPhoto(Photo $photo) {
		$this->photo = $photo;
	}
	
	/** @return Link */
	public function getLink() {
		return $this->link;
	}
	
	public function setLink(Link $link) {
		$this->link = $link;
	}
	
	/** @return Comment */
	public function getParent() {
		return $this->parent;
	}
	
	public function setParent(Comment $comment) {
		$this->parent = $comment;
	}
	
	/** @return User */
	public function getFrom() {
		return $this->from;
	}
	
	public function setFrom(User $from) {
		$this->from = $from;
	}
	
	/** @return string */
	public function getMessage() {
		return $this->message;
	}
	
	public function setMessage($message) {
		$this->message = $message;
	}
	
	/** @return boolean */
	public function getUserLikes() {
		return ($this->userLikes != 0 ? true : false);
	}
	
	public function setUserLikes($userLikes) {
		$this->userLikes = ($userLikes ? 1 : 0);
	}
	
	/** @return boolean */
	public function getCanLike() {
		return ($this->canLike != 0 ? true : false);
	}
	
	public function setCanLike($canLike) {
		$this->canLike = ($canLike ? 1 : 0);
	}
	
	/** @return boolean */
	public function getCanRemove() {
		return ($this->canRemove != 0 ? true : false);
	}
	
	public function setCanRemove($canRemove) {
		$this->canRemove = ($canRemove ? 1 : 0);
	}
	
	/** @return boolean */
	public function getIsPrivate() {
		return ($this->isPrivate != 0 ? true : false);
	}
	
	public function setIsPrivate($isPrivate) {
		$this->isPrivate = ($isPrivate ? 1 : 0);
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
	
	/** @return ArrayCollection */
	public function getComments() {
		return $this->comments;
	}
	
	public function setComments(ArrayCollection $comments) {
		$this->comments = $comments;
	}

	/** @return ArrayCollection */
	public function getLikes() {
		return $this->likes;
	}
	
	public function setLikes(ArrayCollection $likes) {
		$this->likes = $likes;
	}
	
	/*----- Methods -----*/
	
	public function addComment(Comment $comment) {
		$comment->setParent($this);
		$this->comments->add($comment);
	}
	
	public function addLike(Like $like) {
		$like->setComment($this);
		$this->likes->add($like);
	}
	
	/** @see \Application\StdLib\Observable::attach() */
	public function attach(Observer $observer) {
		if(!in_array($observer, $this->observers)) {
			array_push($this->observers, $observer);
		}
	}
	
	/** @return int */
	public function countLikes() {
		return $this->getLikes()->count();
	}
	
	/** @see \Application\StdLib\Observable::detach() */
	public function detach(Observer $observer) {
		/* @var $o Observer */
		foreach ($this->observers as $key => $o) {
			if($o->equals($observer)) {
				unset($this->observers[$key]);
				break;
			}
		}
	}
	
	/** @see \Application\StdLib\Object::equals() */
	public function equals(Object $o) {
		if(!$o instanceof $this) {
			throw new \ClassCastException();
		}
		if($o->getId() == $this->getId()) {
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
		$postType = '<unknown post>';
		if($this->getStatus() != null) {
			$postType = 'status';
		} else if($this->getLink() != null) {
			$postType = 'link';
		} elseif($this->getPhoto() != null) {
			$postType = 'photo';
		}elseif($this->getParent() != null) {
			$postType = 'comment';
		}

		$title = $sender->getDisplayName() . ' commented on your ' . $postType . '.';
		$htmlTitle = '<span class="blue-name">'
				. $user->getDisplayName()
				. '</span> commented on your ' . $postType . '.';

		return new NotifierText($title, $htmlTitle, null, null);
		
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
		return 'Comment[id=' . $this->getId()
			. ',fromId=' . $this->getFrom()->getId()
			. ',statusId=' . (null != $this->getStatus() ? $this->getStatus()->getId() : null)
			. ',photoId=' . (null != $this->getPhoto() ? $this->getPhoto()->getId() : null)
			. ',linkId=' . (null != $this->getLin ? $this->getStatus()->getId() : null)
			. ',message=' . $this->getMessage()
			. ',state=' . $this->getState()
			. ',creationDate=' . $this->getCreationDate()
			. ']';
	}
}
?>