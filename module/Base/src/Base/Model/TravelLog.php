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
use Base\Model\TravelLogAdmin;
use Base\Model\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Travel Log Class
 * @author Blair
 *
 * @ORM\Entity
 * @ORM\Table(name="travel_log")
 */
class TravelLog extends Traveloti {
	
	/*----- Properties -----*/
	
	/** @ORM\Column(type="string") */
	private $name;
	
	/** @ORM\Column(type="string") */
	private $about;
	
	/** @ORM\Column(type="string") */
	private $description;
	
	/** @ORM\Column(type="string", name="general_info") */
	private $generalInfo;
	
	/** @ORM\Column(type="string") */
	private $link;
	
	/** @ORM\Column(type="string") */
	private $category;
	
	/** @ORM\Column(type="integer", name="is_published") */
	private $isPublished;
	
	/** @ORM\Column(type="integer", name="can_post") */
	private $canPost;
	
	/** @ORM\Column(type="integer", name="num_likes") */
	private $numLikes;
	
	/** @ORM\Column(type="string") */
	private $location;
	
	/** @ORM\Column(type="string") */
	private $phone;
	
	/** @ORM\Column(type="integer", name="num_checkins") */
	private $numCheckins;
	
	/** @ORM\Column(type="string") */
	private $website;
	
	private $affiliation;
	private $currentLocation;
	private $fanCount;
	private $keywords;
	private $members;
	private $pageUrl;
	private $parent;
	private $unreadMessageCount;
	private $unreadNotificationCount;
	private $unseenMessageCount;
	private $unseenNotificationCount;
	private $visits;
	
	/*----- Collections -----*/
	
	/**
	 * Bidirectional one-to-many: inverse side
	 * @ORM\OneToMany(targetEntity="TravelLogAdmin", mappedBy="travelLog")
	 */
	private $administrators;
	
	/**
	 * Constructor
	 */
	public function __construct() {
		// Superclass properties
		$this->albums = new ArrayCollection();
		$this->interests = new ArrayCollection();
		$this->links = new ArrayCollection();
		$this->notifications = new ArrayCollection();
		$this->statuses = new ArrayCollection();
		
		$this->type = 'travel_log';
		$this->administrators = new ArrayCollection();
	}
	
	/*----- Getter/Setters -----*/
	
	/** @return string */
	public function getName() {
		return $this->name;
	}
	
	public function setName($name) {
		$this->name = $name;
	}
	
	/** @see \Base\Model\Traveloti::getUsername() */
	public function getUsername() {
		if($this->username == null) {
			$this->username = strtolower(str_replace(' ', '', $this->getName()));
		}
		return $this->username;
	}
	
	/** @see \Base\Model\Traveloti::setUsername() */
	public function setUsername($username) {
		$this->username = $username;
	}
	
	/** @return string */
	public function getAbout() {
		return $this->about;
	}
	
	public function setAbout($about) {
		$this->about = $about;
	}
	
	/** @return string */
	public function getDescription() {
		return $this->description;
	}
	
	public function setDescription($description) {
		$this->description = $description;
	}
	
	/** @return string */
	public function getGeneralInfo() {
		return $this->generalInfo;
	}
	
	public function setGeneralInfo($generalInfo) {
		$this->generalInfo = $generalInfo;
	}
	
	/** @return string */
	public function getLink() {
		return $this->link;
	}
	
	public function setLink($link) {
		$this->link = $link;
	}
	
	/** @return string */
	public function getCategory() {
		return $this->category;
	}
	
	public function setCategory($category) {
		$this->category = $category;
	}
	
	public function getIsPublished() {
		return ($this->isPublished ? true : false);
	}
	
	public function setIsPublished($isPublished) {
		$this->isPublished = ($isPublished ? 1 : 0);
	}
	
	public function getCanPost() {
		return ($this->canPost ? true : false);
	}
	
	public function setCanPost($canPost) {
		$this->canPost = ($canPost ? 1 : 0);
	}
	
	/** @return int */
	public function getNumLikes() {
		return $this->numLikes;
	}
	
	public function setNumLikes($numLikes) {
		$this->numLikes = (int) $numLikes;
	}
	
	/** @return string */
	public function getLocation() {
		return $this->location;
	}
	
	public function setLocation($location) {
		$this->location = $location;
	}
	
	public function getPhone() {
		return $this->phone;
	}
	
	public function setPhone($phone) {
		$this->phone = $phone;
	}
	
	/** @return int */
	public function geteNumCheckins() {
		return $this->numCheckins;
	}
	
	public function setNumCheckins($numCheckins) {
		$this->numCheckins = (int) $numCheckins;
	}
	
	/** @return string */
	public function getWebsite() {
		return $this->website;
	}
	
	public function setWebsite($website) {
		$this->website = $website;
	}
	
	/** @return ArrayCollection */
	public function getAdministrators() {
		return $this->administrators;
	}
	
	public function setAdministrators(ArrayCollection $administrators) {
		$this->administrators = $administrators;
	}
	
	/*----- Methods -----*/
	
	/** @see \Base\Model\Traveloti::getDisplayName() */
	public function getDisplayName() {
		return $this->getName();
	}
	
	public function addAdministrator(TravelLogAdmin $travelLogAdmin) {
		$travelLogAdmin->setTravelLog($this);
		$this->administrators->add($travelLogAdmin);
	}
	
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
		return false;
	}
	
	/**
	 * @return string
	 * @see \Application\StdLib\Object::getClass()
	 */
	public function getClass() {
		return get_class($this);
	}
	
	/** @return array */
	public function getUsers() {
		/* @var $user User */
		/* @var $tla TravelLogAdmin */
		
		$result = array();
		$administrators = $this->getAdministrators()->getValues();
		foreach ($administrators as $tla) {
			$result[] = $tla->getUser();
		}
		return $result;
	}
	
	/**
	 * @return string
	 * @see \Application\StdLib\Object::__toString()
	 */
	public function __toString() {
		return 'TravelLog[id=' . $this->getId()
			. ',name=' . $this->getName()
			. ',about=' . $this->getAbout()
			. ',description=' . $this->getDescription()
			. ',generalInfo=' . $this->getGeneralInfo()
			. ',link=' . $this->getLink()
			. ',category=' . $this->getCategory()
			. ',isPublished=' . ($this->getIsPublished() ? 'true' : 'false')
			. ',numLikes=' . $this->getNumLikes()
			. ',location=' . $this->getLocation()
			. ',phone=' . $this->getPhone()
			. ',numCheckins=' . $this->geteNumCheckins()
			. ',pictureId=' . ($this->getPicture() ? $this->getPicture()->getId() : null)
			. ',coverPictureId=' . ($this->getCoverPicture() ? $this->getCoverPicture()->getId() : null)
			. ',website=' . $this->getWebsite()
			. ']';
	}
}
?>