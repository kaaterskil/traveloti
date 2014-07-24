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
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * A superclass that defines both Users and TravelLogs so that Posts can be
 * written to and accessed by both.
 * @author Blair
 *
 * @ORM\Entity
 * @ORM\Table(name="traveloti")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(type="string", name="type")
 * @ORM\DiscriminatorMap({"user" = "User", "travel_log" = "TravelLog"})
 */
abstract class Traveloti implements Object {
	
	const USER_TYPE = "Traveler";
	const AGENT_TYPE = "Agent";
	const BLOGGER_TYPE = "Blogger";
	const LOG_TYPE = "travel_log";

	/*----- Properties -----*/

	protected $type;
	
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="traveloti_id")
	 */
	private $id;

	/** @ORM\Column(type="string") */
	protected $username;

	/**
	 * The user's profile picture
	 * @ORM\OneToOne(targetEntity="Photo")
	 * @ORM\JoinColumn(name="picture_id", referencedColumnName="photo_id")
	 */
	private $picture;

	/**
	 * The user's cover page picture
	 * @ORM\OneToOne(targetEntity="Photo")
	 * @ORM\JoinColumn(name="cover_picture_id", referencedColumnName="photo_id")
	 */
	private $coverPicture;

	/** @ORM\Column(type="datetime", name="creation_date") */
	private $creationDate;

	/** @ORM\Column(type="datetime", name="last_update_date") */
	private $lastUpdateDate;

	/*----- Collections -----*/

	/**
	 * The photo albums this user has created
	 * Bidirectional one-to-many: inverse side
	 * @ORM\OneToMany(targetEntity="Album", mappedBy="user")
	 */
	private $albums;

	/**
	 * The user's feed
	 * Bidirectional one-to-many: inverse side
	 * @ORM\OneToMany(targetEntity="Status", mappedBy="to")
	 * @ORM\OrderBy({"creationDate"="desc"})
	 */
	private $feed;

	/**
	 * The interests listed in the user's profile
	 * Bidirectional one-to-many: inverse side
	 * @ORM\OneToMany(targetEntity="Interest", mappedBy="user")
	 */
	private $interests;

	/**
	 * The user's posted links
	 * Bidirectional one-to-many: inverse side
	 * @ORM\OneToMany(targetEntity="Link", mappedBy="from")
	 * @ORM\OrderBy({"creationDate"="desc"})
	 */
	private $links;

	/**
	 * Notifications for the user
	 * Bidirectional one-to-many: inverse side
	 * @ORM\OneToMany(targetEntity="Notification", mappedBy="recipient")
	 */
	private $notifications;

	/**
	 * Photos for the user
	 * Bidirectional one-to-many: inverse side
	 * @ORM\OneToMany(targetEntity="Photo", mappedBy="from")
	 */
	private $photos;

	/**
	 * The user's status updates
	 * Bidirectional one-to-many: inverse side
	 * @ORM\OneToMany(targetEntity="Status", mappedBy="from")
	 * @ORM\OrderBy({"creationDate"="desc"})
	 */
	private $statuses;

	/*----- Getter/Setters -----*/

	/** @return int */
	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = (int) $id;
	}

	/** @return string */
	abstract public function getUsername();

	abstract public function setUsername($username);

	/** @return Photo */
	public function getPicture() {
		return $this->picture;
	}

	public function setPicture(Photo $picture) {
		$this->picture = $picture;
	}

	/** @return Photo */
	public function getCoverPicture() {
		return $this->coverPicture;
	}

	public function setCoverPicture(Photo $coverPicture) {
		$this->coverPicture = $coverPicture;
	}

	public function getType() {
		return $this->type;
	}

	public function setType($type) {
		$this->type = $type;
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

	/** @return ArrayCollection */
	public function getAlbums() {
		if($this->albums == null) {
			$this->albums = new ArrayCollection();
		}
		return $this->albums;
	}

	public function setAlbums(ArrayCollection $albums) {
		$this->albums = $albums;
	}
	
	/** @return ArrayCollection */
	public function getFeed() {
		if($this->feed == null) {
			$this->feed = new ArrayCollection();
		}
		return $this->feed;
	}
	
	public function setFeed(ArrayCollection $feed) {
		$this->feed = $feed;
	}

	/** @return ArrayCollection */
	public function getInterests() {
		if($this->interests == null) {
			$this->interests = new ArrayCollection();
		}
		return $this->interests;
	}

	public function setInterests(ArrayCollection $interests) {
		$this->interests = $interests;
	}

	/** @return ArrayCollection */
	public function getLinks() {
		if($this->links == null) {
			$this->links = new ArrayCollection();
		}
		return $this->links;
	}

	public function setLinks(ArrayCollection $links) {
		$this->links = $links;
	}

	/** @return ArrayCollection */
	public function getNotifications() {
		if($this->notifications == null) {
			$this->notifications = new ArrayCollection();
		}
		return $this->notifications;
	}

	public function setNotifications(ArrayCollection $notifications) {
		$this->notifications = $notifications;
	}

	public function setPhotos(ArrayCollection $photos) {
		$this->photos = $photos;
	}

	/** @return ArrayCollection */
	public function getStatuses() {
		if($this->statuses == null) {
			$this->statuses = new ArrayCollection();
		}
		return $this->statuses;
	}

	public function setStatuses(ArrayCollection $statuses) {
		$this->statuses = $statuses;
	}

	/*----- Methods -----*/

	abstract public function getDisplayName();

	public function addAlbum(Album $album) {
		$album->setUser($this);
		$this->getAlbums()->add($album);
	}
	
	public function addFeed(Status $status) {
		$status->setTo($this);
		$this->feed->add($status);
	}

	public function addInterest(Interest $interest) {
		$interest->setUser($this);
		$this->getInterests()->add($interest);
	}

	public function addLink(Link $link) {
		$link->setFrom($this);
		$this->getLinks()->add($link);
	}

	public function addNotification(Notification $notification) {
		$notification->setSender($this);
		$this->getNotifications()->add($notification);
	}

	public function addStatus(Status $status) {
		$status->setFrom($this);
		$this->getStatuses()->add($status);
	}

	public function countNotifications() {
		return $this->getNotifications()->count();
	}

	/** @return Album|null */
	public function getAlbum($name = null) {
		/* @var $album Album */

		if($name == null) {
			return null;
		}

		$result = null;
		if($this->albums->count()) {
			foreach ($this->getAlbums()->getValues() as $album) {
				if($album->getName() == $name) {
					$result = $album;
					break;
				}
			}
		}
		return $result;
	}

	/** @return Album|null */
	public function getAlbumById($id = 0) {
		/* @var $album Album */
		$result = null;
		$id = (int) $id;

		foreach ($this->getAlbums()->getValues() as $album) {
			if($album->getId() == $id) {
				$result = $album;
				break;
			}
		}
		return $result;
	}
	
	/**
	 * Returns an array of Status instances created by others
	 * @return array:
	 */
	public function getMessages() {
		/* @var $status Status */
		
		$result = array();
		foreach ($this->getFeed()->getValues() as $status) {
			if($status->getFrom()->getId() != $this->getId()) {
				array_push($result, $status);
			}
		}
		usort($result, array('Base\Model\Traveloti', 'comparePosts'));
		return $result;
	}

	/** @return array */
	public function getPhotos() {
		/* @var $album Album */

		$result = array();
		if($this->albums->count()) {
			$albums = $this->getAlbums()->getValues();
			foreach ($albums as $album) {
				$photos = $album->getPhotos()->getValues();
				$result = array_unique(array_merge($result, $photos));
			}
		}
		usort($result, array('Base\Model\Traveloti', 'comparePhotos'));
		return $result;
	}

	/** @return array */
	public function getPosts() {
		/* @var $album Album */

		$result = array();
		if($this->getStatuses()->count()) {
			$result = $this->statuses->getValues();
		}
		if($this->getLinks()->count()) {
			$links = $this->getLinks()->getValues();
			$result = array_unique(array_merge($result, $links));
		}
		if($this->getAlbums()->count()) {
			$albums = $this->getAlbums()->getValues();
			foreach ($albums as $album) {
				$photos = $album->getPhotos()->getValues();
				$result = array_unique(array_merge($result, $photos));
			}
		}
		usort($result, array('Base\Model\Traveloti', 'comparePosts'));
		return $result;
	}

	/** @return int */
	protected function comparePhotos(Photo $o1, Photo $o2) {
		if($o1->getCreationDate() instanceof \DateTime) {
			$t1 = $o1->getCreationDate()->getTimestamp();
			$t2 = $o2->getCreationDate()->getTimestamp();
		} else {
			$t1 = strtotime($o1->getCreationDate());
			$t2 = strtotime($o2->getCreationDate());
		}
		if($t2 < $t1) {
			return -1;
		} elseif($t2 > $t1) {
			return 1;
		}
		return 0;
	}

	/** @return int */
	protected function comparePosts(Post $o1, Post $o2) {
		if($o1->getCreationDate() instanceof \DateTime) {
			$t1 = $o1->getCreationDate()->getTimestamp();
			$t2 = $o2->getCreationDate()->getTimestamp();
		} else {
			$t1 = strtotime($o1->getCreationDate());
			$t2 = strtotime($o2->getCreationDate());
		}
		if($t2 < $t1) {
			return -1;
		} elseif($t2 > $t1) {
			return 1;
		}
		return 0;
	}
}
?>