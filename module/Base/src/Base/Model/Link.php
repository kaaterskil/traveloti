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
use Base\Model\Comment;
use Base\Model\Like;
use Base\Model\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * A link shared on a user's wall. The User and Page objects have a links connection.
 * @author Blair
 *
 * @ORM\Entity
 * @ORM\Table(name="links")
 */
class Link implements Post {

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="link_id")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * Bidirectional one-to-many: owning side
	 * @ORM\ManyToOne(targetEntity="Traveloti", inversedBy="links")
	 * @ORM\JoinColumn(name="from_id", referencedColumnName="traveloti_id")
	 */
	private $from;

	/** @ORM\Column(type="string") */
	private $link;

	/** @ORM\Column(type="string") */
	private $name;

	/** @ORM\Column(type="string") */
	private $description;

	/** @ORM\Column(type="string") */
	private $icon;

	/** @ORM\Column(type="string") */
	private $picture;

	/** @ORM\Column(type="string") */
	private $message;

	/** @ORM\Column(type="string") */
	private $visibility;

	/** @ORM\Column(type="string") */
	private $type = 'link';

	/** @ORM\Column(type="datetime", name="creation_date") */
	private $creationDate;

	/** @ORM\Column(type="datetime", name="last_update_date") */
	private $lastUpdateDate;
	
	/*----- Collections -----*/

	/**
	 * Bidirectional one-to-many: inverse side
	 * @ORM\OneToMany(targetEntity="Comment", mappedBy="link")
	 * @ORM\OrderBy({"creationDate" = "desc"})
	 */
	private $comments;

	/**
	 * Bidirectional one-to-many: inverse side
	 * @ORM\OneToMany(targetEntity="Like", mappedBy="link")
	 * @ORM\OrderBy({"creationDate" = "desc"})
	 */
	private $likes;
	
	/** @var array */
	private $observers = array();
	
	/*----- Constructor -----*/
	
	public function __construct() {
		$this->comments = new ArrayCollection();
		$this->likes = new ArrayCollection();
	}
	
	/*----- Getter/Setters -----*/
	
	/** @see \Base\Model\Post::getId() */
	public function getId() {
		return $this->id;
	}
	
	public function setId($id) {
		$this->id = (int) $id;
	}
	
	/** @see \Base\Model\Post::getFrom() */
	public function getFrom() {
		return $this->from;
	}
	
	public function setFrom(User $from) {
		$this->from = $from;
	}
	
	/** @return string */
	public function getLink() {
		return $this->link;
	}
	
	public function setLink($link) {
		$this->link = $link;
	}
	
	/** @return string */
	public function getName() {
		return $this->name;
	}
	
	public function setName($name) {
		$this->name = $name;
	}
	
	/** @return string */
	public function getDescription() {
		return $this->description;
	}
	
	public function setDescription($description) {
		$this->description = $description;
	}
	
	/** @return string */
	public function getIcon() {
		return $this->icon;
	}
	
	public function setIcon($icon) {
		$this->icon = $icon;
	}

	/** @return string */
	public function getPicture() {
		return $this->picture;
	}
	
	public function setPicture($picture) {
		$this->picture = $picture;
	}

	/** @see \Base\Model\Post::getMessage() */
	public function getMessage() {
		return $this->message;
	}
	
	public function setMessage($message) {
		$this->message = $message;
	}

	/** @see \Base\Model\Post::getVisibility() */
	public function getVisibility() {
		return $this->visibility;
	}
	
	public function setVisibility($visibility) {
		$this->visibility = $visibility;
	}
	
	/** @see \Base\Model\Post::getType() */
	public function getType() {
		return $this->type;
	}
	
	/** @see \Base\Model\Post::getCreationDate() */
	public function getCreationDate() {
		return $this->creationDate;
	}
	
	public function setCreationDate($creationDate) {
		$this->creationDate = $creationDate;
	}
	
	/** @see \Base\Model\Post::getLastUpdateDate() */
	public function getLastUpdateDate() {
		return $this->lastUpdateDate;
	}
	
	public function setLastUPdateDate($lastUpdateDate) {
		$this->lastUpdateDate = $lastUpdateDate;
	}
	
	/** @see \Base\Model\Post::getComments() */
	public function getComments() {
		return $this->comments;
	}
	
	public function setComments(ArrayCollection $comments) {
		$this->comments = $comments;
	}
	
	/** @see \Base\Model\Post::getLikes() */
	public function getLikes() {
		return $this->likes;
	}
	
	public function setLikes(ArrayCollection $likes) {
		$this->likes = $likes;
	}
	
	/** @return array */
	public function getObservers() {
		return $this->observers;
	}
	
	public function setObservers(array$observers = array()) {
		$this->observers = $observers;
	}
	
	/*----- Methods -----*/
	
	/** @see \Base\Model\Post::addComment() */
	public function addComment(Comment $comment) {
		$comment->setPost($this);
		$this->comments->add($comment);
	}
	
	/** @see \Base\Model\Post::addLike() */
	public function addLike(Like $like) {
		$like->setPost($this);
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
		return $this->likes->count();
	}
	
	/** @see \Application\StdLib\Observable::detach() */
	public function detach(Observer $observer) {
		/* @var $friend Observer */
		
		foreach ($this->observers as $key => $friend) {
			if($friend->equals($observer)) {
				unset($this->observers[$key]);
			}
		}
	}
	
	/** @see \Base\Model\Notifiable::getNotifierText() */
	public function getNotifierText(Traveloti $sender) {
		$title = $sender->getDisplayName() . ' posted a link on your timeline.';
		$htmlTitle = '<span class="blue-name">'
				. $user->getDisplayName()
				. '</span> posted a link on your timeline.';

		return new NotifierText($title, $htmlTitle, null, null);
	}
	
	/** @see \Application\StdLib\Observable::notify() */
	public function notify() {
		/* @var $friend Observer */
		
		foreach ($this->observers as $key => $friend) {
			$friend->update($this);
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
				&& ($that->getLink() == $this->getLink())
				&& ($that->getCreationDate() == $this->getCreationDate())) {
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
		return 'Link[id=' . $this->getId()
			. ',fromId=' . $this->getFrom()->getId()
			. ',link=' . $this->getLink()
			. ',name=' . $this->getName()
			. ',description=' . $this->getDescription()
			. ',icon=' . $this->getIcon()
			. ',picture=' . $this->getPicture()
			. ',message=' . $this->getMessage()
			. ',visibility=' . $this->getVisibility()
			. ',type=' . $this->getType()
			. ',creationDate=' . $this->getCreationDate()
			. ',lastUpdateDate=' . $this->getLastUpdateDate()
			. ']';
	}
}
?>