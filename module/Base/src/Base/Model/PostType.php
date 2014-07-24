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

use Application\StdLib\Enum;

/**
 * PostType Class
 * @author Blair
 */
class PostType extends Enum {
	const POST_TYPE_VIDEO = "post_type_video";
	const POST_TYPE_PHOTO = "post_type_photo";
	const POST_TYPE_LINK = "post_type_link";
	const POST_TYPE_QUESTION = "post_type_question";
	const POST_TYPE_EVENT = "post_type_event";
	const POST_TYPE_TEXT = "post_type_text";
	
	public static function VIDEO() {
		return self::instance(self::POST_TYPE_VIDEO);
	}
	
	public static function PHOTO() {
		return self::instance(self::POST_TYPE_PHOTO);
	}
	
	public static function LINK() {
		return self::instance(self::POST_TYPE_LINK);
	}
	
	public static function QUESTION() {
		return self::instance(self::POST_TYPE_QUESTION);
	}
	
	public static function EVENT() {
		return self::instance(self::POST_TYPE_EVENT);
	}
	
	public static function TEXT() {
		return self::instance(self::POST_TYPE_TEXT);
	}
}
?>