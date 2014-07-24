<?php
/**
 * Traveloti Library
 *
 * @category	Traveloti
 * @package		Traveloti_Base
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Base\Controller\Plugin;

use Base\Model\TravelLog;
use Base\Model\User;
use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * SideBarGenerator Class
 *
 * @author Blair
 */
class SideBarGenerator extends AbstractPlugin {

	public function getSideBar($selected = null) {
		return $this->getConfig($selected);
	}

	private function getConfig($selected = null, $isSameRoute = false) {
		/* @var $user User */
		/* @var $travelLog TravelLog */

		if(null == $selected) {
			$selected = 'news_feed';
		}

		$logDirectory = array();
		$user = $this->getController()->zfcUserAuthentication()->getIdentity();
		$travelLogs = $user->getTravelLogs();
		if(count($travelLogs)) {
			foreach ($travelLogs as $travelLog) {
				$name = $travelLog->getName();
				$id = str_replace(' ', '-', $name);
				$href = 'timeline/' . $travelLog->getUsername();
				$icon = $travelLog->getCoverPicture() ? $travelLog->getCoverPicture()->getIcon(): '';
				$text = $travelLog->getDisplayName();
					
				$logDirectory[$name] = array(
					'id' => $id,
					'href' => $href,
					'text' => $text,
				);
			}
		} else {
			$logDirectory['travel_log'] = array(
				'id' => 'log',
				'href' => '/base/createLog',
				'img_class' => 'fb-icons-3',
				'img_pos_class' => 'icon-notes',
				'text' => 'Create a Travel Log',
			);
		}

		$subroute = ($isSameRoute) ? '' : '/settings';

		$configs = array(
			'settings' => array(
				'general' => array(
					'id' => 'general',
					'href' => $subroute . '/general',
					'img_class' => 'fb-icons-5',
					'img_pos_class' => 'icon-general',
					'text' => 'General',
				),
				'profile' => array(
					'id' => 'profile',
					'href' => $subroute . '/profile',
					'img_class' => '',
					'img_pos_class' => '',
					'text' => 'Profile',
				),
				'notifications' => array(
					'id' => 'notifications',
					'href' => $subroute . '/notifications',
					'img_class' => 'fb-icons-5',
					'img_pos_class' => 'icon-notifications',
					'text' => 'Notifications',
				),
			),
			'favorites' => array(
				'news_feed' => array(
					'id' => 'news',
					'href' => '/base',
					'img_class' => 'fb-icons-2',
					'img_pos_class' => 'icon-news',
					'text' => 'News Feed',
				),
				'messages' => array(
					'id' => 'messages',
					'href' => '/base/messages',
					'img_class' => 'fb-icons-1',
					'img_pos_class' => 'icon-messages',
					'text' => 'Messages',
				),
				'travel_log' => array(
					'id' => 'log',
					'href' => '/base/createLog',
					'img_class' => 'fb-icons-3',
					'img_pos_class' => 'icon-notes',
					'text' => 'Create a Travel Log',
				),
				'photos' => array(
					'id' => 'photos',
					'href' => '/timeline/' . $user->getUsername() . '/photos',
					'img_class' => 'fb-icons-3',
					'img_pos_class' => 'icon-photos',
					'text' => 'Photos',
				),
				'find_friends' => array(
					'id' => 'find',
					'href' => '/find-traveloti',
					'img_class' => 'fb-icons-4',
					'img_pos_class' => 'icon-friends',
					'text' => 'Find Traveloti',
				),
			),
			'Travel Logs' => $logDirectory,
		);

		foreach($configs as $category => $config) {
			foreach($config as $item => $values) {
				$configs[$category][$item]['selected'] = ($item == $selected ? true : false);
			}
		}

		return $configs;
	}
}
?>