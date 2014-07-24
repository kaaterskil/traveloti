<?php
/**
 * Traveloti Library
 *
 * @category	Traveloti
 * @package		Traveloti_Application
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Application\View\Helper;

use Application\StdLib\Constants;

use Base\Model\Like;
use Base\Model\Photo;
use Base\Model\TravelLog;
use Base\Model\TravelLogAdmin;
use Base\Model\Traveloti;
use Base\Model\User;
use Zend\Form\View\Helper\AbstractHelper;

/**
 * StreamPhotoSizer Class
 *
 * @author Blair
 */
class NavigationTile extends AbstractHelper {
	const FRIENDS = 'friend';
	const FRIENDS_LARGE = "friends_large";
	const PHOTOS = 'photos';
	const PHOTOS_LARGE = "photos_large";
	const LIKES = 'likes';
	const TRAVEL_LOGS = 'travel_logs';
	const TRAVEL_LOGS_LARGE = 'travel_logs_large';

	public function __invoke(Traveloti $o, $property, $width = null) {
		/* @var $travelLogAdmin TravelLogAdmin */
		/* @var $travelLog TravelLog */

		$xhtml = '';
		switch($property) {
			case self::FRIENDS:
				if($o instanceof User) {
					$data = $o->getAllFriends();
					$xhtml = $this->generateTravelotiImages($data, $width);
				}
				break;
			case self::FRIENDS_LARGE:
				if($o instanceof User) {
					$data = $o->getAllFriends();
					$xhtml = $this->generateLargeTravelotiImages($data, $width);
				}
				break;
			case self::TRAVEL_LOGS:
				if($o instanceof User) {
					$data = array();
					foreach ($o->getAccounts()->getValues() as $travelLogAdmin) {
						array_push($data, $travelLogAdmin->getTravelLog());
					}
					$xhtml = $this->generateTravelotiImages($data, $width);
				}
				break;
			case self::TRAVEL_LOGS_LARGE:
				if($o instanceof User) {
					$data = array();
					foreach ($o->getAccounts()->getValues() as $travelLogAdmin) {
						array_push($data, $travelLogAdmin->getTravelLog());
					}
					$xhtml = $this->generateLargeTravelotiImages($data, $width);
				}
				break;
			case self::PHOTOS:
				$data = $o->getPhotos();
				$xhtml = $this->generatePhotoImages($data, $o, $width);
				break;
			case self::PHOTOS_LARGE:
				$data = $o->getPhotos();
				$xhtml = $this->generateLargePhotoImages($data, $o, $width);
				break;
			case self::LIKES:
				//TODO
		}
		return $xhtml;
	}

	private function generatePhotoImages(array $photos, Traveloti $traveloti, $width = null) {
		/* @var $photo Photo */

		if($width == null) {
			$width = 30;
		}

		$xhtml = '';
		$count = count($photos) > 6 ? 6 : count($photos);
		for($i = 0; $i < $count; $i++) {
			$photo = $photos[$i];

			$username = $traveloti->getUsername();
			$icon = ($photo->getIcon() ? $photo->getIcon() : $photo->getSource());
			$src = $this->getView()->basePath('/content/' . $username . '/' . $icon);
			$caption = $photo->getName() ? $photo->getName() : $traveloti->getDisplayName();
			$xhtml .= $this->getView()->htmlImg($username, $src, array(
				'alt' => $caption,
				'height' => $width,
				'width' => $width,
				'class' => 'nav-tile img',
			)) . "\n";
		}
		return $xhtml;
	}

