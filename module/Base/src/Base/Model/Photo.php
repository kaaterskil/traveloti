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
use Application\StdLib\Observer;
use Application\StdLib\Observable;
use Application\StdLib\Object;
use Base\Model\Album;
use Base\Model\Comment;
use Base\Model\Like;
use Base\Model\Post;
use Base\Model\Tag;
use Base\Model\Traveloti;
use Base\Model\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Photo Class
 * @author Blair
 *
 * @ORM\Entity
 * @ORM\Table(name="photos")
 */
class Photo implements Post {

	const SHRINK_ONLY = 1;
	const STRETCH = 2;
	const FIT = 0;
	const FILL = 4;
	const EXACT = 9;
	const JPEG = IMAGETYPE_JPEG;
	const PNG = IMAGETYPE_PNG;
	const GIF = IMAGETYPE_GIF;
	const EMPTY_GIF = "GIF89a\x01\x00\x01\x00\x80\x00\x00\x00\x00\x00\x00\x00\x00!\xf9\x04\x01\x00\x00\x00\x00,\x00\x00\x00\x00\x01\x00\x01\x00\x00\x02\x02D\x01\x00;";

	/**
	 * Return the given RGB colors as an array
	 * @param int $red
	 * @param int $green
	 * @param int $blue
	 * @param number $transparency
	 * @return array
	 */
	public static function rgb($red, $green, $blue, $transparency = 0){
		return array(
			'red' => max(0, min(255, (int) $red)),
			'green' => max(0, min(255, (int) $green)),
			'blue' => max(0, min(255, (int) $blue)),
			'alpha' => max(0, min(255, (int) $transparency)),
		);
	}

	/**
	 * Returns a Photo instance from a given filename
	 * @param string $filename
	 * @param string $format
	 * @throws \Exception
	 * @return \Base\Model\Photo
	 */
	public static function fromFile($filename, & $format = null){
		if(!extension_loaded('gd')) {
			throw new \Exception("PHP extension GD is not loaded.");
		}

		$info = @getimagesize($filename);

		switch($format = $info[2]) {
			case self::JPEG:
				return new self(imagecreatefromjpeg($filename));
			case self::PNG:
				return new self(imagecreatefrompng($filename));
			case self::GIF:
				return new self(imagecreatefromgif($filename));
			default:
				throw new \Exception(
				"Unknown image type or file '" . $filename . "' not found.");
		}
	}

	/**
	 * Returns the format from the image stream in the given string
	 * @param string $s
	 * @return mixed Detected image format
	 */
	public static function getFormatFromString($s) {
		$types = array(
			'image/jpeg' => self::JPEG,
			'image/png' => self::PNG,
			'image/gif' => self::GIF,
		);
		$type = MimeTypeDetector::fromString($s);
		return (isset($types[$type]) ? $types[$type] : null);
	}

	/**
	 * Returns a new Photo instance from the image stream in the given string
	 * @param string $s
	 * @param string $format
	 * @throws \Exception
	 * @return \Base\Model\Photo
	 */
	public static function fromString($s, & $format = null) {
		if(!extension_loaded('gd')) {
			throw new \Exception("PHP extension GD is not loaded.");
		}

		$format = self::getFormatFromString($s);
		return new self(imagecreatefromstring($s));
	}

	/**
	 * Creates a blank Photo instance with the given width and height	 *
	 * @param int $width
	 * @param int $height
	 * @param string $color
	 * @throws \Exception
	 * @throws \InvalidArgumentException
	 * @return \Base\Model\Photo
	 */
	public static function fromBlank($width, $height, $color = null) {
		if(!extension_loaded('gd')) {
			throw new \Exception("PHP extension GD is not loaded.");
		}

		$width = (int) $width;
		$height = (int) $height;
		if($width < 1 || $height < 1) {
			throw new \InvalidArgumentException(
				"Photo width and height must be greater than zero");
		}

		$image = imagecreatetruecolor($width, $height);
		if(is_array($color)) {
			$color += array('alpha' => 0);
			$color = imagecolorallocatealpha(
				$image, $color['red'], $color['green'], $color['blue'], $color['alpha']);
			imagealphablending($image, false);
			imagefilledrectangle($image, 0, 0, $width - 1, $height - 1, $color);
			imagealphablending($image, true);
		}

		return new self($image);
	}

