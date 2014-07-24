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
use Application\StdLib\MimeTypeDetector;

use Base\Form\CombinedAlbumImageForm;
use Base\Form\CommentForm;
use Base\Form\InlineImageForm;
use Base\Form\StatusForm;
use Base\Form\TravelLogForm;

use Base\Model\Album;
use Base\Model\Comment;
use Base\Model\Like;
use Base\Model\Notifiable;
use Base\Model\Notification;
use Base\Model\NotifierText;
use Base\Model\Photo;
use Base\Model\Status;
use Base\Model\TravelLog;
use Base\Model\TravelLogAdmin;
use Base\Model\Traveloti;
use Base\Model\User;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\SequenceGenerator;
use Doctrine\ORM\Query\ResultSetMapping;
use Zend\View\Model\ViewModel;

/**
 * BaseController Class
 *
 * @author Blair
 */
class BaseController extends AbstractBaseController {

	/**
	 * Index page
	 * @return \Zend\View\Model\ViewModel
	 */
	public function indexAction() {
		/* @var $user User */
		/* @var $friend User */

		$em = $this->getEntityManager();

		// Test for login
		if(!$this->loginTest()->test()) {
			return $this->redirect()->toRoute('home');
		}

		$userId = $this->zfcUserAuthentication()->getIdentity()->getId();
		$user = $em->getRepository('Base\Model\User')->find($userId);

		// Test for friend
		$identity = $this->params('id');
		if(($identity != null) && ($identity != $user->getUsername())) {
			$friend = $em->getRepository('Base\Model\User')->findOneBy(array('username' => $identity));
			if($friend == null) {
				$this->getResponse()->setStatusCode(404);
				return;
			}
		} else {
			$friend = $user;
		}

		// Test if user has a profile album
		if(!$user->getProfileAlbum()) {
			$this->createProfileAlbum($user);
		}
		// Test if we need to create a default headshot
		if(($user->getPicture() == null) && !$user->getHasSilhouette()) {
			$this->createDefaultHeadshots($user);
		}

		// Construct pagelets
		$welcomeBox = $this->getWelcomeBoxViewModel();
		$navigation = $this->getSidebarNavViewModel();
		$composer = $this->getComposerViewModel($user);
		$homeStream = $this->getHomeStreamViewModel($friend);

		// Construct main container
		$view = new ViewModel();
		$view->setTemplate('base/base/index');
		$view->addChild($welcomeBox, 'pageletWelcomeBox');
		$view->addChild($navigation, 'pageletNavigation');
		$view->addChild($composer, 'pageletComposer');
		$view->addChild($homeStream, 'pageletHomeStream');

		return $this->getView('left-column')->addChild($view);
	}

	/**
	 * Logout - forward to ZfcUser for management
	 */
	public function logoutAction() {
		//Reset the Openfire cookies
		$nickname = new \Zend\Http\Header\SetCookie();
		$nickname->setName('nickname');
		$nickname->setValue('');
		$nickname->setPath('/');
		$nickname->setExpires(-1);
		$this->getResponse()->getHeaders()->addHeader($nickname);

		$unique0 = new \Zend\Http\Header\SetCookie();
		$unique0->setName('unique-10000000');
		$unique0->setValue('');
		$unique0->setPath('/');
		$unique0->setExpires(-1);
		$this->getResponse()->getHeaders()->addHeader($unique0);

		$unique = new \Zend\Http\Header\SetCookie();
		$unique->setName('unique');
		$unique->setValue('');
		$unique->setPath('/');
		$unique->setExpires(-1);
		$this->getResponse()->getHeaders()->addHeader($unique);

		// Forward to the ZfcUSer logout action
		return $this->forward()->dispatch('zfcuser', array('action' => 'logout'));
	}

