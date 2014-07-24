<?php
/**
 * Traveloti Library
 *
 * @category	Traveloti
 * @package		Traveloti_Base
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Base\Controller;

use Application\StdLib\Constants;
use Base\Form\CoverPictureForm;
use Base\Form\PhotoUploadForm;
use Base\Form\StatusForm;
use Base\Model\Album;
use Base\Model\Photo;
use Base\Model\Post;
use Base\Model\Status;
use Base\Model\TravelLog;
use Base\Model\TravelLogAdmin;
use Base\Model\Traveloti;
use Base\Model\User;
use Zend\View\Model\ViewModel;

/**
 * TimelineController Class
 *
 * @author Blair
 */
class TimelineController extends AbstractBaseController {

	/**
	 * Index page
	 * @return \Zend\View\Model\ViewModel
	 */
	public function indexAction() {
		/* @var $user User */
		/* @var $friend User */
		/* @var $admin TravelLogAdmin */

		if(!$this->loginTest()) {
			return $this->redirect()->toRoute('home');
		}

		$em = $this->getEntityManager();
		$userId = $this->zfcUserAuthentication()->getIdentity()->getId();
		$user = $em->getRepository('Base\Model\User')->find($userId);

		// Test for friend
		$isUserAdmin = true;
		$identity = $this->params('id');
		if(($identity != null) && ($identity != $user->getUsername())) {
			$traveloti = $em->getRepository('Base\Model\Traveloti')->findOneBy(array('username' => $identity));
			if($traveloti == null) {
				$this->getResponse()->setStatusCode(404);
				return;
			}
			// Test if the Traveloti is a TravelLog and if the User is an administrator
			$isUserAdmin = false;
			if($traveloti instanceof TravelLog) {
				$admins = $traveloti->getAdministrators()->getValues();
				foreach($admins as $admin) {
					if($admin->getUser()->getId() == $userId) {
						$isUserAdmin = true;
						break;
					}
				}
			}
		} else {
			$traveloti = $user;
		}

		// Construct pagelets
		$pageletComposer = $this->getComposerViewModel($user, $traveloti);
		$pageletFeed = $this->getFeedViewModel($traveloti);
		$pageletTimelineNav = $this->getTimelineNavigationViewModel($traveloti);
		$pageletSidebar = $this->getChatSidebarViewModel($user);

		$pageletTimeline = new ViewModel(array(
			'isUserAdmin' => $isUserAdmin,
			'traveloti' => $traveloti,
		));
		$pageletTimeline->setTemplate('base/base/timeline');
		$pageletTimeline->addChild($pageletComposer, 'pageletComposer');
		$pageletTimeline->addChild($pageletFeed, 'pageletFeed');
		$pageletTimeline->addChild($pageletTimelineNav, 'pageletTimelineNavigation');

		// Construct main container
		$view = new ViewModel(array());
		$view->setTemplate('base/base/index_timeline');
		$view->addChild($pageletTimeline);
		$view->addChild($pageletSidebar, 'pageletSidebar');

		return $this->getView('full-page')->addChild($view);
	}

