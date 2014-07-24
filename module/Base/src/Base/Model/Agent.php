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

use Base\Model\Expert;
use Base\Model\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Agent Class
 * @author Blair
 *
 * @ORM\Entity
 * @ORM\Table(name="user_agent")
 */
class Agent extends User implements Expert {
	
	/*----- Properties -----*/
	
	/** @ORM\Column(type="string", length=100) */
	private $certification;
	
	/** @ORM\Column(type="string") */
	private $specialization;
	
	/** @ORM\Column(type="date", name="agent_since") */
	private $since;
	
	/** @ORM\Column(type="string", name="agency_name", length=100) */
	private $agencyName;
	
	/** @ORM\Column(type="string", length=100) */
	private $address1;
	
	/** @ORM\Column(type="string", length=100) */
	private $address2;
	
	/** @ORM\Column(type="string", length=60) */
	private $city;
	
	/** @ORM\Column(type="string", length=2) */
	private $region;
	
	/** @ORM\Column(type="string", name="postal_code", length=10) */
	private $postalCode;
	
	/** @ORM\Column(type="string", length=15) */
	private $telephone;
	
	/** @ORM\Column(type="string", name="business_hours", length=100) */
	private $hours;
	
	/** @ORM\Column(type="string", name="biography") */
	private $bio;
	
	/** @ORM\Column(type="string") */
	private $testimonial;
	
	/** @ORM\Column(type="string", name="link_website", length=255) */
	private $website;
	
	/** @ORM\Column(type="string", name="link_facebook", length=255) */
	private $facebookLink;
	
	/** @ORM\Column(type="string", name="link_twitter", length=255) */
	private $twitterLink;
	
	/** @ORM\Column(type="string", name="link_linkedin", length=255) */
	private $linkedinLink;
	
	/** @ORM\Column(type="string", name="link_youtube", length=255) */
	private $youtubeLink;
	
	/** @ORM\Column(type="string", name="link_blog", length=255) */
	private $blogLink;

	/*----- Getter/Setters -----*/

	/** @return string */
	public function getCertification() {
		return $this->certification;
	}

	public function setCertification($certification) {
		$this->certification = $certification;
	}

	/** @return string */
	public function getSpecialization() {
		return $this->specialization;
	}

	public function setSpecialization($specialization) {
		$this->specialization = $specialization;
	}

	/** @see \Base\Model\Expert::getSince() */
	public function getSince() {
		return $this->since;
	}

	/** @see \Base\Model\Expert::setSince() */
	public function setSince($since) {
		$this->since = $since;
	}

	/** @return string */
	public function getAgencyName() {
		return $this->agencyName;
	}

	public function setAgencyName($agencyName) {
		$this->agencyName = $agencyName;
	}

	/** @return string */
	public function getAddress1() {
		return $this->address1;
	}

	public function setAddress1($address1) {
		$this->address1 = $address1;
	}

	/** @return string */
	public function getAddress2() {
		return $this->address2;
	}

	public function setAddress2($address2) {
		$this->address2 = $address2;
	}

	/** @return string */
	public function getCity() {
		return $this->city;
	}

	public function setCity($city) {
		$this->city = $city;
	}

	/** @return string */
	public function getRegion() {
		return $this->region;
	}

	public function setRegion($region) {
		$this->region = $region;
	}

	/** @return string */
	public function getPostalCode() {
		return $this->postalCode;
	}

	public function setPostalCode($postalCode) {
		$this->postalCode = $postalCode;
	}

	/** @return string */
	public function getTelephone() {
		return $this->telephone;
	}

	public function setTelephone($telephone) {
		$this->telephone = $telephone;
	}

	/** @return string */
	public function getHours() {
		return $this->hours;
	}

	public function setHours($hours) {
		$this->hours = $hours;
	}

	/** @see \Base\Model\Expert::getBio() */
	public function getBio() {
		return $this->bio;
	}

	/** @see \Base\Model\Expert::setBio() */
	public function setBio($bio) {
		$this->bio = $bio;
	}

	/** @see \Base\Model\Expert::getTestimonial() */
	public function getTestimonial() {
		return $this->testimonial;
	}

	/** @see \Base\Model\Expert::setTestimonial() */
	public function setTestimonial($testimonial) {
		$this->testimonial = $testimonial;
	}

	/** @see \Base\Model\Expert::getWebsite() */
	public function getWebsite() {
		return $this->website;
	}

	/** @see \Base\Model\Expert::setWebsite() */
	public function setWebsite($website) {
		$this->website = $website;
	}

	/** @see \Base\Model\Expert::getFacebookLink() */
	public function getFacebookLink() {
		return $this->facebookLink;
	}

	/** @see \Base\Model\Expert::setFacebookLink() */
	public function setFacebookLink($facebookLink) {
		$this->facebookLink = $facebookLink;
	}

	/** @see \Base\Model\Expert::getTwitterLink() */
	public function getTwitterLink() {
		return $this->twitterLink;
	}

	/** @see \Base\Model\Expert::setTwitterLink() */
	public function setTwitterLink($twitterLink) {
		$this->twitterLink = $twitterLink;
	}

	/** @see \Base\Model\Expert::getLinkedinLink() */
	public function getLinkedinLink() {
		return $this->linkedinLink;
	}

	/** @see \Base\Model\Expert::setLinkedinLink() */
	public function setLinkedinLink($linkedInLink) {
		$this->linkedinLink = $linkedInLink;
	}

	/** @see \Base\Model\Expert::getYoutubeLink() */
	public function getYoutubeLink() {
		return $this->youtubeLink;
	}

	/** @see \Base\Model\Expert::setYoutubeLink() */
	public function setYoutubeLink($youtubeLink) {
		$this->youtubeLink = $youtubeLink;
	}

	/** @see \Base\Model\Expert::getBlogLink() */
	public function getBlogLink() {
		return $this->blogLink;
	}

	/** @see \Base\Model\Expert::setBlogLink() */
	public function setBlogLink($blogLink) {
		$this->blogLink = $blogLink;
	}

	/*----- Methods ----- */

	/** @see \Base\Model\Expert::getAddress() */
	public function getAddress() {
		$result = '';
		if($this->address1 != '') {
			$result = $this->address1;
		}
		if($this->address2 != '') {
			if($result != '') {
				$result .= "\n";
			}
			$result .= $this->address2;
		}
		if($this->city != '') {
			if($result != '') {
				$result .= "\n";
			}
			$result .= $this->city;
		}
		if($this->region != '') {
			if($this->city != '') {
				$result .= ', ';
			} elseif ($result != '') {
				$result .= "\n";
			}
			$result .= $this->region;
		}
		if($this->postalCode != '') {
			if($this->city != '' || $this->region != '') {
				$result .= ' ';
			} elseif($result != '') {
				$result .= "\n";
			}
			$result .= $this->postalCode;
		}
		return $result;
	}
	
	/** @see \Base\Model\Expert::getExpertName() */
	public function getExpertName() {
		return $this->getAgencyName();
	}
	
	/** @see \Base\Model\Expert::setExpertName() */
	public function setExpertName($expertName) {
		$this->setAgencyName($expertName);
	}
}
?>