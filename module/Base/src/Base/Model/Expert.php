<?php
/**
 * Traveloti Library
 *
 * @category	Traveloti
 * @package		Traveloti_Base
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Base\Model;

use Application\StdLib\Object;

/**
 * interface Expert
 * @author Blair
 */
interface Expert extends Object {
	
	/** @return \DateTime */
	public function getSince();
	
	/** @return \DateTime */
	public function setSince($since);
	
	/** @return string */
	public function getExpertName();
	
	public function setExpertName($expertName);
	
	/** @return string */
	public function getAddress();
	
	/** @return string */
	public function getBio();
	
	public function setBio($bio);
	
	/** @return string */
	public function getTestimonial();
	
	public function setTestimonial($testimonial);
	
	/** @return string */
	public function getWebsite();
	
	public function setWebsite($website);
	
	/** @return string */
	public function getFacebookLink();
	
	public function setFacebookLink($facebookLink);
	
	/** @return string */
	public function getTwitterLink();
	
	public function setTwitterLink($twitterLink);
	
	/** @return string */
	public function getLinkedinLink();
	
	public function setLinkedinLink($linkedinLink);
	
	/** @return string */
	public function getYoutubeLink();
	
	public function setYoutubeLink($youtubeLink);
	
	/** @return string */
	public function getBlogLink();
	
	public function setBlogLink($blogLink);
}
?>