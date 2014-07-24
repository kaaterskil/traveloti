<?php
/**
 * Traveloti Library
 *
 * @package		Traveloti_Base
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Base\Form;

use Zend\Form\Form;
use Zend\Form\Element;

/**
 * Leaderboard search form
 * @author Blair
 */
class SearchForm extends Form {
	
	public function __construct() {
		parent::__construct();
		$this->init();
	}
	
	private function init() {
		$query = new Element\Text();
		$query->setAttributes(array(
			'autocomplete' => 'off',
			'class' => 'input-text',
			'id' => 'query',
			'maxlength' => 100,
			'name' => 'query',
			'placeholder' => 'Search for destinations, interests, seasons and people',
			'title' => 'Search for destinations, interests, seasons and people',
		));
		$this->add($query);
		
		$submit = new Element\Submit();
		$submit->setAttributes(array(
			'class' => 'search-submit',
			'name' => 'submit_btn',
			'title' => 'Search for destinations, interests, seasons and people',
			'value' => null,
		));
		$this->add($submit);
	}
}
?>