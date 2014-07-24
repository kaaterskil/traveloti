<?php
/**
 * Traveloti Library
 *
 * @package		Traveloti_Base
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Base\Model;

use Base\Model\Post;
use Application\StdLib\Constants;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * An Offer represents an offer that is published by a page. Only Page objects have
 * offers connections.
 * @author Blair
 *
 * @ORM\Entity
 * @ORM\Table(name="offers")
 */
class Offer implements Post {
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="offer_id")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * Bidirectional one-to-many: owning side
	 * @ORM\ManyToOne(targetEntity="TravelLog", inversedBy="offers")
	 * @ORM\JoinColumn(name="from_id", referencedColumnName="traveloti_id")
	 */
	private $from;
	
	/** @ORM\COlumn(type="string") */
	private $title;
	
	/** @ORM\Column(type="string") */
	private $message;
	
	/** @ORM\COlumn(type="datetime", name="expiration_date") */
	private $expirationDate;
	
	/** @ORM\Column(type="string") */
	private $terms;

	/**
	 * @ORM\OneToOne(targetEntity="Photo")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="photo_id")
	 */
	private $image;
	
	/** @ORM\COlumn(type="string", name="image_url") */
	private $imageUrl;
	
	/** @ORM\Column(type="integer", name="claim_limit") */
	private $claimLimit;
	
	/** @ORM\Column(type="string", name="coupon_type") */
	private $couponType;
	
	/** @ORM\Column(type="string", name="redemption_link") */
	private $redemptionLink;
	
	/** @ORM\Column(type="string", name="redemption_code") */
	private $redemptionCode;
	
	/** @ORM\Column(type="string") */
	private $visbility = Constants::PRIVACY_EVERYONE;
	
	/** @ORM\Column(type="string") */
	private $type = 'offer';
	
	/** @ORM\Column(type="datetime", name="creation_date") */
	private $creationDate;
	
	/** @ORM\Column(type="datetime", name="last_update_date") */
	private $lastUpdateDate;

	private $likes;
	
	private $observers = array();

	/**
	 * Constructor
	*/
	public function __construct() {
		$this->likes = new ArrayCollection();
	}

	/*----- Getter/Setters -----*/

	/**
	 * @return number
	 * @see \Base\Model\Post::getId()
	 */
	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = (int) $id;
	}

	/**
	 * @return User
	 * @see \Base\Model\Notifiable::getFrom()
	 */
	public function getFrom() {
		return $this->from;
	}

	public function setFrom(User $from) {
		$this->from = $from;
	}

	/** @return string */
	public function getTitle() {
		return $this->title;
	}

	public function settitle($title) {
		$this->title = $title;
	}

	/**
	 * @return string
	 * @see \Base\Model\Post::getMessage()
	 */
	public function getMessage() {
		return $this->message;
	}

	public function setMessage($message) {
		$this->message = $message;
	}

	/** @return DateTime */
	public function getExpirationDate() {
		return $this->expirationDate;
	}

	public function setExpirationDate($expirationDate) {
		$this->expirationDate = $expirationDate;
	}

	/** @return string */
	public function getTerms() {
		return $this->terms;
	}

	public function setTerms($terms) {
		$this->terms = $terms;
	}

	/** @return Photo */
	public function getImage() {
		return $this->image;
	}

	public function setImage(Photo $image) {
		$this->image = $image;
	}

	/** @return string */
	public function getImageUrl() {
		return $this->imageUrl;
	}

	public function setImageUrl($imageUrl) {
		$this->imageUrl = $imageUrl;
	}

	/** @return int */
	public function getClaimLimit() {
		return $this->claimLimit;
	}

	public function setClaimLimit($claimLimit = 1) {
		$this->claimLimit = (int) $claimLimit;
	}

	/** @return string */
	public function getCouponType() {
		return $this->couponType;
	}

	public function setCouponType($couponType) {
		$this->couponType = $couponType;
	}

	public function getRedemptionLink() {
		return $this->redemptionLink;
	}

	public function setRedemptionLink($redemptionLink) {
		$this->redemptionLink = $redemptionLink;
	}

	/** @return string */
	public function getRedemptionCode() {
		return $this->redemptionCode;
	}

	public function setRedemptioNCode($redemptionCode) {
		$this->redemptionCode = $redemptionCode;
	}

	/**
	 * @return string
	 * @see \Base\Model\Post::getVisibility()
	 */
	public function getVisibility() {
		return $this->visbility;
	}

	public function setVisibility($visibility) {
		$this->visbility = $visibility;
	}

	/**
	 * @return string
	 * @see \Base\Model\Notifiable::getType()
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @return DateTime
	 * @see \Base\Model\Post::getCreationDate()
	 */
	public function getCreationDate() {
		return $this->creationDate;
	}

	public function setCreationDate($creationDate) {
		$this->creationDate = $creationDate;
	}

	/**
	 * @return DateTime
	 * @see \Base\Model\Post::getLastUpdateDate()
	 */
	public function getLastUpdateDate() {
		return $this->lastUpdateDate;
	}

	public function setLastUpdateDate($lastUpdateDate) {
		$this->lastUpdateDate = $lastUpdateDate;
	}

	/**
	 * @return NULL
	 * @see \Base\Model\Post::getComments()
	 */
	public function getComments() {
		return null;
	}

	/**
	 * @return ArrayCollection
	 * @see \Base\Model\Post::getLikes()
	 */
	public function getLikes() {
		return $this->likes;
	}

	public function setLikes(ArrayCollection $likes) {
		$this->likes = $likes;
	}

	/*----- Methods ----- */

	/**
	 * @param Comment $comment
	 * @throws RuntimeException
	 * @see \Base\Model\Post::addComment()
	 */
	public function addComment(Comment $comment) {
		throw new RuntimeException("Method not implemented.");
	}

	/**
	 * @param Like $like
	 * @throws RuntimeException
	 * @see \Base\Model\Post::addLike()
	 */
	public function addLike(Like $like) {
		throw new RuntimeException("Method not implemented.");
	}

	/**
	 * @param Observer $observer
	 * @return void
	 * @see \Application\StdLib\Observable::attach()
	 */
	public function attach(Observer $observer) {
		if(!in_array($observer, $this->observers)) {
			array_push($this->observers, $observer);
		}
	}

	/**
	 * @param Observer $observer
	 * @return void
	 * @see \Application\StdLib\Observable::detach()
	 */
	public function detach(Observer $observer) {
		/* @var $friend Observer */

		foreach ($this->observers as $key => $friend) {
			if($friend->equals($observer)) {
				unset($this->observers[$key]);
				break;
			}
		}
	}

	/**
	 * @param Object $o
	 * @return boolean
	 * @see \Application\StdLib\Object::equals()
	 */
	public function equals(Object $o) {
		if(!$o instanceof Offer) {
			return false;
		}
		$that = $o;
		if($that->getId() == $this->getId()) {
			return true;
		}
		if(($that->title == $this->title) && ($that->terms == $this->terms)
				&& ($that->expirationDate == $this->expirationDate)) {
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
	 * @param User $sender
	 * @return string
	 * @see \Base\Model\Notifiable::getNotifierText()
	 */
	public function getNotifierText(User $sender) {
		return $this->message;
	}

	/**
	 * @return void
	 * @see \Application\StdLib\Observable::notify()
	 */
	public function notify() {
		/* @var $friend Observer */

		foreach ($this->observers as $key => $friend) {
			$friend->update($this);
		}
	}
	
	/**
	 * @return string
	 * @see \Application\StdLib\Object::__toString()
	 */
	public function __toString() {
		return 'Offer[id=' . $this->getId()
			. ',fromId=' . ($this->getFrom() ? $this->getFrom()->getId() : null)
			. ',title=' . $this->getTitle()
			. ',message=' . $this->getMessage()
			. ',expirationDate=' . $this->getExpirationDate()
			. ',terms=' . $this->getTerms()
			. ',imageId=' . ($this->getImage() ? $this->getImage()->getId() : null)
			. ',imageUrl=' . $this->getImageUrl()
			. ',claimLimit=' . $this->getClaimLimit()
			. ',couponType=' . $this->getCouponType()
			. ',redemptionLink=' . $this->redemptionLink()
			. ',redemptionCode=' . $this->getRedemptionCode()
			. ',visibility=' . $this->getVisibility()
			. ',type=' . $this->getType()
			. ',creationDate=' . $this->getCreationDate()
			. ',lastUpdateDate=' . $this->getLastUpdateDate()
			. ']';
	}
}
?>