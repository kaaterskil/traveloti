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
use Base\Controller\Plugin\SideBarGenerator;
use Base\Form\StatusForm;
use Base\Model\Notifiable;
use Base\Model\Notification;
use Base\Model\Status;
use Base\Model\Traveloti;
use Base\Model\User;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * AbstractBaseController Class
 * @author Blair
 */
abstract class AbstractBaseController extends AbstractActionController {

	/** @var Doctrine\ORM\EntityManager */
	private $em;

	/** @var Zend\View\Model\ViewModel */
	protected $view;

	/** @var string */
	private $layout;

	/** @var string */
	private $temp_dir;

	/** @var string */
	protected $image_filename_regexp = '/(.*)\.(\w+)$/';

	/*----- Constructor -----*/

	public function __construct() {
		$this->temp_dir = dirname(dirname(dirname(dirname(dirname(__DIR__)))))
		. '/public/tmp';
		if(!file_exists($this->temp_dir)) {
			mkdir($this->temp_dir);
		}
	}

	/*----- Getter/Setters -----*/

	/**
	 * @return Doctrine\ORM\EntityManager
	 */
	public function getEntityManager() {
		if(null == $this->em) {
			$this->em = $this->getServiceLocator()->get('\Doctrine\ORM\EntityManager');
		}
		return $this->em;
	}

	protected function setEntityManager(EntityManager $em) {
		$this->em = $em;
	}

	/** @return string */
	protected function getTempDir() {
		return $this->temp_dir;
	}

	protected function setTempDir($temp_dir) {
		if(!file_exists($temp_dir)){
			mkdir($temp_dir);
		}
		$this->temp_dir = $temp_dir;
	}

	/**
	 * @return \Zend\View\Model\ViewModel
	 */
	protected function getView($layout = null) {
		if((null == $this->view) || (null == $this->layout)
			|| (null != $layout && $layout != $this->layout)) {
			if(null == $layout) {
				$layout = 'left-column';
			}
			switch($layout) {
				case 'timeline':
					// $template = 'base/base/timeline_layout';
					// break;
				case 'full-page':
					$template = 'base/base/full_page_layout';
					break;
				case 'left-column':
				default:
					$template = 'base/base/left_column_layout';
			}

			// Construct leaderboard view model
			$requestsJewel = new ViewModel();
			$requestsJewel->setTemplate('base/base/requests_jewel');

			$notificationsJewel = new ViewModel();
			$notificationsJewel->setTemplate('base/base/notifications_jewel');

			$leaderboard = new ViewModel();
			$leaderboard->setTemplate('base/base/leaderboard');
			$leaderboard->addChild($requestsJewel, 'requestsJewel');
			$leaderboard->addChild($notificationsJewel, 'notificationsJewel');

			// Construct footer view model
			$footer = new ViewModel();
			$footer->setTemplate('base/base/footer');
				
			// Construct chat dock view model
			$dock = new ViewModel();
			//$dock->setTemplate('base/base/dock');
			$dock->setTemplate('base/base/new_dock');
			$xmppLogger = new ViewModel();
			$xmppLogger->setTemplate('base/base/xmpp_logger');

			$view = new ViewModel();
			$view->setTemplate($template);
			$view->addChild($leaderboard, 'leaderboardPagelet');
			$view->addChild($footer, 'footer');
			$view->addChild($dock, 'dockPagelet');
			$view->addChild($xmppLogger, 'xmppLoggerPagelet');

			$this->view = $view;
		}
		return $this->view;
	}

	/*----- Methods -----*/

	protected function createNotification(
			Traveloti $sender, Traveloti $recipient, Notifiable $obj) {
		
		$now = new \DateTime();
		$text = $obj->getNotifierText($sender);

		$n = new Notification();
		$n->setBody($text->getBody());
		$n->setCreationDate($now);
		$n->setHtmlBody($text->getHtmlBody());
		$n->setHtmlTitle($text->getHtmlTitle());
		$n->setIsHidden(false);
		$n->setIsUnread(true);
		$n->setLastUpdateDate($now);
		$n->setRecipient($recipient);
		$n->setSender($sender);
		$n->setTitle($text->getTitle());
		$n->setType($obj->getType());

		return $n;
	}

	protected function getComposerViewModel(
			Traveloti $from, Traveloti $to = null, StatusForm $form = null) {
		
		if(null == $form) {
			$form = new StatusForm();
			$form->get($form::ELEM_USERID)->setValue($from->getId());
		}
		
		if($to != null) {
			$form->setAttribute('action', $to->getUsername() . '/statusUpdate');
		} else {
			$form->setAttribute('action', 'base/statusUpdate');
		}

		$view = new ViewModel(array('statusForm' => $form));
		$view->setTemplate('base/base/composer');
		return $view;
	}

	protected function getSidebarNavViewModel($selected = null) {
		$navigation = $this->SideBarGenerator()->getSideBar($selected);

		// Set nested view model for sidebar navigation
		$snvm = new ViewModel(array('sidebarNav' => $navigation));
		$snvm->setTemplate('base/base/navigation');

		return $snvm;
	}

	protected function getWelcomeBoxViewModel() {
		$view = new ViewModel();
		$view->setTemplate('base/base/welcome_box');
		return $view;
	}
}
?>