	/**
	 * Calculates dimensions of resized image
	 * @param mixed $sourceWidth	source width
	 * @param mixed $sourceHeight	source height
	 * @param mixed $newWidth	width in pixels or percent
	 * @param mixed $newHeight	height in pixels or percent
	 * @param int $flags
	 * @throws \InvalidArgumentException
	 * @return array			format: array(width, height)
	 */
	public static function calculateSize(
		$sourceWidth, $sourceHeight, $newWidth, $newHeight, $flags = self::FIT) {

		$percents;

		if(substr($newWidth, -1) === '%') {
			$newWidth = round($sourceWidth / 100 * abs($newWidth));
			$percents = true;
		} else {
			$newWidth = (int) abs($newWidth);
		}

		if(substr($newHeight, -1) === '%') {
			$newHeight = round($sourceHeight / 100 * abs($newHeight));
			$flags |= (empty($percents) ? 0 : self::STRETCH);
		} else {
			$newHeight = (int) abs($newHeight);
		}

		if($flags & self::STRETCH) {
			if(empty($newWidth) || empty($newHeight)) {
				throw new \InvalidArgumentException(
					"Width and height must be specified for stretching.");
			}
			if($flags & self::SHRINK_ONLY) {
				$newWidth = round($sourceWidth * min(1, $newWidth / $sourceWidth));
				$newHeight = round($sourceHeight * min(1, $newHeight / $sourceHeight));
			}
		} else {
			if(empty($newWidth) && empty($newHeight)) {
				throw new \InvalidArgumentException("Width and height must be specified.");
			}

			$scale = array();
			if($newWidth > 0) {
				$scale[] = $newWidth / $sourceWidth;
			}
			if($newHeight > 0) {
				$scale[] = $newHeight / $sourceHeight;
			}
			if($flags & self::FILL) {
				$scale = array(max($scale));
			}
			if($flags & self::SHRINK_ONLY) {
				$scale[] = 1;
			}
			// $scale = min($scale);
			$scale = max($scale);
			$newWidth = round($sourceWidth * $scale);
			$newHeight = round($sourceHeight * $scale);
		}

		return array(
			max((int) $newWidth, 1),
			max((int) $newHeight, 1),
		);
	}

	/**
	 * Returns the dimensions of the given cutout in the image
	 * @param mixed $sourceWidth	source width
	 * @param mixed $sourceHeight	source height
	 * @param mixed $left		x-offset in pixels or percent
	 * @param mixed $top		y-offset in pixels or percent
	 * @param mixed $newWidth	width in pixels or percent
	 * @param mixed $newHeight	height in pixels or percent
	 * @return array			format: array(left, top, width, height)
	 */
	public static function calculateCutout(
		$sourceWidth, $sourceHeight, $left, $top, $newWidth, $newHeight) {

		if(substr($newWidth, -1) === '%') {
			$newWidth = round($sourceWidth / 100 * $newWidth);
		}
		if(substr($newHeight, -1) === '%') {
			$newHeight = round($sourceHeight / 100 * $newHeight);
		}
		if(substr($left, -1) === '%') {
			$left = round(($sourceWidth - $newWidth) / 100 * $left);
		}
		if(substr($top, -1) === '%') {
			$top = round(($sourceHeight - $newHeight) / 100 * $top);
		}

		if($left < 0) {
			$newWidth += $left;
			$left = 0;
		}
		if($top < 0) {
			$newHeight += $top;
			$top = 0;
		}

		$newWidth = min((int) $newWidth, $sourceWidth - $left);
		$newHeight = min((int) $newHeight, $sourceHeight - $top);

		return array($left, $top, $newWidth, $newHeight);
	}