	/**
	 * Connections page
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function travelotiAction(){
		if(!$this->loginTest()) {
			return $this->redirect()->toRoute('home');
		}

		$em = $this->getEntityManager();
		$userId = $this->zfcUserAuthentication()->getIdentity()->getId();
		$user = $em->getRepository('Base\Model\User')->find($userId);

		// Test for friend
		$isUserAdmin = true;
		$identity = $this->params('id');
		if(($identity != null) && ($identity != $user->getUsername())) {
			$traveloti = $em->getRepository('Base\Model\Traveloti')->findOneBy(array('username' => $identity));
			if($traveloti == null) {
				$this->getResponse()->setStatusCode(404);
				return;
			}
			// Test if the Traveloti is a TravelLog and if the user is an administrator
			$isUserAdmin = false;
			if($traveloti instanceof TravelLog) {
				foreach ($traveloti->getAdministrators()->getValues() as $admin) {
					if($admin->equals($user)) {
						$isUserAdmin = true;
						break;
					}
				}
			}
		} else {
			$traveloti = $user;
		}

		$pageletConnections = $this->getConnectionsViewModel($traveloti);
		$pageletSidebar = $this->getChatSidebarViewModel($user);

		// Construct main container
		$view = new ViewModel(array());
		$view->setTemplate('base/base/index_timeline');
		$view->addChild($pageletConnections);
		$view->addChild($pageletSidebar, 'pageletSidebar');

		return $this->getView('full-page')->addChild($view);
	}

	/**
	 * Photos page
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function photosAction(){
		if(!$this->loginTest()) {
			return $this->redirect()->toRoute('home');
		}

		$em = $this->getEntityManager();
		$userId = $this->zfcUserAuthentication()->getIdentity()->getId();
		$user = $em->getRepository('Base\Model\User')->find($userId);

		// Test for friend
		$isUserAdmin = true;
		$identity = $this->params('id');
		if(($identity != null) && ($identity != $user->getUsername())) {
			$traveloti = $em->getRepository('Base\Model\Traveloti')->findOneBy(array('username' => $identity));
			if($traveloti == null) {
				$this->getResponse()->setStatusCode(404);
				return;
			}
			// Test if the Traveloti is a TravelLog and if the user is an administrator
			$isUserAdmin = false;
			if($traveloti instanceof TravelLog) {
				foreach ($traveloti->getAdministrators()->getValues() as $admin) {
					if($admin->equals($user)) {
						$isUserAdmin = true;
						break;
					}
				}
			}
		} else {
			$traveloti = $user;
		}

		$pageletPhotos = $this->getPhotosViewModel($traveloti);
		$pageletSidebar = $this->getChatSidebarViewModel($user);

		// Construct main container
		$view = new ViewModel(array());
		$view->setTemplate('base/base/index_timeline');
		$view->addChild($pageletPhotos);
		$view->addChild($pageletSidebar, 'pageletSidebar');

		return $this->getView('full-page')->addChild($view);
	}

	/**
	 * Album page
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function albumAction(){
		/* @var $album Album */

		if(!$this->loginTest()) {
			return $this->redirect()->toRoute('home');
		}

		$em = $this->getEntityManager();
		$userId = $this->zfcUserAuthentication()->getIdentity()->getId();
		$user = $em->getRepository('Base\Model\User')->find($userId);

		// Test for friend
		$isUserAdmin = true;
		$identity = $this->params('id');
		if(($identity != null) && ($identity != $user->getUsername())) {
			$traveloti = $em->getRepository('Base\Model\Traveloti')->findOneBy(array('username' => $identity));
			if($traveloti == null) {
				$this->getResponse()->setStatusCode(404);
				return;
			}
			// Test if the Traveloti is a TravelLog and if the user is an administrator
			$isUserAdmin = false;
			if($traveloti instanceof TravelLog) {
				foreach ($traveloti->getAdministrators()->getValues() as $admin) {
					if($admin->getUser()->getId() == $user->getId()) {
						$isUserAdmin = true;
						break;
					}
				}
			}
		} else {
			$traveloti = $user;
		}

		// Fetch album photos
		$albumId = $this->params('set');
		$album = $traveloti->getAlbumById($albumId);

		// Construct main container
		$pageletPhotos = $this->getAlbumViewModel($album, $traveloti);
		$pageletSidebar = $this->getChatSidebarViewModel($user);
		$view = new ViewModel(array());
		$view->setTemplate('base/base/index_timeline');
		$view->addChild($pageletPhotos);
		$view->addChild($pageletSidebar, 'pageletSidebar');

