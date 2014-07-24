<?php
/**
 * Traveloti Library
 *
 * @category	Traveloti
 * @package		Traveloti_Application
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Application\StdLib;

/**
 * Application constants
 * @author Blair
 */
class Constants {
	// Status
	const NOTIFICATION_NEW = "notification_new";
	const NOTIFICATION_UNREAD = "notification_unread";
	const NOTIFICATION_READ = "notification_read";
	
	const POST_STATUS_NEW = "post_status_new";
	const POST_STATUS_SENT = "post_status_sent";
	const COMMENT_STATUS_NEW = "comment_status_new";
	const COMMENT_STATUS_SENT = "comment_status_sent";
	const LIKE_STATUS_NEW = "like_status_new";
	const LIKE_STATUS_SENT = "like_status_sent";
	
	// Privacy
	const PRIVACY_EVERYONE = "privacy_everyone";
	const PRIVACY_FRIENDS_OF_FRIENDS = "privacy_friends_of_friends";
	const PRIVACY_FRIENDS = "privacy_friends";
	const PRIVACY_SELF = "privacy_self";
	
	// Default albums
	const ALBUM_PROFILE = 'Profile Pictures';
	const ALBUM_UNDEFINED = 'Undefined Album';
	
	// Profile thumbnail filenames
	const PROFILE_THUMB_5050 = 'profile_thumb_5050.jpg';
	const PROFILE_THUMB_3232 = 'profile_thumb_3232.jpg';
	const DEFAULT_THUMB_5050 = 'default_thumb_5050.jpg';
	const DEFAULT_THUMB_3232 = 'default_thumb_3232.jpg';
	const THUMBNAIL_FILENAME_SUFFIX = '.thumb';
	const PICTURE_FILENAME_SUFFIX = '.pict';
	const PICTURE_SIZE = 180;
	const THUMBNAIL_SIZE = 90;
}
?>