	/*----- Properties -----*/

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="photo_id")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * Bidirectional one-to-many: owning side
	 * @ORM\ManyToOne(targetEntity="Album", inversedBy="photos")
	 * @ORM\JoinColumn(name="album_id", referencedColumnName="album_id")
	 */
	private $album;

	/**
	 * Bidirectional one-to-many: owning side
	 * @ORM\ManyToOne(targetEntity="Traveloti", inversedBy="photos")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="traveloti_id")
	 */
	private $from;

	/** @ORM\Column(type="string", name="caption") */
	private $name;

	/** @ORM\Column(type="string") */
	private $place;

	/** @ORM\Column(type="string") */
	private $icon;

	/** @ORM\Column(type="string") */
	private $picture;

	/** @ORM\Column(type="string", name="src") */
	private $source;

	/** @ORm\Column(type="integer", name="src_height") */
	private $srcHeight;

	/** @ORM\Column(type="integer", name="src_width") */
	private $srcWidth;

	/** @ORM\Column(type="string") */
	private $link;

	/** @ORM\Column(type="integer") */
	private $position;

	/** @ORM\Column(type="string") */
	private $visibility;

	/** @ORM\Column(type="string") */
	private $type = 'photo';

	/** @ORM\Column(type="datetime", name="creation_date") */
	private $creationDate;

	/** @ORM\Column(type="datetime", name="last_update_date") */
	private $lastUpdateDate;

	/*----- Collections -----*/

	/**
	 * Bidirectional one-to-many: inverse side
	 * @ORM\OneToMany(targetEntity="Comment", mappedBy="photo")
	 * @ORM\OrderBy({"creationDate" = "desc"})
	 */
	private $comments;

	/**
	 * Bidirectional one-to-many: inverse side
	 * @ORM\OneToMany(targetEntity="Like", mappedBy="photo")
	 * @ORM\OrderBy({"creationDate" = "desc"})
	 */
	private $likes;

	/**
	 * Bidirectional one-to-many: inverse side
	 * @ORM\OneToMany(targetEntity="Tag", mappedBy="photo")
	 */
	private $tags;

	/** @var resource */
	private $image;

	/** @var array */
	private $observers = array();

	/*----- Constructor -----*/

	public function __construct($resource = null) {
		if(null !== $resource) {
			$this->setImageResource($resource);
			imagesavealpha($resource, true);
		}
		$this->comments = new ArrayCollection();
		$this->likes = new ArrayCollection();
		$this->tags = new ArrayCollection();
	}

	/*----- Getter/Setters -----*/

	/** @see \Base\Model\Post::getId() */
	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = (int) $id;
	}

	/** @return Album */
	public function getAlbum() {
		return $this->album;
	}

	public function setAlbum(Album $album) {
		$this->album = $album;
	}

	/** @see \Base\Model\Notifiable::getFrom() */
	public function getFrom() {
		return $this->from;
	}

	public function setFrom(Traveloti $from) {
		$this->from = $from;
	}

	/** @return string */
	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
	}
	
	/** @see \Base\Model\Post::getMessage() */
	public function getMessage() {
		if($this->getName() != null) {
			return $this->getName();
		}
		return $this->getFrom()->getDisplayName() . ' added a new photo';
	}

	/** @return string */
	public function getPlace() {
		return $this->place;
	}

	public function setPlace($place) {
		$this->place = $place;
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

	/** @return string */
	public function getSource() {
		return $this->source;
	}

	public function setSource($source) {
		$this->source = $source;
	}

	/** @return int */
	public function getSrcHeight() {
		return $this->srcHeight;
	}

	public function setSrcHeight($srcHeight) {
		$this->srcHeight = (int) $srcHeight;
	}

	/** @return int */
	public function getSrcWidth() {
		return $this->srcWidth;
	}

	public function setSrcWidth($srcWidth) {
		$this->srcWidth = (int) $srcWidth;
	}

	/** @return string */
	public function getLink() {
		return $this->link;
	}

	public function setLink($link) {
		$this->link = $link;
	}

	/** @return int */
	public function getPosition() {
		return $this->position;
	}

	public function setPosition($position) {
		$this->position = (int) $position;
	}

	/** @see \Base\Model\Post::getVisibility() */
	public function getVisibility() {
		return $this->visibility;
	}

	public function setVisibility($visibility) {
		$this->visibility = $visibility;
	}

	/** @see \Base\Model\Notifiable::getType() */
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

	public function setLastUpdateDate($lastUpdateDate) {
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

	/** @return ArrayCollection */
	public function getTags() {
		return $this->tags;
	}

	public function setTags(ArrayCollection $tags) {
		$this->tags = $tags;
	}

	/** @return resource */
	public function getImageResource() {
		if((null == $this->image) && (null !== $this->source)) {
			$basepath = dirname(dirname(dirname(dirname(dirname(__DIR__)))))
			. '/public/content/' . $this->getFrom()->getUsername() . '/';
			$this->image = self::fromFile($basepath . $this->source)->getImageResource();
		}
		return $this->image;
	}

	/** @param resource */
	protected function setImageResource($image) {
		if(!is_resource($image) || get_resource_type($image) !== 'gd') {
			throw new \InvalidArgumentException("Photo is not valid.");
		}

		$this->image = $image;
	}

	/** @return array */
	public function getObservers() {
		return $this->observers;
	}

	public function setObservers(array $observers = array()) {
		$this->observers = $observers;
	}

	/*----- Methods -----*/

	/** @see \Base\Model\Post::addComment() */
	public function addComment(Comment $comment) {
		$comment->setPhoto($this);
		$this->comments->add($comment);
	}

	/** @see \Base\Model\Post::addLike() */
	public function addLike(Like $like) {
		$like->setPhoto($this);
		$this->likes->add($like);
	}

	public function addTag(Tag $tag) {
		$tag->setPhoto($this);
		$this->tags->add($tag);
	}

	/** @see \Application\StdLib\Observable::attach() */
	public function attach(Observer $observer) {
		if(!in_array($observer, $this->observers)) {
			array_push($this->observers, $observer);
		}
	}

	/**
	 * Compares the given instances by creationDate (asc)
	 *
	 * @param Photo $o
	 * @return number
	 */
	public function compareTo(Photo $o) {
		if($this->getCreationDate() instanceof \DateTime) {
			$t1 = $o->getCreationDate()->getTimestamp();
			$t2 = $this->getCreationDate()->getTimestamp();
		} else {
			$t1 = strtotime($o->getCreationDate());
			$t2 = strtotime($this->getCreationDate());
		}
		if($t2 < $t1) {
			return -1;
		} elseif($t2 > $t1) {
			return 1;
		}
		return 0;
	}

	/** @return int */
	public function countLikes() {
		return $this->likes->count();
	}

	/**
	 * Crops the image resource in this instance by the given parameters
	 * @param int $left		X-offset in pixels or percent
	 * @param int $top		Y-offset in pixels or percent
	 * @param int $width	width in pixels or percent
	 * @param int $height	height in pixels or percent
	 */
	public function crop($left, $top, $width, $height) {
		list($left, $top, $width, $height) = self::calculateCutout(
			$this->getWidth(), $this->getHeight(), $left, $top, $width, $height);

		$temp = self::fromBlank($width, $height, self::rgb(0, 0, 0, 127));
		$newPhoto = $temp->getImageResource();
		imagecopy(
		$newPhoto, $this->getImageResource(), 0, 0, $left, $top, $width, $height);
		$this->image = $newPhoto;
	}

	/** @see \Application\StdLib\Object::equals() */
	public function equals(Object $o) {
		if(!$o instanceof $this) {
			throw new ClassCastException();
		}
		$that = $o;
		if($that->getId() == $this->getId()) {
			return true;
		}
		if(($that->getFrom()->equals($this->getFrom()))
			&& ($that->getSource() == $this->getSource())) {
			return true;
		}
		return false;
	}

	/** @see \Application\StdLib\Object::getClass() */
	public function getClass() {
		return get_class($this);
	}

	/** @return int */
	public function getHeight() {
		$this->srcHeight = imagesy($this->getImageResource());
		return $this->srcHeight;
	}

	/** @see \Base\Model\Notifiable::getNotifierText() */
	public function getNotifierText(Traveloti $sender) {
		$title = $sender->getDisplayName() . ' posted a photo on your timeline.';
		$htmlTitle = '<span class="blue-name">'
			. $sender->getDisplayName()
			. '</span> posted a photo on your timeline.';

		return new NotifierText($title, $htmlTitle, null, null);
	}

	/** @return int */
	public function getWidth() {
		$this->srcWidth = imagesx($this->getImageResource());
		return $this->srcWidth;
	}

	/** @see \Application\StdLib\Observable::notify() */
	public function notify() {
		/* @var $friend Observer */

		foreach ($this->observers as $key => $friend) {
			$friend->update($this);
		}
	}

	/**
	 * Puts another image into this image
	 * @param Photo $image
	 * @param mixed $left	x-coordinate in pixels or percent
	 * @param mixed $top	y-coordinate in pixels or percent
	 * @param int $opacity
	 */
	public function place(Photo $image, $left = 0, $top = 0, $opacity = 100) {
		$opacity = max(0, min(100, (int) $opacity));

		if(substr($left, -1) === '%') {
			$left = round(($this->getWidth() - $image->getWidth()) / 100 * $left);
		}
		if(substr($top, -1) === '%') {
			$top = round(($this->getHeight() - $image->getHeight()) / 100 * $top);
		}

		if($opacity === 100) {
			imagecopy(
			$this->getImageResource(), $image->getImageResource(),
			$left, $top, 0, 0, $image->getWidth(), $image->getHeight()
			);
		} elseif ($opacity <> 0) {
			imagecopymerge(
			$this->getImageResource(), $image->getImageResource(),
			$left, $top, 0, 0, $image->getWidth(), $image->getHeight(),
			$opacity
			);
		}
	}

	/**
	 * Resizes the image resource in this instance
	 *
	 * NOTE: The method calculateSize() has been reworked to ensure that the
	 * image is AT LEAST as tall or wide as the specified dimensions. This
	 * method is further modified to crop the resulting image.
	 *
	 * @param mixed $width			width in pixels or percent
	 * @param mixed $height			height in pixels or percent
	 * @param int $flags
	 * @return \Base\Model\Photo
	 */
	public function resize($width, $height, $flags = self::FIT) {
		if($flags & self::EXACT) {
			return $this->resize($width, $height, self::FILL)
			->crop('50%', '50%', $width, $height);
		}

		list($newWidth, $newHeight) = self::calculateSize(
			$this->getWidth(), $this->getHeight(), $width, $height, $flags);

		if(($newWidth !== $this->getWidth()) || ($newHeight !== $this->getHeight())) {
			$temp = self::fromBlank($newWidth, $newHeight, self::rgb(0, 0, 0, 127));
			$newPhoto = $temp->getImageResource();
			imagecopyresampled(
			$newPhoto, $this->getImageResource(),
			0, 0, 0, 0,
			$newWidth, $newHeight, $this->getWidth(), $this->getHeight()
			);
			$this->image = $newPhoto;
		}

		if(($width < 0) || ($height < 0)) {
			$temp = self::fromBlank($newWidth, $newHeight, self::rgb(0, 0, 0, 127));
			$newPhoto = $temp->getImageResource();
			imagecopyresampled(
			$newPhoto, $this->getImageResource(),
			0, 0, ($width < 0 ? $newWidth - 1 : 0), ($height < 0 ? $newHeight - 1 : 0),
			$newWidth, $newHeight,
			($width < 0 ? -$newWidth : $newWidth),
			($height < 0 ? -$newHeight : $newHeight)
			);
			$this->image = $newPhoto;
		}

		$this->crop(0, 0, $width, $height);

		return $this;
	}

	/**
	 * Saves the image resource to the given filename
	 * @param string $filename
	 * @param string $quality
	 * @param string $type
	 * @throws \InvalidArgumentException
	 * @return boolean TRUE on success and FALSE on failure
	 */
	public function save($filename = null, $quality = null, $type = null) {
		if($type == null) {
			switch (strtolower(pathinfo($filename, PATHINFO_EXTENSION))) {
				case 'jpg':
				case 'jpeg':
					$type = self::JPEG;
					break;
				case 'png':
					$type = self::PNG;
					break;
				case 'gif':
					$type = self::GIF;
			}
		}

		switch ($type) {
			case self::JPEG:
				$quality = ($quality == null ? 85 : max(0, min(100, (int) $quality)));
				return imagejpeg($this->getImageResource(), $filename, $quality);
			case self::PNG:
				$quality = ($quality == null ? 9 : max(0, min(9, (int) $quality)));
				return imagepng($this->getImageResource(), $filename, $quality);
			case self::GIF:
				return ($filename == null
				? imagegif($this->getImageResource())
				: imagegif($this->getImageResource(), $filename));
			default:
				throw new \InvalidArgumentException("Unsupported image type.");
		}
	}

	/**
	 * Outputs the image to the browser
	 * @param int $type
	 * @param string $quality
	 * @throws \InvalidArgumentException
	 * @return boolean TRUE on success or FALSE on failure
	 */
	public function send($type = self::JPEG, $quality = null) {
		if(($type !== self::GIF) && ($type !== self::PNG) && ($type !== self::JPEG)) {
			throw new \InvalidArgumentException("Unsupported image type");
		}
		header('Content-Type: ' . image_type_to_mime_type($type));
		return $this->save(null, $quality, $type);
	}

	/**
	 * Sharpens the image
	 */
	public function sharpen() {
		imageconvolution(
		$this->getImageResource(),
		array(array(-1,-1,-1), array(-1,-24,-1), array(-1,-1,-1)),
		16,
		0
		);
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

	/** @see \Application\StdLib\Object::__toString() */
	public function __toString() {
		return 'Photo[id=' . $this->getId()
		. ',albumId=' . $this->getAlbum()->getId()
		. ',fromId=' . $this->getFrom()->getId()
		. ',name=' . $this->getName()
		. ',place=' . $this->getPlace()
		. ',icon=' . $this->getIcon()
		. ',picture=' . $this->getPicture()
		. ',source=' . $this->getSource()
		. ',srcHeight=' . $this->getSrcHeight()
		. ',srcWidth=' . $this->getSrcWidth()
		. ',link=' . $this->getLink()
		. ',position=' . $this->getPosition()
		. ',visibility=' . $this->getVisibility()
		. ',type=' . $this->getType()
		. ',creationDate=' . $this->getCreationDate()->format('Y-m-d H:i:s')
		. ',lastUpdateDate=' . $this->getLastUpdateDate()->format('Y-m-d H:i:s')
		. ']';
	}
}
?>