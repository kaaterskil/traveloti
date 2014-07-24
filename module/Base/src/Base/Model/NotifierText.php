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

/**
 * NotifierText is a small object that contains the text for a notification
 * @author Blair
 */
class NotifierText {
	private $title;
	private $htmlTitle;
	private $body;
	private $htmlBody;

	public function __construct($title, $htmlTitle, $body, $htmlBody) {
		$this->title = $title;
		$this->htmlTitle = $htmlTitle;
		$this->body = $body;
		$this->htmlBody = $htmlBody;
	}

	public function getTitle() {
		return $title;
	}

	public function getHtmlTitle() {
		return $this->htmlTitle;
	}

	public function getBody() {
		return $body;
	}

	public function getHtmlBody() {
		return $this->htmlBody;
	}
}
?>