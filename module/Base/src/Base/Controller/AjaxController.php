<?php
/**
 * Traveloti Library
 *
 * @package		Traveloti_Base
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Base\Controller;

use Zend\View\View;

use Application\StdLib\Constants;

use Base\Model\Comment;
use Base\Model\FriendRequest;
use Base\Model\Like;
use Base\Model\Link;
use Base\Model\Post;
use Base\Model\Status;
use Base\Model\User;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

/**
 * Controller to handle Ajax requests
 * @author Blair
 */
class AjaxController extends AbstractBaseController {

	public function indexAction() {
		$view = new ViewModel(array());
		$view->setTemplate('base/ajax/index');
		$view->setTerminal(true);
		return $view;
	}

	public function fetchContactThumbnailAction() {
		$response = 'failure';
		if($this->getRequest()->isXmlHttpRequest()) {
			/* @var $receiver User */

			$em = $this->getEntityManager();

			// Fetch user
			$username = $this->params('oid');
			$receiver = $em->getRepository('Base\Model\Traveloti')->findOneBy(array('username' => $username));
			if($receiver == null) {
				$response = 'failure';
			} else {
				// Fetch view helpers
				$basePath = $this->getServiceLocator()->get('viewhelpermanager')->get('basePath');
				$htmlImg = $this->getServiceLocator()->get('viewhelpermanager')->get('htmlImg');

				$nv = new View();
				$icon = ($receiver->getPicture()
					? $receiver->getPicTure()->getIcon()
					: Constants::DEFAULT_THUMB_3232);
				$src = $basePath(
					'/content/' . $receiver->getUsername() . '/' . $icon);
				$img = $htmlImg(
					$receiver->getDisplayName(),
					$src,
					array('class' => 'chat-list-thumbnail img', 'height' => 30, 'width' => 30)
				);
				$response = $img;
			}
		}

		$view = new ViewModel(array('response' => $response));
		$view->setTemplate('base/ajax/index');
		$view->setTerminal(true);
		return $view;
	}

	/**
	 * Process a Like for a status update
	 */
	public function addLikeAction() {
		$response = 'Invalid XmlHttpRequest';
		if($this->getRequest()->isXmlHttpRequest()) {
			/* @var $post Status */
			/* @var $like Like */
			/* @var $from User */

			$em = $this->getEntityManager();

			// Fetch user
			$userId = $this->zfcUserAuthentication()->getIdentity()->getId();
			$user = $em->getRepository('Base\Model\User')->find($userId);

			// Fetch post
			$type = $this->params('oid');
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
			$postId = $this->params('uid');
			$post = $em->getRepository($clazz)->find($postId);

			// Test if the user already liked this post
			$alreadyLiked = false;
			$likes = $post->getLikes()->getValues();
			foreach ($likes as $like) {
				$likeId = $like->getFrom()->getId();
				if($likeId == $user->getId()){
					$alreadyLiked = true;
					break;
				}
			}
			if(!$alreadyLiked){
				try {
					$now = new \DateTime();

					// Create a new Like object and set values
					$like = new Like();
					$like->setCreationDate($now);
					$like->setFrom($user);
					switch($type) {
						case 'photo':
							$like->setPhoto($post);
							break;
						case 'link':
							$like->setLink($post);
							break;
						case 'comment':
							$like->setComment($post);
							break;
						case 'status':
						default:
							$like->setStatus($post);
					}

					// Add the like to its parent and persist it
					$post->addLike($like);
					$em->persist($like);

					// Test if the status update is on someone else's timeline.
					$fromId= $post->getFrom()->getId();
					if($fromId != $user->getId()) {
						$recipient = $em->getRepository('Base\Model\Traveloti')->find(array('id' => $fromId));
						// Create and persist a notification
						$notification = $this->createNotification($user, $recipient, $like);
						$recipient->addNotification($notification);
						$em->persist($notification);
					}

					// Database commit
					$em->flush();
					$response = 'success';
				} catch(\Exception $e) {
					$response = $e->getMessage();
				}
			} else {
				$response = 'You already liked this.';
			}
		}

		$view = new ViewModel(array('response' => $response));
		$view->setTemplate('base/ajax/index');
		$view->setTerminal(true);
		return $view;
	}

