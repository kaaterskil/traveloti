<?php
/**
 * Traveloti Library
 *
 * @category	Traveloti
 * @package		Traveloti_Application
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Application\Validator;

use Zend\I18n\Filter\Alnum as AlnumFilter;
use Zend\I18n\Validator\Alnum as ZendAlnum;

/**
 * Alnum Class
 *
 * @author Blair
 */
class Alnum extends ZendAlnum {
	
    /**
     * Options for this validator
     *
     * @var array
     */
    protected $options = array(
        'allowWhiteSpace' => false,  // Whether to allow white space characters; off by default
        'allowNull' => false, // Whether to allow null values by default
    );
    
    /**
     * returns the allowNull option
     *
     * @return boolean:
     */
    public function getAllowNull() {
    	return $this->options['allowNull'];
    }
    
    /**
     * Sets the allowNull option
     *
     * @param boolean $allowNull
     * @return \Application\Validator\Alnum
     */
    public function setAllowNull($allowNull) {
    	$this->options['allowNull'] = (boolean) $allowNull;
    	return $this;
    }
	
    /**
     * Returns true if and only if $value contains only alphabetic and digit characters
     *
     * @param string $value
     * @return boolean
     * @see \Zend\I18n\Validator\Alnum::isValid()
     */
    public function isValid($value) {
    	if(!is_string($value) && !is_int($value) && !is_float($value)) {
    		$this->error(self::INVALID);
    		return false;
    	}
    	
    	$this->setValue($value);
    	
    	if($value == '') {
    		if($this->options['allowNull'] == true) {
    			return true;
    		} else {
    			$this->error(self::STRING_EMPTY);
    			return false;
    		}
    	}
    	
    	if(self::$filter == null) {
    		self::$filter = new AlnumFilter();
    	}
    	
    	self::$filter->setAllowWhiteSpace($this->options['allowWhiteSpace']);
    	if($value != self::$filter->filter($value)) {
    		$this->error(self::NOT_ALNUM);
    		return false;
    	}
    	
    	return true;
    }
}
?>