	/**
	 * Wizard to create a Travel Log
	 */
	public function createLogAction() {
		$em = $this->getEntityManager();

		// Test for login
		if(!$this->loginTest()->test()) {
			return $this->redirect()->toRoute('home');
		}
		$userId = $this->zfcUserAuthentication()->getIdentity()->getId();
		$user = $em->getRepository('Base\Model\User')->find($userId);

		$form = new TravelLogForm();

		$request = $this->getRequest();
		if($request->isPost()) {
			$form->setData($request->getPost());
			if($form->isValid()) {
				$now = new \DateTime();

				// Fetch the next insert id from the stored function
				// This is required by Doctrine for Class Table Inheritance
				$rsm = new ResultSetMapping();
				$rsm->addScalarResult('hash', 'hash');
				$q = $em->createNativeQuery('select sf_next_insert_id("traveloti") as hash', $rsm);
				$r = $q->getResult()[0];

				// Create the TravelLog
				$log = new TravelLog();
				$log->setId($r['hash']);
				$log->setAbout($form->get('about')->getValue());
				$log->setCreationDate($now);
				$log->setDescription($form->get('description')->getValue());
				$log->setGeneralInfo($form->get('general_info')->getValue());
				$log->setLastUpdateDate($now);
				$log->setLocation($form->get('location')->getValue());
				$log->setName($form->get('name')->getValue());
				$log->setUsername($log->getUsername());

				// Create the TravelLogAdmin
				$tla = new TravelLogAdmin();
				$tla->setCreationDate($now);
				$tla->setLastUpdateDate($now);
				$tla->setTravelLog($log);
				$tla->setUser($user);

				// Add the TravelLogAdmin to the User
				$user->addTravelLogAdmin($tla);

				// Persist the objects
				$em->persist($log);
				$em->persist($tla);
				$em->flush();

				if($_FILES['cover_picture']) {
					// Create album
					$album = new Album();
					$album->setCreationDate($now);
					$album->setLastUpdateDate($now);
					$album->setLocation($form->get('location')->getValue());
					$album->setName($form->get('name')->getValue());
					$album->setUser($log);
					$album->setVisibility(Constants::PRIVACY_FRIENDS);
					$em->persist($album);

					// Create image instance
					$filename = $_FILES['cover_picture']['name'];
					$tmp_name = $_FILES['cover_picture']['tmp_name'];
					$photo = Photo::fromFile($tmp_name);
					$photo->setCreationDate($now);
					$photo->setFrom($log);
					$photo->setLastUpdateDate($now);
					$photo->setSource($filename);
					
					// Extract filename and extension
					$pathinfo = pathinfo($photo->getSource());
					$filename = $pathinfo['filename'];
					$ext = $pathinfo['extension'];
					$basename = dirname(dirname(dirname(dirname(dirname(__DIR__)))))
					. '/public/content/' . $log->getUsername();
					if(!file_exists($basename)) {
						mkdir($basename);
					}
					// Resize and save the image for pictures
					$temp = Photo::fromFile($tmp_name);
					$pict_filename = $filename . Constants::PICTURE_FILENAME_SUFFIX . '.' . $ext;
					$temp->resize(Constants::PICTURE_SIZE, Constants::PICTURE_SIZE);
					$temp->save($basename . '/' . $pict_filename);
				
					// Resize and save the image for thumbnails
					$thumb_filename = $filename . Constants::THUMBNAIL_FILENAME_SUFFIX . '.' . $ext;
					$temp->resize(Constants::THUMBNAIL_SIZE, Constants::THUMBNAIL_SIZE);
					$temp->save($basename . '/' . $thumb_filename);
				
					// Save the image
					$filepath = $basename . '/' . $photo->getSource();
					$photo->save($filepath);
					
					// Add the photo to the various owners
					$album->addPhoto($photo);
					$log->setCoverPicture($photo);
					$log->addAlbum($album);
					
					$em->persist($photo);
					$em->flush();
				}

				return $this->redirect()->toRoute('base');
			}
		}

		$formViewModel = new ViewModel(array('travelLogForm' => $form));
		$formViewModel->setTemplate('base/base/travel_log_create');

		// Construct pagelets
		$welcomeBox = $this->getWelcomeBoxViewModel();
		$navigation = $this->getSidebarNavViewModel();

		// Construct main container
		$view = new ViewModel();
		$view->setTemplate('base/base/index_timeline');
		$view->addChild($welcomeBox, 'pageletWelcomeBox');
		$view->addChild($navigation, 'pageletNavigation');
		$view->addChild($formViewModel);

		return $this->getView('left-column')->addChild($view);
	}

	/**
	 * Photos page
	 * @return \Zend\View\Model\ViewModel
	 */
	public function photoStreamAction() {
		// Fetch user
		$userId = $this->zfcUserAuthentication()->getIdentity()->getId();
		$user = $this->getEntityManager()->getRepository('Base\Model\User')->find($userId);

		// Construct pagelet
		$photoStream = $this->getPhotoStreamViewModel($user);

		return $this->getView('timeline')->addChild($photoStream);
	}