	private function generateLargePhotoImages(array $photos, Traveloti $traveloti, $width = null) {
		/* @var $photo Photo */

		if($width == null) {
			$width = 30;
		}

		$xhtml = '';
		$count = count($photos) > 8 ? 8 : count($photos);
		for($i = 0; $i < $count; $i++) {
			$photo = $photos[$i];

			$username = $traveloti->getUsername();
			$icon = $photo->getSource();
			$src = $this->getView()->basePath('/content/' . $username . '/' . $icon);
			$caption = $photo->getName() ? $photo->getName() : $traveloti->getDisplayName();
			$photoAttribs = $this->getView()->photoSizer($photo, $width, false, true);

			$xhtml .= '<a class="timeline-photo-box" href="' . $src . '" title="' . $caption . '" rel="shadowbox">' . "\n";
			$xhtml .= '<div class="photo-list-wrapper">' . "\n";
			$xhtml .= $this->getView()->htmlImg($username, $src, array(
				'alt' => $caption,
				'width' => $photoAttribs['width'],
				'height' => $photoAttribs['height'],
				'class' => 'nav-tile img',
			)) . "\n";
			$xhtml .= '</div>' . "\n";
			$xhtml .= '</a>' . "\n";
		}
		return $xhtml;
	}

	private function generateTravelotiImages(array $data, $width = null) {
		/* @var $traveloti Traveloti */

		if($width == null) {
			$width = 30;
		}

		$xhtml = '';
		$count = count($data) > 6 ? 6 : count($data);
		for($i = 0; $i < $count; $i++) {
			$traveloti = $data[$i];
			$id = $traveloti->getUsername();
			if($width > 50) {
				if($traveloti instanceof TravelLog) {
					$icon = ($traveloti->getCoverPicture()
						? $traveloti->getCoverPicture()->getSource() : '');
				} else {
					$icon = $traveloti->getPicture()
					? $traveloti->getPicture()->getIcon()
					: Constants::DEFAULT_THUMB_3232;
				}
			} else {
				if($traveloti instanceof TravelLog) {
					$icon = ($traveloti->getCoverPicture()
						? $traveloti->getCoverPicture()->getSource() : '');
				} else {
					$icon = $traveloti->getPicture()
					? $traveloti->getPicture()->getSource()
					: Constants::DEFAULT_THUMB_5050;
				}
			}
			$src = $this->getView()->basePath('/content/' . $id . '/' . $icon);
			$xhtml .= $this->getView()->htmlImg($id, $src, array(
				'alt' => $id,
				'height' => $width,
				'width' => $width,
				'class' => 'nav-tile img'
			)) . "\n";
		}
		return $xhtml;
	}

	private function generateLargeTravelotiImages(array $data, $width = null) {
		/* @var $traveloti Traveloti */

		if($width == null) {
			$width = 30;
		}

		$xhtml = '';
		$count = count($data) > 8 ? 8 : count($data);
		for($i = 0; $i < $count; $i++) {
			$traveloti = $data[$i];
			$id = $traveloti->getUsername();
			$displayName = $traveloti->getDisplayName();
			if($width > 50) {
				if($traveloti instanceof TravelLog) {
					$icon = ($traveloti->getCoverPicture()
						? $traveloti->getCoverPicture()->getSource() : '');
				} else {
					$icon = $traveloti->getPicture()
					? $traveloti->getPicture()->getIcon()
					: Constants::DEFAULT_THUMB_3232;
				}
			} else {
				if($traveloti instanceof TravelLog) {
					$icon = ($traveloti->getCoverPicture()
						? $traveloti->getCoverPicture()->getSource() : '');
				} else {
					$icon = $traveloti->getPicture()
					? $traveloti->getPicture()->getSource()
					: Constants::DEFAULT_THUMB_5050;
				}
			}
			$src = $this->getView()->basePath('/content/' . $id . '/' . $icon);
			$href = $this->getView()->url('timeline', array('id' => $id));

			$xhtml .= '<div class="connection-list-wrapper">' . "\n";
			$xhtml .= '<a class="timeline-connection-box" href="' . $href
			. '" title="' . $displayName
			. '">' . "\n";
			$xhtml .= $this->getView()->htmlImg($id, $src, array(
				'alt' => $id,
				'height' => $width,
				'width' => $width,
				'class' => 'nav-tile img'
			)) . "\n";
			$xhtml .= '<div class="connection-name"><div class="margin-small">' . $displayName . '</div></div>' . "\n";
			$xhtml .= '</a>' . "\n";
			$xhtml .= '</div>' . "\n";
		}
		return $xhtml;
	}
}
?>