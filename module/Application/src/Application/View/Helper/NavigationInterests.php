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

use Config\Model\Profile;
use Config\Model\ProfileCategory;
use Base\Model\Interest;
use Base\Model\Traveloti;
use Zend\Form\View\Helper\AbstractHelper;

/**
 * NavigationTile Class
 * @author Blair
 */
class NavigationInterests extends AbstractHelper {

	public function __invoke(Traveloti $traveloti) {
		/* @var $interest Interest */
		/* @var $profile Profile */

		$xhtml = '';
		$interests = $this->initInterests($traveloti->getInterests()->getValues());
		if(count($interests)) {
			$firstColumn = true;
			$categoryTest = null;
			$xhtml .= '<ul class="timeline-navigation-interest-list">' . "\n";
			for($i = 0; $i < count($interests['categories']); $i++) {
				$category = $interests['categories'][$i];
				$profile = $interests['profiles'][$i];
				if($category != $categoryTest) {
					if($firstColumn) {
						$firstColumn = false;
					} else {
						$xhtml .= '</li>' . "\n";
					}
					$categoryTest = $category;
					$xhtml .= '<li class="interest-category"><span class="">' . $category . '</span>' . "\n";
				}
				$xhtml .= '<div class="interest">' . $profile . '</div>' . "\n";
			}
			$xhtml .= '</li>' . "\n" . '</ul>' . "\n";
		}
		return $xhtml;
	}

	private function initInterests(array $interests) {
		/* @var $interest Interest */

		$categories = array();
		$profiles = array();
		foreach($interests as $interest) {
			$category = $interest->getProfile()->getCategory()->getDisplayName();
			$name = $interest->getProfile()->getDisplayName();
			$categories[] = $category;
			$profiles[] = $name;
		}
		array_multisort($categories, SORT_ASC, $profiles, SORT_ASC);
		return array('categories' => $categories, 'profiles' => $profiles);
	}
}
?>