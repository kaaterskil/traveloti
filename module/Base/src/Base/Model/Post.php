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

use Application\StdLib\Observable;
use Base\Model\Comment;
use Base\Model\Like;
use Base\Model\Notifiable;
use Base\Model\User;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Defines common properties of status updates, photos, videos and links.
 * @author Blair
 */
interface Post extends Observable, Notifiable {
	
	/** @return int */
	public function getId();
	
	/** @return string */
	public function getMessage();
	
	/** @return string */
	public function getVisibility();
	
	/** @return \DateTime() */
	public function getCreationDate();
	
	/** @return \DateTime() */
	public function getLastUpdateDate();
	
	/** @return ArrayCollection */
	public function getComments();
	
	public function addComment(Comment $comment);
	
	/** @return ArrayCollection */
	public function getLikes();
	
	public function addLike(Like $like);
}
?>