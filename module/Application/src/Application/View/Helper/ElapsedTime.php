<?php
/**
 * Traveloti Library
 *
 * @category	Traveloti
 * @package		Traveloti_Base
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHtmlElement;

/**
 * Returns text describing the elapsed time between now and a specified timestamp.
 * @author Blair
 */
class ElapsedTime extends AbstractHtmlElement {

	public function __invoke($timestamp) {
		if($timestamp instanceof \DateTime) {
			$then = $timestamp->getTimestamp();
		} elseif (is_numeric($timestamp)) {
			$then = (int) $timestamp;
		} else {
			$then = strtotime($timestamp);
			if($then === false) {
				throw new \InvalidArgumentException();
			}
		}

		$now = time();
		$minutes = ceil(($now - $then) / 60);
		$hours = floor(($now - $then) / (60 * 60));
		$days = (int) date('w', $now) - (int) date('w', $then);
		$elapsedDays = ceil(($now - $then) / (60 * 60 *24));

		$ret = '';
		if($hours < 24) {
			if($minutes < 60) {
				if($minutes == 1) {
					$ret .= $minutes . ' minute ago';
				} else {
					$ret .= $minutes . ' minutes ago';
				}
			} else {
				if($hours == 1) {
					$ret .= $hours . ' hour ago';
				} else {
					$ret .= $hours . ' hours ago';
				}
			}
		} elseif($days == 1 || $days == -6) {
			$ret = 'Yesterday at ' . date('g:ia', $then);
		} elseif($elapsedDays < 7) {
			$ret = date('l \a\t g:ia', $then);
		} else {
			$ret = 'on ' . date('l, M d \a\t g:ia', $then);
		}
		return $ret;
	}
}
?>