	/**
	 * Responds to an Ajax query to confirm a friend request
	 * @return \Zend\View\Model\ViewModel
	 */
	public function confirmFriendAction() {
		$response = 'Invalid XmlHttpRequest';
		if($this->getRequest()->isXmlHttpRequest()) {
			/* @var $user User */
			/* @var $sender User */
			/* @var $friend User */
			/* @var $request FriendRequest */

			$em = $this->getEntityManager();

			$userId = $this->zfcUserAuthentication()->getIdentity()->getId();
			$user = $em->getRepository('Base\Model\User')->find($userId);

			$senderId = $this->getRequest()->getQuery('uid');
			$sender = $em->getRepository('Base\Model\User')->find($senderId);

			$requestId = $this->getRequest()->getQuery('oid');
			$request = $em->getRepository('Base\Model\FriendRequest')->find($requestId);

			// Test if sender is already a friend
			$alreadyFriends - false;
			$friends = $user->getAllFriends();
			foreach ($friends as $friend) {
				if($sender->equals($friend)) {
					$alreadyFriends = true;
					break;
				}
			}
			try {
				if(!$alreadyFriends) {
					// Create and persist friendship
					$user->addFollower($sender);
					$em->persist($sender);
					$response = 'success';
				} else {
					$response = 'duplicate';
				}

				// Delete friend request
				$em->remove($request);
				$em->flush();
			} catch (\Exception $e) {
				$response = $e->getMessage();
			}
		}

		$view = new ViewModel(array('response' => $response));
		$view->setTemplate('base/ajax/index');
		$view->setTerminal(true);
		return $view;
	}

	/**
	 * Responds to an Ajax query to ignore a friend request
	 * @return \Zend\View\Model\ViewModel
	 */
	public function ignoreFriendRequestAction() {
		$response = 'Invalid XmlHttpRequest';
		if($this->getRequest()->isXmlHttpRequest()) {
			/* @var $sender User */
			/* @var $request FriendRequest */

			$em = $this->getEntityManager();

			$senderId = $this->getRequest()->getQuery('uid');
			$sender = $em->getRepository('Base\Model\User')->find($senderId);

			$requestId = $this->getRequest()->getQuery('oid');
			$request = $em->getRepository('Base\Model\FriendRequest')->find($requestId);

			// Set request status to 'read'
			$request->setIsUnread(false);
			$em->persist($request);
			$em->flush();
			$response = 'success';
		}

		$view = new ViewModel(array('response' => $response));
		$view->setTemplate('base/ajax/index');
		$view->setTerminal(true);
		return $view;
	}

	/**
	 * Responds to an Ajax query to delete a friend request
	 * @return \Zend\View\Model\ViewModel
	 */
	public function deleteFriendRequest() {
		$response = 'Invalid XmlHttpRequest';
		if($this->getRequest()->isXmlHttpRequest()) {
			/* @var $sender User */
			/* @var $request FriendRequest */

			$em = $this->getEntityManager();

			$senderId = $this->getRequest()->getQuery('uid');
			$sender = $em->getRepository('Base\Model\User')->find($senderId);

			$requestId = $this->getRequest()->getQuery('oid');
			$request = $em->getRepository('Base\Model\FriendRequest')->find($requestId);

			try {
				// Remove friend request
				$em->remove($request);
				$em->flush();
				$response = 'success';
			} catch(\Exception $e) {
				$response = $e->getMessage();
			}
		}

		$view = new ViewModel(array('response' => $response));
		$view->setTemplate('base/ajax/index');
		$view->setTerminal(true);
		return $view;
	}

	/**
	 * Responds to Ajax query to send a friend request
	 * @return \Zend\View\Model\ViewModel
	 */
	public function sendFriendRequestAction() {
		/* @var $user User */
		/* @var $targetFriend User */
		/* @var $sentRequest FriendRequest */

		$response = 'Invalid XmlHttpRequest.';
		if ($this->getRequest()->isXmlHttpRequest()) {
			$em = $this->getEntityManager();

			$userId = $this->zfcUserAuthentication()->getIdentity()->getId();
			$user = $em->getRepository('Base\Model\User')->find($userId);

			$friendId = $this->getRequest()->getQuery('uid');
			$targetFriend = $em->getRepository('Base\Model\User')->find($friendId);

			// Test if this request is a duplicate
			$isDuplicate = false;
			$sentRequests = $user->getSentFriendRequests()->getValues();
			foreach($sentRequests as $sentRequest) {
				if($sentRequest->getRecipient()->equals($targetFriend)) {
					$isDuplicate = true;
					break;
				}
			}
			try {
				if($isDuplicate) {
					$response = 'duplicate';
				} else {
					$now = new \DateTime();

					// Create new Friend Request
					$fr = new FriendRequest();
					$fr->setCreationDate($now);
					$fr->setIsUnread(true);
					$fr->setRecipient($targetFriend);
					$fr->setSender($user);

					// Add to users
					$user->addSentFriendRequest($fr);
					$targetFriend->addFriendRequest($fr);

					// Persist entity
					$em->persist($fr);
					$em->flush();

					$response ='success';
				}
			} catch(Exception $e) {
				$response = $e->getMessage();
			}
		}

		// Set view model for an ajax response
		$view = new ViewModel(array('response' => $response));
		$view->setTemplate('base/ajax/index');
		$view->setTerminal(true);
		return $view;
	}
}
?>