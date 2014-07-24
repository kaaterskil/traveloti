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

use Application\StdLib\Exception\ClassCastException;
use Application\StdLib\Object;
use Application\StdLib\Observable;
use Application\StdLib\Observer;
use Application\StdLib\Constants;
use Base\Model\TravelLogAdmin;
use Base\Model\TravelLog;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use ZfcUser\Entity\UserInterface as ZfcUserInterface;

/**
 * A Traveloti member
 * @author Blair
 *
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends Traveloti implements ZfcUserInterface, Observer {

	const LOG_STATUS_NEW = "log_status_new";
	const LOG_STATUS_SENT = "log_status_sent";

	/*----- Properties -----*/

	/** @ORM\Column(type="string", name="user_type", length=50) */
	private $userType;

	/** @ORM\Column(type="string", name="first_name", length=20, nullable=true) */
	private $firstName;

	/** @ORM\Column(type="string", name="middle_name", length=20, nullable=true) */
	private $middleName;

	/** @ORM\Column(type="string", name="last_name", length=20) */
	private $lastName;

	/** @ORM\Column(type="string", name="alternate_name", length=50, nullable=true) */
	private $alternateName;

	/** @ORM\Column(type="integer", name="show_alternate_name", nullable=true) */
	private $showAlternateName;

	/** @ORM\Column(type="string", name="display_name", length=100, nullable=true) */
	private $displayName;

	/** @ORM\Column(type="string", name="display_name_order", length=4, nullable=true) */
	private $displayNameOrder = 'fnln';

	/** @ORM\Column(type="string", length=255, unique=true) */
	private $email;

	/** @ORM\Column(type="string", length=128) */
	private $password;

	/** @ORM\Column(type="integer", name="has_silhouette") */
	private $hasSilhouette;

	/** @ORM\Column(type="string", length=2, nullable=true) */
	private $gender;

	/** @ORM\Column(type="integer", name="email_preset", nullable=true) */
	private $emailPreset = 0;

	/** @ORM\Column(type="integer", name="notifications", nullable=true) */
	private $notificationSetting = 0;

	/** @ORM\Column(type="integer", name="tag_notifications", nullable=true) */
	private $tagNotifications = 0;

	/** @ORM\Column(type="string", length=100) */
	private $certification;

	/** @ORM\Column(type="string") */
	private $specialization;

	/** @ORM\Column(type="date", name="expert_since") */
	private $since;

	/** @ORM\Column(type="string", name="expert_name", length=100) */
	private $expertName;

	/** @ORM\Column(type="string", length=100) */
	private $address1;

	/** @ORM\Column(type="string", length=100) */
	private $address2;

	/** @ORM\Column(type="string", length=60) */
	private $city;

	/** @ORM\Column(type="string", length=2) */
	private $region;

	/** @ORM\Column(type="string", name="postal_code", length=10) */
	private $postalCode;

	/** @ORM\Column(type="string", length=15) */
	private $telephone;

	/** @ORM\Column(type="string", name="business_hours", length=100) */
	private $hours;

	/** @ORM\Column(type="string", name="biography") */
	private $bio;

	/** @ORM\Column(type="string") */
	private $testimonial;

	/** @ORM\Column(type="string", name="link_website", length=255) */
	private $website;

	/** @ORM\Column(type="string", name="link_facebook", length=255) */
	private $facebookLink;

	/** @ORM\Column(type="string", name="link_twitter", length=255) */
	private $twitterLink;

	/** @ORM\Column(type="string", name="link_linkedin", length=255) */
	private $linkedinLink;

	/** @ORM\Column(type="string", name="link_youtube", length=255) */
	private $youtubeLink;

	/** @ORM\Column(type="string", name="link_blog", length=255) */
	private $blogLink;

	/*----- Collections -----*/

	/**
	 * The travel logs owned by the user
	 *
	 * Bidirectional one-to-many: inverse side
	 * @ORM\OneToMany(targetEntity="TravelLogAdmin", mappedBy="user")
	 */
	private $accounts;

	/**
	 * The user's incoming friend requests
	 *
	 * Bidirectional one-to-many: inverse side
	 * @ORM\OneToMany(targetEntity="FriendRequest", mappedBy="recipient")
	 */
	private $friendRequests;

	/**
	 * The user's friends
	 *
	 * Many-to-many: self-referencing
	 * @ORM\ManyToMany(targetEntity="User", mappedBy="following")
	 */
	private $followers;

	/**
	 * Many-to-many: self-referencing
	 * @ORM\ManyToMany(targetEntity="User", inversedBy="followers")
	 * @ORM\JoinTable(name="friend",
	 * 		joinColumns={@ORM\JoinColumn(name="user1_id", referencedColumnName="traveloti_id")},
	 * 		inverseJoinColumns={@ORM\JoinColumn(name="user2_id", referencedColumnName="traveloti_id")}
	 * 		)
	 */
	private $following;

	/**
	 * All the pages this user has liked
	 *
	 * Bidirectional one-to-many: inverse side
	 * @ORM\OneToMany(targetEntity="Like", mappedBy="from")
	 * @ORM\OrderBy({"creationDate"="desc"})
	 */
	private $likes;

	/**
	 * Photos the user is tagged in.
	 *
	 * Bidirectional one-to-many: inverse side
	 * @ORM\OneToMany(targetEntity="Tag", mappedBy="subject")
	 */
	private $photoTags;

	/**
	 * Bidirectional one-to-many: inverse side
	 * @ORM\OneToMany(targetEntity="FriendRequest", mappedBy="sender")
	 * @ORM\OrderBy({"creationDate"="desc"})
	 */
	private $sentFriendRequests;

	/**
	 * Bidirectional one-to-many: inverse side
	 * @ORM\OneToMany(targetEntity="TravelLogAdmin", mappedBy="user")
	 */
	private $travelLogAdmins;

	/*----- Constructor -----*/

	public function __construct() {
		// Superclass properties
		$this->albums = new ArrayCollection();
		$this->interests = new ArrayCollection();
		$this->links = new ArrayCollection();
		$this->notifications = new ArrayCollection();
		$this->statuses = new ArrayCollection();

		$this->type = 'user';
		$this->accounts = new ArrayCollection();
		$this->friendRequests = new ArrayCollection();
		$this->followers = new ArrayCollection();
		$this->following = new ArrayCollection();
		$this->likes = new ArrayCollection();
		$this->photoTags = new ArrayCollection();
		$this->sentFriendRequests = new ArrayCollection();
		$this->travelLogAdmins = new ArrayCollection();
	}

	/*----- Getter/Setters -----*/

	/** @return string */
	public function getUserType() {
		return $this->userType;
	}

	public function setUserType($userType) {
		$this->userType = $userType;
	}

	/** @return string */
	public function getFirstName() {
		return $this->firstName;
	}

	public function setFirstName($firstName) {
		$this->firstName = $firstName;
	}

	/** @return string */
	public function getMiddleName() {
		return $this->middleName;
	}

	public function setMiddleName($middleName) {
		$this->middleName = $middleName;
	}

	/** @return string */
	public function getLastName() {
		return $this->lastName;
	}

	public function setLastName($lastName) {
		$this->lastName = $lastName;
	}

	/** @return string */
	public function getAlternateName() {
		return $this->alternateName;
	}

	public function setAlternateName($alternameName) {
		$this->alternateName = $alternameName;
	}

	/** @return boolean */
	public function getShowAlternateName() {
		return ($this->showAlternateName != 0 ? true : false);
	}

	public function setShowAlternateName($showAlternateName) {
		$this->showAlternateName = ($showAlternateName ? 1 : 0);
	}

	/** @see \ZfcUser\Entity\UserInterface::getDisplayName() */
	public function getDisplayName() {
		if(null == $this->displayName) {
			$this->initDisplayName();
		}
		return $this->displayName;
	}

	/** @see \ZfcUser\Entity\UserInterface::setDisplayName() */
	public function setDisplayName($displayName = null) {
		if($displayName == null) {
			$displayName = $this->initDisplayName();
		}
		$this->displayName = $displayName;
	}

	/** @return string */
	public function getDisplayNameOrder() {
		return $this->displayNameOrder;
	}

	public function setDisplayNameOrder($displayNameOrder) {
		$this->displayNameOrder = $displayNameOrder;
	}

	/** @see \ZfcUser\Entity\UserInterface::getUsername() */
	public function getUsername() {
		if(null == $this->username) {
			$this->initUsername();
		}
		return $this->username;
	}

	/** @see \ZfcUser\Entity\UserInterface::setUsername() */
	public function setUsername($username = null) {
		if($username == null) {
			$username = $this->initUsername();
		}
		$this->username = $username;
	}

	/** @see \ZfcUser\Entity\UserInterface::getEmail() */
	public function getEmail() {
		return $this->email;
	}

	/** @see \ZfcUser\Entity\UserInterface::setEmail() */
	public function setEmail($email) {
		$this->email = $email;
	}

	/** @see \ZfcUser\Entity\UserInterface::getPassword() */
	public function getPassword() {
		return $this->password;
	}

	/** @see \ZfcUser\Entity\UserInterface::setPassword() */
	public function setPassword($password) {
		$this->password = $password;
	}

	/** @return boolean */
	public function getHasSilhouette() {
		return ($this->hasSilhouette != 0 ? true : false);
	}

	public function setHasSilhouette($hasSilhouette) {
		$this->hasSilhouette = ($hasSilhouette ? 1 : 0);
	}

	/** @return string */
	public function getGender() {
		return $this->gender;
	}

	public function setGender($gender) {
		$this->gender = $gender;
	}

	/** @return integer */
	public function getEmailPreset() {
		$this->emailPreset;
	}

	public function setEmailPreset($emailPreset) {
		$this->emailPreset = $emailPreset;
	}

	/** @return integer */
	public function getNotificationSetting() {
		return $this->notificationSetting;
	}

	public function setNotificationSetting($notificationSetting) {
		$this->notificationSetting = $notificationSetting;
	}

	/** @return integer */
	public function getTagNotifications() {
		return $this->tagNotifications;
	}

	public function setTagNotifications($tagNotifications) {
		$this->tagNotifications = $tagNotifications;
	}

	/** @return string */
	public function getCertification() {
		return $this->certification;
	}

	public function setCertification($certification) {
		$this->certification = $certification;
	}

	/** @return string */
	public function getSpecialization() {
		return $this->specialization;
	}

	public function setSpecialization($specialization) {
		$this->specialization = $specialization;
	}

	/** @see \Base\Model\Expert::getSince() */
	public function getSince() {
		return $this->since;
	}

	/** @see \Base\Model\Expert::setSince() */
	public function setSince($since) {
		$this->since = $since;
	}

	/** @return string */
	public function getExpertName() {
		return $this->expertName;
	}

	public function setExpertName($expertName) {
		$this->expertName = $expertName;
	}

	/** @return string */
	public function getAddress1() {
		return $this->address1;
	}

	public function setAddress1($address1) {
		$this->address1 = $address1;
	}

	/** @return string */
	public function getAddress2() {
		return $this->address2;
	}

	public function setAddress2($address2) {
		$this->address2 = $address2;
	}

	/** @return string */
	public function getCity() {
		return $this->city;
	}

	public function setCity($city) {
		$this->city = $city;
	}

	/** @return string */
	public function getRegion() {
		return $this->region;
	}

	public function setRegion($region) {
		$this->region = $region;
	}

	/** @return string */
	public function getPostalCode() {
		return $this->postalCode;
	}

	public function setPostalCode($postalCode) {
		$this->postalCode = $postalCode;
	}

	/** @return string */
	public function getTelephone() {
		return $this->telephone;
	}

	public function setTelephone($telephone) {
		$this->telephone = $telephone;
	}

	/** @return string */
	public function getHours() {
		return $this->hours;
	}

	public function setHours($hours) {
		$this->hours = $hours;
	}

	/** @return string */
	public function getBio() {
		return $this->bio;
	}

	public function setBio($bio) {
		$this->bio = $bio;
	}

	/** @return string */
	public function getTestimonial() {
		return $this->testimonial;
	}

	public function setTestimonial($testimonial) {
		$this->testimonial = $testimonial;
	}

	/** @return string */
	public function getWebsite() {
		return $this->website;
	}

	public function setWebsite($website) {
		$this->website = $website;
	}

	/** @return string */
	public function getFacebookLink() {
		return $this->facebookLink;
	}

	public function setFacebookLink($facebookLink) {
		$this->facebookLink = $facebookLink;
	}

	/** @return string */
	public function getTwitterLink() {
		return $this->twitterLink;
	}

	public function setTwitterLink($twitterLink) {
		$this->twitterLink = $twitterLink;
	}

	/** @return string */
	public function getLinkedinLink() {
		return $this->linkedinLink;
	}

	public function setLinkedinLink($linkedInLink) {
		$this->linkedinLink = $linkedInLink;
	}

	/** @return string */
	public function getYoutubeLink() {
		return $this->youtubeLink;
	}

	public function setYoutubeLink($youtubeLink) {
		$this->youtubeLink = $youtubeLink;
	}

	/** @return string */
	public function getBlogLink() {
		return $this->blogLink;
	}

	public function setBlogLink($blogLink) {
		$this->blogLink = $blogLink;
	}

	/** @return ArrayCollection */
	public function getAccounts() {
		return $this->accounts;
	}

	public function setAccounts(ArrayCollection $accounts) {
		$this->accounts = $accounts;
	}

	/** @return ArrayCollection */
	public function getFriendRequests() {
		return $this->friendRequests;
	}

	public function setFriendRequests(ArrayCollection $receivedRequests) {
		$this->friendRequests = $receivedRequests;
	}

	/** @return ArrayCollection */
	public function getFollowers() {
		return $this->followers;
	}

	public function setFollowers(ArrayCollection $followers) {
		$this->followers = $followers;
	}

	/** @return ArrayCollection */
	public function getFollowing() {
		return $this->following;
	}

	public function setFollowing(ArrayCollection $following) {
		$this->following = $following;
	}

	/** @return ArrayCollection */
	public function getLikes() {
		return $this->likes;
	}

	public function setLikes(ArrayCollection $likes) {
		$this->likes = $likes;
	}

	/** @return ArrayCollection */
	public function getPhotoTaags() {
		return $this->photoTags;
	}

	public function setPhotoTags(ArrayCollection $photoTags) {
		$this->photoTags = $photoTags;
	}

	/** @return ArrayCollection */
	public function getSentFriendRequests() {
		return $this->sentFriendRequests;
	}

	public function setSentFriendRequests(ArrayCollection $sentRequests) {
		$this->sentFriendRequests = $sentRequests;
	}

	/** @return ArrayCollection */
	public function getTravelLogAdmins() {
		return $this->travelLogAdmins;
	}

	public function setTravelLogAdmins(ArrayCollection $travelLogAdmins) {
		$this->travelLogAdmins = $travelLogAdmins;
	}

	/*----- Methods -----*/

	public function update(Observable $observable) {
		/* @var $friend User */

		$followers = $this->getFollowers()->getValues();
		$following = $this->getFollowing()->getValues();
		$friends = array_merge($following, $following);
		foreach ($friends as $friend) {
			// Do something here
			echo "Sending " . $friend->getDisplayName() . " an update.\n";
		}
	}

	/*----- Methods -----*/

	public function addAccount(TravelLogAdmin $account) {
		$account->setUser($this);
		$this->accounts->add($account);
	}

	public function addFriendRequest(FriendRequest $friendRequest) {
		$friendRequest->setRecipient($this);
		$this->friendRequests->add($friendRequest);
	}

	public function addFollower(User $follower) {
		$follower->getFollowing()->add($this);
		$this->followers->add($follower);
	}

	public function addFollowing(User $following) {
		$following->getFollowers()->add($this);
		$this->following->add($following);
	}

	public function addLike(Like $like) {
		$like->setFrom($this);
		$this->likes->add($like);
	}

	public function addPhoto(Photo $photo) {
		$photo->setFrom($this);
		$this->photoTags->add($photo);
	}

	public function addSentFriendRequest(FriendRequest $friendRequest) {
		$friendRequest->setSender($this);
		$this->sentFriendRequests->add($friendRequest);
	}

	public function addTravelLogAdmin(TravelLogAdmin $travelLogAdmin) {
		$travelLogAdmin->setUser($this);
		$this->travelLogAdmins->add($travelLogAdmin);
	}


	/**
	 * Adds a TravelLogAdmin to the User from the specified TravelLog.
	 * NOTE: This method does not persist any entities. It only creates associations.
	 *
	 * @param TravelLog $travelLog
	 * @return TravelLogAdmin		the existing or a new TravelLogAdmin instance if
	 * 								an existing one was not found
	 */
	public function addTravelLog(TravelLog $travelLog) {
		/* @var $tla TravelLogAdmin */
		/* @var $target TravelLogAdmin */

		$isAdmin = false;
		foreach ($travelLog->getAdministrators()->getValues() as $target) {
			if($target->getUser()->getId() == $this->getId()) {
				$tla = $target;
				if(!$this->travelLogAdmins->contains($tla)) {
					$this->travelLogAdmins->add($tla);
				}
				break;
			}
		}
		if(!$isAdmin) {
			$tla = new TravelLogAdmin();
			$tla->setCreationDate($now);
			$tla->setLastUpdateDate($now);
			$tla->setTravelLog($travelLog);
			$tla->setUser($this);

			$this->travelLogAdmins->add($tla);
		}
		return $tla;
	}

	/** @return int */
	public function countFriends() {
		$followers = $this->getFollowers() ? $this->getFollowers()->count() : 0;
		$following = $this->getFollowing() ? $this->getFollowing()->count() : 0;
		return $followers + $following;
	}

	/** @return int */
	public function countMutualConnections(User $user) {
		$mine = $this->getAllFriends();
		$theirs = $user->getAllFriends();
		$mutual = array_intersect($mine, $theirs);
		return count($mutual);
	}

	/** @return int */
	public function countRequests() {
		return count($this->getRequests());
	}

	/** @return int */
	public function countActiveRequests() {
		/* @var $request FriendRequest */

		$count = 0;
		$requests = $this->getRequests();
		foreach ($requests as $request) {
			if($request->getIsUnread()) {
				$count++;
			}
		}
		return $count;
	}

	/** @see \Application\StdLib\Object::equals() */
	public function equals(Object $o) {
		if(!$o instanceof $this) {
			throw new ClassCastException();
		}
		if($o->getId() == $this->getId()) {
			return true;
		}
		if(($o->getFirstName() == $this->getFirstName())
			&& ($o->getLastName() == $this->getLastName())
			&& ($o->getCreationDate() == $this->getCreationDate())) {
			return true;
		}
		return false;
	}

	public function getAddress() {
		$result = '';
		if($this->address1 != '') {
			$result = $this->address1;
		}
		if($this->address2 != '') {
			if($result != '') {
				$result .= "\n";
			}
			$result .= $this->address2;
		}
		if($this->city != '') {
			if($result != '') {
				$result .= "\n";
			}
			$result .= $this->city;
		}
		if($this->region != '') {
			if($this->city != '') {
				$result .= ', ';
			} elseif ($result != '') {
				$result .= "\n";
			}
			$result .= $this->region;
		}
		if($this->postalCode != '') {
			if($this->city != '' || $this->region != '') {
				$result .= ' ';
			} elseif($result != '') {
				$result .= "\n";
			}
			$result .= $this->postalCode;
		}
		return $result;
	}

	/** @see \Application\StdLib\Object::getClass() */
	public function getClass() {
		return get_class($this);
	}

	public function getProfileAlbum() {
		/* @var $album Album */

		$albums = $this->getAlbums()->getValues();
		foreach($albums as $album){
			if($album->getName() == Constants::ALBUM_PROFILE) {
				return $album;
			}
		}
		return null;
	}

	/** @return array */
	public function getAllFriends() {
		$followers = $this->getFollowers()->getValues();
		$following = $this->getFollowing()->getValues();
		$friends = array_unique(array_merge($followers, $following));
		usort($friends, array('Base\Model\User', 'compareFriends'));
		return $friends;
	}

	/**
	 * Returns the user's news feed
	 * @return array
	 */
	public function getHomeStream() {
		/* @var $friend User */

		$posts = $this->getPosts();
		$friends = $this->getAllFriends();
		foreach ($friends as $friend) {
			$posts = array_unique(array_merge($posts, $friend->getPosts()));
		}

		// Limit the feed to no more than 25 posts
		if(count($posts) > 25) {
			$posts = array_slice($posts, 0, 25);
		}
		usort($posts, array('Base\Model\User', 'comparePosts'));
		return $posts;
	}

	/**
	 * Returns all requests sorted chronologically (desc)
	 *
	 * @return array
	 */
	public function getRequests() {
		$result = $this->getFriendRequests()->getValues();
		usort($result, array('Base\Model\User', 'compareRequests'));
		return $result;
	}

	/**
	 * Returns an array of Posts the user was tagged in
	 * @return array
	 */
	public function getTagged() {
		$result = array();
		return $result;
	}

	/** @return TravelLog|null */
	public function getTravelLog($name = null) {
		/* @var $logAdmin TravelLogAdmin */
		/* @var $travelLog TravelLog */

		$result = null;
		if($name != null) {
			$admins = $this->travelLogAdmins->getValues();
			foreach ($admins as $logAdmin) {
				$travelLog = $logAdmin->getTravelLog();
				if($travelLog->getName() == $name) {
					$result = $travelLog;
					break;
				}
			}
		}
		return $result;
	}

	/** @return array */
	public function getTravelLogs() {
		/* @var $logAdmin TravelLogAdmin */
		/* @var $travelLog TravelLog */

		$result = array();
		$admins = $this->travelLogAdmins->getValues();
		foreach ($admins as $logAdmin) {
			$result[] = $logAdmin->getTravelLog();
		}
		return $result;
	}

	public function initDisplayName(){
		$displayName = '';
		if($this->displayNameOrder == 'lnfn') {
			if(null != $this->lastName) {
				$displayName .= $this->lastName;
			}
			if(null != $this->firstName) {
				if($displayName .= '') {
					$displayName .= ' ';
				}
				$displayName .= $this->firstName;
			}
			if(null != $this->middleName) {
				if($displayName != '') {
					$displayName .= ' ';
				}
				$displayName .= $this->middleName;
			}
		} else {
			if(null != $this->firstName) {
				$displayName .= $this->firstName;
			}
			if(null != $this->middleName) {
				if($displayName != '') {
					$displayName .= ' ';
				}
				$displayName .= $this->middleName;
			}
			if(null != $this->lastName) {
				if($displayName != '') {
					$displayName .= ' ';
				}
				$displayName .= $this->lastName;
			}
		}
		return $displayName;
	}

	/**
	 * Tests if the specified User is a friend
	 *
	 * @param User $user
	 * @return boolean
	 */
	public function isConnectedTo(User $user = null) {
		if($user != null) {
			if($this->getFollowers()->contains($user)
				|| $this->getFollowing()->contains($user)) {
				return true;
			}
		}
		return false;
	}

	/** @see \Application\StdLib\Object::__toString() */
	public function __toString() {
		$result = 'User[id=' . $this->getId()
		. ',userType=' . $this->getUserType()
		. ',firstName=' . $this->getFirstName()
		. ',middleName=' . $this->getMiddleName()
		. ',lastName=' . $this->getLastName()
		. ',alternateName=' . $this->getAlternateName()
		. ',showAlternateName=' . $this->getShowAlternateName()
		. ',displayName=' . $this->getDisplayName()
		. ',displayNameOrder=' . $this->getDisplayNameOrder()
		. ',username=' . $this->getUsername()
		. ',email=' . $this->getEmail()
		. ',password=' . $this->getPassword()
		. ',picture' . ($this->getPicture() ? $this->getPicture()->getSource() : '')
		. ',hasSilhouette' . ($this->getHasSilhouette() ? 'true' : 'false')
		. ',gender=' . $this->getGender()
		. ',emailPreset=' . $this->getEmailPreset()
		. ',notificationSetting=' . $this->getNotificationSetting()
		. ',tagNotifications=' . $this->getTagNotifications()
		. ',creationDate=' . date('Y-m-d H:i:s', $this->getCreationDate()->getTimestamp())
		. ',lastUpdateDate=' . date('Y-m-d H:i:s', $this->getLastUpdateDate()->getTimestamp());
		
		if($this->getUserType() == Traveloti::AGENT_TYPE) {
			$result .= '.certification=' . $this->getCertification()
			. '.specialization=' . $this->getSpecialization()
			. ',telephone=' . $this->getTelephone()
			. ',hours=' . $this->getHours();
		}
		
		if($this->getUserType() == Traveloti::AGENT_TYPE
				|| $this->getUserType() == Traveloti::BLOGGER_TYPE) {
			$result .= ',since=' . $this->getSince()
			. ',expertName=' . $this->getExpertName()
			. ',address1=' . $this->getAddress1()
			. ',address2=' . $this->getAddress2()
			. ',city=' . $this->getCity()
			. ',region=' . $this->getRegion()
			. ',postalCode=' . $this->getPostalCode()
			. ',bio=' . $this->getBio()
			. ',testimonial=' . $this->getTestimonial()
			. ',website=' . $this->getWebsite()
			. ',facebookLink=' . $this->getFacebookLink()
			. ',twitterLink=' . $this->getTwitterLink()
			. ',linkedinLink=' . $this->getLinkedinLink()
			. ',youtubeLink=' . $this->getYoutubeLink()
			. ',blogLink=' . $this->getBlogLink();
		}
		$result .= ']';
		return $result;
	}

	private function initUsername() {
		$username = str_replace(' ', '.', strtolower($this->getDisplayName()));
		return str_replace('..', '.', $username);
	}

	private function compareRequests(Request $o1, Request $o2) {
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

	private function compareFriends(User $o1, User $o2) {
		$dn1 = $o1->getDisplayName();
		$dn2 = $o2->getDisplayName();

		if($dn1 < $dn2) {
			return -1;
		} elseif($dn1 > $dn2) {
			return 1;
		}
		return 0;
	}
}
?>