		return $this->getView('full-page')->addChild($view);
	}

	/**
	 * Travel Log List page
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function travelLogsAction(){
		die('travelLogAction() method not implemented');
	}

	/**
	 * Updates a User's profile picture
	 */
	public function editProfilePictureAction() {
		/* @var $user User */
		/* @var $album Album */
		/* @var $photo Photo */

		if(!$this->loginTest()) {
			return $this->redirect()->toRoute('home');
		}

		$em = $this->getEntityManager();

		// Fetch user
		$userId = $this->zfcUserAuthentication()->getIdentity()->getId();
		$user = $em->getRepository('Base\Model\User')->find($userId);

		$request = $this->getRequest();
		if($request->isPost() && (isset($_FILES['picture']) && count($_FILES['picture']))) {
			$now = new \DateTime();

			// Fetch and update Album
			$album = $user->getAlbum(Constants::ALBUM_PROFILE);
			$album->setLastUpdateDate($now);

			// Fetch data
			$src = $_FILES['picture']['name'];
			$tmp_name = $_FILES['picture']['tmp_name'];

			// Extract filename and extension
			$pathinfo = pathinfo($src);
			$filename = $pathinfo['filename'];
			$ext = $pathinfo['extension'];
			$basename = dirname(dirname(dirname(dirname(dirname(__DIR__)))))
			. '/public/content/' . $user->getUsername();

			// Resize and save the image for pictures
			$temp = Photo::fromFile($tmp_name);
			$pict_filename = $filename . Constants::PICTURE_FILENAME_SUFFIX . '.' . $ext;
			$temp->resize(Constants::PICTURE_SIZE, Constants::PICTURE_SIZE);
			$temp->save($basename . '/' . $pict_filename);

			// Resize and save the image for thumbnails
			$thumb_filename = $filename . Constants::THUMBNAIL_FILENAME_SUFFIX . '.' . $ext;
			$temp->resize(Constants::THUMBNAIL_SIZE, Constants::THUMBNAIL_SIZE);
			$temp->save($basename . '/' . $thumb_filename);

			// Create image instance
			$photo = Photo::fromFile($tmp_name);
			$photo->setAlbum($album);
			$photo->setCreationDate($now);
			$photo->setFrom($user);
			$photo->setIcon($thumb_filename);
			$photo->setLastUpdateDate($now);
			$photo->setPicture($pict_filename);
			$photo->setSource($src);
			$photo->setSrcHeight($photo->getHeight());
			$photo->setSrcWidth($photo->getWidth());
			$photo->setVisibility(Constants::PRIVACY_FRIENDS);

			// Save the image to the user's upload directory
			$filepath = $basename . '/' . $photo->getSource();
			$photo->save($filepath);

			// Add the image to the Album and the User
			$album->addPhoto($photo);
			$user->setPicture($photo);

			// Persist the image
			$em->persist($photo);
			$em->flush();
		}

		return $this->redirect()->toRoute(
			'timeline', array('id' => $user->getUsername()));
	}

	/**
	 * Updates a Traveloti cover picture
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function addCoverAction() {
		/* @var $user User */
		/* @var $traveloti Traveloti */
		/* @var $album Album */
		/* @var $photo Photo */
		/* @var $admin TravelLogAdmin */

		if(!$this->loginTest()) {
			return $this->redirect()->toRoute('home');
		}

		// Fetch user
		$em = $this->getEntityManager();
		$userId = $this->zfcUserAuthentication()->getIdentity()->getId();
		$user = $em->getRepository('Base\Model\User')->find($userId);

		// Test for TravelLog
		$identity = $this->params('id');
		if(($identity != null) && ($identity != $user->getUsername())) {
			$traveloti = $em->getRepository('Base\Model\Traveloti')->findOneBy(array('username' => $identity));
			if($traveloti == null) {
				$this->getResponse()->setStatusCode(404);
				return;
			}
			// Test if the Traveloti is a TravelLog and if the user is an administrator
			$isUserAdmin = false;
			if($traveloti instanceof TravelLog) {
				foreach ($traveloti->getAdministrators()->getValues() as $admin) {
					if($admin->getUser()->getId() == $userId) {
						$isUserAdmin = true;
						break;
					}
				}
			}
			if(!$isUserAdmin) {
				return $this->redirect()->toRoute('home');
			}
		} else {
			$traveloti = $user;
		}

		// Fetch request
		$request = $this->getRequest();
		if($request->isPost() && (isset($_FILES['picture']) && count($_FILES['picture']))) {
			$now = new \DateTime();

			// Test for new album
			$album = $traveloti->getAlbum(Constants::ALBUM_UNDEFINED);
			if(null == $album) {
				// Create new Album
				$album = new Album();
				$album->setCreationDate($now);
				$album->setName(Constants::ALBUM_UNDEFINED);
				$album->setVisibility(Constants::PRIVACY_FRIENDS);
				$traveloti->addAlbum($album);

				// Persist Album
				$em->persist($album);
			}
			$album->setLastUpdateDate($now);

			// Fetch image
			$src = $_FILES['picture']['name'];
			$tmp_name = $_FILES['picture']['tmp_name'];

			// Extract filename and extension
			$pathinfo = pathinfo($src);
			$filename = $pathinfo['filename'];
			$ext = $pathinfo['extension'];
			$basename = dirname(dirname(dirname(dirname(dirname(__DIR__)))))
			. '/public/content/' . $traveloti->getUsername();

			// Resize and save the image for pictures
			$temp = Photo::fromFile($tmp_name);
			$pict_filename = $filename . Constants::PICTURE_FILENAME_SUFFIX . '.' . $ext;
			$temp->resize(Constants::PICTURE_SIZE, Constants::PICTURE_SIZE);
			$temp->save($basename . '/' . $pict_filename);

			// Resize and save the image for thumbnails
			$thumb_filename = $filename . Constants::THUMBNAIL_FILENAME_SUFFIX . '.' . $ext;
			$temp->resize(Constants::THUMBNAIL_SIZE, Constants::THUMBNAIL_SIZE);
			$temp->save($basename . '/' . $thumb_filename);

			// Create image instance
			$photo = Photo::fromFile($tmp_name);
			$photo->resize(700, 315);

			// Set the image values
			$photo->setAlbum($album);
			$photo->setCreationDate($now);
			$photo->setFrom($traveloti);
			$photo->setIcon($thumb_filename);
			$photo->setLastUpdateDate($now);
			$photo->setPicture($pict_filename);
			$photo->setSource($src);
			$photo->setSrcHeight($photo->getHeight());
			$photo->setSrcWidth($photo->getWidth());
			$photo->setVisibility(Constants::PRIVACY_FRIENDS);

			// Save the image to the user's upload directory
			$filepath = $basename . '/' . $photo->getSource();
			$photo->save($filepath);

			// Add the image to the album and the user
			$album->addPhoto($photo);
			$traveloti->setCoverPicture($photo);

			// Persist the image
			$em->persist($photo);
			$em->flush();
		}

		return $this->redirect()->toRoute(
			'timeline', array('id' => $traveloti->getUsername()));
	}

	/**
	 * Adds a Status/Photo
	 */
	public function statusUpdateAction() {
		/* @var $user User */
		/* @var $traveloti Traveloti */
		/* @var $status Status */
		/* @var $album Album */
		/* @var $photo Photo */
		/* @var $post Post */

		if(!$this->loginTest()) {
			return $this->redirect()->toRoute('home');
		}

		// Fetch user
		$em = $this->getEntityManager();
		$userId = $this->zfcUserAuthentication()->getIdentity()->getId();
		$user = $em->getRepository('Base\Model\User')->find($userId);

		// Test for TravelLog
		$identity = $this->params('id');
		if(($identity != null) && ($identity != $user->getUsername())) {
			$traveloti = $em->getRepository('Base\Model\Traveloti')->findOneBy(array('username' => $identity));
			if($traveloti == null) {
				$this->getResponse()->setStatusCode(404);
				return;
			}
		} else {
			$traveloti = $user;
		}

		$request = $this->getRequest();
		if($request->isPost()) {
			$form = new StatusForm();
			$form->setData($request->getPost());

			if($form->isValid()) {
				$now = new \DateTime();

				if(isset($_FILES['picture']) && (isset($_FILES['picture']['name']) && $_FILES['picture']['name'] != '')) {
					// Fetch the Album
					$album = $traveloti->getAlbum(Constants::ALBUM_UNDEFINED);
					if($album == null) {
						// Create the Album
						$album = new Album();
						$album->setCreationDate($now);
						$album->setUser($traveloti);
						
						$traveloti->addAlbum($album);
						$em->persist($album);
					}
					$album->setLastUpdateDate($now);
					$album->setVisibility(Constants::PRIVACY_FRIENDS);

					// Fetch image
					$src = $_FILES['picture']['name'];
					$tmp_name = $_FILES['picture']['tmp_name'];

					// Extract filename and extension
					$pathinfo = pathinfo($src);
					$filename = $pathinfo['filename'];
					$ext = $pathinfo['extension'];
					$basename = dirname(dirname(dirname(dirname(dirname(__DIR__)))))
					. '/public/content/' . $traveloti->getUsername();

					// Resize and save the Photo for pictures
					$temp = Photo::fromFile($tmp_name);
					$pict_filename = $filename . Constants::PICTURE_FILENAME_SUFFIX . '.' . $ext;
					$temp->resize(Constants::PICTURE_SIZE, Constants::PICTURE_SIZE);
					$temp->save($basename . '/' . $pict_filename);

					// Resize and save the Photo for thumbnails
					$thumb_filename = $filename . Constants::THUMBNAIL_FILENAME_SUFFIX . '.' . $ext;
					$temp->resize(Constants::THUMBNAIL_SIZE, Constants::THUMBNAIL_SIZE);
					$temp->save($basename . '/' . $thumb_filename);

					// Create Photo instance
					$photo = Photo::fromFile($tmp_name);
					$photo->setAlbum($album);
					$photo->setCreationDate($now);
					$photo->setFrom($traveloti);
					$photo->setIcon($thumb_filename);
					$photo->setLastUpdateDate($now);
					$photo->setPicture($pict_filename);
					$photo->setPlace($form->get($form::ELEM_LOCATION)->getValue());
					$photo->setSource($src);
					$photo->setSrcHeight($photo->getHeight());
					$photo->setSrcWidth($photo->getWidth());
					$photo->setVisibility($form->get($form::ELEM_AUDIENCE)->getValue());

					// Save the Photo to the Traveloti's upload directory
					$filepath = $basename . '/' . $photo->getSource();
					$photo->save($filepath);

					// Add the Photo to the Album
					$album->addPhoto($photo);

					// Persist the Photo
					$em->persist($photo);
					$post = $photo;
				} else {
					// Create new Status
					$status = new Status();
					$status->setCreationDate($now);
					$status->setFrom($user);
					$status->setLastUpdateDate($now);
					$status->setMessage($form->get($form::ELEM_MESSAGE)->getValue());
					$status->setPlace($form->get($form::ELEM_LOCATION)->getValue());
					$status->setTo($traveloti);
					$status->setVisibility($form->get($form::ELEM_AUDIENCE)->getValue());
					
					// Add the Status to the Traveloti
					$user->addStatus($status);
					$traveloti->addFeed($status);
					
					// Persist the Status
					$em->persist($status);
					$post = $status;
				}

				// Test if Status is on another Traveloti timeline
				if(($traveloti->getId() != $user->getId()) && !$isUserAdmin) {
					// Create Notification
					$n = $this->createNotification($user, $traveloti, $post);
					$traveloti->addNotification($n);
					$em->persist($n);
				}
				
				$em->flush();
			}
		}

		return $this->redirect()->toRoute(
			'timeline', array('id' => $traveloti->getUsername()));
	}

	/**
	 * Adds a Photo
	 */
	public function photoUploadAction() {
		/* @var $user User */
		/* @var $traveloti Traveloti */
		/* @var $album Album */
		/* @var $photo Photo */
		/* @var $admin TravelLogAdmin */

		if(!$this->loginTest()) {
			return $this->redirect()->toRoute('home');
		}

		// Fetch user
		$em = $this->getEntityManager();
		$userId = $this->zfcUserAuthentication()->getIdentity()->getId();
		$user = $em->getRepository('Base\Model\User')->find($userId);

		// Test for TravelLog
		$identity = $this->params('id');
		if(($identity != null) && ($identity != $user->getUsername())) {
			$traveloti = $em->getRepository('Base\Model\Traveloti')->findOneBy(array('username' => $identity));
			if($traveloti == null) {
				$this->getResponse()->setStatusCode(404);
				return;
			}
			// Test if the Traveloti is a TravelLog and if the user is an administrator
			$isUserAdmin = false;
			if($traveloti instanceof TravelLog) {
				foreach ($traveloti->getAdministrators()->getValues() as $admin) {
					if($admin->getUser()->getId() == $userId) {
						$isUserAdmin = true;
						break;
					}
				}
			}
			if(!$isUserAdmin) {
				return $this->redirect()->toRoute('home');
			}
		} else {
			$traveloti = $user;
		}

		$request = $this->getRequest();
		if($request->isPost() && (isset($_FILES['picture']) && count($_FILES['picture']))) {
			$form = new PhotoUploadForm();
			$form->setData($request->getPost());
			if($form->isValid()) {
				$now = new \DateTime();

				// Test if album exists
				$albumName = $form->get($form::ELEM_ALBUMNAME)->getValue();
				$album = $traveloti->getAlbum($albumName);
				if($album == null) {
					// Create new Album
					$album = new Album();
					$album->setCreationDate($now);
					$album->setName($albumName);
					$album->setUser($traveloti);
					$traveloti->addAlbum($album);

					// Persist Album
					$em->persist($album);
				}
				$album->setDescription($form->get($form::ELEM_ALBUMDESCRIPTION)->getValue());
				$album->setLastUpdateDate($now);
				$album->setLocation($form->get($form::ELEM_ALBUMLOCATION)->getValue());
				$album->setVisibility($form->get($form::ELEM_ALBUMPRIVACY)->getValue());

				// Fetch image
				$src = $_FILES['picture']['name'];
				$tmp_name = $_FILES['picture']['tmp_name'];

				// Extract filename and extension
				$pathinfo = pathinfo($src);
				$filename = $pathinfo['filename'];
				$ext = $pathinfo['extension'];
				$basename = dirname(dirname(dirname(dirname(dirname(__DIR__)))))
				. '/public/content/' . $traveloti->getUsername();

				// Resize and save the image for pictures
				$temp = Photo::fromFile($tmp_name);
				$pict_filename = $filename . Constants::PICTURE_FILENAME_SUFFIX . '.' . $ext;
				$temp->resize(Constants::PICTURE_SIZE, Constants::PICTURE_SIZE);
				$temp->save($basename . '/' . $pict_filename);

				// Resize and save the image for thumbnails
				$thumb_filename = $filename . Constants::THUMBNAIL_FILENAME_SUFFIX . '.' . $ext;
				$temp->resize(Constants::THUMBNAIL_SIZE, Constants::THUMBNAIL_SIZE);
				$temp->save($basename . '/' . $thumb_filename);

				// Create image instance
				$photo = Photo::fromFile($tmp_name);
				$photo->setAlbum($album);
				$photo->setCreationDate($now);
				$photo->setFrom($traveloti);
				$photo->setIcon($thumb_filename);
				$photo->setLastUpdateDate($now);
				$photo->setName($form->get($form::ELEM_CAPTION)->getValue());
				$photo->setPicture($pict_filename);
				$photo->setPlace($form->get($form::ELEM_LOCATION)->getValue());
				$photo->setSource($src);
				$photo->setSrcHeight($photo->getHeight());
				$photo->setSrcWidth($photo->getWidth());
				$photo->setVisibility($form->get($form::ELEM_ALBUMPRIVACY)->getValue());

				// Save the image to the user's upload directory
				$filepath = $basename . '/' . $photo->getSource();
				$photo->save($filepath);

				// Add the image to the album
				$album->addPhoto($photo);

				// Persist the image
				$em->persist($photo);
				$em->flush();
			}
		}

		return $this->redirect()->toRoute(
			'timeline', array('action' => 'photos', 'id' => $traveloti->getUsername()));
	}

	/**
	 * Returns the specified Album photos
	 *
	 * @param Traveloti $traveloti
	 * @return \Zend\View\Model\ViewModel
	 */
	private function getAlbumViewModel(Album $album, Traveloti $traveloti) {
		$view = new ViewModel(array(
			'photos' => $album->getPhotos()->getValues(),
			'traveloti' => $traveloti,
			'album' => $album,
		));
		$view->setTemplate('base/timeline/photos');
		return $view;
	}

	/**
	 * Returns the current User's connections
	 *
	 * @param User $user
	 * @return \Zend\View\Model\ViewModel
	 */
	private function getChatSidebarViewModel(User $user) {
		$friends = $user->getAllFriends();

		$view = new ViewModel(array(
			'friends' => $friends,
		));
		$view->setTemplate('base/base/chat_roster');
		return $view;
	}

	/**
	 * Returns the specified User's connections
	 *
	 * @param User $user
	 * @return \Zend\View\Model\ViewModel
	 */
	private function getConnectionsViewModel(User $user) {
		$connections = $user->getAllFriends();

		$view = new ViewModel(array(
			'connections' => $connections,
			'traveloti' => $user,
		));
		$view->setTemplate('base/timeline/connections');
		return $view;
	}

	/**
	 * Returns the specified Traveloti's Posts (Status, Photo, Link,
	 * Comment, or Offer) in reverse chronological order.
	 *
	 * @param Traveloti $traveloti
	 * @return \Zend\View\Model\ViewModel
	 */
	private function getFeedViewModel(Traveloti $traveloti) {
		$feed = $traveloti->getPosts();

		$view = new ViewModel(array('feed' => $feed, 'width' => 'timeline'));
		$view->setTemplate('base/base/home_stream');
		return $view;
	}

	/**
	 * Returns the specified Traveloti's photos
	 *
	 * @param Traveloti $traveloti
	 * @return \Zend\View\Model\ViewModel
	 */
	private function getPhotosViewModel(Traveloti $traveloti) {
		$photos = $traveloti->getPhotos();

		$view = new ViewModel(array(
			'photos' => $photos,
			'traveloti' => $traveloti,
			'album' => null,
		));
		$view->setTemplate('base/timeline/photos');
		return $view;
	}

	/**
	 * Returns the specified Traveloti's timeline navigation bar
	 *
	 * @param Traveloti $traveloti
	 * @return \Zend\View\Model\ViewModel
	 */
	private function getTimelineNavigationViewModel(Traveloti $traveloti) {
		$view = new ViewModel(array('traveloti' => $traveloti));
		$view->setTemplate('base/base/timeline_navigation');
		return $view;
	}
}
?>