	/**
	 * Process a post
	 * @return \Zend\View\Model\ViewModel
	 */
	public function statusUpdateAction() {
		/* @var $user User */
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
						$album->setUser($user);
						
						$user->addAlbum($album);
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
					$photo->setFrom($user);
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
					$status->setTo($user);
					$status->setLastUpdateDate($now);
					$status->setMessage($form->get($form::ELEM_MESSAGE)->getValue());
					$status->setPlace($form->get($form::ELEM_LOCATION)->getValue());
					$status->setVisibility($form->get($form::ELEM_AUDIENCE)->getValue());
					
					// Add the Status to the Traveloti
					$user->addStatus($status);
					
					// Persist the Status
					$em->persist($status);
					$post = $status;
				}
				
				$em->flush();
			}
		}

		return $this->redirect()->toRoute('base');
	}

	/**
	 * Intermediate page to create a photo
	 * @return \Zend\View\Model\ViewModel
	 */
	public function uploadPhotoAction() {
		/* @var $user User */

		// Fetch user
		$userId = $this->zfcUserAuthentication()->getIdentity()->getId();
		$user = $this->getEntityManager()->getRepository('Base\Model\User')->find($userId);

		// Create new form and image
		$form = new CombinedAlbumImageForm();
		$form->get('user_id')->setValue($user->getId());
		$photo = Photo::fromBlank(206, 206);

		$request = $this->getRequest();
		if($request->isPost() && (isset($_FILES['photos']) && count($_FILES['photos']))) {
			// Create image instance
			$filename = $_FILES['photos']['name'];
			$tmp_name = $_FILES['photos']['tmp_name'];

			$photo = Photo::fromFile($tmp_name);
			$photo->setSource($filename);
			$photo->setFrom($user);

			preg_match($this->image_filename_regexp, $filename, $matches);
			$new_filename = md5($matches[1])  . '.' . $matches[2];

			// Save the image to the temp directory
			$filepath = $this->getTempDir() . '/' . $new_filename;
			$photo->save($filepath);

			$form->get('src')->setValue($filename);
			$form->get('filepath')->setValue($new_filename);
		}

		// Construct pagelets
		$overlay = $this->getPhotoStreamOverlayViewModel($photo, $form);
		$photoStream = $this->getPhotoStreamViewModel($user);

		return $this->getView('timeline')->addChild($photoStream)
		->addChild($overlay, 'overlay');
	}

	/**
	 * Process a Photo
	 * @return \Zend\View\Model\ViewModel
	 */
	public function addPhotoAction() {
		$em = $this->getEntityManager();

		// Fetch user
		$userId = $this->zfcUserAuthentication()->getIdentity()->getId();
		$user = $em->getRepository('Base\Model\User')->find($userId);

		// Create a new form and image
		$form = new CombinedAlbumImageForm();
		$photo = Photo::fromBlank(206, 206);

		// Fetch request
		$request = $this->getRequest();
		if($request->isPost()) {
			// Create a new form and populate it with the request data
			$form = new CombinedAlbumImageForm();
			$form->setData($request->getPost());
			$form->get('user_id')->setValue($user->getId());

			// Create a new image and populate it with the tmp file
			$tmpFilepath = $this->getTempDir() . '/' . $request->getPost('filepath');
			$photo = Photo::fromFile($tmpFilepath);

			if($form->isValid()) {
				$now = new \DateTime();

				// Test for new album
				$album = $user->getAlbum($form->get('name')->getValue());
				if(null == $album) {
					$album = new Album();
					$album->setCreationDate($now);
					$album->setName($form->get('name')->getValue());
				}

				// Set album values
				$album->setDescription($form->get('description_text')->getValue());
				$album->setLastUpdateDate($now);
				$album->setLocation($form->get('album_location')->getValue());
				$album->setVisibility($form->get('album_audience')->getValue());

				// Add album to user and persist
				$user->addAlbum($album);
				$em->persist($album);
				$em->flush();

				// Extract filename and extension
				$pathinfo = pathinfo($form->get('src')->getValue());
				$filename = $pathinfo['filename'];
				$ext = $pathinfo['extension'];
				$basename = dirname(dirname(dirname(dirname(dirname(__DIR__)))))
				. '/public/content/' . $user->getUsername();

				// Resize and save the image for pictures
				$temp = Photo::fromFile($tmpFilepath);
				$pict_filename = $filename . Constants::PICTURE_FILENAME_SUFFIX . '.' . $ext;
				$temp->resize(Constants::PICTURE_SIZE, Constants::PICTURE_SIZE);
				$temp->save($basename . '/' . $pict_filename);

				// Resize and save the image for thumbnails
				$thumb_filename = $filename . Constants::THUMBNAIL_FILENAME_SUFFIX . '.' . $ext;
				$temp->resize(Constants::THUMBNAIL_SIZE, Constants::THUMBNAIL_SIZE);
				$temp->save($basename . '/' . $thumb_filename);

				// Set the image values
				$photo->setAlbum($album);
				$photo->setCreationDate($now);
				$photo->setFrom($user);
				$photo->setIcon($thumb_filename);
				$photo->setLastUpdateDate($now);
				$photo->setName($form->get('caption_text')->getValue());
				$photo->setPicture($pict_filename);
				$photo->setPlace($form->get('location')->getValue());
				$photo->setSource($form->get('src')->getValue());
				$photo->setSrcHeight($photo->getHeight());
				$photo->setSrcWidth($photo->getWidth());
				$photo->setVisibility($form->get('album_audience')->getValue());

				// Delete the temp image
				@unlink($tmpFilepath);

				// Save the image to the user's upload directory
				$filepath = $basename . '/' . $photo->getSource();
				$photo->save($filepath);

				// Add the image to the album
				$album->addPhoto($photo);

				// Persist the image
				$em->persist($photo);
				$em->flush();

				return $this->redirect()->toRoute('photoStream');
			} else {
				$msg = '<pre>';
				foreach ($form->getMessages() as $element => $messages) {
					$msg .= $element . ': ';
					foreach($messages as $valError => $message) {
						$msg .= $valError . ': ' . $message . "\n";
					}
				}
				$msg .= '</pre>';
				die($msg);
			}
		} else {
			die('not post');
		}

		// Construct pagelets
		$overlay = $this->getPhotoStreamOverlayViewModel($photo, $form);
		$photoStream = $this->getPhotoStreamViewModel($user);
		$photoStream->addChild($overlay, 'overlay');

		return $this->getView('timeline')->addChild($photoStream);
	}

	public function addLinkAction() {
		die('Sorry! This method is not implemented yet. :)');
	}

	/**
	 * Process a Comment for a status update
	 * @return \Zend\View\Model\ViewModel
	 */
	public function addCommentAction() {
		/* @var $post Status */
		/* @var $from User */

		$em = $this->getEntityManager();

		// Fetch user
		$userId = $this->zfcUserAuthentication()->getIdentity()->getId();
		$user = $em->getRepository('Base\Model\User')->find($userId);
			
		$request = $this->getRequest();
		if($request->isPost()) {
			// Create new post form and set values from request
			$cf = new CommentForm();
			$cf->setData($request->getPost());

			if($cf->isValid()) {
				$now = new \DateTime();

				// Fetch user and post
				$type = $request->getPost('type');
				switch($type) {
					case 'photo':
						$clazz = 'Base\Model\Photo';
						break;
					case 'link':
						$clazz = 'Base\Model\Link';
						break;
					case 'comment':
						$clazz = 'Base\Model\Comment';
						break;
					case 'status':
					default:
						$clazz = 'Base\Model\Status';
				}
				$id = $request->getPost('post_id');
				$post = $em->getRepository($clazz)->find($id);

				// Create a new comment object and set values
				$comment = new Comment();
				$comment->setCanLike(true);
				$comment->setCanRemove(true);
				$comment->setCreationDate($now);
				$comment->setFrom($user);
				$comment->setMessage($request->getPost('message'));
				$comment->setUserLikes(true);
				switch($type) {
					case 'photo':
						$comment->setPhoto($post);
						break;
					case 'link':
						$comment->setLink($post);
						break;
					case 'comment':
						$comment->setParent($post);
						break;
					case 'status':
					default:
						$comment->setStatus($post);
				}

				// @todo IMPLEMENT USER GLOBAL PRIVACY SETTINGS
				$comment->setIsPrivate(false);

				// Add comment to post
				$post->addComment($comment);
				$em->persist($comment);

				// Test if the status update belongs to someone else
				$fromId = $post->getFrom()->getId();
				if($user->getId() != $fromId) {
					$recipient = $em->getRepository('Base\Model\Traveloti')->find(array('id' => $fromId));
					// Create and persist a notification
					$notification = $this->createNotification($user, $recipient, $post);
					$recipient->addNotification($notification);
					$em->persist($notification);
				}

				// Database commit
				$em->flush();

				return $this->redirect()->toRoute('base');
			}
		}

		// Construct pagelets
		$welcomeBox = $this->getWelcomeBoxViewModel();
		$navigation = $this->getSidebarNavViewModel();
		$composer = $this->getComposerViewModel($user);
		$homeStream = $this->getHomeStreamViewModel($user);

		// Construct main container
		$view = new ViewModel();
		$view->setTemplate('base/base/index');
		$view->addChild($welcomeBox, 'pageletWelcomeBox');
		$view->addChild($navigation, 'pageletNavigation');
		$view->addChild($composer, 'pageletComposer');
		$view->addChild($homeStream, 'pageletHomeStream');

		return $this->getView('left-column')->addChild($view);
		return $this->redirect()->toRoute('photoStream');
	}

	/**
	 * Process a new profile headshot
	 */
	public function makeProfileAction() {
		/* @var $user User */
		/* @var $newPhoto Photo */
		/* @var $album Album */

		$em = $this->getEntityManager();

		// Fetch user
		$userId = $this->zfcUserAuthentication()->getIdentity()->getId();
		$user = $em->getRepository('Base\Model\User')->find($userId);

		// Fetch the image and set as the new profile photo
		$photoId = $this->params('id');
		$newPhoto = $em->getRepository('Base\Model\Photo')->find($photoId);
		$user->setPicture($newPhoto);
		$em->persist($user);

		// Fetch the profile album and add the new photo to it
		$album = $user->getProfileAlbum();
		if(!$album->contains($newPhoto)) {
			$album->addPhoto($newPhoto);
		}

		// Persist the entities
		$em->persist($newPhoto);
		$em->flush();

		return $this->redirect()->toRoute('photoStream');
	}

	private function createProfileAlbum(User $user) {
		$em = $this->getEntityManager();
		$now = new \DateTime();

		// Create a profile picture album
		$album = new Album();
		$album->setCreationDate($now);
		$album->setName(Constants::ALBUM_PROFILE);
		$album->setLastUpdateDate($now);
		$album->setUser($user);
		$album->setVisibility(Constants::PRIVACY_SELF);

		// Add the album to the user
		$user->addAlbum($album);

		// Persist the album
		$em->persist($album);
		$em->flush();
	}

	private function createDefaultHeadshots(User $user) {
		// Create the headshot
		$basepath = dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/public/';
		$src = ($user->getGender() ? 'fb-avatar-male.jpg' : 'fb-avatar-female.jpg');
		$filepath = $basepath . 'images/' . $src;
		$headshot = Photo::fromFile($filepath);

		// Test if user's upload directory exists
		$basepath .= 'content/' . $user->getUsername();
		if(!file_exists($basepath)) {
			mkdir($basepath);
		}

		// Create and save a large thumbnail
		$filepath = $basepath . '/' . Constants::DEFAULT_THUMB_5050;
		$headshot->resize(50, 50);
		$success = $headshot->save($filepath, 80, Photo::JPEG);

		// Create and save a small thumbnail
		if($success) {
			$filepath = $basepath . '/' . Constants::DEFAULT_THUMB_3232;
			$headshot->resize(32, 32);
			$success = $headshot->save($filepath, 80, Photo::JPEG);
		}
		if(!$success) {
			throw new \RuntimeException("Application error writing headshot images to file.");
		}

		// Update user
		$em = $this->getEntityManager();
		$user->setHasSilhouette(true);
		$em->persist($user);
		$em->flush();
	}

	/**
	 * Returns the specified User's home stream, which include their own Posts
	 * (Status, Photo, Link or Comment) as well as their friends' Posts all in
	 * reverse chronological order.
	 *
	 * @param Traveloti $traveloti
	 * @return \Zend\View\Model\ViewModel
	 */
	private function getHomeStreamViewModel(Traveloti $traveloti) {
		$feed = $traveloti->getHomeStream();

		$view = new ViewModel(array('feed' => $feed, 'width' => 'base'));
		$view->setTemplate('base/base/home_stream');
		return $view;
	}

	private function getPhotoStreamViewModel(User $user) {
		$photos = $user->getPhotos();
		$form = new InlineImageForm();

		$view = new ViewModel(array(
			'photos' => $photos,
			'inlineImageForm' => $form,
		));
		$view->setTemplate('base/base/photo_stream');
		return $view;
	}

	private function getPhotoStreamOverlayViewModel(Photo $photo, CombinedAlbumImageForm $form) {
		$view = new ViewModel(array(
			'photo' => $photo,
			'albumImageForm' => $form,
		));
		$view->setTemplate('base/base/photo-stream-overlay');
		return $view;
	}
}
?>